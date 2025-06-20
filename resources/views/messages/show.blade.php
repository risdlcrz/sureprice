@extends('layouts.app')

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
                                <span class="badge bg-primary rounded-pill ms-2">{{ $sideConversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}</span>
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
                                            @if(isset($message->image) && $message->image)
                                                <div class="mt-2 position-relative attachment-container">
                                                    <img src="{{ asset('storage/' . $message->image) }}" alt="attachment" style="max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                    @if($message->sender_id === auth()->id() || auth()->user()->user_type === 'admin')
                                                        <form method="POST" action="{{ route('messages.attachment.remove', $message) }}" class="remove-attachment-form position-absolute top-0 end-0 m-1" data-message-id="{{ $message->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-light p-0" style="border-radius:50%;"><i class="bi bi-x-lg"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <small class="message-time mt-1 {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.85rem; opacity: 0.7;">
                                            {{ $message->created_at->timezone('Asia/Manila')->format('g:i A') }}
                                            @if($message->is_read && $message->sender_id === auth()->id())
                                                <span class="ms-1">✓✓</span>
                                            @endif
                                        </small>
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
                            <textarea name="content" class="form-control border-0 px-3 py-2" rows="1" placeholder="Type your message..." style="resize: none; background: transparent;" required></textarea>
                            <input type="file" name="image" accept="image/*" class="d-none" id="fileInput">
                            <button type="button" class="btn btn-link px-2" id="attachBtn" title="Attach image">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/timezone.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/utc.js"></script>
<script>
    dayjs.extend(dayjs_plugin_utc);
    // Scroll to bottom of messages container
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Listen for new messages (only if Echo is available)
    if (typeof Echo !== 'undefined') {
        Echo.private('conversation.{{ $conversation->id }}')
            .listen('NewMessage', (e) => {
                const message = e.message;
                const isCurrentUser = message.sender_id === {{ auth()->id() }};
                let imageHtml = '';
                if (message.image) {
                    imageHtml = `<div class=\"mt-2\"><img src=\"/storage/${message.image}\" alt=\"attachment\" style=\"max-width: 200px; max-height: 200px; border-radius: 8px;\"></div>`;
                }
                const messageHtml = `
                    <div class=\"d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-3\">
                        <div class=\"d-flex flex-column align-items-${isCurrentUser ? 'end' : 'start'}\">
                            <div class=\"message-content ${isCurrentUser ? 'sent' : 'received' } messenger-bubble\">
                                <div class=\"message-text\">${message.content}</div>
                                ${imageHtml}
                            </div>
                            <small class=\"message-time mt-1 ${isCurrentUser ? 'text-white-50' : 'text-muted'}\" style="font-size: 0.85rem; opacity: 0.7;">
                                ${message.created_at}
                                ${message.is_read && isCurrentUser ? '<span class=\"ms-1\">✓✓</span>' : ''}
                            </small>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
    }

    // Auto-scroll to bottom when new messages are added
    if (messagesContainer) {
        const observer = new MutationObserver(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });

        observer.observe(messagesContainer, {
            childList: true,
            subtree: true
        });
    }

    // File input trigger
    document.getElementById('attachBtn').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });
    // File preview
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('attachmentPreview');
        if (file) {
            let icon = '<i class="bi bi-file-earmark-image text-warning fs-3 me-2"></i>';
            if (!file.type.startsWith('image/')) {
                icon = '<i class="bi bi-file-earmark text-secondary fs-3 me-2"></i>';
            }
            preview.innerHTML = `<div class='d-flex align-items-center bg-light rounded-3 p-2 border position-relative'><span>${icon}</span><div><div class='fw-semibold'>${file.name}</div><div class='text-muted' style='font-size:0.9em;'>${(file.size/1024).toFixed(1)}KB</div></div><button type='button' id='removeAttachmentBtn' class='btn btn-sm btn-light position-absolute top-0 end-0 m-1 p-0' style='border-radius:50%;'><i class='bi bi-x-lg'></i></button></div>`;
            preview.style.display = '';
            document.getElementById('removeAttachmentBtn').onclick = function() {
                document.getElementById('fileInput').value = '';
                preview.innerHTML = '';
                preview.style.display = 'none';
            };
        } else {
            preview.innerHTML = '';
            preview.style.display = 'none';
        }
    });

    // Messenger-style right-click context menu for deleting messages
    let contextMenu = document.getElementById('contextMenu');
    document.querySelectorAll('.messenger-bubble').forEach(function(bubble) {
        bubble.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            // Only show for sender or admin
            const group = bubble.closest('.group-message');
            const messageId = group.getAttribute('data-message-id');
            const isSender = group.querySelector('.remove-attachment-form') || group.querySelector('form[action*="message.destroy"]');
            if (!isSender) return;
            contextMenu.innerHTML = `<button class='dropdown-item text-danger' id='deleteMsgBtn' data-message-id='${messageId}'><i class='bi bi-trash me-2'></i>Delete Message</button>`;
            contextMenu.style.display = 'block';
            contextMenu.style.left = e.pageX + 'px';
            contextMenu.style.top = e.pageY + 'px';
        });
    });
    document.addEventListener('click', function() { contextMenu.style.display = 'none'; });
    contextMenu.addEventListener('click', function(e) {
        if (e.target.closest('#deleteMsgBtn')) {
            const messageId = e.target.closest('#deleteMsgBtn').getAttribute('data-message-id');
            const form = document.querySelector(`.group-message[data-message-id='${messageId}'] form[action*='message.destroy']`);
            if (form && confirm('Delete this message?')) {
                fetch(form.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(res => {
                    if (res.ok) {
                        form.closest('.group-message').remove();
                    }
                });
            }
            contextMenu.style.display = 'none';
        }
    });
</script>
@endpush

<style>
.message-content {
    display: inline-block;
    max-width: 75%;
    min-width: 40px;
    width: auto;
    font-size: 1rem;
    line-height: 1.5;
    white-space: pre-line;
    word-break: break-word;
    overflow-wrap: break-word;
    padding: 8px 16px;
    border-radius: 18px;
    margin: 2px 0;
    background: #f0f2f5;
    color: #222;
    box-shadow: none;
    border: none;
}
.message-content.sent {
    background: #1877f2;
    color: #fff;
    text-align: right;
}
.message-content.received {
    background: #f0f2f5;
    color: #222;
    text-align: left;
}
</style>
@endsection 