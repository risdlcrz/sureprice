@extends(request('popup') ? 'layouts.chat-popup' : 'layouts.app')

@section('content')
@if(request('popup'))
    <div class="flex h-screen bg-gray-100">
        <div class="flex-1 flex flex-col">
            @include('chat._chat_area', ['conversation' => $conversation, 'messages' => $messages, 'otherUser' => $otherUser])
        </div>
    </div>
@else
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
                @foreach($conversations as $conv)
                <a href="{{ route('chat.show', [$conv['id']]) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 border-b transition group {{ $conv['id'] === $conversation->id ? 'bg-blue-50' : '' }}">
                    <div class="relative">
                        <img src="{{ $conv['other_user']['avatar'] ?? asset('images/default-avatar.png') }}"
                             alt="{{ $conv['other_user']['name'] }}"
                             class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @if($conv['other_user']['is_online'])
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $conv['other_user']['name'] }}</h3>
                            @if($conv['last_message'])
                            <span class="text-xs text-gray-400">
                                {{ $conv['last_message']['created_at']->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                        @if($conv['last_message'])
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-600 truncate flex-1">
                                {{ $conv['last_message']['message'] }}
                            </p>
                            @if($conv['last_message']['status'] === 'sent')
                                <i class="fas fa-check text-gray-300 text-xs"></i>
                            @elseif($conv['last_message']['status'] === 'delivered')
                                <i class="fas fa-check-double text-blue-300 text-xs"></i>
                            @elseif($conv['last_message']['status'] === 'read')
                                <i class="fas fa-check-double text-blue-500 text-xs"></i>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        <!-- Chat Area -->
        <div class="flex-1 flex flex-col">
            @include('chat._chat_area', ['conversation' => $conversation, 'messages' => $messages, 'otherUser' => $otherUser])
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
@endsection

@include('chat._chat_area_js') 