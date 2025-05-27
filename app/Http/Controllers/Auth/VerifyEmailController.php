<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->getRedirectBasedOnUserType($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->getRedirectBasedOnUserType($user);
    }

    /**
     * Get the redirect route based on user type.
     */
    private function getRedirectBasedOnUserType($user): RedirectResponse
    {
        
        // For company users, check approval status and designation
        if ($user->user_type === 'company') {
            $company = $user->company;
            if (!$company || $company->status !== 'approved') {
                return redirect()->route('pending.approval');
            }

            // If approved, redirect based on designation
            if ($company->designation === 'client') {
                return redirect()->route('client.dashboard');
            } elseif ($company->designation === 'supplier') {
                return redirect()->route('supplier.dashboard');
            }
        }

        // For all other cases, redirect to pending approval
        return redirect()->route('pending.approval');
    }
}
