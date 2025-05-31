<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        return view('admin.purchase-requests.index');
    }

    public function create()
    {
        return view('admin.purchase-request');
    }

    public function store(Request $request)
    {
        // TODO: Implement store logic
        return redirect()->route('purchase-request.index')
            ->with('success', 'Purchase request created successfully');
    }
} 