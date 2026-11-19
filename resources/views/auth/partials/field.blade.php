{{-- Labelled input for <x-auth-card> forms. Params: name, label, type, placeholder, value?, attrs? --}}
<div class="form-group">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" required {!! $attrs ?? '' !!}
        @if($type !== 'password') value="{{ old($name, $value ?? '') }}" @endif
        class="form-input @error($name) error @enderror" placeholder="{{ $placeholder }}">
    @error($name)<span class="error-message">{{ $message }}</span>@enderror
</div>
