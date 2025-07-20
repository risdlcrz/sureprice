@extends('layouts.app')

@push('meta')
<meta name="current-user-id" content="{{ auth()->id() }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-3 pe-0">
            <div class="bg-white rounded-4 shadow-sm p-0 h-100" style="min-height: 600px; max-height: 700px; overflow-y: auto;">
                <div class="d-flex align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold flex-grow-1"><i class="bi bi-chat-left-text me-2 text-primary"></i>Chats</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($conversations as $sideConversation)
                        <a href="{{ route('messages.show', $sideConversation) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ $sideConversation->id == $conversation->id ? 'active bg-primary text-white' : '' }}" style="border:0;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($sideConversation->getOtherParticipant(auth()->user())->name) }}&background=0D8ABC&color=fff" class="rounded-circle me-2" style="width:38px;height:38px;object-fit:cover;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $sideConversation->getOtherParticipant(auth()->user())->name }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 140px;">
                                    @if($sideConversation->messages->count() > 0)
                                        {{ Str::limit($sideConversation->messages->first()->content, 30) }}
                                    @else
                                        No messages yet
                                    @endif
                                </div>
                            </div>
                            @if($sideConversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                                <span class="message-badge bg-primary rounded-pill ms-2">{{ $sideConversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-7 ps-0">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 rounded-top-4" style="padding: 1.25rem 1.5rem;">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-chat-dots me-2 text-primary"></i>
                        Conversation with 
                        {{ $conversation->getOtherParticipant(auth()->user())->name }}
                    </h5>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body bg-light rounded-bottom-4" style="padding: 2rem 1.5rem 1rem 1.5rem;">
                    <div class="messages-container px-2 py-3 mb-3" style="height: 420px; overflow-y: auto; background: #f4f7fa; border-radius: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        @if($messages->count() > 0)
                            @foreach($messages as $message)
                                <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-3 position-relative group-message" data-message-id="{{ $message->id }}">
                                    @if($message->sender_id !== auth()->id())
                                        <div class="me-2 align-self-end">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($message->sender->name) }}&background=0D8ABC&color=fff" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                        </div>
                                    @endif
                                    <div class="d-flex flex-column align-items-{{ $message->sender_id === auth()->id() ? 'end' : 'start' }} position-relative">
                                        <div class="message-content {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }} messenger-bubble">
                                            <div class="d-flex align-items-center">
                                                <div class="message-text flex-grow-1">{{ $message->content }}</div>
                                            </div>
                                            @if($message->isImage())
                                                <a href="{{ $message->download_url }}" target="_blank">
                                                    <img src="{{ $message->download_url }}" alt="attachment" style="max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                </a>
                                            @endif
                                            @if($message->hasAttachment() && !$message->isImage())
                                                <a href="{{ $message->download_url }}" download>
                                                    <i class="bi bi-download"></i> Download {{ $message->getAttachmentName() }}
                                                </a>
                                            @endif
                                            @if($message->sender_id === auth()->id() || auth()->user()->user_type === 'admin')
                                                <form method="POST" action="{{ route('messages.attachment.remove', $message) }}" class="remove-attachment-form position-absolute top-0 end-0 m-1" data-message-id="{{ $message->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light p-0" style="border-radius:50%;"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    @if($message->sender_id === auth()->id())
                                        <div class="ms-2 align-self-end"></div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted">No messages yet. Start the conversation!</p>
                        @endif
                    </div>
                    <form action="{{ route('messages.store', $conversation) }}" method="POST" class="mt-3" enctype="multipart/form-data" id="messageForm">
                        @csrf
                        <div id="attachmentPreview" style="display:none; position:relative;" class="mb-2"></div>
                        <div class="input-group rounded-pill shadow-sm bg-white" style="overflow: hidden;">
                            <textarea name="content" class="form-control border-0 px-3 py-2" rows="1" placeholder="Type your message..." style="resize: none; background: transparent;" id="messageContent"></textarea>
                            <input type="file" name="file" class="d-none" id="fileInput">
                            <button type="button" class="btn btn-link px-2" id="attachBtn" title="Attach file">
                                <i class="bi bi-paperclip fs-4"></i>
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contextMenu" class="position-fixed bg-white border rounded shadow-sm" style="display:none; z-index:9999; min-width:140px;"></div>

@push('styles')
    @vite(['resources/css/messages/show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/messages-index.js'])
@endpush
@endsection 