@php
    use App\Models\Company;

    $steps = [
        'type' => ['Account type', 'bi-person-badge'],
        'login' => ['Login & contact', 'bi-person-lock'],
        'business' => ['Business', 'bi-briefcase'],
        'address' => ['Address', 'bi-geo-alt'],
        'documents' => ['Documents', 'bi-file-earmark-arrow-up'],
        'review' => ['Review', 'bi-check2-square'],
    ];
    $role = old('designation');
    $clientType = old('client_type');
    $file = fn ($name) => 'form-control' . ($errors->has($name) ? ' is-invalid' : '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account - GDC Admin Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/pages/auth-register.css')
</head>
<body class="register-page">

<header class="register-header">
    <a href="{{ route('login.form') }}" class="register-header__brand">
        <img src="{{ asset('images/gdc_logo.png') }}" alt="GDC logo">
        <span>GDC Admin Center</span>
    </a>
    <span class="register-header__login d-none d-sm-inline">Already registered? <a href="{{ route('login.form') }}">Log in</a></span>
</header>

<main class="register-main">
    <div class="register-intro">
        <h1>Create your account</h1>
        <p>Register as a client or supplier. An administrator reviews every new account &mdash; we&rsquo;ll email you once yours is approved.</p>
    </div>

    <ol class="register-steps" id="registerSteps" aria-label="Registration steps">
        @foreach($steps as $key => [$label, $icon])
            <li class="register-steps__item" data-step-link="{{ $key }}">
                <span class="register-steps__dot"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
                <span class="register-steps__label">{{ $label }}</span>
            </li>
        @endforeach
    </ol>

    <div class="register-card">
        @if (session('success'))
            <div class="alert alert-success d-flex gap-2" role="alert"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please fix the following:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.company') }}" enctype="multipart/form-data" id="registrationForm" novalidate>
            @csrf
            <input type="hidden" name="type" value="company">

            {{-- 1. Account type --}}
            <section class="register-step" data-step="type">
                <h2 class="register-step__title">How will you use SurePrice?</h2>
                <div class="choice-grid @error('designation') is-invalid @enderror" role="radiogroup" aria-label="Account type">
                    <label class="choice-card">
                        <input type="radio" name="designation" value="client" required @checked($role === 'client')>
                        <span class="choice-card__body">
                            <i class="bi bi-house-door choice-card__icon" aria-hidden="true"></i>
                            <span class="choice-card__title">Client</span>
                            <span class="choice-card__text">I want to request quotations and manage construction projects.</span>
                        </span>
                    </label>
                    <label class="choice-card">
                        <input type="radio" name="designation" value="supplier" required @checked($role === 'supplier')>
                        <span class="choice-card__body">
                            <i class="bi bi-truck choice-card__icon" aria-hidden="true"></i>
                            <span class="choice-card__title">Supplier</span>
                            <span class="choice-card__text">I supply materials or services and want to receive purchase requests.</span>
                        </span>
                    </label>
                </div>
                @error('designation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                <div class="mt-4" data-when="client">
                    <h3 class="register-step__subtitle">Are you registering as an individual or a company?</h3>
                    <div class="choice-grid choice-grid--compact" role="radiogroup" aria-label="Client type">
                        <label class="choice-card">
                            <input type="radio" name="client_type" value="individual" data-required-when="client" @checked($clientType === 'individual')>
                            <span class="choice-card__body">
                                <i class="bi bi-person choice-card__icon" aria-hidden="true"></i>
                                <span class="choice-card__title">Individual</span>
                            </span>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="client_type" value="company" data-required-when="client" @checked($clientType === 'company')>
                            <span class="choice-card__body">
                                <i class="bi bi-building choice-card__icon" aria-hidden="true"></i>
                                <span class="choice-card__title">Company</span>
                            </span>
                        </label>
                    </div>
                    @error('client_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="register-note mt-4" data-when="supplier">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    Suppliers need to upload their DTI/SEC registration, business permit and a valid ID for approval.
                </div>
                <div class="register-note mt-4" data-when="client">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    Document uploads are optional for clients &mdash; you can provide them later if needed.
                </div>
            </section>

            {{-- 2. Login & contact --}}
            <section class="register-step" data-step="login">
                <h2 class="register-step__title">Login details</h2>
                <div class="row">
                    <x-field name="username" label="Username" col="md-6" required maxlength="50" autocomplete="username" placeholder="Choose a username" />
                    <x-field name="email" type="email" label="Email address" col="md-6" required autocomplete="email" placeholder="you@company.com" />
                    <x-field name="password" label="Password" col="md-6" required>
                        <div class="input-group has-validation">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                required minlength="8" autocomplete="new-password" placeholder="At least 8 characters" aria-describedby="passwordHelp">
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="password-meter" aria-hidden="true"><span id="passwordMeter"></span></div>
                        <div class="form-text" id="passwordHelp">Use 8 or more characters; mixing letters, numbers and symbols makes it stronger.</div>
                    </x-field>
                    <x-field name="password_confirmation" label="Confirm password" col="md-6" required>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                required autocomplete="new-password" placeholder="Re-enter your password">
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password_confirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </x-field>
                </div>

                <h2 class="register-step__title mt-2">Contact person</h2>
                <div class="row">
                    <x-field name="contact_person" label="Full name" col="md-12" required maxlength="100" autocomplete="name" placeholder="e.g. Juan Dela Cruz" />
                    <x-field name="mobile_number" type="tel" label="Mobile number" col="md-6" required inputmode="numeric" maxlength="11"
                        pattern="09\d{9}" title="11 digits starting with 09, e.g. 09171234567" autocomplete="tel" placeholder="09171234567" />
                    <x-field name="telephone_number" type="tel" label="Telephone number" col="md-6" inputmode="numeric" maxlength="20" placeholder="e.g. 02 8123 4567" />
                </div>
            </section>

            {{-- 3. Business --}}
            <section class="register-step" data-step="business">
                <h2 class="register-step__title">About your business</h2>
                <div class="row">
                    <div class="col-md-12 mb-3 reg-field" data-when="supplier client-company" data-required-field>
                        <label for="company_name" class="form-label">Company name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" maxlength="100"
                            class="form-control @error('company_name') is-invalid @enderror" data-required-when="supplier client-company"
                            autocomplete="organization" placeholder="Registered business name">
                        @error('company_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <x-field name="supplier_type" label="Type of company" col="md-6" required>
                        <select id="supplier_type" name="supplier_type" class="form-select @error('supplier_type') is-invalid @enderror" required>
                            <option value="">Select company type</option>
                            @foreach(Company::TYPES as $t)
                                <option value="{{ $t }}" @selected(old('supplier_type') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <div class="col-md-6 mb-3 reg-field is-required" id="other_supplier_type_group">
                        <label for="other_supplier_type" class="form-label">Specify type</label>
                        <input type="text" id="other_supplier_type" name="other_supplier_type" value="{{ old('other_supplier_type') }}" maxlength="100"
                            class="form-control @error('other_supplier_type') is-invalid @enderror" placeholder="Describe your business">
                        @error('other_supplier_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <x-field name="years_operation" type="number" label="Years in operation" col="md-6" min="0" max="200" step="1" placeholder="e.g. 5" />
                    <x-field name="business_size" label="Business size" col="md-6">
                        <select id="business_size" name="business_size" class="form-select @error('business_size') is-invalid @enderror">
                            <option value="">Select business size</option>
                            @foreach(Company::BUSINESS_SIZES as $size)
                                <option value="{{ $size }}" @selected(old('business_size') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field name="primary_products_services" type="textarea" label="Primary products / services" col="md-12" maxlength="500"
                        placeholder="e.g. Cement, gravel, scaffolding rental, skilled labor" />
                    <x-field name="service_areas" type="textarea" label="Areas of operation" col="md-12" maxlength="255" rows="2"
                        placeholder="e.g. Metro Manila, Cavite, Laguna" />
                </div>

                <h2 class="register-step__title mt-2">Pricing &amp; terms</h2>
                <div class="row">
                    <x-field name="payment_terms" label="Preferred payment terms" col="md-4">
                        <select id="payment_terms" name="payment_terms" class="form-select @error('payment_terms') is-invalid @enderror">
                            <option value="">Select terms</option>
                            @foreach(Company::PAYMENT_TERMS as $term)
                                <option value="{{ $term }}" @selected(old('payment_terms') === $term)>{{ $term }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    @foreach(['vat_registered' => 'VAT registered?', 'use_sureprice' => 'Use SurePrice?'] as $name => $label)
                        <div class="col-md-4 mb-3">
                            <span class="form-label d-block">{{ $label }}<span class="text-danger"> *</span></span>
                            <div class="btn-group w-100 segmented" role="radiogroup" aria-label="{{ $label }}">
                                <input type="radio" class="btn-check" name="{{ $name }}" id="{{ $name }}_1" value="1" required @checked(old($name) === '1')>
                                <label class="btn btn-outline-success" for="{{ $name }}_1">Yes</label>
                                <input type="radio" class="btn-check" name="{{ $name }}" id="{{ $name }}_0" value="0" required @checked(old($name) === '0')>
                                <label class="btn btn-outline-success" for="{{ $name }}_0">No</label>
                            </div>
                            @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 4. Address --}}
            <section class="register-step" data-step="address">
                <h2 class="register-step__title">Business address</h2>
                <p class="register-step__hint">Pick your region first &mdash; the province, city and barangay lists narrow down as you go.</p>
                <x-address-fields />
            </section>

            {{-- 5. Documents --}}
            <section class="register-step" data-step="documents">
                <h2 class="register-step__title">Documents</h2>
                <p class="register-step__hint">{{ \App\Support\Uploads::hint('documents') }} each.
                    <span data-when="supplier">Fields marked <span class="text-danger">*</span> are required for suppliers.</span>
                    <span data-when="client">All documents are optional for clients.</span>
                </p>
                <div class="row">
                    @foreach([
                        'dti_sec_registration' => ['DTI / SEC registration', 'supplier'],
                        'business_permit_mayor_permit' => ["Business permit / Mayor's permit", 'supplier'],
                        'valid_id_owner_rep' => ['Valid ID (owner / representative)', 'supplier'],
                        'accreditations_certifications' => ['Accreditations / certifications', null],
                        'company_profile_portfolio' => ['Company profile / portfolio', null],
                    ] as $name => [$label, $requiredFor])
                        <div class="col-md-6 mb-3 reg-field" @if($requiredFor) data-required-field @endif>
                            <label for="{{ $name }}" class="form-label">{{ $label }}</label>
                            <input type="file" id="{{ $name }}" name="{{ $name }}" class="{{ $file($name) }}"
                                data-max-size="{{ \App\Support\Uploads::maxKb() * 1024 }}" @if($requiredFor) data-required-when="{{ $requiredFor }}" @endif {{ \App\Support\Uploads::attributes('documents', 1) }}>
                            @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                    <div class="col-md-6 mb-3 reg-field" data-when="supplier">
                        <label for="sample_price_list" class="form-label">Sample price list</label>
                        <input type="file" id="sample_price_list" name="sample_price_list" class="{{ $file('sample_price_list') }}" data-max-size="{{ \App\Support\Uploads::maxKb() * 1024 }}" {{ \App\Support\Uploads::attributes('documents', 1) }}>
                        @error('sample_price_list')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h2 class="register-step__title mt-2">Bank details <span class="register-step__optional">optional</span></h2>
                <div class="row">
                    <x-field name="bank_name" label="Bank" col="md-4">
                        <select id="bank_name" name="bank_name" class="form-select @error('bank_name') is-invalid @enderror">
                            <option value="">Select a bank</option>
                            @foreach(Company::BANKS as $bank)
                                <option value="{{ $bank }}" @selected(old('bank_name') === $bank)>{{ $bank }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field name="bank_account_name" label="Account name" col="md-4" maxlength="100" placeholder="Account holder" />
                    <x-field name="bank_account_number" label="Account number" col="md-4" maxlength="50" inputmode="numeric" placeholder="Account number" />
                </div>
            </section>

            {{-- 6. Review --}}
            <section class="register-step" data-step="review">
                <h2 class="register-step__title">Review &amp; submit</h2>
                <dl class="review-list" id="reviewList" aria-live="polite"></dl>

                <div class="form-check mb-2">
                    <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox" name="agree_terms" id="agree_terms" value="1" required @checked(old('agree_terms'))>
                    <label class="form-check-label" for="agree_terms">
                        I agree to the <a href="{{ route('terms.conditions') }}" target="_blank" rel="noopener">Terms and Conditions</a>
                        and <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">Privacy Policy</a><span class="text-danger"> *</span>
                    </label>
                    @error('agree_terms')<div class="invalid-feedback d-block">You must agree to the terms and conditions.</div>@enderror
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agree_contact" id="agree_contact" value="1" @checked(old('agree_contact'))>
                    <label class="form-check-label" for="agree_contact">I consent to be contacted for verification and partnership opportunities</label>
                </div>
            </section>

            <div class="register-actions">
                <button type="button" class="btn btn-outline-secondary" data-step-prev><i class="bi bi-arrow-left"></i> Back</button>
                <span class="register-actions__progress" id="stepProgress"></span>
                <button type="button" class="btn btn-success" data-step-next>Continue <i class="bi bi-arrow-right"></i></button>
                <button type="submit" class="btn btn-success" data-step-submit><i class="bi bi-send"></i> Create account</button>
            </div>
        </form>
    </div>

    <p class="register-footer">Already have an account? <a href="{{ route('login.form') }}">Log in here</a></p>
</main>

@vite('resources/js/pages/auth-register.js')
</body>
</html>
