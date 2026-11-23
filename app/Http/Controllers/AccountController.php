<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Support\Uploads;

/**
 * "Account settings" for every signed-in user: name, username and email (kept in sync with the employee /
 * company record), plus contact details and address for clients. Passwords go through Auth\PasswordController
 * (route password.update). Supplier company details stay on the supplier profile page.
 */
class AccountController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->loadMissing(['company', 'employee']);

        $company = $user->user_type === 'company' ? $user->company : null;

        return view('account.edit', [
            'user' => $user,
            'company' => $company,
            'employee' => $user->employee,
            // latest company-name change request (pending, or the last decision) for clients
            'nameChange' => $company?->designation === 'client'
                ? $company->changeRequests()->where('field', 'company_name')->where('status', '!=', 'cancelled')->with('reviewer')->first()
                : null,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user()->loadMissing(['company', 'employee']);
        $company = $user->user_type === 'company' ? $user->company : null;
        $employee = $user->employee;
        $isClient = $company?->designation === 'client';

        $rules = [
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ];
        if ($employee) {
            $rules += ['first_name' => 'required|string|max:50', 'last_name' => 'required|string|max:50'];
        } elseif (!$company) {
            $rules['name'] = 'required|string|max:100';
        }
        if ($isClient) {
            $rules += [
                'contact_person' => 'required|string|max:100',
                'mobile_number' => ['required', 'regex:/^09\d{9}$/'],
                'telephone_number' => 'nullable|string|max:20',
                'street' => 'required|string|max:255',
                'barangay' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal' => 'required|digits:4',
            ];
        }

        $data = $request->validate($rules, [
            'username.regex' => 'Use only letters, numbers, dots, dashes and underscores.',
            'mobile_number.regex' => 'Enter an 11-digit mobile number starting with 09.',
        ]);

        DB::transaction(function () use ($user, $company, $employee, $isClient, $data) {
            $account = ['username' => $data['username'], 'email' => $data['email']];

            if ($employee) {
                $user->name = trim($data['first_name'] . ' ' . $data['last_name']);
                $employee->update($account + ['first_name' => $data['first_name'], 'last_name' => $data['last_name']]);
            } elseif (isset($data['name'])) {
                $user->name = $data['name'];
            }

            if ($company) {
                $companyData = $account;
                if ($isClient) {
                    $companyData += collect($data)->only(['contact_person', 'mobile_number', 'telephone_number', 'street', 'barangay', 'city', 'state', 'postal'])->all();
                    // Individual clients are shown by their contact name (see RegisteredUserController::createUser)
                    if (!$company->company_name) {
                        $user->name = $data['contact_person'];
                    }
                }
                $company->update($companyData);
            }

            $user->fill($account)->save();
        });

        return redirect()->route('account.edit')->with('success', 'Your account settings were saved.');
    }

    /** Upload or replace the profile photo (the browser crops it to a square first; see resources/js/pages/account.js). */
    public function updatePhoto(Request $request)
    {
        $photo = Uploads::file('photo', 'images', required: true, maxKb: 4096, label: 'The photo');
        $request->validateWithBag('photo', [
            'photo' => [...$photo->rules['photo'], 'dimensions:min_width=64,min_height=64,max_width=4000,max_height=4000'],
        ], $photo->messages + [
            'photo.dimensions' => 'The photo must be at least 64 × 64 pixels.',
        ]);

        $user = $request->user();
        $old = $user->avatar_path;
        $user->update(['avatar_path' => $request->file('photo')->store("avatars/{$user->id}", User::AVATAR_DISK)]);
        if ($old) {
            Storage::disk(User::AVATAR_DISK)->delete($old);
        }

        return redirect()->route('account.edit')->with('success', 'Your profile photo was updated.');
    }

    public function destroyPhoto(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) {
            Storage::disk(User::AVATAR_DISK)->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()->route('account.edit')->with('success', 'Your profile photo was removed.');
    }
}
