<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserAccountNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InformationManagementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:procurement,warehousing,contractor',
            'username' => 'required|string|max:255|unique:users',
        ]);

        try {
            Log::info('Starting employee creation process', [
                'email' => $request->email,
                'role' => $request->role,
                'queue_connection' => config('queue.default')
            ]);

            // Generate a random temporary password
            $temporaryPassword = Str::random(12);

            DB::beginTransaction();

            // Create the user with combined name
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($temporaryPassword),
                'role' => $request->role,
                'user_type' => 'employee',
                'force_password_change' => true,
                'email_verified_at' => null, // Ensure email needs verification
            ]);

            // Create the employee record
            $employee = $user->employee()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            Log::info('User and employee records created successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Send notification email with temporary password
            try {
                Log::info('Starting email notification process', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // First send our custom notification with the temporary password
                try {
                    $user->notify(new NewUserAccountNotification($temporaryPassword, $request->role));
                    Log::info('Custom notification sent successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to send custom notification', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // Then trigger the Registered event which will send verification email
                try {
                    event(new \Illuminate\Auth\Events\Registered($user));
                    Log::info('Verification email event triggered successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to trigger verification email', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                Log::info('Email notification process completed');

                DB::commit();

                return redirect()->route('information-management.index')
                    ->with('success', 'User account created successfully. An email has been sent to the user with login instructions.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed in email notification process', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->route('information-management.index')
                    ->with('warning', 'User account creation completed but email notification failed. Error: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('information-management.index')
                ->with('error', 'Failed to create user account. Error: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'employee');
        $role = $request->get('role', 'all');
        $search = $request->get('search');

        $query = User::query();

        if ($type === 'employee') {
            $query->where('user_type', 'employee');
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        } else {
            $query->where('user_type', 'company');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(10);

        return view('admin.information-management', compact('items', 'type', 'role', 'search'));
    }

    // Add other controller methods as needed...
} 