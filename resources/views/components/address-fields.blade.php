{{--
    Philippine address: Region → Province → City/Municipality → Barangay dropdowns, then street and ZIP.
    <x-address-fields :street="$c->street" :barangay="$c->barangay" :city="$c->city" :state="$c->state" :postal="$c->postal" />
    Submits street, barangay, city, state (province) and postal as plain names, like the old text inputs
    (address_region is only there to drive the lists; nothing stores it).
    The lists come from public/data/ph-address (built by scripts/build-ph-address.mjs); resources/js/ph-address.js
    fills them, re-selects saved/old() values, and falls back to text inputs if the data can't be loaded.
    :required="false" leaves them optional; a page can toggle them later through [data-address-control]
    (skip controls marked data-optional — the region, when a saved address predates the dropdowns).
--}}
@props(['street' => null, 'barangay' => null, 'city' => null, 'state' => null, 'postal' => null, 'required' => true])
@php
    $req = $required ? 'required' : '';
    $select = fn ($name) => 'form-select' . ($errors->has($name) ? ' is-invalid' : '');
@endphp
<div class="row ph-address" data-ph-address data-source="{{ asset('data/ph-address') }}">
    <x-field name="address_region" label="Region" col="md-6" :required="$required">
        <select id="address_region" name="address_region" class="form-select" data-address-control data-level="region" {{ $req }} disabled>
            <option value="">Loading regions…</option>
        </select>
    </x-field>
    <x-field name="state" label="Province" col="md-6" :required="$required">
        <select id="state" name="state" class="{{ $select('state') }}" data-address-control data-level="province" data-value="{{ old('state', $state) }}" {{ $req }} disabled>
            <option value="">Select a region first</option>
        </select>
    </x-field>
    <x-field name="city" label="City / Municipality" col="md-6" :required="$required">
        <select id="city" name="city" class="{{ $select('city') }}" data-address-control data-level="city" data-value="{{ old('city', $city) }}" {{ $req }} disabled>
            <option value="">Select a province first</option>
        </select>
    </x-field>
    <x-field name="barangay" label="Barangay" col="md-6" :required="$required">
        <select id="barangay" name="barangay" class="{{ $select('barangay') }}" data-address-control data-level="barangay" data-value="{{ old('barangay', $barangay) }}" {{ $req }} disabled>
            <option value="">Select a city / municipality first</option>
        </select>
    </x-field>
    <x-field name="street" label="House / Unit No., Building, Street" col="md-8" :value="$street" :required="$required"
        placeholder="e.g. Unit 4B, Greenfield Tower, 123 Rizal St." autocomplete="address-line1" maxlength="255" data-address-control />
    <x-field name="postal" label="ZIP Code" col="md-4" :value="$postal" :required="$required"
        placeholder="e.g. 1200" inputmode="numeric" pattern="\d{4}" maxlength="4" autocomplete="postal-code"
        title="4-digit ZIP code" data-address-control />
</div>
@vite('resources/js/ph-address.js')
