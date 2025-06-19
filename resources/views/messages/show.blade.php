@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Conversation with 
                        @if(auth()->user()->user_type === 'admin')
                            {{ $conversation->client->name ?? 'Unknown Client' }}
                        @else
                            {{ $conversation->admin->name ?? 'Unknown Admin' }}
                        @endif
                    </h5>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                        Back to Messages
                    </a>
                </div>

                <div class="card-body">
                    <div class="messages-container" style="height: 400px; overflow-y: auto; padding: 15px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                        @if($messages->count() > 0)
                            @foreach($messages as $message)
                                <div class="message {{ $message->sender_id === auth()->id() ? 'text-end' : '' }} mb-3">
                                    <div class="message-content d-inline-block p-3 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}" 
                                         style="max-width: 70%; word-wrap: break-word;">
                                        <div class="message-text">{{ $message->content }}</div>
                                        <small class="message-time d-block {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('g:i A') }}
                                            @if($message->is_read && $message->sender_id === auth()->id())
                                                <span class="ms-1">✓✓</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted">No messages yet. Start the conversation!</p>
                        @endif
                    </div>

                    <form action="{{ route('messages.store', $conversation) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <textarea name="content" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
                
                const messageHtml = `
                    <div class="message ${isCurrentUser ? 'text-end' : ''} mb-3">
                        <div class="message-content d-inline-block p-3 rounded ${isCurrentUser ? 'bg-primary text-white' : 'bg-white border'}" 
                             style="max-width: 70%; word-wrap: break-word;">
                            <div class="message-text">${message.content}</div>
                            <small class="message-time d-block ${isCurrentUser ? 'text-white-50' : 'text-muted'}">
                                ${message.created_at}
                                ${message.is_read && isCurrentUser ? '<span class="ms-1">✓✓</span>' : ''}
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
</script>
@endpush
@endsection 