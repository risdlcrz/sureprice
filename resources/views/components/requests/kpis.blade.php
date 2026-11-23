{{--
    Status KPI tiles for a request list; each tile filters the list (?status=key), clicking the active one clears it.
    <x-requests.kpis :filters="App\Models\PurchaseRequest::STATUS_FILTERS" :counts="$counts" route="purchase-requests.index" />
--}}
@props(['filters', 'counts', 'route'])
@php $current = request('status'); @endphp
<div class="req-kpis">
    @foreach($filters as $key => [$label, $statuses, $tone, $icon])
        @php $active = $current === $key; @endphp
        <a href="{{ route($route, array_filter(request()->except(['status', 'page']) + ['status' => $active ? null : $key])) }}"
           class="req-kpi req-kpi--{{ $tone }} @if($active) is-active @endif" @if($active) aria-current="true" @endif
           title="{{ $active ? 'Show all' : 'Show only: ' . $label }}">
            <span class="req-kpi__icon"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
            <span>
                <span class="req-kpi__value">{{ number_format($counts[$key] ?? 0) }}</span>
                <span class="req-kpi__label">{{ $label }}</span>
            </span>
        </a>
    @endforeach
</div>
