{{-- Confirmation modal that submits a form. <x-confirm-modal id="approve5" title="Approve X" :action="route(...)" confirm="Approve" tone="success">Are you sure?</x-confirm-modal> --}}
@props(['id', 'title', 'action', 'method' => 'POST', 'confirm' => 'Confirm', 'tone' => 'primary'])
<x-modal-dialog :id="$id">
    <x-slot:title>{{ $title }}</x-slot:title>
    <form action="{{ $action }}" method="POST">
        @csrf
        @unless(strtoupper($method) === 'POST') @method($method) @endunless
        <div class="modal-body">{{ $slot }}</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-{{ $tone }}">{{ $confirm }}</button>
        </div>
    </form>
</x-modal-dialog>
