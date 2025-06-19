@extends('layouts.app')

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
            <h3>Chats</h3>
            <button class="btn btn-primary btn-sm" style="border-radius:50%;padding:6px 10px;" data-bs-toggle="modal" data-bs-target="#newMessageModalAdmin"><i class="bi bi-plus-lg"></i></button>
        </div>
        <div class="messenger-search">
            <input type="text" placeholder="Search Messenger..." id="sidebarSearchInput" autocomplete="off">
            <i class="bi bi-search"></i>
            <div id="sidebarSearchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none; top: 38px;"></div>
        </div>
        <div class="messenger-chat-list" id="chatList">
            @forelse($conversations as $sideConversation)
                <a href="{{ route('messages.index', ['conversation' => $sideConversation->id]) }}" class="messenger-chat-item @if(isset($conversation) && $sideConversation->id == $conversation->id) active @endif">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($sideConversation->getOtherParticipant(auth()->user())->name) }}&background=0D8ABC&color=fff" class="messenger-chat-avatar">
                    <div class="messenger-chat-info">
                        <div class="messenger-chat-name">{{ $sideConversation->getOtherParticipant(auth()->user())->name }}</div>
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
                    <div class="messenger-message-row {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        @if($message->sender_id !== auth()->id())
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($message->sender->name) }}&background=0D8ABC&color=fff" class="messenger-message-avatar">
                        @endif
                        <div>
                            <div class="messenger-message-bubble">
                                {{ $message->content }}
                                @if(isset($message->image) && $message->image)
                                    <div class="mt-2"><img src="{{ asset('storage/' . $message->image) }}" alt="attachment" style="max-width: 180px; max-height: 180px; border-radius: 8px;"></div>
                                @endif
                            </div>
                            <div class="messenger-message-time">{{ $message->created_at->format('g:i A') }}</div>
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
            <button type="button" class="messenger-input-icon" id="attachBtn" title="Attach image"><i class="bi bi-paperclip"></i></button>
            <textarea name="content" class="messenger-input-box" rows="1" placeholder="Type your message..."></textarea>
            <input type="file" name="image" accept="image/*" class="d-none" id="fileInput">
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
                            <label for="admin_id" class="form-label">Select Admin</label>
                            <select class="form-select" id="admin_id" name="admin_id" required>
                                <option value="">Choose an admin...</option>
                                @foreach(\App\Models\User::where('user_type', 'admin')->where('role', 'admin')->get() as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
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
                        <div class="mb-3 position-relative">
                            <label for="company_search_input" class="form-label">Search Client or Supplier</label>
                            <input type="text" id="company_search_input" name="company_search_input" class="form-control" placeholder="Type to search..." autocomplete="off" required>
                            <input type="hidden" name="client_id" id="company_id" required>
                            <div id="companySearchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></div>
                            <div class="form-text">Start typing to search for a client or supplier to message. Their type will be shown below.</div>
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
@endsection 

@push('scripts')
<script>
// ... (insert the merged scripts from previous index/show views here, including delete modal, right-click, file preview, etc.) ...
// Responsive sidebar toggle for Messenger mobile style
const sidebar = document.getElementById('messengerSidebar');
const content = document.getElementById('messengerContent');
const showSidebarBtn = document.getElementById('showSidebarBtn');
if (showSidebarBtn) {
    showSidebarBtn.addEventListener('click', function() {
        sidebar.classList.remove('hide-mobile');
        content.classList.add('hide-mobile');
    });
}
document.querySelectorAll('.messenger-sidebar .list-group-item').forEach(function(item) {
    item.addEventListener('click', function() {
        if (window.innerWidth <= 900) {
            sidebar.classList.add('hide-mobile');
            content.classList.remove('hide-mobile');
        }
    });
});
$(document).ready(function() {
    // Admin: Company search for chat (custom, not Select2)
    var $input = $('#company_search_input');
    var $results = $('#companySearchResults');
    var $hiddenId = $('#company_id');
    var searchTimeout;
    $input.on('input', function() {
        clearTimeout(searchTimeout);
        var term = $input.val().trim();
        $hiddenId.val('');
        if (term.length < 2) {
            $results.hide().empty();
            return;
        }
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route('admin.companies.search-for-chat') }}',
                data: { search: term },
                success: function(data) {
                    if (data.data && data.data.length > 0) {
                        var html = data.data.map(function(item) {
                            return `<a href="#" class="list-group-item list-group-item-action" data-id="${item.id}" data-name="${item.text}"><div><strong>${item.text}</strong><br><small class='text-muted'>${item.designation}</small>${item.email ? '<br><small>' + item.email + '</small>' : ''}</div></a>`;
                        }).join('');
                        $results.html(html).show();
                    } else {
                        $results.html('<div class="list-group-item">No results found</div>').show();
                    }
                }
            });
        }, 250);
    });
    $results.on('click', '.list-group-item-action', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $input.val(name);
        $hiddenId.val(id);
        $results.hide().empty();
    });
    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#company_search_input, #companySearchResults').length) {
            $results.hide();
        }
    });
    // Clear hidden id if input is cleared
    $input.on('change', function() {
        if (!$input.val().trim()) {
            $hiddenId.val('');
        }
    });
    // Sidebar AJAX search for clients and suppliers (like new message modal)
    var $sidebarInput = $('#sidebarSearchInput');
    var $sidebarResults = $('#sidebarSearchResults');
    var sidebarTimeout;
    $sidebarInput.on('input', function() {
        clearTimeout(sidebarTimeout);
        var term = $sidebarInput.val().trim();
        if (term.length < 2) {
            $sidebarResults.hide().empty();
            return;
        }
        sidebarTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route('admin.companies.search-for-chat') }}',
                data: { search: term },
                success: function(data) {
                    if (data.data && data.data.length > 0) {
                        var html = data.data.map(function(item) {
                            return `<a href="#" class="list-group-item list-group-item-action" data-id="${item.id}" data-name="${item.text}"><div><strong>${item.text}</strong><br><small class='text-muted'>${item.designation}</small>${item.email ? '<br><small>' + item.email + '</small>' : ''}</div></a>`;
                        }).join('');
                        $sidebarResults.html(html).show();
                    } else {
                        $sidebarResults.html('<div class="list-group-item">No results found</div>').show();
                    }
                }
            });
        }, 250);
    });
    $sidebarResults.on('click', '.list-group-item-action', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        // Check if conversation already exists in the sidebar
        var found = false;
        $('.messenger-chat-item').each(function() {
            if ($(this).find('.messenger-chat-name').text().trim() === name.trim()) {
                found = $(this).attr('href');
                return false;
            }
        });
        if (found) {
            window.location.href = found;
        } else {
            // Create new conversation via POST, then redirect
            $.ajax({
                url: '{{ route('messages.start') }}',
                method: 'POST',
                data: {
                    client_id: id,
                    message: '',
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (resp && resp.redirect) {
                        window.location.href = resp.redirect;
                    } else {
                        window.location.reload();
                    }
                },
                error: function() {
                    window.location.reload();
                }
            });
        }
        $sidebarResults.hide().empty();
    });
    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#sidebarSearchInput, #sidebarSearchResults').length) {
            $sidebarResults.hide();
        }
    });
});
$(function() {
    function isMobile() { return window.innerWidth <= 900; }
    var $sidebar = $('.messenger-sidebar');
    var $main = $('.messenger-main-content');
    var $showSidebarBtn = $('#showSidebarBtn');
    // Show sidebar on back button
    $showSidebarBtn.on('click', function() {
        $sidebar.removeClass('hide-mobile');
        $main.addClass('hide-mobile');
    });
    // Hide sidebar when a chat is selected (on mobile)
    $('.messenger-chat-item').on('click', function() {
        if (isMobile()) {
            $sidebar.addClass('hide-mobile');
            $main.removeClass('hide-mobile');
        }
    });
    // On page load, if mobile and a conversation is open, hide sidebar
    if (isMobile() && $('.messenger-main-content').find('.messenger-header-title').length) {
        $sidebar.addClass('hide-mobile');
        $main.removeClass('hide-mobile');
        $showSidebarBtn.show();
    } else {
        $showSidebarBtn.hide();
    }
    // On resize, adjust visibility
    $(window).on('resize', function() {
        if (isMobile()) {
            if ($main.find('.messenger-header-title').length) {
                $sidebar.addClass('hide-mobile');
                $main.removeClass('hide-mobile');
                $showSidebarBtn.show();
            }
        } else {
            $sidebar.removeClass('hide-mobile');
            $main.removeClass('hide-mobile');
            $showSidebarBtn.hide();
        }
    });
});
// Attach image preview and removal
$(function() {
    const fileInput = document.getElementById('fileInput');
    const attachBtn = document.getElementById('attachBtn');
    const preview = document.getElementById('attachmentPreview');
    attachBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });
    fileInput.addEventListener('change', function(e) {
        const file = fileInput.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                alert('Only image files are allowed.');
                fileInput.value = '';
                preview.innerHTML = '';
                preview.style.display = 'none';
                return;
            }
            if (file.size > 4 * 1024 * 1024) {
                alert('Image must be less than 4MB.');
                fileInput.value = '';
                preview.innerHTML = '';
                preview.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.innerHTML = `<div style='display:flex;align-items:center;gap:8px;'><img src='${ev.target.result}' style='max-width:60px;max-height:60px;border-radius:8px;'><button type='button' id='removeAttachmentBtn' class='btn btn-sm btn-light' style='border-radius:50%;'><i class='bi bi-x-lg'></i></button></div>`;
                preview.style.display = '';
                document.getElementById('removeAttachmentBtn').onclick = function() {
                    fileInput.value = '';
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                };
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
            preview.style.display = 'none';
        }
    });
});
</script>
@endpush 