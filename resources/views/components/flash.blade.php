@php
    $flashTypes = [
        'success' => ['success', 'circle-check'],
        'error' => ['danger', 'circle-exclamation'],
        'warning' => ['warning', 'triangle-exclamation'],
        'info' => ['info', 'circle-info'],
    ];
@endphp
@foreach ($flashTypes as $key => [$type, $icon])
    @if (session($key))
        <div class="alert alert-{{ $type }} alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
            <i class="fas fa-{{ $icon }} mt-1"></i>
            <div>{{ session($key) }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach
@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <i class="fas fa-circle-exclamation mt-1"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
