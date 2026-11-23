{{--
    Labelled form control with validation feedback.
    <x-field name="qty" label="Quantity" type="number" :value="$item?->qty" required step="0.01" />
    Pass a slot to render a custom control (select, textarea…) under the same label/error wrapper.
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'col' => null])
@php $id = $attributes->get('id', $name); $invalid = $errors->has($name) ? ' is-invalid' : ''; @endphp
<div class="{{ $col ? "col-$col" : '' }} mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}@if($attributes->has('required') && $attributes->get('required') !== false)<span class="text-danger"> *</span>@endif</label>
    @if($slot->isNotEmpty())
        {{ $slot }}
    @elseif($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->except('id')->merge(['class' => 'form-control' . $invalid, 'rows' => 3]) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->except('id')->merge(['class' => 'form-control' . $invalid]) }}>
    @endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
