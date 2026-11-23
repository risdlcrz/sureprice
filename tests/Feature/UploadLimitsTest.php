<?php

use App\Models\Company;
use App\Models\Conversation;
use App\Models\User;
use App\Support\Uploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

function ulClient(): User
{
    $user = User::factory()->create(['role' => 'client', 'user_type' => 'company']);
    Company::create(['user_id' => $user->id, 'email' => $user->email, 'username' => $user->username,
        'company_name' => 'Mabini Realty', 'designation' => 'client', 'status' => 'approved']);

    return $user;
}

function ulConversation(): array
{
    $manager = User::factory()->create(['role' => 'manager', 'user_type' => 'employee']);
    $client = ulClient();

    return [Conversation::create(['client_id' => $client->id, 'admin_id' => $manager->id, 'status' => 'active', 'last_message_at' => now()]), $client];
}

beforeEach(fn () => Storage::fake('public'));

test('the limits come from config/uploads.php and never exceed what PHP accepts', function () {
    expect(Uploads::maxKb())->toBe(min(10240, Uploads::serverMaxKb() ?? PHP_INT_MAX))
        ->and(Uploads::maxKb(2048))->toBe(2048)                    // stricter per upload is allowed
        ->and(Uploads::maxKb(99999))->toBe(Uploads::maxKb())        // looser is not
        ->and(Uploads::hint('attachments', 5))->toBe('JPG, PNG, WEBP, PDF, DOC, DOCX, XLS or XLSX · up to 10 MB each · up to 5 files')
        ->and(Uploads::hint('documents', 1, 5120))->toBe('JPG, PNG, WEBP or PDF · up to 5 MB');

    $attrs = (string) Uploads::attributes('images', 5, 2048);
    expect($attrs)->toContain('accept=".jpg,.jpeg,.png,.webp"', 'data-upload-max-kb="2048"', 'data-upload-max-files="5"');
});

test('chat attachments reject disallowed types, disguised files and oversize files with clear messages', function () {
    [$conversation, $client] = ulConversation();
    $send = fn (UploadedFile $file) => $this->actingAs($client)->postJson(route('messages.store', $conversation), ['file' => $file]);

    $send(UploadedFile::fake()->create('setup.exe', 10, 'application/x-msdownload'))
        ->assertStatus(422)->assertJsonPath('errors.file.0', 'The attachment must be a JPG, PNG, WEBP, PDF, DOC, DOCX, XLS or XLSX file.');

    // an HTML page renamed to .jpg: the name passes, the detected content does not.
    // (A real temp file: Laravel's fake files report their type from the name, which would hide this check.)
    $path = tempnam(sys_get_temp_dir(), 'spoof');
    file_put_contents($path, '<!DOCTYPE html><html><body><script>alert(1)</script></body></html>');
    $send(new UploadedFile($path, 'photo.jpg', null, null, true))
        ->assertStatus(422)->assertJsonPath('errors.file.0', 'The attachment must be a JPG, PNG, WEBP, PDF, DOC, DOCX, XLS or XLSX file.');

    $send(UploadedFile::fake()->create('scan.pdf', 10241, 'application/pdf'))
        ->assertStatus(422)->assertJsonPath('errors.file.0', 'The attachment must not be larger than 10 MB.');

    $send(UploadedFile::fake()->create('quote.pdf', 300, 'application/pdf'))->assertOk();
    $send(UploadedFile::fake()->create('boq.xlsx', 300, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))->assertOk();
    expect($conversation->messages()->count())->toBe(2);
});

test('multi-file uploads accept at most five files', function () {
    $upload = Uploads::files('attachments', 'attachments', label: 'Attachments');
    $files = fn (int $n) => array_map(fn ($i) => UploadedFile::fake()->create("doc{$i}.pdf", 10, 'application/pdf'), range(1, $n));

    expect(Validator::make(['attachments' => $files(5)], $upload->rules, $upload->messages)->passes())->toBeTrue();

    $six = Validator::make(['attachments' => $files(6)], $upload->rules, $upload->messages);
    expect($six->fails())->toBeTrue()
        ->and($six->errors()->first('attachments'))->toBe('You can upload at most 5 files for Attachments.');
});

test('supplier profile and registration documents are validated before anything is saved', function () {
    $supplier = User::factory()->create(['role' => 'supplier', 'user_type' => 'company']);
    Company::create(['user_id' => $supplier->id, 'email' => $supplier->email, 'username' => $supplier->username,
        'company_name' => 'Acme Supply', 'designation' => 'supplier', 'status' => 'approved']);

    $this->actingAs($supplier)->from('/supplier/profile')
        ->put(route('supplier.profile.update'), ['dti_sec_registration' => UploadedFile::fake()->create('permit.exe', 10)])
        ->assertSessionHasErrors(['dti_sec_registration' => 'The DTI/SEC registration must be a JPG, PNG, WEBP or PDF file.']);
    expect($supplier->company->fresh()->company_name)->toBe('Acme Supply');

    auth()->logout();
    $this->from('/register')->post(route('register.company'), ['type' => 'company', 'designation' => 'supplier',
        'valid_id_owner_rep' => UploadedFile::fake()->create('id.pdf', 10241, 'application/pdf')])
        ->assertSessionHasErrors(['valid_id_owner_rep' => 'The valid ID must not be larger than 10 MB.']);
});

test('file inputs carry the limits and the visible hint for the browser check', function () {
    [$conversation, $client] = ulConversation();

    $this->actingAs($client)->get('/messages?conversation=' . $conversation->id)->assertOk()
        ->assertSee('data-upload-types="jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx"', false)
        ->assertSee('data-upload-max-kb="10240"', false)
        ->assertSeeText('Attachments: JPG, PNG, WEBP, PDF, DOC, DOCX, XLS or XLSX · up to 10 MB');
});
