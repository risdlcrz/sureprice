@extends('layouts.app')

@php
    $me = auth()->user();
    $tz = 'Asia/Manila';

    // [display name, role label] for the other side of a conversation
    $who = function ($conv) use ($me) {
        $other = $conv?->getOtherParticipant($me);
        if (!$other) {
            return ['Deleted user', null];
        }
        if ($other instanceof \App\Models\Company) {
            return [$other->company_name ?: 'Supplier', 'Supplier'];
        }
        $label = match (true) {
            $other->role === 'manager' => 'Manager',
            $other->user_type === 'admin' || $other->role === 'admin' => 'Administrator',
            $other->company?->designation === 'supplier' => 'Supplier',
            $other->company?->designation === 'client' || $other->role === 'client' => 'Client',
            default => null,
        };

        return [$other->company?->company_name ?: ($other->name ?: 'User'), $label];
    };
    $initial = fn (?string $name) => Str::upper(Str::substr(trim((string) $name) ?: '?', 0, 1));
    $shortTime = function ($date) use ($tz) {
        if (!$date) {
            return '';
        }
        $d = $date->copy()->timezone($tz);

        return $d->isToday() ? $d->format('g:i A') : ($d->isCurrentWeek() ? $d->format('D') : $d->format('M j'));
    };
    $dayLabel = function ($date) use ($tz) {
        $d = $date->copy()->timezone($tz);

        return $d->isToday() ? 'Today' : ($d->isYesterday() ? 'Yesterday' : $d->format('l, F j, Y'));
    };

    $isClient = $me->user_type === 'company' && $me->company?->designation === 'client';
    $isSupplier = $me->user_type === 'company' && $me->company?->designation === 'supplier';
    $isStaff = $me->role === 'manager' || $me->user_type === 'admin' || $me->role === 'admin';
    // MessageController::startConversation: managers/admins write to a client or supplier, clients to a manager or admin, suppliers to an admin
    $canStart = $isStaff || $isClient || $isSupplier;
    $admins = fn () => \App\Models\User::where(fn ($q) => $q->where('user_type', 'admin')->orWhere('role', 'admin'))->orderBy('name')->get();
    $openThread = isset($conversation, $messages) && $conversation;
    [$otherName, $otherRole] = $openThread ? $who($conversation) : [null, null];
@endphp

@push('styles')
@vite('resources/css/pages/messages.css')
@endpush

@section('content')
<div class="chat @if($openThread && request()->has('conversation')) is-thread-open @endif" id="chat">
    {{-- Conversation list --}}
    <aside class="chat-sidebar" aria-label="Conversations">
        <div class="chat-sidebar__head">
            <h1 class="chat-sidebar__title">Messages</h1>
            @if($canStart)
                <button type="button" class="btn btn-success btn-sm chat-new-btn" data-bs-toggle="modal" data-bs-target="#startConversationModal">
                    <i class="bi bi-pencil-square me-1" aria-hidden="true"></i> New
                </button>
            @endif
        </div>
        <div class="chat-sidebar__search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" id="chatSearch" class="form-control form-control-sm" placeholder="Search conversations" aria-label="Search conversations" autocomplete="off">
        </div>
        <nav class="chat-list" id="chatList">
            @forelse($conversations as $c)
                @php
                    [$name, $role] = $who($c);
                    $last = $c->messages->first();
                    $unread = $c->messages->where('is_read', false)->where('sender_id', '!=', $me->id)->count();
                    $active = $openThread && $c->id == $conversation->id;
                @endphp
                <a href="{{ route('messages.index', ['conversation' => $c->id]) }}" class="chat-list__item @if($active) is-active @endif @if($unread) is-unread @endif"
                   data-conversation-id="{{ $c->id }}" @if($active) aria-current="true" @endif>
                    <span class="chat-avatar" aria-hidden="true">{{ $initial($name) }}</span>
                    <span class="chat-list__text">
                        <span class="chat-list__row">
                            <span class="chat-list__name">{{ $name }}</span>
                            <span class="chat-list__time">{{ $shortTime($last?->created_at ?? $c->last_message_at) }}</span>
                        </span>
                        <span class="chat-list__row">
                            <span class="chat-list__preview">
                                @if($last)
                                    @if($last->sender_id === $me->id)<span class="text-muted">You: </span>@endif{{ $last->content ? Str::limit($last->content, 60) : 'Sent an attachment' }}
                                @else
                                    No messages yet
                                @endif
                            </span>
                            @if($unread)<span class="chat-list__badge" aria-label="{{ $unread }} unread">{{ $unread }}</span>@endif
                        </span>
                    </span>
                </a>
            @empty
                <div class="chat-empty chat-empty--list">
                    <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                    <p class="mb-2">No conversations yet.</p>
                    @if($canStart)
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#startConversationModal">Start one</button>
                    @endif
                </div>
            @endforelse
            <p class="chat-empty chat-empty--search text-muted small" id="chatSearchEmpty" hidden>No conversations match your search.</p>
        </nav>
    </aside>

    {{-- Thread --}}
    <section class="chat-thread" aria-label="Conversation">
        @if($openThread)
            <header class="chat-thread__head">
                <button type="button" class="btn btn-sm btn-light chat-back" id="chatBack" aria-label="Back to conversations"><i class="bi bi-arrow-left"></i></button>
                <span class="chat-avatar" aria-hidden="true">{{ $initial($otherName) }}</span>
                <div class="chat-thread__who">
                    <div class="chat-thread__name">{{ $otherName }}</div>
                    @if($otherRole)<div class="chat-thread__role">{{ $otherRole }}</div>@endif
                </div>
                <div class="dropdown ms-auto">
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Conversation options">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteConversationModal">
                                <i class="bi bi-trash me-2"></i>Delete conversation
                            </button>
                        </li>
                    </ul>
                </div>
            </header>

            <div class="chat-thread__messages" id="messagesArea" aria-live="polite">
                @php $prev = null; @endphp
                @forelse($messages as $message)
                    @php
                        $newDay = !$prev || !$prev->created_at->copy()->timezone($tz)->isSameDay($message->created_at->copy()->timezone($tz));
                        $grouped = !$newDay && $prev->sender_id === $message->sender_id && $prev->created_at->diffInMinutes($message->created_at) < 5;
                    @endphp
                    @if($newDay)
                        <div class="chat-day" data-day="{{ $message->created_at->copy()->timezone($tz)->toDateString() }}"><span>{{ $dayLabel($message->created_at) }}</span></div>
                    @endif
                    @include('messages.partials.message', ['message' => $message, 'mine' => $message->sender_id === $me->id, 'grouped' => $grouped])
                    @php $prev = $message; @endphp
                @empty
                    <div class="chat-empty" id="threadEmpty">
                        <i class="bi bi-chat-dots" aria-hidden="true"></i>
                        <p class="mb-0">No messages yet. Say hello to {{ $otherName }}!</p>
                    </div>
                @endforelse
            </div>

            <form action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data" class="chat-composer" id="messageForm">
                @csrf
                <div class="chat-composer__attachment" id="attachmentPreview" hidden></div>
                <div class="chat-composer__row">
                    <button type="button" class="btn chat-composer__icon" id="attachBtn" title="Attach a file (max 10 MB)" aria-label="Attach a file">
                        <i class="bi bi-paperclip"></i>
                    </button>
                    <input type="file" name="file" class="d-none" id="fileInput">
                    <textarea name="content" class="chat-composer__input" rows="1" maxlength="1000" placeholder="Write a message…" id="messageContent" aria-label="Message"></textarea>
                    <button type="submit" class="btn btn-success chat-composer__send" id="sendBtn" aria-label="Send">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
                <div class="chat-composer__hint">Enter to send · Shift + Enter for a new line</div>
            </form>
        @else
            <div class="chat-empty chat-empty--thread">
                <i class="bi bi-chat-dots" aria-hidden="true"></i>
                <h2 class="h5 mb-1">Your messages</h2>
                <p class="text-muted mb-3">Pick a conversation on the left{{ $canStart ? ', or start a new one' : '' }}.</p>
                @if($canStart)
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#startConversationModal"><i class="bi bi-pencil-square me-1"></i> New message</button>
                @endif
            </div>
        @endif
    </section>
</div>

{{-- Start conversation --}}
@if($canStart)
    <x-modal-dialog id="startConversationModal">
        <x-slot:title>New message</x-slot:title>
        <form action="{{ route('messages.start') }}" method="POST" id="startConversationForm">
            @csrf
            <div class="modal-body">
                @if($isStaff)
                    <div class="mb-3 position-relative">
                        <label for="participant_search_start" class="form-label">To</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" id="participant_search_start" placeholder="Search for a client or supplier" autocomplete="off" required>
                        </div>
                        <div id="participant_search_results_start" class="list-group chat-search-results" hidden></div>
                        <input type="hidden" id="client_id_hidden_start">
                        <input type="hidden" id="supplier_id_hidden_start">
                    </div>
                @elseif($isClient)
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">To</label>
                        <select class="form-select" id="manager_id" name="manager_id" required>
                            <option value="">Choose who to message</option>
                            <optgroup label="Project managers">
                                @foreach(\App\Models\User::where('role', 'manager')->orderBy('name')->get() as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Administrators">
                                @foreach($admins() as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                @elseif($isSupplier)
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">To</label>
                        <select class="form-select" id="admin_id" name="admin_id" required>
                            <option value="">Choose an administrator</option>
                            @foreach($admins() as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label for="startMessage" class="form-label">Message</label>
                    <textarea class="form-control" id="startMessage" name="message" rows="4" maxlength="1000" required placeholder="Write your message…"></textarea>
                    <div class="form-text">Enter to send · Shift + Enter for a new line</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-send-fill me-1"></i> Send</button>
            </div>
        </form>
    </x-modal-dialog>
@endif

@if($openThread)
    <x-confirm-modal id="deleteConversationModal" title="Delete conversation" :action="route('messages.destroy', $conversation)" method="DELETE" confirm="Delete" tone="danger">
        Delete your conversation with <strong>{{ $otherName }}</strong>? All of its messages and attachments will be removed. This can't be undone.
    </x-confirm-modal>
@endif

{{-- Attachment viewer --}}
<div class="modal fade" id="attachmentViewerModal" tabindex="-1" aria-labelledby="attachmentViewerTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-truncate" id="attachmentViewerTitle">Attachment</h5>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomOutBtn" aria-label="Zoom out" hidden><i class="bi bi-zoom-out"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomInBtn" aria-label="Zoom in" hidden><i class="bi bi-zoom-in"></i></button>
                    <a href="#" class="btn btn-sm btn-success" id="downloadAttachmentBtn"><i class="bi bi-download me-1"></i> Download</a>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body chat-viewer" id="attachmentViewerContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, {
    conversationId: {{ Js::from($openThread ? $conversation->id : null) }},
    currentUserId: {{ Js::from($me->id) }},
    sendUrl: {{ Js::from($openThread ? route('messages.store', $conversation) : null) }},
    participantSearchUrl: {{ Js::from(route('admin.companies.search-for-chat')) }},
});</script>
@vite('resources/js/pages/messages-index.js')
@endpush
