@extends('layouts.app')

@push('meta')
<meta name="search-chat-route" content="{{ route('admin.companies.search-for-chat') }}">
<meta name="start-message-route" content="{{ route('messages.start') }}">
@endpush

@push('styles')
@vite('resources/css/pages/messages-index-0.css')
@endpush

@section('content')
@vite('resources/css/pages/messages-index-1.css')
<div class="messenger-root">
    <!-- Sidebar -->
    <div class="messenger-sidebar">
        <div class="messenger-sidebar-header">
            <h3>Messages</h3>
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->user_type === 'company' && Auth::user()->company) || Auth::user()->role === 'manager')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startConversationModal" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0;">
                    <i class="fas fa-plus"></i>
                </button>
            @endif
        </div>
        <div class="messenger-search">
            <input type="text" id="messenger-search-input" placeholder="Search Messenger...">
            <i class="bi bi-search"></i>
            <div id="sidebarSearchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none; top: 38px;"></div>
        </div>
        <div class="messenger-chat-list" id="chatList">
            @forelse($conversations as $sideConversation)
                <a href="{{ route('messages.index', ['conversation' => $sideConversation->id]) }}" class="messenger-chat-item @if(isset($conversation) && $sideConversation->id == $conversation->id) active @endif">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($sideConversation->getOtherParticipant(auth()->user())->name) }}&background=0D8ABC&color=fff" class="messenger-chat-avatar">
                    <div class="messenger-chat-info">
                        <div class="messenger-chat-name">
                            @if($sideConversation->getOtherParticipant(auth()->user()))
                                {{ $sideConversation->getOtherParticipant(auth()->user())->name ?? $sideConversation->getOtherParticipant(auth()->user())->company_name }}
                            @else
                                [Deleted User]
                            @endif
                        </div>
                        <div class="messenger-chat-preview">
                            @if($sideConversation->messages->count() > 0)
                                {{ Str::limit($sideConversation->messages->first()->content, 30) }}
                            @else
                                No messages yet
                            @endif
                        </div>
                    </div>
                    @if($sideConversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                        <span class="messenger-unread-dot"></span>
                    @endif
                </a>
            @empty
                <div class="text-center text-muted p-4">No conversations yet.</div>
            @endforelse
        </div>
    </div>
    <!-- Main Content -->
    <div class="messenger-main-content">
        @if(isset($conversation) && isset($messages))
        <div class="messenger-header">
            <button class="back-btn" style="display:none;" id="showSidebarBtn"><i class="bi bi-arrow-left"></i></button>
            <div class="messenger-header-title">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation->getOtherParticipant(auth()->user())->name) }}&background=0D8ABC&color=fff" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                {{ $conversation->getOtherParticipant(auth()->user())->name }}
            </div>
            <div class="messenger-header-actions">
                <i class="bi bi-telephone"></i>
                <i class="bi bi-camera-video"></i>
                <i class="bi bi-info-circle"></i>
            </div>
        </div>
        <div class="messenger-messages-area" id="messagesArea">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="messenger-message-row {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}" data-message-id="{{ $message->id }}">
                        @if($message->sender_id !== auth()->id())
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($message->sender->name) }}&background=0D8ABC&color=fff" class="messenger-message-avatar">
                        @endif
                        <div>
                            <div class="messenger-message-bubble">
                                @if($message->content)
                                    {{ $message->content }}
                                @endif
                                @if($message->file_path || $message->image)
                                    @php
                                        $filePath = $message->file_path ?: $message->image;
                                        $fileName = $message->file_name ?: basename($filePath);
                                        $isImage = $message->file_type ? str_starts_with($message->file_type, 'image/') : 
                                                  (str_contains($filePath, '.jpg') || str_contains($filePath, '.jpeg') || 
                                                   str_contains($filePath, '.png') || str_contains($filePath, '.gif'));
                                    @endphp
                                    @if($isImage)
                                        <div class="mt-2">
                                            <div class="attachment-preview" 
                                                 onclick="messengerApp.openAttachmentViewer('{{ Storage::url($filePath) }}', '{{ $fileName }}', 'image')"
                                                 style="cursor: pointer; display: inline-block; position: relative;">
                                                <img src="{{ Storage::url($filePath) }}" 
                                                     alt="attachment" 
                                                     style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                                <div class="attachment-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                                    <i class="bi bi-zoom-in text-white fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <div class="attachment-preview" 
                                                 onclick="messengerApp.openAttachmentViewer('{{ Storage::url($filePath) }}', '{{ $fileName }}', 'file')"
                                                 style="cursor: pointer; display: inline-block; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                                                <i class="bi bi-file-earmark me-2"></i>{{ $fileName }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="messenger-message-time">{{ $message->created_at->timezone('Asia/Manila')->format('g:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted" style="margin-top: 40px;">No messages yet. Start the conversation!</div>
            @endif
        </div>
        <form action="{{ route('messages.store', $conversation) }}" method="POST" class="messenger-input-area" enctype="multipart/form-data" id="messageForm">
            @csrf
            <div id="attachmentPreview" style="display:none; position:relative; margin-right:10px;"></div>
            <button type="button" class="messenger-input-icon" id="attachBtn" title="Attach file"><i class="bi bi-paperclip"></i></button>
            <textarea name="content" class="messenger-input-box" rows="1" placeholder="Type your message..." id="messageContent"></textarea>
            <input type="file" name="file" class="d-none" id="fileInput">
            <button type="submit" class="messenger-send-btn">Send</button>
        </form>
        @else
        <div class="d-flex flex-column justify-content-center align-items-center h-100 p-5">
            <i class="bi bi-chat-dots display-1 text-muted mb-3"></i>
            <h4 class="text-muted">Select a conversation to start messaging</h4>
        </div>
        @endif
    </div>
</div>
<!-- Inline modals (from previous index view) -->
@if(auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
    <!-- New Message Modal for Client -->
    <x-messages.new-message-modal id="newMessageModalClient">
        <label for="manager_id" class="form-label">Select Manager</label>
        <select class="form-select" id="manager_id" name="manager_id" required>
            <option value="">Choose a manager...</option>
            @foreach(\App\Models\User::where('role', 'manager')->get() as $manager)
                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
            @endforeach
        </select>
    </x-messages.new-message-modal>
@endif
@if(auth()->user()->user_type === 'admin')
    <!-- New Message Modal for Admin -->
    <x-messages.new-message-modal id="newMessageModalAdmin">
        <label for="participant_select_admin" class="form-label">Start conversation with</label>
        <select class="form-control" id="participant_select_admin" required>
            <option value="">Select Client or Supplier</option>
            @foreach(\App\Models\User::where('user_type', 'company')->whereHas('company', function($q){ $q->where('designation', 'client'); })->get() as $client)
                <option value="client_{{ $client->id }}">Client: {{ $client->company?->company_name ?? $client->name }}</option>
            @endforeach
            @foreach(\App\Models\Supplier::all() as $supplier)
                <option value="supplier_{{ $supplier->id }}">Supplier: {{ $supplier->company_name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="client_id" id="client_id_hidden_admin">
        <input type="hidden" name="supplier_id" id="supplier_id_hidden_admin">
    </x-messages.new-message-modal>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const participantSelect = document.getElementById('participant_select_admin');
        const clientIdHidden = document.getElementById('client_id_hidden_admin');
        const supplierIdHidden = document.getElementById('supplier_id_hidden_admin');
        
        if (participantSelect) {
            participantSelect.addEventListener('change', function() {
                // Clear both hidden fields initially
                clientIdHidden.value = '';
                supplierIdHidden.value = '';
                clientIdHidden.name = '';
                supplierIdHidden.name = '';
                
                if (this.value.startsWith('client_')) {
                    clientIdHidden.name = 'client_id';
                    clientIdHidden.value = this.value.replace('client_', '');
                } else if (this.value.startsWith('supplier_')) {
                    supplierIdHidden.name = 'supplier_id';
                    supplierIdHidden.value = this.value.replace('supplier_', '');
                }
            });
        }
    });
    </script>
@endif
<!-- Delete Conversation Modal -->
<x-modal-dialog id="deleteConversationModal">
    <x-slot:title>Delete Conversation</x-slot:title>
            <div class="modal-body">
                Are you sure you want to delete this conversation? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteConversation">Delete</button>
            </div>
</x-modal-dialog>
<div id="contextMenu" class="position-fixed bg-white border rounded shadow-sm" style="display:none; z-index:9999; min-width:140px;"></div>
<!-- Start Conversation Modal -->
<x-modal-dialog id="startConversationModal">
    <x-slot:title>Start Conversation</x-slot:title>
            <div class="modal-body">
                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    @if(Auth::user()->user_type === 'admin' || Auth::user()->role === 'manager')
                        <div class="mb-3">
                            <label for="participant_search_start" class="form-label">Search for Client or Supplier</label>
                            <input type="text" class="form-control" id="participant_search_start" placeholder="Type to search..." autocomplete="off" required>
                            <div id="participant_search_results_start" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></div>
                            <input type="hidden" name="client_id" id="client_id_hidden_start">
                            <input type="hidden" name="supplier_id" id="supplier_id_hidden_start">
                        </div>
                        <script>window.__page = Object.assign(window.__page || {}, { index1_0: @json((string) (route('admin.companies.search-for-chat'))) });</script>
                    @elseif(Auth::user()->user_type === 'company' && Auth::user()->company && Auth::user()->company->designation === 'client')
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Start conversation with</label>
                            <select class="form-control" id="manager_id" name="manager_id" required>
                                <option value="">Select Manager</option>
                                @foreach(\App\Models\User::where('role', 'manager')->get() as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif(Auth::user()->user_type === 'company' && Auth::user()->company && Auth::user()->company->designation === 'supplier')
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">Start conversation with Admin</label>
                            <select class="form-control" id="admin_id" name="admin_id" required>
                                <option value="">Select Admin</option>
                                @foreach(\App\Models\User::where('user_type', 'admin')->get() as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
</x-modal-dialog>
<!-- Attachment Viewer Modal -->
<div class="modal fade" id="attachmentViewerModal" tabindex="-1" aria-labelledby="attachmentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentViewerModalLabel">Attachment Viewer</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="downloadAttachmentBtn">
                        <i class="bi bi-download"></i> Download
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomInBtn" style="display:none;">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomOutBtn" style="display:none;">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="resetZoomBtn" style="display:none;">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body text-center p-0">
                <div id="attachmentViewerContent" class="position-relative">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { index2_0: @json($conversation->id ?? null), index2_1: @json(auth()->id()), index2_2: @json((string) (csrf_token())), index2_3: {{ Js::from((string) (route('messages.store', $conversation ?? 1))) }}, index2_4: @json((string) (route("messages.conversations.update"))), index2_5: @json($conversation->id ?? null) });</script>
@vite('resources/js/pages/messages-index.js')
@endpush 