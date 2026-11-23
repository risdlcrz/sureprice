<?php

use App\Models\Company;
use App\Models\CompanyChangeRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function cncClient(?string $companyName = 'Mabini Realty'): User
{
    $user = User::factory()->create(['role' => 'client', 'user_type' => 'company', 'name' => $companyName ?? 'Juan Dela Cruz']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username, 'company_name' => $companyName,
        'contact_person' => 'Juan Dela Cruz', 'designation' => 'client', 'status' => 'approved']);

    return $user;
}

function cncProof(): UploadedFile
{
    return UploadedFile::fake()->create('sec-certificate.pdf', 120, 'application/pdf');
}

beforeEach(fn () => Storage::fake(CompanyChangeRequest::PROOF_DISK));

test('a client requests a company name change with proof; nothing changes until an admin approves', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $client = cncClient();

    $this->actingAs($client)->get('/account')->assertOk()->assertSee('Company name')->assertSee('Request a name change');

    // proof is required, and the name must actually change
    $this->post('/account/company-name', ['new_company_name' => 'Mabini Realty'])->assertSessionHasErrorsIn('companyName', ['new_company_name', 'proof']);

    $this->post('/account/company-name', ['new_company_name' => 'Mabini Realty Holdings Inc.', 'reason' => 'Incorporated with the SEC', 'proof' => cncProof()])
        ->assertRedirect(route('account.edit'))->assertSessionHas('success');

    $cr = CompanyChangeRequest::firstOrFail();
    expect($cr->only(['field', 'old_value', 'new_value', 'status']))->toBe(['field' => 'company_name', 'old_value' => 'Mabini Realty', 'new_value' => 'Mabini Realty Holdings Inc.', 'status' => 'pending']);
    Storage::disk(CompanyChangeRequest::PROOF_DISK)->assertExists($cr->proof_path);
    expect($client->company->fresh()->company_name)->toBe('Mabini Realty')
        ->and(Notification::where('user_id', $admin->id)->where('type', 'company_change_requested')->exists())->toBeTrue();

    // only one pending request at a time; the client sees it as under review
    $this->post('/account/company-name', ['new_company_name' => 'Another Name', 'proof' => cncProof()])->assertSessionHas('error');
    $this->get('/account')->assertSee('Waiting for review:')->assertSee('Mabini Realty Holdings Inc.')->assertSee('Withdraw request');

    // the owner and admins can open the proof; other clients can't
    $this->get(route('company-changes.proof', $cr))->assertOk();
    $this->actingAs(cncClient('Other Co'))->get(route('company-changes.proof', $cr))->assertForbidden();
    $this->actingAs($admin)->get(route('company-changes.proof', $cr))->assertOk();

    // admin reviews and approves
    $this->get('/admin/company-changes')->assertOk()->assertSee('Mabini Realty Holdings Inc.')->assertSee('sec-certificate.pdf');
    $this->post(route('admin.company-changes.approve', $cr), ['review_notes' => 'Matches the SEC certificate'])->assertSessionHas('success');

    expect($cr->fresh()->status)->toBe('approved')
        ->and($client->company->fresh()->company_name)->toBe('Mabini Realty Holdings Inc.')
        ->and($client->fresh()->name)->toBe('Mabini Realty Holdings Inc.')
        ->and(Notification::where('user_id', $client->id)->where('type', 'company_change_approved')->exists())->toBeTrue();

    $this->get('/admin/company-changes?tab=history')->assertOk()->assertSee('Matches the SEC certificate');
    $this->actingAs($client)->get('/account')->assertSee('Name change approved');
});

test('a rejected change keeps the old name, needs a reason, and tells the client why', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $client = cncClient();
    $this->actingAs($client)->post('/account/company-name', ['new_company_name' => 'Totally New Corp', 'proof' => cncProof()]);
    $cr = CompanyChangeRequest::firstOrFail();

    $this->actingAs($admin)->post(route('admin.company-changes.reject', $cr), [])->assertSessionHasErrors('review_notes');
    $this->post(route('admin.company-changes.reject', $cr), ['review_notes' => 'The document shows a different name.'])->assertSessionHas('success');

    expect($cr->fresh()->status)->toBe('rejected')->and($client->company->fresh()->company_name)->toBe('Mabini Realty');
    $this->post(route('admin.company-changes.approve', $cr))->assertSessionHas('error');   // already reviewed

    $this->actingAs($client)->get('/account')->assertSee("wasn't approved", false)->assertSee('The document shows a different name.')->assertSee('Request a name change');
});

test('clients can withdraw a pending request; only clients and admins can use these pages', function () {
    $client = cncClient(null);   // individual client adding a company name
    $this->actingAs($client)->get('/account')->assertSee('Add a company name');
    $this->post('/account/company-name', ['new_company_name' => 'Juan Builders', 'proof' => cncProof()]);
    $cr = CompanyChangeRequest::firstOrFail();

    $this->delete(route('account.company-name.destroy', $cr))->assertRedirect(route('account.edit'));
    expect($cr->fresh()->status)->toBe('cancelled');
    Storage::disk(CompanyChangeRequest::PROOF_DISK)->assertMissing($cr->proof_path);

    $supplier = User::factory()->create(['role' => 'supplier', 'user_type' => 'company']);
    Company::create(['user_id' => $supplier->id, 'email' => $supplier->email, 'username' => $supplier->username, 'company_name' => 'Acme', 'designation' => 'supplier', 'status' => 'approved']);
    $this->actingAs($supplier)->post('/account/company-name', ['new_company_name' => 'Acme 2', 'proof' => cncProof()])->assertForbidden();
    $this->get('/admin/company-changes')->assertForbidden();
    $this->actingAs(User::factory()->create(['role' => 'manager', 'user_type' => 'employee']))->get('/admin/company-changes')->assertForbidden();
});
