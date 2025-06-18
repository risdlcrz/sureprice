@extends(request('popup') ? 'layouts.chat-popup' : 'layouts.chat')

@section('content')
<div class="container-fluid h-100" style="height:100vh;">
    <div class="row h-100" style="height:100vh;">
        <!-- Left Sidebar -->
        <div class="col-3 d-flex flex-column p-0 border-end bg-white" style="height:100vh;min-width:300px;max-width:350px;">
            <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                <div></div>
                <div>
                    <button class="btn btn-light btn-sm me-2" id="enlarge-btn" title="Open in new tab"><i class="fas fa-external-link-alt"></i></button>
                    <button class="btn btn-success btn-sm" id="new-message-btn" title="New Message"><i class="fas fa-edit"></i></button>
                </div>
            </div>
            <div class="px-3 py-2 border-bottom">
                <input type="text" class="form-control" placeholder="Search Messenger...">
            </div>
            <div class="d-flex px-3 py-2 border-bottom gap-3">
                <button class="btn btn-link p-0 text-success fw-bold">All</button>
                <button class="btn btn-link p-0 text-secondary">Unread</button>
            </div>
            <div class="flex-grow-1 overflow-auto" style="background:#f8f9fa;">
                <div class="list-group list-group-flush">
                    @forelse($conversations as $conversation)
                        @php $popup = request('popup') ? ['popup' => 1] : []; @endphp
                        <a href="{{ route('chat.show', array_merge(['conversation' => $conversation['id']], $popup)) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ isset($activeConversationId) && $activeConversationId == $conversation['id'] ? 'bg-success text-white' : '' }}">
                            <img src="{{ $conversation['other_user']['avatar'] ?? asset('images/default-avatar.png') }}" class="rounded-circle me-3" width="48" height="48">
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $conversation['other_user']['name'] }}</div>
                                @if($conversation['last_message'])
                                    <small>{{ $conversation['last_message']['message'] }} &middot; {{ $conversation['last_message']['created_at']->diffForHumans() }}</small>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <div>No conversations yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Center Chat Area -->
        <div class="col-6 d-flex flex-column p-0" style="height:100vh;min-width:400px;">
            <!-- Chat Header -->
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white">
                <div class="d-flex align-items-center">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle me-3" width="48" height="48">
                    <div>
                        <div class="fw-bold">modus operandi</div>
                        <small>Patricia Anne Dela Cruz <span class="fw-bold">Attachment</span></small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-link text-success"><i class="fas fa-phone fa-lg"></i></button>
                    <button class="btn btn-link text-success"><i class="fas fa-video fa-lg"></i></button>
                    <button class="btn btn-link text-success"><i class="fas fa-info-circle fa-lg"></i></button>
                </div>
            </div>
            <!-- Messages -->
            <div class="flex-grow-1 px-4 py-3 overflow-auto" style="background:#f4f6fb;">
                <!-- Example messages -->
                <div class="d-flex flex-column gap-3">
                    <div class="align-self-end">
                        <div class="bg-success text-white rounded-pill px-4 py-2 mb-1" style="max-width:60%;">Sige sige</div>
                        <div class="bg-success text-white rounded-pill px-4 py-2 mb-1" style="max-width:60%;">So yung button mag rereddirect sa buong page?</div>
                        <div class="bg-success text-white rounded-pill px-4 py-2" style="max-width:60%;">ay sige sige wag na ganto?</div>
                    </div>
                    <div class="align-self-start">
                        <div class="bg-light text-dark rounded-pill px-4 py-2 mb-1" style="max-width:60%;">YUS</div>
                        <div class="bg-light text-dark rounded-pill px-4 py-2 mb-1" style="max-width:60%;">pwede naman both</div>
                        <div class="bg-light text-dark rounded-pill px-4 py-2" style="max-width:60%;">para mas accessible</div>
                    </div>
                </div>
            </div>
            <!-- Message Input -->
            <div class="border-top bg-white px-4 py-3">
                <form class="d-flex align-items-center gap-2">
                    <button class="btn btn-link text-success" type="button"><i class="fas fa-plus fa-lg"></i></button>
                    <button class="btn btn-link text-success" type="button"><i class="fas fa-image fa-lg"></i></button>
                    <button class="btn btn-link text-success" type="button"><i class="fas fa-sticky-note fa-lg"></i></button>
                    <button class="btn btn-link text-success" type="button"><i class="fas fa-gift fa-lg"></i></button>
                    <input type="text" class="form-control rounded-pill" placeholder="Aa">
                    <button class="btn btn-link text-success" type="button"><i class="far fa-smile fa-lg"></i></button>
                    <button class="btn btn-link text-success" type="button"><i class="fas fa-thumbs-up fa-lg"></i></button>
                </form>
            </div>
        </div>
        @if(!request('popup'))
        <!-- Right Sidebar (hide in popup) -->
        <div class="col-3 d-flex flex-column p-0 border-start bg-white" style="height:100vh;min-width:300px;max-width:350px;">
            <div class="d-flex flex-column align-items-center py-4 border-bottom">
                <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle mb-2" width="72" height="72">
                <div class="fw-bold">modus operandi</div>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <button class="btn btn-light w-100 mb-2"><i class="fas fa-bell me-2"></i>Mute</button>
                    <button class="btn btn-light w-100"><i class="fas fa-search me-2"></i>Search</button>
                </div>
                <div class="mb-4">
                    <div class="fw-bold mb-2">Chat info</div>
                    <div class="mb-2">Customize chat</div>
                    <div class="mb-2">Chat members</div>
                </div>
                <div>
                    <div class="fw-bold mb-2">Media, files and links</div>
                    <div class="mb-2"><i class="fas fa-photo-video me-2"></i>Media</div>
                    <div><i class="fas fa-file-alt me-2"></i>Files</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">Start New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="user-search-input" placeholder="Search users...">
                <div id="user-list-container">
                    <div class="text-center text-muted">Type to search for users...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Enlarge button (only in popup)
    $('#enlarge-btn').on('click', function() {
        if (window.parent !== window) {
            window.parent.postMessage('enlarge-chat', '*');
        } else {
            window.open('{{ route('chat.index') }}', '_blank');
        }
    });
    // New Message Modal
    $('#new-message-btn').on('click', function() {
        $('#newMessageModal').modal('show');
        $('#user-search-input').val('');
        $('#user-list-container').html('<div class="text-center text-muted">Type to search for users...</div>');
    });
    $('#user-search-input').on('input', function() {
        let query = $(this).val();
        if (query.length < 2) {
            $('#user-list-container').html('<div class="text-center text-muted">Type to search for users...</div>');
            return;
        }
        $('#user-list-container').html('<div class="text-center text-muted">Searching...</div>');
        $.get('{{ route('chat.users') }}', {q: query}, function(data) {
            let html = '';
            if (data.length === 0) {
                html = '<div class="text-center text-muted">No users found.</div>';
            } else {
                html = '<ul class="list-group">';
                data.forEach(function(user) {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center user-list-item" data-user-id="${user.id}">
                        <span>
                            <strong>${user.name}</strong><br>
                            <small class="text-muted">${user.type}</small>
                        </span>
                        <button class="btn btn-sm btn-success start-chat-btn">Chat</button>
                    </li>`;
                });
                html += '</ul>';
            }
            $('#user-list-container').html(html);
        });
    });
    $(document).on('click', '.start-chat-btn', function() {
        const userId = $(this).closest('.user-list-item').data('user-id');
        let url = '/chat/start/' + userId;
        @if(request('popup'))
            url += '?popup=1';
        @endif
        window.location.href = url;
    });
</script>
@endpush
@endsection 