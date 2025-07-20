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
            const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').getAttribute('content'));
            const isCurrentUser = message.sender_id === currentUserId;
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
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            fileInput.value = '';
            preview.innerHTML = '';
            preview.style.display = 'none';
            return;
        }

        // Determine file icon based on type
        let icon = 'bi-file-earmark text-secondary';
        if (file.type.startsWith('image/')) {
            icon = 'bi-file-earmark-image text-warning';
        } else if (file.type.startsWith('video/')) {
            icon = 'bi-file-earmark-play text-danger';
        } else if (file.type.startsWith('audio/')) {
            icon = 'bi-file-earmark-music text-info';
        } else if (file.type.includes('pdf')) {
            icon = 'bi-file-earmark-pdf text-danger';
        } else if (file.type.includes('word') || file.type.includes('document')) {
            icon = 'bi-file-earmark-word text-primary';
        } else if (file.type.includes('excel') || file.type.includes('spreadsheet')) {
            icon = 'bi-file-earmark-excel text-success';
        } else if (file.type.includes('powerpoint') || file.type.includes('presentation')) {
            icon = 'bi-file-earmark-ppt text-warning';
        }

        // Format file size
        let sizeText = '';
        if (file.size < 1024) {
            sizeText = file.size + ' B';
        } else if (file.size < 1024 * 1024) {
            sizeText = (file.size / 1024).toFixed(1) + ' KB';
        } else {
            sizeText = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
        }

        preview.innerHTML = `<div class='d-flex align-items-center bg-light rounded-3 p-2 border position-relative'><span><i class='bi ${icon} fs-3 me-2'></i></span><div><div class='fw-semibold'>${file.name}</div><div class='text-muted' style='font-size:0.9em;'>${sizeText}</div></div><button type='button' id='removeAttachmentBtn' class='btn btn-sm btn-light position-absolute top-0 end-0 m-1 p-0' style='border-radius:50%;'><i class='bi bi-x-lg'></i></button></div>`;
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

// Form validation
document.getElementById('messageForm').addEventListener('submit', function(e) {
    const content = document.getElementById('messageContent').value.trim();
    const file = document.getElementById('fileInput').files[0];
    
    if (!content && !file) {
        e.preventDefault();
        alert('Please enter a message or attach a file.');
        return false;
    }
});

// Enter key to send message, Shift+Enter for new line
document.getElementById('messageContent').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        
        const content = this.value.trim();
        // ... (rest of the script as needed)
    }
}); 