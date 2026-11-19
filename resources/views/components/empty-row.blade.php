@props(['cols' => 1])
<tr><td colspan="{{ $cols }}" {{ $attributes->merge(['class' => 'text-center text-muted py-4']) }}>{{ $slot }}</td></tr>
