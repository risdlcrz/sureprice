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
     * Display the pending approval page.
     */
    public function pendingApproval()
    {
        return view('auth.pending-approval');
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

            // Log successful registration
            Log::info('Registration successful', [
                'type' => $type,
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'designation' => $type === 'company' ? $company->designation : null
            ]);

            // Debug: Log the redirect attempt
            Log::info('About to redirect after successful registration', [
                'type' => $type,
                'route' => 'pending.approval',
                'has_success_message' => true,
                'company_designation' => $type === 'company' ? $company->designation : 'N/A',
                'company_id' => $type === 'company' ? $company->id : 'N/A'
            ]);

            // Redirect to pending approval page for both client and supplier
            if ($type === 'company' && in_array($company->designation, ['client', 'supplier'])) {
                Log::info('Redirecting company registration to pending approval', [
                    'designation' => $company->designation,
                    'company_id' => $company->id,
                    'user_id' => $user->id
                ]);
                return redirect()->route('pending.approval');
            }
            if ($type === 'employee') {
                Log::info('Redirecting employee registration to pending approval');
                return redirect()->route('pending.approval');
            }

            // fallback redirect
            Log::info('Using fallback redirect to pending approval', [
                'type' => $type,
                'company_designation' => $type === 'company' ? $company->designation : 'N/A'
            ]);
            return redirect()->route('pending.approval');

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
            'role' => 'required|in:procurement,warehousing,finance',
            'type' => 'required|in:employee,company'
        ]);
    }

    protected function validateCompany(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Registration request data:', [
            'designation' => $request->designation,
            'client_type' => $request->client_type,
            'supplier_type' => $request->supplier_type,
            'primary_products_services' => $request->primary_products_services ?? 'NOT_SET',
            'primary_products_services_type' => gettype($request->primary_products_services ?? null),
            'primary_products_services_empty' => empty($request->primary_products_services ?? null),
            'all_data' => $request->except(['password', 'password_confirmation'])
        ]);

        $rules = [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'contact_person' => 'required|string|max:100',
            'mobile_number' => 'required|string|max:20',
            'telephone_number' => 'nullable|string|max:20',
            'supplier_type' => $request->designation === 'supplier' ? 'nullable|string' : 'required|in:Construction & Engineering,Architecture & Design,Real Estate & Property Development,Manufacturing,Wholesale & Distribution,Retail & E-Commerce,Information Technology & Software,Telecommunications,Healthcare & Medical,Logistics & Transportation,Energy & Utilities,Financial Services,Legal & Compliance,Education & Training,Marketing & Advertising,Hospitality & Tourism,Government & Public Sector,Nonprofit / NGO,Other',
            'designation' => 'required|in:client,supplier',
            'business_reg_no' => 'nullable|string|max:100',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal' => 'nullable|string|max:10',
            'years_operation' => 'nullable|numeric|min:0',
            'primary_products_services' => 'nullable|string|max:500',
            'business_size' => 'nullable|string|max:100',
            'service_areas' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:100',
            'vat_registered' => 'required|in:0,1',
            'use_sureprice' => 'required|in:0,1',
            'bank_name' => 'nullable|in:BDO,BPI,MetroBank,PNB,Security Bank,Union Bank,RCBC,China Bank',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'accreditations_certifications' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'company_profile_portfolio' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'sample_price_list' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'agree_terms' => 'required|accepted',
            'agree_contact' => 'nullable|accepted',
            'type' => 'required|in:employee,company',
        ];

        // Set file upload rules based on designation
        if ($request->designation === 'supplier') {
            \Log::info('Setting supplier rules');
            $rules['company_name'] = 'required|string|max:100';
            $rules['dti_sec_registration'] = 'required|file|mimes:pdf,jpg,png|max:10240';
            $rules['business_permit_mayor_permit'] = 'required|file|mimes:pdf,jpg,png|max:10240';
            $rules['valid_id_owner_rep'] = 'required|file|mimes:pdf,jpg,png|max:10240';
        } else if ($request->designation === 'client') {
            \Log::info('Setting client rules');
            $rules['client_type'] = 'required|in:individual,company';
            
            if ($request->client_type === 'company') {
                $rules['company_name'] = 'required|string|max:100';
                $rules['dti_sec_registration'] = 'nullable|file|mimes:pdf,jpg,png|max:10240';
            } else {
                $rules['company_name'] = 'nullable|string|max:100';
                $rules['dti_sec_registration'] = 'nullable|file|mimes:pdf,jpg,png|max:10240';
            }
            
            // For clients, all file uploads are optional
            $rules['business_permit_mayor_permit'] = 'nullable|file|mimes:pdf,jpg,png|max:10240';
            $rules['valid_id_owner_rep'] = 'nullable|file|mimes:pdf,jpg,png|max:10240';
        } else {
            \Log::warning('Unknown designation: ' . $request->designation);
        }

        if ($request->supplier_type === 'Other' && $request->designation !== 'supplier') {
            $rules['other_supplier_type'] = 'required|string|max:100';
        }

        // Debug: Log the final rules being applied
        \Log::info('Final validation rules:', [
            'designation' => $request->designation,
            'client_type' => $request->client_type,
            'business_permit_rule' => $rules['business_permit_mayor_permit'] ?? 'not set',
            'valid_id_rule' => $rules['valid_id_owner_rep'] ?? 'not set',
            'dti_rule' => $rules['dti_sec_registration'] ?? 'not set',
        ]);

        return $request->validate($rules);
    }

    protected function createUser(array $data, string $type)
    {
        $name = null;
        
        if ($type === 'employee') {
            $name = $data['firstname'] . ' ' . $data['lastname'];
        } elseif ($type === 'company') {
            // For company registrations, use company_name if available, otherwise use contact_person
            if (!empty($data['company_name'])) {
                $name = $data['company_name'];
            } else {
                // For individual clients, use contact_person as the name
                $name = $data['contact_person'];
            }
        }

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
        // Debug: Log the primary_products_services field specifically
        \Log::info('Creating company record with primary_products_services:', [
            'primary_products_services' => $data['primary_products_services'] ?? 'NOT_SET',
            'primary_products_services_type' => gettype($data['primary_products_services'] ?? null),
            'all_data_keys' => array_keys($data),
            'has_primary_products_services' => isset($data['primary_products_services']),
            'primary_products_services_empty' => empty($data['primary_products_services'] ?? null),
        ]);

        $company = Company::create([
            'user_id' => $userId,
            'company_name' => $data['company_name'] ?? null,
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
            'primary_products_services' => $data['primary_products_services'] ?? null,
            'business_size' => $data['business_size'] ?? null,
            'service_areas' => $data['service_areas'] ?? null,
            'vat_registered' => $data['vat_registered'] ?? 0,
            'use_sureprice' => $data['use_sureprice'] ?? 0,
            'payment_terms' => $data['payment_terms'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'status' => 'pending',
        ]);

        // Debug: Log the created company record
        \Log::info('Company record created:', [
            'company_id' => $company->id,
            'primary_products_services_saved' => $company->primary_products_services,
            'primary_products_services_saved_type' => gettype($company->primary_products_services),
        ]);

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
            // Only process files that are actually uploaded
            if ($request->hasFile($inputName) && $request->file($inputName) && $request->file($inputName)->isValid()) {
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