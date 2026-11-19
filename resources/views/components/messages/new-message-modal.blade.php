{{-- "New Message" modal that starts a conversation (POST messages.start). The slot is the recipient field. --}}
@props(['id'])
<x-modal-dialog :id="$id" :title-id="$id . 'Label'">
    <x-slot:title>New Message</x-slot:title>
    <form action="{{ route('messages.start') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">{{ $slot }}</div>
            <div class="mb-3">
                <label for="{{ $id }}Message" class="form-label">Message</label>
                <textarea class="form-control" id="{{ $id }}Message" name="message" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
    </form>
</x-modal-dialog>
