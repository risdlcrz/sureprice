@extends('layouts.app')

@php
    $isClient = $company?->designation === 'client';
    $isSupplier = $company?->designation === 'supplier';
    $roleLabel = $company ? ucfirst($company->designation) : ucfirst((string) ($user->role ?: $user->user_type));
    $pwErrors = $errors->updatePassword;
@endphp

@section('content')
<div class="container-fluid req-page account-page">
    <x-page-header title="Account settings" icon="user-cog" subtitle="Update how you sign in and how we can reach you." />

    <div class="row g-4">
        <div class="col-xl-8">
            <form method="POST" action="{{ route('account.update') }}" id="accountForm">
                @csrf
                @method('PUT')

                {{-- Sign-in details --}}
                <div class="card mb-4">
                    <div class="card-header"><h2 class="req-section-title"><i class="fas fa-user"></i> Profile</h2></div>
                    <div class="card-body">
                        <div class="row">
                            @if($employee)
                                <x-field name="first_name" label="First name" :value="$employee->first_name" col="md-6" required maxlength="50" autocomplete="given-name" />
                                <x-field name="last_name" label="Last name" :value="$employee->last_name" col="md-6" required maxlength="50" autocomplete="family-name" />
                            @elseif(!$company)
                                <x-field name="name" label="Full name" :value="$user->name" col="md-12" required maxlength="100" autocomplete="name" />
                            @endif
                            <x-field name="username" label="Username" :value="$user->username" col="md-6" required maxlength="50" autocomplete="username"
                                pattern="[A-Za-z0-9._\-]+" title="Letters, numbers, dots, dashes and underscores" />
                            <x-field name="email" type="email" label="Email address" :value="$user->email" col="md-6" required maxlength="255" autocomplete="email" />
                        </div>
                        <p class="form-text mb-0">You can sign in with either your username or your email address.</p>
                    </div>
                </div>

                @if($isClient)
                    <div class="card mb-4">
                        <div class="card-header"><h2 class="req-section-title"><i class="fas fa-address-book"></i> Contact details</h2></div>
                        <div class="card-body">
                            <div class="row">
                                <x-field name="contact_person" label="Contact person" :value="$company->contact_person" col="md-12" required maxlength="100" autocomplete="name" />
                                <x-field name="mobile_number" type="tel" label="Mobile number" :value="$company->mobile_number" col="md-6" required maxlength="11"
                                    inputmode="numeric" pattern="09\d{9}" title="11 digits starting with 09, e.g. 09171234567" placeholder="09171234567" />
                                <x-field name="telephone_number" type="tel" label="Telephone number" :value="$company->telephone_number" col="md-6" maxlength="20" placeholder="e.g. 02 8123 4567" />
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h2 class="req-section-title"><i class="fas fa-map-marker-alt"></i> Address</h2></div>
                        <div class="card-body">
                            <x-address-fields :street="$company->street" :barangay="$company->barangay" :city="$company->city" :state="$company->state" :postal="$company->postal" />
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end mb-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Save changes</button>
                </div>
            </form>

            @if($isClient)
                @include('account.partials.company-name')
            @endif

            {{-- Password --}}
            <form method="POST" action="{{ route('password.update') }}" class="card mb-4" id="passwordForm">
                @csrf
                @method('PUT')
                <div class="card-header"><h2 class="req-section-title"><i class="fas fa-lock"></i> Password</h2></div>
                <div class="card-body">
                    @if(session('status') === 'password-updated')
                        <div class="alert alert-success" role="alert"><i class="fas fa-check-circle me-1"></i> Your password was changed.</div>
                    @endif
                    <div class="row">
                        @foreach([
                            'current_password' => ['Current password', 'current-password', 'col-md-12'],
                            'password' => ['New password', 'new-password', 'col-md-6'],
                            'password_confirmation' => ['Confirm new password', 'new-password', 'col-md-6'],
                        ] as $field => [$label, $autocomplete, $col])
                            <div class="{{ $col }} mb-3">
                                <label for="{{ $field }}" class="form-label">{{ $label }}<span class="text-danger"> *</span></label>
                                <input type="password" id="{{ $field }}" name="{{ $field }}" class="form-control @if($pwErrors->has($field)) is-invalid @endif"
                                    required autocomplete="{{ $autocomplete }}" @if($field === 'password') minlength="8" aria-describedby="passwordHelp" @endif>
                                @if($pwErrors->has($field))<div class="invalid-feedback d-block">{{ $pwErrors->first($field) }}</div>@endif
                                @if($field === 'password')<div class="form-text" id="passwordHelp">At least 8 characters.</div>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="req-form-footer">
                    <button type="submit" class="btn btn-outline-success"><i class="fas fa-key me-1"></i> Change password</button>
                </div>
            </form>
        </div>

        {{-- Summary --}}
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="account-avatar" aria-hidden="true">{{ Str::upper(Str::substr($company?->company_name ?: $user->name ?: '?', 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $company?->company_name ?: $user->name }}</div>
                            <div class="text-muted small">{{ $roleLabel }}</div>
                        </div>
                    </div>
                    <dl class="req-meta">
                        <div><dt>Member since</dt><dd>{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Last sign-in</dt><dd>{{ $user->last_login_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '—' }}</dd></div>
                        @if($company)
                            <div><dt>Account status</dt><dd><x-status-badge :status="$company->status" /></dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($isSupplier)
                <div class="req-callout mb-4">
                    <div class="req-callout__text">
                        <i class="fas fa-building text-success" aria-hidden="true"></i>
                        <div>
                            <strong>{{ $company->company_name ?: 'Business details' }}</strong><br>
                            Company name, documents, bank details and the rest of your supplier profile are edited on your supplier profile.
                        </div>
                    </div>
                    <a href="{{ route('supplier.profile.edit') }}" class="btn btn-outline-secondary">Supplier profile</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite(['resources/css/pages/requests.css', 'resources/css/pages/account.css'])
@endpush
