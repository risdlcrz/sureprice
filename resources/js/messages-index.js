// Modern AJAX-based messaging system
class MessengerApp {
    constructor() {
        this.conversationId = window.conversationId || null;
        this.currentUserId = window.currentUserId || null;
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
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        if (file) {
            formData.append('file', file);
        }

        try {
            const response = await fetch(window.messageStoreRoute, {
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
                const response = await fetch(window.conversationsUpdateRoute);
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
                <a href="${conversation.url}" class="messenger-chat-item ${conversation.id == window.currentConversationId ? 'active' : ''}" data-conversation-id="${conversation.id}">
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

// Participant selection functionality
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

// Search functionality for start conversation modal
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('participant_search_start');
    const resultsDiv = document.getElementById('participant_search_results_start');
    const clientIdHidden = document.getElementById('client_id_hidden_start');
    const supplierIdHidden = document.getElementById('supplier_id_hidden_start');
    
    if (searchInput && resultsDiv) {
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
                fetch(`${window.searchChatRoute}?search=${encodeURIComponent(term)}`)
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
    }
}); 