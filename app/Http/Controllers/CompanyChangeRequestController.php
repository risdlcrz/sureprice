<?php

namespace App\Http\Controllers;

use App\Models\CompanyChangeRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Clients ask to change their registered company name from Account settings. The change only takes effect once an
 * admin approves it against the uploaded proof (Admin\CompanyChangeRequestController).
 */
class CompanyChangeRequestController extends Controller
{
    public function store(Request $request)
    {
        $company = $this->clientCompany($request);

        if ($company->changeRequests()->pending()->where('field', 'company_name')->exists()) {
            return back()->with('error', 'You already have a company name change waiting for review.');
        }

        $data = $request->validateWithBag('companyName', [
            'new_company_name' => ['required', 'string', 'min:2', 'max:100', Rule::notIn(array_filter([$company->company_name]))],
            'reason' => 'nullable|string|max:500',
            'proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'new_company_name.not_in' => 'That is already your registered company name.',
            'proof.required' => 'Upload a document that shows the new name (e.g. DTI/SEC registration or amended certificate).',
        ], ['new_company_name' => 'new company name']);

        $file = $request->file('proof');
        $changeRequest = $company->changeRequests()->create([
            'requested_by' => $request->user()->id,
            'field' => 'company_name',
            'old_value' => $company->company_name,
            'new_value' => trim($data['new_company_name']),
            'reason' => $data['reason'] ?? null,
            'proof_path' => $file->store("company-change-requests/{$company->id}", CompanyChangeRequest::PROOF_DISK),
            'proof_name' => $file->getClientOriginalName(),
            'proof_mime' => $file->getMimeType(),
            'proof_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        foreach (User::role('admin')->get() as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'company_change_requested',
                'notifiable_type' => CompanyChangeRequest::class,
                'notifiable_id' => $changeRequest->id,
                'for_role' => 'admin',
                'data' => [
                    'title' => 'Company name change to review',
                    'message' => ($company->company_name ?: $company->contact_person) . ' asked to change their company name to "' . $changeRequest->new_value . '".',
                    'link' => route('admin.company-changes.index'),
                ],
            ]);
        }

        return redirect()->route('account.edit')->with('success', 'Your company name change was sent for review. We\'ll notify you once it\'s approved.');
    }

    public function destroy(Request $request, CompanyChangeRequest $changeRequest)
    {
        $company = $this->clientCompany($request);
        abort_unless($changeRequest->company_id === $company->id, 403);

        if (!$changeRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $changeRequest->update(['status' => 'cancelled']);
        $changeRequest->deleteProof();

        return redirect()->route('account.edit')->with('success', 'Your company name change request was withdrawn.');
    }

    /** The proof document, for its owner or an admin. */
    public function proof(Request $request, CompanyChangeRequest $changeRequest)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->company?->id === $changeRequest->company_id, 403);
        abort_unless(Storage::disk(CompanyChangeRequest::PROOF_DISK)->exists($changeRequest->proof_path), 404);

        return Storage::disk(CompanyChangeRequest::PROOF_DISK)->response($changeRequest->proof_path, $changeRequest->proof_name);
    }

    private function clientCompany(Request $request)
    {
        $user = $request->user();
        $company = $user->user_type === 'company' ? $user->company : null;
        abort_unless($company && $company->designation === 'client', 403);

        return $company;
    }
}
