<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\Activity;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function dashboard()
    {
        $recentContracts = Contract::with('client')
            ->latest()
            ->take(5)
            ->get();

        $recentInquiries = Inquiry::with('project')
            ->latest()
            ->take(5)
            ->get();

        $recentQuotations = Quotation::with('project')
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Activity::latest()
            ->take(5)
            ->get();

        return view('admin.project-dashboard', compact(
            'recentContracts',
            'recentInquiries',
            'recentQuotations',
            'recentActivities'
        ));
    }
} 