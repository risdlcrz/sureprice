<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        // You can add logic here to fetch finance-related data
        return view('finance.dashboard');
    }
} 