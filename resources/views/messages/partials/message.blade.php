{{--
    One chat message. Mirrored by messageHtml() in resources/js/pages/messages-index.js — keep the two in sync.
    $message, $mine (bool), $grouped (bool: same sender as the previous message, so no avatar repeat)
--}}
@php
    $filePath = $message->file_path ?: $message->image;
    $fileName = $message->file_name ?: ($filePath ? basename($filePath) : null);
    $isImage = $filePath && ($message->file_type
        ? str_starts_with($message->file_type, 'image/')
        : (bool) preg_match('/\.(jpe?g|png|gif|webp)$/i', $filePath));
    $senderName = $message->sender->name ?? 'Deleted user';
@endphp
<div class="chat-msg {{ $mine ? 'is-mine' : 'is-theirs' }} @if($grouped) is-grouped @endif" data-message-id="{{ $message->id }}" data-sender-id="{{ $message->sender_id }}">
    @unless($mine)
        <x-avatar :user="$message->sender" :name="$senderName" size="sm" class="chat-avatar chat-avatar--sm" />
    @endunless
    <div class="chat-msg__body">
        @if($message->content)
            <div class="chat-bubble">{{ $message->content }}</div>
        @endif
        @if($filePath)
            @if($isImage)
                <button type="button" class="chat-attachment chat-attachment--image" data-attachment-url="{{ Storage::url($filePath) }}"
                    data-attachment-name="{{ $fileName }}" data-attachment-type="image" data-download-url="{{ route('messages.attachment.download', $message) }}">
                    <img src="{{ Storage::url($filePath) }}" alt="{{ $fileName }}" loading="lazy">
                </button>
            @else
                <button type="button" class="chat-attachment chat-attachment--file" data-attachment-url="{{ Storage::url($filePath) }}"
                    data-attachment-name="{{ $fileName }}" data-attachment-type="file" data-download-url="{{ route('messages.attachment.download', $message) }}">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    <span>{{ $fileName }}</span>
                </button>
            @endif
        @endif
        <time class="chat-msg__time" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->timezone('Asia/Manila')->format('g:i A') }}</time>
    </div>
</div>
