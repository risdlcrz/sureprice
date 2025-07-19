@extends('layouts.app')

@push('meta')
<meta name="search-chat-route" content="{{ route('admin.companies.search-for-chat') }}">
<meta name="start-message-route" content="{{ route('messages.start') }}">
@endpush

@push('styles')
<style>
body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    background: #f0f2f5;
}
.messenger-root {
    display: flex;
    height: 100vh;
    background: #f0f2f5;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.messenger-sidebar {
    width: 350px;
    background: #fff;
    border-right: 1px solid #e4e6eb;
    display: flex;
    flex-direction: column;
    height: 100vh;
    min-width: 260px;
    max-width: 100vw;
}
.messenger-sidebar-header {
    padding: 18px 20px 10px 20px;
    border-bottom: 1px solid #e4e6eb;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.messenger-sidebar-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
    flex: 1;
}
.messenger-search {
    margin: 0 20px 12px 20px;
    position: relative;
}
.messenger-search input {
    width: 100%;
    padding: 8px 36px 8px 14px;
    border-radius: 20px;
    border: 1px solid #e4e6eb;
    background: #f5f6fa;
    font-size: 1rem;
}
.messenger-search .bi-search {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 1.1rem;
}
.messenger-chat-list {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 10px;
}
.messenger-chat-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    cursor: pointer;
    transition: background 0.15s;
    border: none;
    background: #fff;
    border-bottom: 1px solid #f0f2f5;
    text-decoration: none;
    color: inherit;
    position: relative;
}
.messenger-chat-item.active, .messenger-chat-item:hover {
    background: #f0f2f5;
}
.messenger-chat-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 14px;
}
.messenger-chat-info {
    flex: 1;
    min-width: 0;
}
.messenger-chat-name {
    font-weight: 600;
    font-size: 1.08rem;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.messenger-chat-preview {
    font-size: 0.97rem;
    color: #888;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.messenger-unread-dot {
    width: 10px;
    height: 10px;
    background: #1877f2;
    border-radius: 50%;
    margin-left: 8px;
    display: inline-block;
}
.messenger-main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100vh;
    min-width: 0;
    position: relative;
    background: #f0f2f5;
}
.messenger-header {
    background: #fff;
    border-bottom: 1px solid #e4e6eb;
    padding: 18px 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: sticky;
    top: 0;
    z-index: 20;
    min-height: 70px;
}
.messenger-header .messenger-header-title {
    font-size: 1.18rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 auto;
}
.messenger-header .messenger-header-actions {
    display: none;
}
.messenger-header .messenger-header-actions i {
    font-size: 1.3rem;
    color: #888;
    cursor: pointer;
    transition: color 0.15s;
}
.messenger-header .messenger-header-actions i:hover {
    color: #1877f2;
}
.messenger-messages-area {
    flex: 1 1 auto;
    overflow-y: auto;
    min-height: 0;
    padding: 32px 0 24px 0;
    background: #f0f2f5;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.messenger-message-row {
    display: flex;
    align-items: flex-end;
    margin-bottom: 8px;
    padding: 0 32px;
}
.messenger-message-row.sent {
    justify-content: flex-end;
}
.messenger-message-row.received {
    justify-content: flex-start;
}
.messenger-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
}
.messenger-message-bubble {
    display: inline-block;
    max-width: 65vw;
    min-width: 36px;
    font-size: 1.05rem;
    line-height: 1.1;
    padding: 4px 12px;
    border-radius: 18px;
    margin: 0 0 2px 0;
    background: #f0f2f5;
    color: #222;
    box-shadow: none;
    border: none;
    word-break: break-word;
    white-space: pre-line;
    vertical-align: middle;
}
.messenger-message-row.sent .messenger-message-bubble {
    background: #1877f2;
    color: #fff;
    border-bottom-right-radius: 6px;
    border-bottom-left-radius: 18px;
}
.messenger-message-row.received .messenger-message-bubble {
    background: #f0f2f5;
    color: #222;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 18px;
}
.messenger-message-time {
    font-size: 0.82rem;
    color: #888;
    margin: 0 0 0 8px;
    align-self: flex-end;
}
.messenger-input-area {
    background: #fff;
    border-top: 1px solid #e4e6eb;
    padding: 18px 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: sticky;
    bottom: 0;
    z-index: 20;
}
.messenger-input-box {
    flex: 1;
    background: #f5f6fa;
    border-radius: 22px;
    border: 1px solid #e4e6eb;
    padding: 10px 16px;
    font-size: 1.05rem;
    outline: none;
    resize: none;
    min-height: 38px;
    max-height: 120px;
}
.messenger-input-icon {
    background: none;
    border: none;
    color: #888;
    font-size: 1.3rem;
    cursor: pointer;
    margin: 0 2px;
    transition: color 0.15s;
}
.messenger-input-icon:hover {
    color: #1877f2;
}
.messenger-send-btn {
    background: #1877f2;
    color: #fff;
    border: none;
    border-radius: 22px;
    padding: 8px 24px;
    font-weight: 600;
    font-size: 1.05rem;
    cursor: pointer;
    transition: background 0.15s;
}
.messenger-send-btn:hover {
    background: #145dc1;
}
.attachment-preview {
    position: relative;
    transition: transform 0.2s ease;
}

.attachment-preview:hover {
    transform: scale(1.02);
}

.attachment-preview:hover .attachment-overlay {
    opacity: 1 !important;
}

#attachmentViewerModal .modal-body {
    max-height: 80vh;
    overflow: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}

#attachmentViewerContent {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

#attachmentViewerContent img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

#attachmentViewerContent img:hover {
    cursor: zoom-out;
}

.zoom-controls {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1060;
    background: rgba(0,0,0,0.7);
    border-radius: 10px;
    padding: 10px;
    display: none;
}

.zoom-controls.show {
    display: block;
}
</style>
@endpush

@section('content')
<style>
body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    background: #f0f2f5;
}
.messenger-root {
    display: flex;
    height: 100vh;
    background: #f0f2f5;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.messenger-sidebar {
    width: 350px;
    background: #fff;
    border-right: 1px solid #e4e6eb;
    display: flex;
    flex-direction: column;
    height: 100vh;
    min-width: 260px;
    max-width: 100vw;
}
.messenger-sidebar-header {
    padding: 18px 20px 10px 20px;
    border-bottom: 1px solid #e4e6eb;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.messenger-sidebar-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
    flex: 1;
}
.messenger-search {
    margin: 0 20px 12px 20px;
    position: relative;
}
.messenger-search input {
    width: 100%;
    padding: 8px 36px 8px 14px;
    border-radius: 20px;
    border: 1px solid #e4e6eb;
    background: #f5f6fa;
    font-size: 1rem;
}
.messenger-search .bi-search {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 1.1rem;
}
.messenger-chat-list {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 10px;
}
.messenger-chat-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    cursor: pointer;
    transition: background 0.15s;
    border: none;
    background: #fff;
    border-bottom: 1px solid #f0f2f5;
    text-decoration: none;
    color: inherit;
    position: relative;
}
.messenger-chat-item.active, .messenger-chat-item:hover {
    background: #f0f2f5;
}
.messenger-chat-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 14px;
}
.messenger-chat-info {
    flex: 1;
    min-width: 0;
}
.messenger-chat-name {
    font-weight: 600;
    font-size: 1.08rem;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.messenger-chat-preview {
    font-size: 0.97rem;
    color: #888;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.messenger-unread-dot {
    width: 10px;
    height: 10px;
    background: #1877f2;
    border-radius: 50%;
    margin-left: 8px;
    display: inline-block;
}
.messenger-main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100vh;
    min-width: 0;
    position: relative;
    background: #f0f2f5;
}
.messenger-header {
    background: #fff;
    border-bottom: 1px solid #e4e6eb;
    padding: 18px 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: sticky;
    top: 0;
    z-index: 20;
    min-height: 70px;
}
.messenger-header .messenger-header-title {
    font-size: 1.18rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 auto;
}
.messenger-header .messenger-header-actions {
    display: none;
}
.messenger-header .messenger-header-actions i {
    font-size: 1.3rem;
    color: #888;
    cursor: pointer;
    transition: color 0.15s;
}
.messenger-header .messenger-header-actions i:hover {
    color: #1877f2;
}
.messenger-messages-area {
    flex: 1 1 auto;
    overflow-y: auto;
    min-height: 0;
    padding: 32px 0 24px 0;
    background: #f0f2f5;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.messenger-message-row {
    display: flex;
    align-items: flex-end;
    margin-bottom: 8px;
    padding: 0 32px;
}
.messenger-message-row.sent {
    justify-content: flex-end;
}
.messenger-message-row.received {
    justify-content: flex-start;
}
.messenger-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
}
.messenger-message-bubble {
    display: inline-block;
    max-width: 65vw;
    min-width: 36px;
    font-size: 1.05rem;
    line-height: 1.1;
    padding: 4px 12px;
    border-radius: 18px;
    margin: 0 0 2px 0;
    background: #f0f2f5;
    color: #222;
    box-shadow: none;
    border: none;
    word-break: break-word;
    white-space: pre-line;
    vertical-align: middle;
}
.messenger-message-row.sent .messenger-message-bubble {
    background: #1877f2;
    color: #fff;
    border-bottom-right-radius: 6px;
    border-bottom-left-radius: 18px;
}
.messenger-message-row.received .messenger-message-bubble {
    background: #f0f2f5;
    color: #222;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 18px;
}
.messenger-message-time {
    font-size: 0.82rem;
    color: #888;
    margin: 0 0 0 8px;
    align-self: flex-end;
}
.messenger-input-area {
    background: #fff;
    border-top: 1px solid #e4e6eb;
    padding: 18px 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: sticky;
    bottom: 0;
    z-index: 20;
}
.messenger-input-box {
    flex: 1;
    background: #f5f6fa;
    border-radius: 22px;
    border: 1px solid #e4e6eb;
    padding: 10px 16px;
    font-size: 1.05rem;
    outline: none;
    resize: none;
    min-height: 38px;
    max-height: 120px;
}
.messenger-input-icon {
    background: none;
    border: none;
    color: #888;
    font-size: 1.3rem;
    cursor: pointer;
    margin: 0 2px;
    transition: color 0.15s;
}
.messenger-input-icon:hover {
    color: #1877f2;
}
.messenger-send-btn {
    background: #1877f2;
    color: #fff;
    border: none;
    border-radius: 22px;
    padding: 8px 24px;
    font-weight: 600;
    font-size: 1.05rem;
    cursor: pointer;
    transition: background 0.15s;
}
.messenger-send-btn:hover {
    background: #145dc1;
}
@media (max-width: 900px) {
    .messenger-root {
        flex-direction: column;
        height: 100vh;
    }
    .messenger-sidebar {
        width: 100vw;
        min-width: 0;
        max-width: 100vw;
        height: 60vh;
        border-right: none;
        border-bottom: 1px solid #e4e6eb;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 10;
        transition: left 0.2s;
        background: #fff;
    }
    .messenger-sidebar.hide-mobile {
        left: -100vw;
    }
    .messenger-main-content {
        height: 100vh;
        min-height: 40vh;
        width: 100vw;
        min-width: 0;
        background: #f0f2f5;
        position: relative;
    }
    .messenger-main-content.hide-mobile {
        display: none !important;
    }
    .messenger-header {
        padding: 10px 4vw;
        min-height: 56px;
    }
    .messenger-header .messenger-header-title {
        font-size: 1.01rem;
        gap: 8px;
    }
    .messenger-header img {
        width: 30px !important;
        height: 30px !important;
    }
    .messenger-messages-area {
        padding: 16px 0 12px 0;
    }
    .messenger-message-row, .messenger-messages-area {
        padding-left: 4vw;
        padding-right: 4vw;
    }
    .messenger-message-bubble {
        font-size: 0.95rem;
        padding: 3px 7px;
        max-width: 90vw;
    }
    .messenger-input-area {
        padding: 10px 4vw;
    }
    .messenger-input-box {
        font-size: 0.95rem;
        padding: 7px 8px;
    }
    .messenger-send-btn {
        padding: 7px 12px;
        font-size: 0.95rem;
    }
}
</style>
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
    <div class="modal fade" id="newMessageModalClient" tabindex="-1" aria-labelledby="newMessageModalLabelClient" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newMessageModalLabelClient">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Select Manager</label>
                            <select class="form-select" id="manager_id" name="manager_id" required>
                                <option value="">Choose a manager...</option>
                                @foreach(\App\Models\User::where('role', 'manager')->get() as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@if(auth()->user()->user_type === 'admin')
    <!-- New Message Modal for Admin -->
    <div class="modal fade" id="newMessageModalAdmin" tabindex="-1" aria-labelledby="newMessageModalLabelAdmin" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newMessageModalLabelAdmin">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="participant_select_admin" class="form-label">Start conversation with</label>
                            <select class="form-control" id="participant_select_admin" required>
                                <option value="">Select Client or Supplier</option>
                                @foreach(\App\Models\User::where('user_type', 'company')->whereHas('company', function($q){ $q->where('designation', 'client'); })->get() as $client)
                                    <option value="client_{{ $client->id }}">Client: {{ $client->company->company_name }}</option>
                                @endforeach
                                @foreach(\App\Models\Supplier::all() as $supplier)
                                    <option value="supplier_{{ $supplier->id }}">Supplier: {{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="client_id" id="client_id_hidden_admin">
                            <input type="hidden" name="supplier_id" id="supplier_id_hidden_admin">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
<div class="modal fade" id="deleteConversationModal" tabindex="-1" aria-labelledby="deleteConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConversationModalLabel">Delete Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this conversation? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteConversation">Delete</button>
            </div>
        </div>
    </div>
</div>
<div id="contextMenu" class="position-fixed bg-white border rounded shadow-sm" style="display:none; z-index:9999; min-width:140px;"></div>
<!-- Start Conversation Modal -->
<div class="modal fade" id="startConversationModal" tabindex="-1" aria-labelledby="startConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startConversationModalLabel">Start Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const searchInput = document.getElementById('participant_search_start');
                            const resultsDiv = document.getElementById('participant_search_results_start');
                            const clientIdHidden = document.getElementById('client_id_hidden_start');
                            const supplierIdHidden = document.getElementById('supplier_id_hidden_start');
                            let searchTimeout;
                            searchInput.addEventListener('input', function() {
                                clearTimeout(searchTimeout);
                                const term = searchInput.value.trim();
                                clientIdHidden.value = '';
                                supplierIdHidden.value = '';
                                clientIdHidden.name = '';
                                supplierIdHidden.name = '';
                                if (term.length < 2) {
                                    resultsDiv.style.display = 'none';
                                    resultsDiv.innerHTML = '';
                                    return;
                                }
                                searchTimeout = setTimeout(function() {
                                    fetch(`{{ route('admin.companies.search-for-chat') }}?search=${encodeURIComponent(term)}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.data && data.data.length > 0) {
                                                resultsDiv.innerHTML = data.data.map(item =>
                                                    `<a href="#" class="list-group-item list-group-item-action d-flex align-items-center" data-id="${item.id}" data-designation="${item.designation.toLowerCase()}">
                                                        <span class="fw-semibold flex-grow-1">${item.text}</span>
                                                        <span class="badge bg-${item.designation.toLowerCase() === 'client' ? 'primary' : 'success'} ms-2">${item.designation}</span>
                                                    </a>`
                                                ).join('');
                                                resultsDiv.style.display = 'block';
                                            } else {
                                                resultsDiv.innerHTML = '<div class="list-group-item">No results found</div>';
                                                resultsDiv.style.display = 'block';
                                            }
                                        });
                                }, 250);
                            });
                            resultsDiv.addEventListener('click', function(e) {
                                if (e.target.closest('.list-group-item-action')) {
                                    e.preventDefault();
                                    const item = e.target.closest('.list-group-item-action');
                                    const id = item.getAttribute('data-id');
                                    const designation = item.getAttribute('data-designation');
                                    clientIdHidden.value = '';
                                    supplierIdHidden.value = '';
                                    clientIdHidden.name = '';
                                    supplierIdHidden.name = '';
                                    if (designation === 'client') {
                                        clientIdHidden.name = 'client_id';
                                        clientIdHidden.value = id;
                                    } else if (designation === 'supplier') {
                                        supplierIdHidden.name = 'supplier_id';
                                        supplierIdHidden.value = id;
                                    }
                                    searchInput.value = item.querySelector('.fw-semibold').textContent;
                                    resultsDiv.style.display = 'none';
                                }
                            });
                            document.addEventListener('click', function(e) {
                                if (!e.target.closest('#participant_search_start, #participant_search_results_start')) {
                                    resultsDiv.style.display = 'none';
                                }
                            });
                        });
                        </script>
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
        </div>
    </div>
</div>
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
<script>
// Modern AJAX-based messaging system
class MessengerApp {
    constructor() {
        this.conversationId = {{ $conversation->id ?? 'null' }};
        this.currentUserId = {{ auth()->id() }};
        this.messagesArea = document.getElementById('messagesArea');
        this.messageForm = document.getElementById('messageForm');
        this.messageContent = document.getElementById('messageContent');
        this.fileInput = document.getElementById('fileInput');
        this.attachBtn = document.getElementById('attachBtn');
        this.attachmentPreview = document.getElementById('attachmentPreview');
        this.sendBtn = document.querySelector('.messenger-send-btn');
        
        // Track processed message IDs to prevent duplicates
        this.processedMessageIds = new Set();
        this.lastMessageId = null;
        
        // Debounce sidebar updates
        this.sidebarUpdateTimeout = null;
        this.lastSidebarUpdate = 0;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupAutoResize();
        this.scrollToBottom();
        this.initializeMessageTracking();
        this.setupRealTimeUpdates();
        // Update sidebar on page load to show current state
        this.updateSidebar();
    }

    initializeMessageTracking() {
        // Clear processed IDs to start fresh
        this.processedMessageIds.clear();
        
        // Track existing messages to prevent duplicates
        const existingMessages = this.messagesArea.querySelectorAll('[data-message-id]');
        existingMessages.forEach(message => {
            const messageId = message.getAttribute('data-message-id');
            this.processedMessageIds.add(parseInt(messageId));
        });
        
        // Set the last message ID for real-time updates
        if (existingMessages.length > 0) {
            const lastMessage = existingMessages[existingMessages.length - 1];
            this.lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
        }
        
        console.log('Initialized message tracking. Processed IDs:', Array.from(this.processedMessageIds));
    }

    setupEventListeners() {
        // Form submission
        this.messageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Enter key handling
        this.messageContent.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // File attachment
        this.attachBtn.addEventListener('click', () => {
            this.fileInput.click();
        });

        this.fileInput.addEventListener('change', (e) => {
            this.handleFileSelection(e.target.files[0]);
        });

        // Auto-resize textarea
        this.messageContent.addEventListener('input', () => {
            this.autoResizeTextarea();
        });
    }

    setupAutoResize() {
        this.autoResizeTextarea();
    }

    autoResizeTextarea() {
        this.messageContent.style.height = 'auto';
        this.messageContent.style.height = Math.min(this.messageContent.scrollHeight, 120) + 'px';
    }

    handleFileSelection(file) {
        if (!file) {
            this.clearAttachmentPreview();
            return;
        }

        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            this.fileInput.value = '';
            this.clearAttachmentPreview();
            return;
        }

        this.showAttachmentPreview(file);
    }

    showAttachmentPreview(file) {
        const icon = this.getFileIcon(file);
        const sizeText = this.formatFileSize(file.size);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.attachmentPreview.innerHTML = `
                    <div class="d-flex align-items-center bg-light rounded-3 p-2 border position-relative">
                        <img src="${e.target.result}" style="max-width:60px;max-height:60px;border-radius:8px;margin-right:10px;">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">${file.name}</div>
                            <div class="text-muted small">${sizeText}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-1 p-0" 
                                style="border-radius:50%;" onclick="messengerApp.clearAttachmentPreview()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
                this.attachmentPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            this.attachmentPreview.innerHTML = `
                <div class="d-flex align-items-center bg-light rounded-3 p-2 border position-relative">
                    <i class="bi ${icon} fs-3 me-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${file.name}</div>
                        <div class="text-muted small">${sizeText}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-1 p-0" 
                            style="border-radius:50%;" onclick="messengerApp.clearAttachmentPreview()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            this.attachmentPreview.style.display = 'block';
        }
    }

    clearAttachmentPreview() {
        this.attachmentPreview.innerHTML = '';
        this.attachmentPreview.style.display = 'none';
        this.fileInput.value = '';
    }

    getFileIcon(file) {
        if (file.type.startsWith('image/')) {
            return 'bi-file-earmark-image text-warning';
        } else if (file.type.startsWith('video/')) {
            return 'bi-file-earmark-play text-danger';
        } else if (file.type.startsWith('audio/')) {
            return 'bi-file-earmark-music text-info';
        } else if (file.type.includes('pdf')) {
            return 'bi-file-earmark-pdf text-danger';
        } else if (file.type.includes('word') || file.type.includes('document')) {
            return 'bi-file-earmark-word text-primary';
        } else if (file.type.includes('excel') || file.type.includes('spreadsheet')) {
            return 'bi-file-earmark-excel text-success';
        } else if (file.type.includes('powerpoint') || file.type.includes('presentation')) {
            return 'bi-file-earmark-ppt text-warning';
        } else {
            return 'bi-file-earmark text-secondary';
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    async sendMessage() {
        const content = this.messageContent.value.trim();
        const file = this.fileInput.files[0];

        if (!content && !file) {
            alert('Please enter a message or attach a file.');
            return;
        }

        // Disable send button and show loading state
        this.sendBtn.disabled = true;
        this.sendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        const formData = new FormData();
        formData.append('content', content);
        formData.append('_token', '{{ csrf_token() }}');
        if (file) {
            formData.append('file', file);
        }

        try {
            const response = await fetch(`{{ route('messages.store', $conversation ?? 1) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const messageData = await response.json();
                this.addMessageToUI(messageData);
                this.clearForm();
                // Update sidebar immediately after sending message
                this.updateSidebar();
            } else {
                const errorData = await response.json();
                alert(errorData.message || 'Failed to send message.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        } finally {
            // Re-enable send button
            this.sendBtn.disabled = false;
            this.sendBtn.innerHTML = 'Send';
        }
    }

    addMessageToUI(messageData) {
        const messageId = parseInt(messageData.id);
        
        // Check if message already exists in processed IDs
        if (this.processedMessageIds.has(messageId)) {
            console.log('Message already processed, skipping duplicate:', messageId);
            return;
        }

        // Double-check if message exists in DOM
        const existingMessage = this.messagesArea.querySelector(`[data-message-id="${messageId}"]`);
        if (existingMessage) {
            console.log('Message already exists in DOM, skipping duplicate:', messageId);
            this.processedMessageIds.add(messageId); // Add to processed set anyway
            return;
        }

        // Add message ID to processed set
        this.processedMessageIds.add(messageId);
        this.lastMessageId = messageId;

        const messageHtml = this.createMessageHTML(messageData);
        this.messagesArea.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();
        
        console.log('Added new message:', messageId);
    }

    createMessageHTML(message) {
        const isCurrentUser = message.sender_id === this.currentUserId;
        const time = new Date(message.created_at).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        let attachmentHtml = '';
        if (message.file_path || message.image) {
            const fileUrl = `/storage/${message.file_path || message.image}`;
            const fileName = message.file_name || 'Download';
            const isImage = message.file_type && message.file_type.startsWith('image/');
            
            if (isImage) {
                attachmentHtml = `
                    <div class="mt-2">
                        <div class="attachment-preview" 
                             onclick="messengerApp.openAttachmentViewer('${fileUrl}', '${fileName}', 'image')"
                             style="cursor: pointer; display: inline-block; position: relative;">
                            <img src="${fileUrl}" 
                                 alt="attachment" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                            <div class="attachment-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                <i class="bi bi-zoom-in text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                attachmentHtml = `
                    <div class="mt-2">
                        <div class="attachment-preview" 
                             onclick="messengerApp.openAttachmentViewer('${fileUrl}', '${fileName}', 'file')"
                             style="cursor: pointer; display: inline-block; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                            <i class="bi bi-file-earmark me-2"></i>${fileName}
                        </div>
                    </div>
                `;
            }
        }

        return `
            <div class="messenger-message-row ${isCurrentUser ? 'sent' : 'received'}" data-message-id="${message.id}">
                ${!isCurrentUser ? `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(message.sender.name)}&background=0D8ABC&color=fff" class="messenger-message-avatar">` : ''}
                <div>
                    <div class="messenger-message-bubble">
                        ${message.content || ''}
                        ${attachmentHtml}
                    </div>
                    <div class="messenger-message-time">${time}</div>
                </div>
            </div>
        `;
    }

    clearForm() {
        this.messageContent.value = '';
        this.clearAttachmentPreview();
        this.autoResizeTextarea();
    }

    scrollToBottom() {
        if (this.messagesArea) {
            this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
        }
    }

    setupRealTimeUpdates() {
        // Temporarily disable Echo to prevent conflicts
        // if (typeof Echo !== 'undefined' && this.conversationId) {
        //     Echo.private(`conversation.${this.conversationId}`)
        //         .listen('NewMessage', (e) => {
        //             if (e.message.sender_id !== this.currentUserId) {
        //                 this.addMessageToUI(e.message);
        //                 // Update sidebar immediately when receiving new messages
        //                 this.updateSidebar();
        //             }
        //         });
        // }
        
        // No automatic polling - only update when messages are sent/received
        this.updateSidebarOnSend = true;
    }

    async updateSidebar() {
        const now = Date.now();
        
        // Prevent updates more frequent than 2 seconds
        if (now - this.lastSidebarUpdate < 2000) {
            console.log('Sidebar update skipped - too frequent');
            return;
        }
        
        // Clear any pending timeout
        if (this.sidebarUpdateTimeout) {
            clearTimeout(this.sidebarUpdateTimeout);
        }
        
        // Debounce the update
        this.sidebarUpdateTimeout = setTimeout(async () => {
            try {
                const response = await fetch('{{ route("messages.conversations.update") }}');
                if (response.ok) {
                    const conversations = await response.json();
                    this.updateConversationList(conversations);
                    this.lastSidebarUpdate = Date.now();
                    console.log('Sidebar updated successfully');
                }
            } catch (error) {
                console.error('Error updating sidebar:', error);
            }
        }, 500); // 500ms debounce
    }

    updateConversationList(conversations) {
        const chatList = document.querySelector('.messenger-chat-list');
        if (!chatList) return;

        // Clear existing conversations to prevent duplicates
        chatList.innerHTML = '';

        conversations.forEach(conversation => {
            const conversationHtml = `
                <a href="${conversation.url}" class="messenger-chat-item ${conversation.id == {{ $conversation->id ?? 'null' }} ? 'active' : ''}" data-conversation-id="${conversation.id}">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(conversation.name)}&background=0D8ABC&color=fff" class="messenger-chat-avatar">
                    <div class="messenger-chat-info">
                        <div class="messenger-chat-name">${conversation.name}</div>
                        <div class="messenger-chat-preview">${conversation.last_message}</div>
                    </div>
                    ${conversation.unread_count > 0 ? `<span class="messenger-unread-dot"></span>` : ''}
                </a>
            `;

            chatList.insertAdjacentHTML('beforeend', conversationHtml);
        });
    }

    openAttachmentViewer(url, name, type) {
        const modalContent = document.getElementById('attachmentViewerContent');
        modalContent.innerHTML = ''; // Clear previous content

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.alt = name;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.style.width = 'auto';
            img.style.height = 'auto';
            img.style.objectFit = 'contain';
            img.style.display = 'block';
            img.style.margin = 'auto';
            
            // Add loading state
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            
            img.onload = function() {
                this.style.opacity = '1';
                console.log('Image loaded successfully:', url);
            };
            
            img.onerror = function() {
                console.error('Failed to load image:', url);
                modalContent.innerHTML = `<div class="text-center text-muted">Failed to load image: ${name}</div>`;
            };
            
            modalContent.appendChild(img);
        } else {
            const a = document.createElement('a');
            a.href = url;
            a.download = name;
            a.textContent = name;
            a.className = 'btn btn-primary';
            modalContent.appendChild(a);
        }

        const modal = new bootstrap.Modal(document.getElementById('attachmentViewerModal'));
        modal.show();

        // Add event listeners for zoom/reset buttons
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const resetZoomBtn = document.getElementById('resetZoomBtn');
        const downloadBtn = document.getElementById('downloadAttachmentBtn');

        const img = modalContent.querySelector('img');
        if (img) {
            img.addEventListener('load', () => {
                this.updateZoomButtons(img.naturalWidth, img.naturalHeight);
            });
        }

        downloadBtn.addEventListener('click', () => {
            const a = modalContent.querySelector('a');
            if (a && a.href) {
                window.open(a.href, '_blank');
            }
        });

        zoomInBtn.addEventListener('click', () => this.zoomImage(img, 'in'));
        zoomOutBtn.addEventListener('click', () => this.zoomImage(img, 'out'));
        resetZoomBtn.addEventListener('click', () => this.resetImageZoom(img));

        // Close modal on outside click
        modalContent.addEventListener('click', (e) => {
            if (e.target === modalContent) {
                modal.hide();
            }
        });
    }

    updateZoomButtons(width, height) {
        const img = document.getElementById('attachmentViewerContent').querySelector('img');
        if (img) {
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const resetZoomBtn = document.getElementById('resetZoomBtn');

            if (width > img.offsetWidth) {
                zoomInBtn.style.display = 'inline-block';
            } else {
                zoomInBtn.style.display = 'none';
            }
            if (img.offsetWidth > 100) { // Smaller than 100px, zoom out
                zoomOutBtn.style.display = 'inline-block';
            } else {
                zoomOutBtn.style.display = 'none';
            }
            resetZoomBtn.style.display = 'inline-block';
        }
    }

    zoomImage(img, direction) {
        if (!img) return;
        const currentTransform = img.style.transform || 'scale(1)';
        const currentScale = parseFloat(currentTransform.replace('scale(', '').replace(')', ''));
        let newScale;
        if (direction === 'in') {
            newScale = currentScale * 1.2;
        } else {
            newScale = currentScale / 1.2;
        }
        img.style.transform = `scale(${newScale})`;
        this.updateZoomButtons(img.naturalWidth, img.naturalHeight);
    }

    resetImageZoom(img) {
        if (!img) return;
        img.style.transform = 'scale(1)';
        this.updateZoomButtons(img.naturalWidth, img.naturalHeight);
    }
}

// Initialize the messenger app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.messengerApp = new MessengerApp();
});

// Enhanced Enter key functionality for modal textareas
function addEnterKeyToModals() {
    document.querySelectorAll('.modal textarea[name="message"], .modal textarea[id="message"]').forEach(function(textarea) {
        textarea.removeEventListener('keydown', handleModalEnterKey);
        textarea.addEventListener('keydown', handleModalEnterKey);
    });
}

function handleModalEnterKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        
        const content = this.value.trim();
        
        if (content) {
            const form = this.closest('form');
            if (form) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.focus();
                    }
                });
                
                if (isValid) {
                    form.submit();
                }
            }
        }
    }
}

// Add Enter key functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    addEnterKeyToModals();
});

// Add Enter key functionality when modals are shown
document.addEventListener('shown.bs.modal', function(e) {
    addEnterKeyToModals();
});

// Periodically check for new modals
setInterval(addEnterKeyToModals, 1000);
</script>
@endpush 