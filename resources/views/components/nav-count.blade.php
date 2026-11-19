{{-- Unread counter for sidebar links; kept in the DOM (hidden at 0) so app-layout.js can update it live. --}}
@props(['kind' => 'notifications'])
@php $count = $kind === 'messages' ? \App\Support\UnreadCounts::messages() : \App\Support\UnreadCounts::notifications(); @endphp
<span data-unread="{{ $kind }}" class="badge rounded-pill bg-danger ms-auto" @if(!$count) style="display:none" @endif>{{ $count }}</span>
