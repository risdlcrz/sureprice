<!-- Chat Header -->
<div class="flex items-center p-4 bg-white border-b border-gray-200">
    <div class="relative">
        <img src="{{ $otherUser->avatar ?? asset('images/default-avatar.png') }}" 
             alt="{{ $otherUser->name }}"
             class="w-10 h-10 rounded-full object-cover">
        @if($otherUser->is_online)
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></span>
        @endif
    </div>
    <div class="ml-4">
        <h3 class="text-sm font-medium text-gray-900 mb-0">{{ $otherUser->name }}</h3>
        <p class="text-xs text-gray-500 mb-0">
            {{ $otherUser->is_online ? 'Online' : 'Offline' }}
        </p>
    </div>
</div>
<!-- Messages -->
<div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
    @foreach($messages as $message)
    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
        <div class="flex items-end {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
            <img src="{{ $message->sender->avatar ?? asset('images/default-avatar.png') }}" 
                 alt="{{ $message->sender->name }}"
                 class="w-8 h-8 rounded-full object-cover {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
            <div class="max-w-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                @if($message->attachment_path)
                    @if(str_starts_with($message->attachment_type, 'image/'))
                        <img src="{{ Storage::url($message->attachment_path) }}" 
                             alt="Attachment"
                             class="max-w-xs rounded-lg mb-2">
                    @else
                        <a href="{{ Storage::url($message->attachment_path) }}" 
                           class="flex items-center text-sm underline mb-2"
                           target="_blank">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Attachment
                        </a>
                    @endif
                @endif
                <p class="text-sm">{{ $message->message }}</p>
                <div class="flex items-center justify-end mt-1 space-x-1">
                    <span class="text-xs opacity-75">
                        {{ $message->created_at->format('g:i A') }}
                    </span>
                    @if($message->sender_id === auth()->id())
                    <span class="text-xs">
                        @if($message->status === 'read')
                            <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        @elseif($message->status === 'delivered')
                            <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        @endif
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<!-- Message Input -->
<div class="p-4 bg-white border-t border-gray-200">
    <form id="message-form" class="flex items-center space-x-4" enctype="multipart/form-data">
        <button type="button" class="text-gray-400 hover:text-gray-600" id="attachment-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
        </button>
        <input type="file" id="attachment" name="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
        <div class="flex-1">
            <input type="text" 
                   id="message-input"
                   name="message"
                   class="w-full rounded-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                   placeholder="Type a message...">
        </div>
        <button type="submit" class="text-green-500 hover:text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </form>
</div>
@push('scripts')
<script>
    // AJAX send message
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var input = $('#message-input');
        var message = input.val();
        var attachment = $('#attachment')[0].files[0];
        if (!message && !attachment) return;
        var formData = new FormData(this);
        input.val('');
        $('#attachment').val('');
        form.find('button[type=submit]').prop('disabled', true);
        $.ajax({
            url: '{{ route('chat.store', $conversation->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                appendMessage(data, true);
                form.find('button[type=submit]').prop('disabled', false);
            },
            error: function() {
                alert('Failed to send message.');
                form.find('button[type=submit]').prop('disabled', false);
            }
        });
    });
    // Attachment button
    $('#attachment-button').on('click', function() {
        $('#attachment').click();
    });
    // Real-time updates with Echo
    window.Echo && window.Echo.private('conversation.{{ $conversation->id }}')
        .listen('NewMessageEvent', (e) => {
            appendMessage(e.message, false);
        });
    // Append message to chat
    function appendMessage(message, isOwn) {
        let html = '';
        let time = message.created_at ? (new Date(message.created_at)).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
        let avatar = message.sender && message.sender.avatar ? message.sender.avatar : '{{ asset('images/default-avatar.png') }}';
        let name = message.sender && message.sender.name ? message.sender.name : '';
        let bubbleClass = isOwn ? 'bg-success text-white justify-end flex-row-reverse' : 'bg-light text-dark justify-start flex-row';
        html += `<div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-2">`;
        html += `<div class="flex items-end ${isOwn ? 'flex-row-reverse' : 'flex-row'}">`;
        html += `<img src="${avatar}" alt="${name}" class="w-8 h-8 rounded-full object-cover ${isOwn ? 'ml-2' : 'mr-2'}">`;
        html += `<div class="max-w-lg ${isOwn ? 'bg-success text-white' : 'bg-light text-dark'} rounded-lg px-4 py-2">`;
        if (message.attachment_path && message.attachment_type && message.attachment_type.startsWith('image/')) {
            html += `<img src="${message.attachment_path}" alt="Attachment" class="max-w-xs rounded-lg mb-2">`;
        } else if (message.attachment_path) {
            html += `<a href="${message.attachment_path}" class="flex items-center text-sm underline mb-2" target="_blank">Attachment</a>`;
        }
        html += `<p class="text-sm">${message.message || ''}</p>`;
        html += `<div class="flex items-center justify-end mt-1 space-x-1"><span class="text-xs opacity-75">${time}</span></div>`;
        html += `</div></div></div>`;
        $('#messages-container').append(html);
        $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
    }
</script>
@endpush 