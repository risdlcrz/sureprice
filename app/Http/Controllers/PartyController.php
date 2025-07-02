<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function ban($id, Request $request)
    {
        $party = Party::findOrFail($id);
        $party->banned = true;
        $party->ban_reason = $request->input('ban_reason');
        $party->save();
        return back()->with('success', 'Client has been banned.');
    }

    public function unban($id)
    {
        $party = Party::findOrFail($id);
        $party->banned = false;
        $party->ban_reason = null;
        $party->save();
        return back()->with('success', 'Client has been unbanned.');
    }

    public function banStatus($id)
    {
        $party = Party::findOrFail($id);
        return response()->json([
            'banned' => (bool) $party->banned,
            'reason' => $party->ban_reason
        ]);
    }
} 