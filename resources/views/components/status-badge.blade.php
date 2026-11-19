@props(['status', 'label' => null, 'kind' => 'status'])
<span {{ $attributes->merge(['class' => 'badge bg-' . \App\Support\Status::color($status, $kind)]) }}>{{ $slot->isEmpty() ? ($label ?? \App\Support\Status::label($status)) : $slot }}</span>
