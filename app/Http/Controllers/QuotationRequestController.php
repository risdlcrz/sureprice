<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuotationRequest;

class QuotationRequestController extends Controller
{
    /**
     * Display the specified quotation request as JSON.
     */
    public function showJson($id)
    {
        $quotationRequest = QuotationRequest::with(['materials', 'suppliers'])->findOrFail($id);
        
        return response()->json($quotationRequest);
    }
} 