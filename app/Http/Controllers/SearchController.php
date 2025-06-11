<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Property;
use App\Models\ScopeType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }

    public function contractors(Request $request)
    {
        $query = \App\Models\Employee::where('role', 'contractor');

        $search = $request->input('q', $request->input('search', ''));
        if ($search) {
            $query->where(function($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $results = $query->paginate(10);
        $results->getCollection()->transform(function ($item) {
            $item->name = trim($item->first_name . ' ' . $item->last_name);
            return $item;
        });
        return $results;
    }

    public function clients(Request $request)
    {
        $query = \App\Models\Company::where('designation', 'client')
                                    ->where('status', 'approved');

        $search = $request->input('q', $request->input('search', ''));
        if ($search) {
            $query->where(function($q2) use ($search) {
                $q2->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('postal', 'like', "%{$search}%");
            });
        }

        $results = $query->paginate(10);
        $results->getCollection()->transform(function ($item) {
            $nameForDisplay = $item->contact_person ?: $item->company_name;

            return [
                'id' => $item->id,
                'text' => $nameForDisplay,
                'data' => [
                    'name' => $item->contact_person,
                    'company_name' => $item->company_name,
                    'email' => $item->email,
                    'phone' => $item->mobile_number,
                    'street' => $item->street,
                    'unit' => $item->unit,
                    'barangay' => $item->barangay,
                    'city' => $item->city,
                    'postal' => $item->postal,
                    'state' => $item->state,
                ]
            ];
        });
        return response()->json($results);
    }

    public function properties(Request $request)
    {
        $query = Property::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }

    public function materials(Request $request)
    {
        $query = Material::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }

    public function suppliers(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }

    public function scopeTypes(Request $request)
    {
        $query = ScopeType::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }

    public function contracts(Request $request)
    {
        $query = Contract::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }
} 