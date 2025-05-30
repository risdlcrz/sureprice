<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function dashboard()
    {
        // Get recent contracts
        $recentContracts = Contract::with(['client'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // For now, we'll pass an empty array for activities
        // You can implement activity logging later
        $recentActivities = [];

        return view('admin.project-dashboard', compact('recentContracts', 'recentActivities'));
    }
} 