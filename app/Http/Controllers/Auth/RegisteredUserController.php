<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Support\Uploads;
use App\Support\SafeErrors;

class RegisteredUserController extends Controller
{
    /** Document uploads on the registration form (and the supplier profile), with the label used in error messages. */
    public const DOCUMENT_LABELS = [
        'dti_sec_registration' => 'The DTI/SEC registration',
        'business_permit_mayor_permit' => "The business / mayor's permit",
        'valid_id_owner_rep' => 'The valid ID',
        'accreditations_certifications' => 'The accreditations / certifications',
        'company_profile_portfolio' => 'The company profile / portfolio',
        'sample_price_list' => 'The sample price list',
    ];

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
        // Public sign-up is for client / supplier companies only. Staff accounts are created by an admin
        // (Information Management), never self-registered, so nobody can give themselves a staff role here.
        if ($request->input('type') !== 'company') {
            return back()->withInput()->withErrors(['type' => 'Invalid registration type.']);
        }

        // Validate first, before starting transaction
        try {
            $validated = $this->validateCompany($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $user = $this->createUser($validated, 'company');
            $company = $this->createCompanyRecord($user->id, $validated);
            $this->handleFileUploads($company, $request);

            // Not logged in: the company waits for an admin to approve it.
            DB::commit();

            return redirect()->route('pending.approval');
        } catch (\Exception $e) {
            DB::rollBack();
            SafeErrors::log($e, 'company registration failed');

            return back()->withInput()->withErrors(['error' => 'Registration could not be completed. Please try again, or contact support if it keeps happening.']);
        }
    }

    protected function validateCompany(Request $request)
    {

        $rules = [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'contact_person' => 'required|string|max:100',
            'mobile_number' => ['required', 'regex:/^09\d{9}$/'],
            'telephone_number' => 'nullable|string|max:20',
            'supplier_type' => ['required', Rule::in(Company::TYPES)],
            'designation' => 'required|in:client,supplier',
            'business_reg_no' => 'nullable|string|max:100',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal' => 'required|digits:4',
            'years_operation' => 'nullable|integer|min:0|max:200',
            'primary_products_services' => 'nullable|string|max:500',
            'business_size' => ['nullable', Rule::in(Company::BUSINESS_SIZES)],
            'service_areas' => 'nullable|string|max:255',
            'payment_terms' => ['nullable', Rule::in(Company::PAYMENT_TERMS)],
            'vat_registered' => 'required|in:0,1',
            'use_sureprice' => 'required|in:0,1',
            'bank_name' => ['nullable', Rule::in(Company::BANKS)],
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'accreditations_certifications' => 'nullable',
            'company_profile_portfolio' => 'nullable',
            'sample_price_list' => 'nullable',
            'agree_terms' => 'required|accepted',
            'agree_contact' => 'nullable|boolean',
            'type' => 'required|in:company',
        ];

        // Which document uploads are required for this designation (the file rules themselves come from Uploads below)
        if ($request->designation === 'supplier') {
            $rules['company_name'] = 'required|string|max:100';
            $rules['dti_sec_registration'] = 'required';
            $rules['business_permit_mayor_permit'] = 'required';
            $rules['valid_id_owner_rep'] = 'required';
        } else if ($request->designation === 'client') {
            $rules['client_type'] = 'required|in:individual,company';
            
            if ($request->client_type === 'company') {
                $rules['company_name'] = 'required|string|max:100';
                $rules['dti_sec_registration'] = 'nullable';
            } else {
                $rules['company_name'] = 'nullable|string|max:100';
                $rules['dti_sec_registration'] = 'nullable';
            }
            
            // For clients, all file uploads are optional
            $rules['business_permit_mayor_permit'] = 'nullable';
            $rules['valid_id_owner_rep'] = 'nullable';
        }

        if ($request->supplier_type === 'Other') {
            $rules['other_supplier_type'] = 'required|string|max:100';
        }

        // Every document upload: documents kind (images + PDF), app-wide size limit; required where set above
        $messages = [];
        foreach (self::DOCUMENT_LABELS as $field => $label) {
            $upload = Uploads::file($field, 'documents', required: str_starts_with((string) ($rules[$field] ?? ''), 'required'), label: $label);
            $rules = array_merge($rules, $upload->rules);
            $messages += $upload->messages;
        }

        return $request->validate($rules, $messages);
    }

    protected function createUser(array $data, string $type)
    {
        return User::create([
            // Individual clients have no company name: use the contact person.
            'name' => !empty($data['company_name']) ? $data['company_name'] : $data['contact_person'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $type,
            'role' => $data['designation'], // 'client' or 'supplier'
        ]);
    }

    protected function createCompanyRecord(int $userId, array $data)
    {

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