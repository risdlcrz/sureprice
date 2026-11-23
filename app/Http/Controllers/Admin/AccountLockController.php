<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/** Admin lock / unlock of user accounts (routes are role:admin). */
class AccountLockController extends Controller
{
    public function lock(Request $request, User $user)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);

        if ($user->is($request->user())) {
            return $this->respond($request, 'You cannot lock your own account.', 422);
        }
        if ($user->isLocked()) {
            return $this->respond($request, "{$user->name}'s account is already locked.", 409);
        }

        $user->lockBy($request->user(), $data['reason']);

        return $this->respond($request, "{$user->name}'s account has been locked.");
    }

    public function unlock(Request $request, User $user)
    {
        if (!$user->isLocked()) {
            return $this->respond($request, "{$user->name}'s account is not locked.", 409);
        }

        $user->unlock();

        return $this->respond($request, "{$user->name}'s account has been unlocked.");
    }

    private function respond(Request $request, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->with($status === 200 ? 'success' : 'error', $message);
    }
}
