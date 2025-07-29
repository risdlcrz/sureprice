<?php

namespace App\Http\Controllers;

use App\Models\ClientFeedback;
use App\Models\Contract;
use App\Models\User;
use App\Notifications\ClientFeedbackSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientFeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return view('client.feedback.index', [
                'feedbacks' => collect([]),
                'error' => 'No company associated with this account. Please contact the administrator.'
            ]);
        }

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        // If no client party found, try to find by email with user_id set (prioritize linked parties)
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('user_id', $user->id)
                ->first();
        }

        // If still no client party found, try to find by email with entity_type client
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find by company
        if (!$clientParty && $company) {
            $clientParty = \App\Models\Party::where('company_name', $company->company_name)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find any party with the same email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)->first();
        }

        if (!$clientParty) {
            return view('client.feedback.index', [
                'feedbacks' => collect([]),
                'error' => 'No client profile found. Please contact the administrator.'
            ]);
        }

        // Get all feedback for this client
        $feedbacks = ClientFeedback::where('client_id', $clientParty->id)
            ->with(['contract', 'contract.contractor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.feedback.index', compact('feedbacks'));
    }

    public function create(Contract $contract)
    {
        $user = Auth::user();
        
        // Check if user can provide feedback for this contract
        if (!$this->canProvideFeedback($user, $contract)) {
            abort(403, 'You are not authorized to provide feedback for this contract.');
        }

        // Check if feedback already exists
        $existingFeedback = ClientFeedback::where('contract_id', $contract->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingFeedback) {
            return redirect()->route('client.feedback.edit', $existingFeedback)
                ->with('info', 'You have already started providing feedback for this contract.');
        }

        return view('client.feedback.create', compact('contract'));
    }

    public function store(Request $request, Contract $contract)
    {
        $user = Auth::user();
        
        // Check if user can provide feedback for this contract
        if (!$this->canProvideFeedback($user, $contract)) {
            abort(403, 'You are not authorized to provide feedback for this contract.');
        }

        // Validate request
        $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'required|integer|min:1|max:5',
            'timeliness_rating' => 'required|integer|min:1|max:5',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'recommendation_likelihood' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        // Find client party
        $clientParty = $this->findClientParty($user);
        if (!$clientParty) {
            abort(403, 'No client profile found.');
        }

        // Create feedback
        $feedback = ClientFeedback::create([
            'contract_id' => $contract->id,
            'client_id' => $clientParty->id,
            'user_id' => $user->id,
            'overall_rating' => $request->overall_rating,
            'communication_rating' => $request->communication_rating,
            'quality_rating' => $request->quality_rating,
            'timeliness_rating' => $request->timeliness_rating,
            'professionalism_rating' => $request->professionalism_rating,
            'value_rating' => $request->value_rating,
            'recommendation_likelihood' => $request->recommendation_likelihood,
            'comments' => $request->comments,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        Log::info("Client feedback submitted", [
            'feedback_id' => $feedback->id,
            'contract_id' => $contract->id,
            'user_id' => $user->id,
            'overall_rating' => $feedback->overall_rating
        ]);

        // Send notifications to admins and managers
        $this->sendFeedbackNotifications($feedback);

        return redirect()->route('client.feedback.index')
            ->with('success', 'Thank you for your feedback! Your response has been submitted successfully.');
    }

    public function edit(ClientFeedback $feedback)
    {
        $user = Auth::user();
        
        // Check if user owns this feedback
        if ($feedback->user_id !== $user->id) {
            abort(403, 'You are not authorized to edit this feedback.');
        }

        // Check if feedback can still be edited
        if ($feedback->isSubmitted()) {
            return redirect()->route('client.feedback.show', $feedback)
                ->with('info', 'This feedback has already been submitted and cannot be edited.');
        }

        return view('client.feedback.edit', compact('feedback'));
    }

    public function update(Request $request, ClientFeedback $feedback)
    {
        $user = Auth::user();
        
        // Check if user owns this feedback
        if ($feedback->user_id !== $user->id) {
            abort(403, 'You are not authorized to edit this feedback.');
        }

        // Check if feedback can still be edited
        if ($feedback->isSubmitted()) {
            abort(403, 'This feedback has already been submitted and cannot be edited.');
        }

        // Validate request
        $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'required|integer|min:1|max:5',
            'timeliness_rating' => 'required|integer|min:1|max:5',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'recommendation_likelihood' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        // Update feedback
        $feedback->update([
            'overall_rating' => $request->overall_rating,
            'communication_rating' => $request->communication_rating,
            'quality_rating' => $request->quality_rating,
            'timeliness_rating' => $request->timeliness_rating,
            'professionalism_rating' => $request->professionalism_rating,
            'value_rating' => $request->value_rating,
            'recommendation_likelihood' => $request->recommendation_likelihood,
            'comments' => $request->comments,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        return redirect()->route('client.feedback.index')
            ->with('success', 'Feedback updated successfully.');
    }

    public function show(ClientFeedback $feedback)
    {
        $user = Auth::user();
        
        // Check if user owns this feedback
        if ($feedback->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this feedback.');
        }

        return view('client.feedback.show', compact('feedback'));
    }

    public function submit(ClientFeedback $feedback)
    {
        $user = Auth::user();
        
        // Check if user owns this feedback
        if ($feedback->user_id !== $user->id) {
            abort(403, 'You are not authorized to submit this feedback.');
        }

        // Check if feedback can be submitted
        if (!$feedback->canBeSubmitted()) {
            abort(403, 'This feedback cannot be submitted at this time.');
        }

        // Submit feedback
        $feedback->submit();

        Log::info("Client feedback submitted", [
            'feedback_id' => $feedback->id,
            'contract_id' => $feedback->contract_id,
            'user_id' => $user->id
        ]);

        return redirect()->route('client.feedback.index')
            ->with('success', 'Thank you for your feedback! Your response has been submitted successfully.');
    }

    private function canProvideFeedback($user, $contract)
    {
        // Find client party
        $clientParty = $this->findClientParty($user);
        if (!$clientParty) {
            return false;
        }

        // Check if contract belongs to this client
        if ($contract->client_id !== $clientParty->id) {
            return false;
        }

        // Check if contract is completed and payments are complete
        if (!$contract->isPaymentComplete()) {
            return false;
        }

        // Check if feedback already exists
        $existingFeedback = ClientFeedback::where('contract_id', $contract->id)
            ->where('user_id', $user->id)
            ->first();

        return !$existingFeedback || !$existingFeedback->isSubmitted();
    }

    private function findClientParty($user)
    {
        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        // If no client party found, try to find by email with user_id set (prioritize linked parties)
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('user_id', $user->id)
                ->first();
        }

        // If still no client party found, try to find by email with entity_type client
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find by company
        if (!$clientParty && $user->company) {
            $clientParty = \App\Models\Party::where('company_name', $user->company->company_name)
                ->where('entity_type', 'client')
                ->first();
        }

        // If still no client party found, try to find any party with the same email
        if (!$clientParty) {
            $clientParty = \App\Models\Party::where('email', $user->email)->first();
        }

        return $clientParty;
    }

    /**
     * Send notifications to admins and managers about new feedback
     */
    private function sendFeedbackNotifications(ClientFeedback $feedback)
    {
        try {
            // Get all admin and manager users
            $adminUsers = User::whereIn('role', ['admin', 'manager'])->get();
            
            foreach ($adminUsers as $adminUser) {
                $adminUser->notify(new ClientFeedbackSubmittedNotification($feedback));
            }

            Log::info("Feedback notifications sent", [
                'feedback_id' => $feedback->id,
                'recipients_count' => $adminUsers->count()
            ]);
        } catch (\Exception $e) {
            Log::error("Error sending feedback notifications", [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 