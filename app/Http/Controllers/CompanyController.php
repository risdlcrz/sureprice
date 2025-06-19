<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function dashboard()
    {
        return view('auth.register'); // make sure this view exists or change accordingly
    }

    public function searchForChat(Request $request)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
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
            return [
                'id' => $company->id,
                'text' => $company->company_name,
                'designation' => ucfirst($company->designation),
                'contact_person' => $company->contact_person,
                'email' => $company->email,
            ];
        });
        return response()->json(['data' => $results]);
    }
}
