<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function procurement()
    {
        $materialRequests = MaterialRequest::with(['requestedBy', 'contract'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.procurement', compact('materialRequests'));
    }
} 