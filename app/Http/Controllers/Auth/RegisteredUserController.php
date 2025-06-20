<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');  // Use your signup view here
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $type = $request->input('type'); // expect 'employee' or 'company'

        // Validate first, before starting transaction
        try {
            if ($type === 'employee') {
                $validated = $this->validateEmployee($request);
            } elseif ($type === 'company') {
                $validated = $this->validateCompany($request);
            } else {
                return back()->withInput()->withErrors(['type' => 'Invalid registration type.']);
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        DB::beginTransaction();

        try {
            if ($type === 'employee') {
                $user = $this->createUser($validated, 'employee');
                $employee = $this->createEmployeeRecord($user->id, $validated);
            } elseif ($type === 'company') {
                $user = $this->createUser($validated, 'company');
                $company = $this->createCompanyRecord($user->id, $validated);
                $this->handleFileUploads($company, $request);
            } else {
                return back()->withInput()->withErrors(['type' => 'Invalid registration type.']);
            }

            // Do not log in the user after registration
            // Auth::login($user);

            DB::commit();

            // Redirect to login with banner for both client and supplier
            if ($type === 'company' && in_array($company->designation, ['client', 'supplier'])) {
                return redirect()->route('login.form')->with('success', 'Sign up successful, wait for approval');
            }
            if ($type === 'employee') {
                return redirect()->route('login.form')->with('success', 'Sign up successful, wait for approval');
            }

            // fallback redirect
            return redirect()->route('login.form')->with('success', 'Sign up successful, wait for approval');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($type . ' registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('password')
            ]);

            // Show the actual error to the user for debugging
            return back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    protected function validateEmployee(Request $request)
    {
        return $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:procurement,warehousing',
            'type' => 'required|in:employee,company'
        ]);
    }

    protected function validateCompany(Request $request)
    {
        $rules = [
            'company_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'contact_person' => 'required|string|max:100',
            'mobile_number' => 'required|string|max:20',
            'telephone_number' => 'nullable|string|max:20',
            'supplier_type' => 'required|in:Individual,Contractor,Material Supplier,Equipment Rental,Other',
            'designation' => 'required|in:client,supplier',
            'business_reg_no' => 'nullable|string|max:100',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal' => 'nullable|string|max:10',
            'years_operation' => 'nullable|numeric|min:0',
            'business_size' => 'nullable|string|max:100',
            'service_areas' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:100',
            'vat_registered' => 'required|in:0,1',
            'use_sureprice' => 'required|in:0,1',

            // Bank details
            'bank_name' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',

            // Multiple file uploads validated here
            'dti_sec_registration' => 'required|file|mimes:pdf,jpg,png|max:10240',
            'business_permit_mayor_permit' => 'required|file|mimes:pdf,jpg,png|max:10240',
            'valid_id_owner_rep' => 'required|file|mimes:pdf,jpg,png|max:10240',
            'accreditations_certifications' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'company_profile_portfolio' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'sample_price_list' => 'nullable|file|mimes:pdf,jpg,png|max:10240',

            'agree_terms' => 'required|accepted',
            'agree_contact' => 'nullable|accepted',
            'type' => 'required|in:employee,company',
            
        ];

        if ($request->supplier_type === 'Other') {
            $rules['other_supplier_type'] = 'required|string|max:100';
        }

        return $request->validate($rules);
    }

    protected function createUser(array $data, string $type)
    {
        $name = ($type === 'employee') 
            ? $data['firstname'] . ' ' . $data['lastname']
            : $data['company_name'];

        $role = null;
        if ($type === 'company') {
            $role = $data['designation']; // 'client' or 'supplier'
        } elseif ($type === 'employee') {
            $role = $data['role'];
        }

        return User::create([
            'name' => $name,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $type,
            'role' => $role,
        ]);
    }

    protected function createEmployeeRecord(int $userId, array $data)
    {
        return Employee::create([
            'user_id' => $userId,
            'username' => $data['username'],
            'first_name' => $data['firstname'],
            'last_name' => $data['lastname'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);
    }

    protected function createCompanyRecord(int $userId, array $data)
    {
        $company = Company::create([
            'user_id' => $userId,
            'company_name' => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'username' => $data['username'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'supplier_type' => $data['supplier_type'],
            'other_supplier_type' => $data['other_supplier_type'] ?? null,
            'designation' => $data['designation'],
            'business_reg_no' => $data['business_reg_no'] ?? null,
            'telephone_number' => $data['telephone_number'] ?? null,
            'street' => $data['street'] ?? null,
            'barangay' => $data['barangay'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal' => $data['postal'] ?? null,
            'years_operation' => $data['years_operation'] ?? null,
            'business_size' => $data['business_size'] ?? null,
            'service_areas' => $data['service_areas'] ?? null,
            'vat_registered' => $data['vat_registered'] ?? 0,
            'use_sureprice' => $data['use_sureprice'] ?? 0,
            'payment_terms' => $data['payment_terms'] ?? null,
            'status' => 'pending',
        ]);

        // Defensive: Only create bank details if all are present and not null
        if (!empty($data['bank_name']) && !empty($data['bank_account_name']) && !empty($data['bank_account_number']) && method_exists($company, 'bankDetails')) {
            $company->bankDetails()->create([
                'bank_name' => $data['bank_name'],
                'account_name' => $data['bank_account_name'],
                'account_number' => $data['bank_account_number']
            ]);
        }

        return $company;
    }

    protected function handleFileUploads(Company $company, Request $request)
    {
        $documentFields = [
            'dti_sec_registration' => 'DTI_SEC_REGISTRATION',
            'accreditations_certifications' => 'ACCREDITATIONS_CERTIFICATIONS',
            'business_permit_mayor_permit' => 'BUSINESS_PERMIT_MAYOR_PERMIT',
            'valid_id_owner_rep' => 'VALID_ID_OWNER_REP',
            'company_profile_portfolio' => 'COMPANY_PROFILE_PORTFOLIO',
            'sample_price_list' => 'SAMPLE_PRICE_LIST',
        ];

        foreach ($documentFields as $inputName => $docType) {
            if ($request->hasFile($inputName) && $request->file($inputName)) {
                $file = $request->file($inputName);
                // Store file in public disk under company_docs directory
                $path = $file->store("company_docs/{$company->id}", 'public');

                if (!$path) {
                    throw new \Exception("Failed to store file for $docType.");
                }

                \App\Models\CompanyDocument::create([
                    'company_id' => $company->id,
                    'type' => $docType,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'disk' => 'public'
                ]);
            }
        }
    }
}