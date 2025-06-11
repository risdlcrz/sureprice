<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Return minimal user info for autofill in contract form.
     */
    public function showMinimal($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'name' => $user->name,
            'company_name' => $user->company_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'street' => $user->street,
            'barangay' => $user->barangay,
            'city' => $user->city,
            'postal' => $user->postal,
        ]);
    }
} 