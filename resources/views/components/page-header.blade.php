@props(['title', 'icon' => null, 'subtitle' => null])
<div {{ $attributes->merge(['class' => 'page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4']) }}>
    <div class="page-header-text">
        <h1 class="page-title h3 mb-0">@if($icon)<i class="fas fa-{{ $icon }} me-2"></i>@endif{{ $title }}</h1>
        @if($subtitle)<p class="page-subtitle">{{ $subtitle }}</p>@endif
    </div>
    @if($slot->isNotEmpty())
        <div class="page-header-actions d-flex flex-wrap align-items-center gap-2">{{ $slot }}</div>
    @endif
</div>
