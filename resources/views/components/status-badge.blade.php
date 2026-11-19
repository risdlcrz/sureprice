@props(['status', 'label' => null])
<span {{ $attributes->merge(['class' => 'badge bg-' . \App\Support\Status::color($status)]) }}>{{ $slot->isEmpty() ? ($label ?? \App\Support\Status::label($status)) : $slot }}</span>
