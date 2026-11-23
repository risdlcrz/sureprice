<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyChangeRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin review of client company-detail changes (CompanyChangeRequest): approve applies the change, reject keeps the old value. */
class CompanyChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'history' ? 'history' : 'pending';

        $requests = CompanyChangeRequest::with(['company', 'requester', 'reviewer'])
            ->when($tab === 'pending', fn ($q) => $q->pending()->oldest())
            ->when($tab === 'history', fn ($q) => $q->where('status', '!=', 'pending')->latest('updated_at'))
            ->paginate(15)
            ->withQueryString();

        $pendingCount = CompanyChangeRequest::pending()->count();

        return view('admin.company-changes.index', compact('requests', 'tab', 'pendingCount'));
    }

    public function approve(Request $request, CompanyChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }
        $data = $request->validate(['review_notes' => 'nullable|string|max:500']);

        DB::transaction(function () use ($changeRequest, $data, $request) {
            $company = $changeRequest->company;
            $field = $changeRequest->field;
            $old = $company->{$field};
            $company->update([$field => $changeRequest->new_value]);

            // The account's display name follows the company name (see RegisteredUserController::createUser)
            $user = $company->user;
            if ($field === 'company_name' && $user && (!$old || $user->name === $old)) {
                $user->update(['name' => $changeRequest->new_value]);
            }

            $changeRequest->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_notes' => $data['review_notes'] ?? null,
            ]);
        });

        $this->notifyRequester($changeRequest, 'approved');

        return back()->with('success', "Approved: the company is now \"{$changeRequest->new_value}\".");
    }

    public function reject(Request $request, CompanyChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }
        $data = $request->validate(['review_notes' => 'required|string|max:500'], [
            'review_notes.required' => 'Tell the client why, so they can send the right proof.',
        ]);

        $changeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'],
        ]);

        $this->notifyRequester($changeRequest, 'rejected');

        return back()->with('success', 'The request was rejected and the client has been notified.');
    }

    private function notifyRequester(CompanyChangeRequest $changeRequest, string $outcome): void
    {
        $userId = $changeRequest->company->user_id ?? $changeRequest->requested_by;
        $approved = $outcome === 'approved';

        Notification::create([
            'user_id' => $userId,
            'type' => 'company_change_' . $outcome,
            'notifiable_type' => \App\Models\CompanyChangeRequest::class,
            'notifiable_id' => $changeRequest->id,
            'data' => [
                'title' => $approved ? 'Company name change approved' : 'Company name change not approved',
                'message' => $approved
                    ? "Your company name is now \"{$changeRequest->new_value}\"."
                    : 'Your request to change your company name to "' . $changeRequest->new_value . '" was not approved: ' . $changeRequest->review_notes,
                'link' => route('account.edit'),
            ],
        ]);
    }
}
