{{--
    Profile photo, or the first letter of the name when there's no photo.
    <x-avatar :user="$user" size="md" />          sizes: xs 24 · sm 30 · md 40 · lg 56 · xl 96 (px)
    <x-avatar name="Acme Supply" />               no user (e.g. a deleted account): initials only
    Extra classes / attributes go on the element (e.g. class="profile-avatar" in the sidebar).
--}}
@props(['user' => null, 'name' => null, 'size' => 'md'])
@php
    $label = $name ?? $user?->avatarName() ?? '?';
    $url = $user?->avatarUrl();
    $letter = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) $label) ?: '?', 0, 1));
@endphp
@if($url)
    <img src="{{ $url }}" alt="{{ $label }}" loading="lazy" {{ $attributes->merge(['class' => "sp-avatar sp-avatar--$size"]) }}>
@else
    <span aria-hidden="true" {{ $attributes->merge(['class' => "sp-avatar sp-avatar--$size sp-avatar--initials"]) }}>{{ $letter }}</span>
@endif
