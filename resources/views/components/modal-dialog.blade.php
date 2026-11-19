{{--
    Bootstrap modal shell. The slot holds the modal-body / footer (or a <form> wrapping them).
    <x-modal-dialog id="deleteModal" dialog-class="modal-lg">
        <x-slot:title>Delete Material</x-slot:title>
        <div class="modal-body">...</div>
    </x-modal-dialog>
    title-id keeps an existing id on the title element (scripts may update it).
--}}
@props(['id', 'dialogClass' => '', 'headerClass' => '', 'titleId' => null, 'closeClass' => ''])
@php $titleId = $titleId ?: $id . 'Label'; @endphp
<div {{ $attributes->merge(['class' => 'modal fade', 'id' => $id, 'tabindex' => '-1', 'aria-labelledby' => $titleId, 'aria-hidden' => 'true']) }}>
    <div class="modal-dialog {{ $dialogClass }}">
        <div class="modal-content">
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title" id="{{ $titleId }}">{{ $title }}</h5>
                <button type="button" class="btn-close {{ $closeClass }}" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
