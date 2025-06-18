@extends(request('popup') ? 'layouts.chat-popup' : 'layouts.app')

@section('content')
@if(request('popup'))
    {{-- Popup: Show only the conversation list as before --}}
    <div class="flex h-screen bg-gray-100">
        <div class="w-full max-w-md mx-auto bg-white h-full shadow-lg rounded-lg flex flex-col relative">
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-0">Chats</h2>
                    <button class="btn btn-link p-0 m-0" id="new-message-btn" title="New Message" style="color:#2563eb;">
                        <i class="fas fa-edit fa-lg"></i>
                    </button>
                </div>
                <button class="btn btn-link p-0 m-0" id="enlarge-btn" title="Open in new tab" style="color:#2563eb;">
                    <i class="fas fa-external-link-alt fa-lg"></i>
                </button>
            </div>
            <!-- Search Bar -->
            <div class="px-4 py-2 border-b bg-gray-50">
                <input type="text" class="form-control rounded-full px-4 py-2" id="conversation-search" placeholder="Search Messenger...">
            </div>
            <!-- Tabs (only All and Unread) -->
            <div class="flex items-center px-4 py-2 border-b bg-gray-50 gap-4">
                <button class="tab-btn active" data-tab="all">All</button>
                <button class="tab-btn" data-tab="unread">Unread</button>
            </div>
            <!-- Conversation List -->
            <div class="flex-1 overflow-y-auto bg-white" id="conversation-list">
                @forelse($conversations as $conversation)
                <a href="{{ route('chat.show', array_merge([$conversation['id']], request('popup') ? ['popup' => 1] : [])) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 border-b transition group">
                    <div class="relative">
                        <img src="{{ $conversation['other_user']['avatar'] ?? asset('images/default-avatar.png') }}"
                             alt="{{ $conversation['other_user']['name'] }}"
                             class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @if($conversation['other_user']['is_online'])
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $conversation['other_user']['name'] }}</h3>
                            @if($conversation['last_message'])
                            <span class="text-xs text-gray-400">
                                {{ $conversation['last_message']['created_at']->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                        @if($conversation['last_message'])
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-600 truncate flex-1">
                                {{ $conversation['last_message']['message'] }}
                            </p>
                            @if($conversation['last_message']['status'] === 'sent')
                                <i class="fas fa-check text-gray-300 text-xs"></i>
                            @elseif($conversation['last_message']['status'] === 'delivered')
                                <i class="fas fa-check-double text-blue-300 text-xs"></i>
                            @elseif($conversation['last_message']['status'] === 'read')
                                <i class="fas fa-check-double text-blue-500 text-xs"></i>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="fas fa-comments fa-3x mb-2"></i>
                    <p>No conversations yet.</p>
                </div>
                @endforelse
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
                  <div class="text-center text-muted">Loading users...</div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
@else
    {{-- Full page: Messenger-style layout with sidebar and main area --}}
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar (Conversation List) -->
        <div class="w-full max-w-sm bg-white h-full shadow-lg flex flex-col relative border-r">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-0">Chats</h2>
                    <button class="btn btn-link p-0 m-0" id="new-message-btn" title="New Message" style="color:#2563eb;">
                        <i class="fas fa-edit fa-lg"></i>
                    </button>
                </div>
            </div>
            <div class="px-4 py-2 border-b bg-gray-50">
                <input type="text" class="form-control rounded-full px-4 py-2" id="conversation-search" placeholder="Search Messenger...">
            </div>
            <div class="flex items-center px-4 py-2 border-b bg-gray-50 gap-4">
                <button class="tab-btn active" data-tab="all">All</button>
                <button class="tab-btn" data-tab="unread">Unread</button>
            </div>
            <div class="flex-1 overflow-y-auto bg-white" id="conversation-list">
                @forelse($conversations as $conversation)
                <a href="{{ route('chat.show', [$conversation['id']]) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 border-b transition group">
                    <div class="relative">
                        <img src="{{ $conversation['other_user']['avatar'] ?? asset('images/default-avatar.png') }}"
                             alt="{{ $conversation['other_user']['name'] }}"
                             class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @if($conversation['other_user']['is_online'])
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $conversation['other_user']['name'] }}</h3>
                            @if($conversation['last_message'])
                            <span class="text-xs text-gray-400">
                                {{ $conversation['last_message']['created_at']->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                        @if($conversation['last_message'])
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-600 truncate flex-1">
                                {{ $conversation['last_message']['message'] }}
                            </p>
                            @if($conversation['last_message']['status'] === 'sent')
                                <i class="fas fa-check text-gray-300 text-xs"></i>
                            @elseif($conversation['last_message']['status'] === 'delivered')
                                <i class="fas fa-check-double text-blue-300 text-xs"></i>
                            @elseif($conversation['last_message']['status'] === 'read')
                                <i class="fas fa-check-double text-blue-500 text-xs"></i>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="fas fa-comments fa-3x mb-2"></i>
                    <p>No conversations yet.</p>
                </div>
                @endforelse
            </div>
        </div>
        <!-- Main Area (Empty by default) -->
        <div class="flex-1 flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <i class="fas fa-comments fa-4x mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold text-gray-700">No conversation selected</h3>
                <p class="text-gray-500">Select a conversation to start messaging</p>
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
                  <div class="text-center text-muted">Loading users...</div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
@endif

@push('styles')
<style>
.tab-btn {
    background: none;
    border: none;
    outline: none;
    font-weight: 500;
    color: #888;
    padding: 0 8px 4px 8px;
    border-bottom: 2px solid transparent;
    transition: color 0.2s, border-color 0.2s;
    cursor: pointer;
}
.tab-btn.active {
    color: #2563eb;
    border-bottom: 2px solid #2563eb;
}
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Tabs (only All is functional)
    $('.tab-btn').on('click', function() {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        // Only All is functional for now
    });
    // New Message Modal
    $('#new-message-btn').on('click', function() {
        $('#newMessageModal').modal('show');
        loadUserList();
    });
    $('#user-search-input').on('input', function() {
        loadUserList($(this).val());
    });
    function loadUserList(query = '') {
        $('#user-list-container').html('<div class="text-center text-muted">Loading users...</div>');
        $.get('/chat/users', {q: query}, function(data) {
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
    }
    $(document).on('click', '.start-chat-btn', function() {
        const userId = $(this).closest('.user-list-item').data('user-id');
        window.location.href = `/chat/start/${userId}`;
    });
    // Conversation search (client-side filter)
    $('#conversation-search').on('input', function() {
        const q = $(this).val().toLowerCase();
        $('#conversation-list a').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) !== -1);
        });
    });
    // Enlarge button (only in popup)
    $('#enlarge-btn').on('click', function() {
        window.open('/chat', '_blank');
    });
});
</script>
@endpush
@endsection 