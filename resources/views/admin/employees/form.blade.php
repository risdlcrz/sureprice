{{-- Create/edit employee account. Optional $employee. Contractor address fields show only for the contractor role. --}}
@extends('layouts.app')

@php
    $e = $employee ?? null;
    $title = $e ? 'Edit Employee' : 'Add New Employee';
    $roles = ['procurement' => 'Procurement', 'warehousing' => 'Warehousing', 'contractor' => 'Contractor', 'finance' => 'Finance'];
@endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" icon="user-tie">
        <a href="{{ route('information-management.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $e ? route('information-management.update', $e->id) : route('information-management.store') }}">
                @csrf
                @if($e) @method('PUT') @endif
                <div class="row">
                    <x-field name="first_name" label="First Name" :value="$e?->first_name" col="md-6" required />
                    <x-field name="last_name" label="Last Name" :value="$e?->last_name" col="md-6" required />
                    <x-field name="email" label="Email" type="email" :value="$e?->email" col="md-6" required />
                    <x-field name="username" label="Username" :value="$e?->username" col="md-6" required />
                    @unless($e)
                        <x-field name="password" label="Password" type="password" col="md-6" required autocomplete="new-password" />
                        <x-field name="password_confirmation" label="Confirm Password" type="password" col="md-6" required autocomplete="new-password" />
                    @endunless
                    <x-field name="role" label="Role" col="md-6" required>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $e?->role) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <fieldset id="contractor-fields" class="row">
                    <legend class="h6 text-muted">Contractor details</legend>
                    <x-field name="company_name" label="Company Name (optional)" :value="$e?->company_name" col="md-6" />
                    <x-field name="phone" label="Phone Number" :value="$e?->phone" col="md-6" data-contractor-required />
                    <div class="col-12">
                        {{-- required is toggled with the role by admin-employees-form.js --}}
                        <x-address-fields :street="$e?->street" :barangay="$e?->barangay" :city="$e?->city" :state="$e?->state" :postal="$e?->postal" :required="false" />
                    </div>
                </fieldset>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('information-management.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $e ? 'Update' : 'Create' }} Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/pages/admin-employees-form.js')
@endpush
