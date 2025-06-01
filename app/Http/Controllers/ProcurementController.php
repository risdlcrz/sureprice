<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierInvitation;
use App\Models\Inquiry;
use App\Models\Quotation;

class ProcurementController extends Controller
{
    public function index()
    {
        $recentInvitations = SupplierInvitation::with(['project', 'materials'])
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

        return view('admin.procurement-dashboard', compact(
            'recentInvitations',
            'recentInquiries',
            'recentQuotations'
        ));
    }
} 