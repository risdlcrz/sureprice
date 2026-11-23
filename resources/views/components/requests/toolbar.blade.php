{{--
    Search box + filter pills above a request table (inside the card). Keeps the other active filters when searching.
    <x-requests.toolbar route="purchase-requests.index" placeholder="Search…" :pills="['type' => ['project' => 'Contract related', …]]" :counts="[...]" />
    $pills: query key => [value => label]; $pillCounts: value => count (optional).
--}}
@props(['route', 'placeholder' => 'Search…', 'pills' => [], 'pillCounts' => []])
@php
    $filtered = collect(request()->only(['q', 'status', ...array_keys($pills)]))->filter()->isNotEmpty();
@endphp
<div class="req-toolbar">
    <form method="GET" action="{{ route($route) }}" class="req-search" role="search">
        @foreach(request()->except(['q', 'page']) as $name => $value)
            @if(is_scalar($value))<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
        @endforeach
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}">
        </div>
    </form>
    <ul class="req-pills">
        @foreach($pills as $param => $options)
            @foreach($options as $value => $label)
                @php $active = request($param) === (string) $value; @endphp
                <li>
                    <a href="{{ route($route, array_filter(request()->except([$param, 'page']) + [$param => $active ? null : $value])) }}"
                       class="req-pill @if($active) is-active @endif" @if($active) aria-current="true" @endif>
                        {{ $label }}
                        @isset($pillCounts[$value])<span class="req-pill__count">{{ $pillCounts[$value] }}</span>@endisset
                    </a>
                </li>
            @endforeach
        @endforeach
        {{ $slot }}
        @if($filtered)
            @if(count($pills))<li class="req-pills__divider" aria-hidden="true"></li>@endif
            <li><a href="{{ route($route) }}" class="req-pill"><i class="fas fa-times" aria-hidden="true"></i> Clear filters</a></li>
        @endif
    </ul>
</div>
