<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{

    public function searchForChat(Request $request)
    {
        if (!auth()->check() || !(auth()->user()->user_type === 'admin' || auth()->user()->role === 'manager')) {
            return response()->json(['data' => []], 403);
        }
        $search = $request->input('search', '');
        $query = Company::query()->where('status', 'approved')
            ->whereIn('designation', ['client', 'supplier']);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }
        $results = $query->limit(15)->get()->map(function($company) {
            // Conversations with a supplier are started by suppliers.id (MessageController::startConversation), clients by user id
            $id = $company->designation === 'supplier'
                ? \App\Models\Supplier::where('company_id', $company->id)->orWhere('user_id', $company->user_id)->value('id')
                : $company->user_id;

            return [
                'id' => $id,
                'text' => $company->company_name,
                'designation' => ucfirst($company->designation),
                'contact_person' => $company->contact_person,
                'email' => $company->email,
            ];
        })->filter(fn ($r) => $r['id'])->values();
        return response()->json(['data' => $results]);
    }
}
