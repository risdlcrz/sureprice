<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    // Controller methods will go here

    public function index()
    {
        $purchaseOrders = \App\Models\PurchaseOrder::paginate(15); // Use pagination for paginator methods
        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function show($id)
    {
        $purchaseOrder = \App\Models\PurchaseOrder::findOrFail($id);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }
}

