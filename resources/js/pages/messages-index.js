// Messages page (resources/views/messages/index.blade.php): conversation list, thread, composer,
// "new message" recipient search and the attachment viewer. Server data comes from window.__page.
const page = window.__page || {};
const TZ = 'Asia/Manila';
const url = (path) => (window.appUrl ? window.appUrl(path) : `/${path}`);

const INLINE_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/avif'];

const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const initial = (name) => esc((String(name || '?').trim()[0] || '?').toUpperCase());
const timeOf = (iso) => new Date(iso).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', timeZone: TZ });
const dayKey = (iso) => new Date(iso).toLocaleDateString('en-CA', { timeZone: TZ }); // YYYY-MM-DD
const formatSize = (bytes) => {
    if (!bytes) return '0 B';
    const i = Math.min(3, Math.floor(Math.log(bytes) / Math.log(1024)));
    return `${parseFloat((bytes / 1024 ** i).toFixed(1))} ${['B', 'KB', 'MB', 'GB'][i]}`;
};

// ---------------------------------------------------------------- conversation list: search + mobile toggle
function setupSidebar() {
    const chat = document.getElementById('chat');
    const search = document.getElementById('chatSearch');
    const list = document.getElementById('chatList');
    const empty = document.getElementById('chatSearchEmpty');

    search?.addEventListener('input', () => {
        const term = search.value.trim().toLowerCase();
        let shown = 0;
        list.querySelectorAll('.chat-list__item').forEach((item) => {
            const match = !term || item.textContent.toLowerCase().includes(term);
            item.hidden = !match;
            shown += match ? 1 : 0;
        });
        if (empty) empty.hidden = !term || shown > 0;
    });

    document.getElementById('chatBack')?.addEventListener('click', () => chat.classList.remove('is-thread-open'));
}

// After sending: show the new message as the active conversation's preview and move it to the top.
function bumpActiveConversation(message) {
    const list = document.getElementById('chatList');
    const item = list?.querySelector(`.chat-list__item[data-conversation-id="${CSS.escape(String(page.conversationId))}"]`);
    if (!item) return;
    const preview = item.querySelector('.chat-list__preview');
    const time = item.querySelector('.chat-list__time');
    if (preview) preview.innerHTML = `<span class="text-muted">You: </span>${esc(message.content ? message.content.slice(0, 60) : 'Sent an attachment')}`;
    if (time) time.textContent = timeOf(message.created_at);
    list.prepend(item);
}

// ---------------------------------------------------------------- thread + composer
class Thread {
    constructor() {
        this.area = document.getElementById('messagesArea');
        this.form = document.getElementById('messageForm');
        this.input = document.getElementById('messageContent');
        this.fileInput = document.getElementById('fileInput');
        this.preview = document.getElementById('attachmentPreview');
        this.sendBtn = document.getElementById('sendBtn');
        this.sending = false;

        this.form.addEventListener('submit', (e) => { e.preventDefault(); this.send(); });
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.send(); }
        });
        this.input.addEventListener('input', () => this.autosize());
        document.getElementById('attachBtn').addEventListener('click', () => this.fileInput.click());
        this.fileInput.addEventListener('change', () => this.pickFile(this.fileInput.files[0]));
        this.preview.addEventListener('click', (e) => { if (e.target.closest('[data-clear-attachment]')) this.clearFile(); });

        this.scrollToBottom();
        this.input.focus({ preventScroll: true });
    }

    autosize() {
        this.input.style.height = 'auto';
        this.input.style.height = `${Math.min(this.input.scrollHeight, 140)}px`;
    }

    scrollToBottom() {
        this.area.scrollTop = this.area.scrollHeight;
    }

    pickFile(file) {
        // Type and size were already checked by resources/js/upload-limits.js; a rejected file arrives here as none.
        if (!file) { this.clearFile(); return; }
        const isImage = file.type.startsWith('image/');
        const thumb = isImage ? `<img src="${URL.createObjectURL(file)}" alt="">` : '<i class="bi bi-file-earmark-text" aria-hidden="true"></i>';
        this.preview.innerHTML = `
            <div class="chat-file-chip">
                ${thumb}
                <span class="chat-file-chip__text"><strong>${esc(file.name)}</strong><small>${formatSize(file.size)}</small></span>
                <button type="button" class="btn btn-sm btn-light" data-clear-attachment aria-label="Remove attachment"><i class="bi bi-x-lg"></i></button>
            </div>`;
        this.preview.hidden = false;
    }

    clearFile() {
        this.fileInput.value = '';
        this.preview.innerHTML = '';
        this.preview.hidden = true;
    }

    flash(text) {
        let note = this.form.querySelector('.chat-composer__error');
        if (!note) {
            note = document.createElement('div');
            note.className = 'chat-composer__error';
            note.setAttribute('role', 'alert');
            this.form.prepend(note);
        }
        note.textContent = text;
        clearTimeout(this.flashTimer);
        this.flashTimer = setTimeout(() => note.remove(), 4000);
    }

    async send() {
        const content = this.input.value.trim();
        const file = this.fileInput.files[0];
        if (this.sending || (!content && !file)) return;

        this.sending = true;
        this.sendBtn.disabled = true;
        this.sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';

        const data = new FormData(this.form);
        data.set('content', content);
        try {
            const res = await fetch(page.sendUrl, { method: 'POST', body: data, headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const body = await res.json().catch(() => ({}));
            if (!res.ok) {
                this.flash(body.message || 'Your message could not be sent. Please try again.');
                return;
            }
            this.append(body);
            this.input.value = '';
            this.clearFile();
            this.autosize();
            bumpActiveConversation(body);
        } catch (e) {
            this.flash('Your message could not be sent. Check your connection and try again.');
        } finally {
            this.sending = false;
            this.sendBtn.disabled = false;
            this.sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
            this.input.focus();
        }
    }

    append(message) {
        if (!message?.id || this.area.querySelector(`[data-message-id="${CSS.escape(String(message.id))}"]`)) return;
        document.getElementById('threadEmpty')?.remove();

        const rows = this.area.querySelectorAll('.chat-msg');
        const prev = rows[rows.length - 1];
        const days = this.area.querySelectorAll('.chat-day');
        const lastDay = days[days.length - 1]?.dataset.day;
        const day = dayKey(message.created_at);
        let html = '';
        if (day !== lastDay) html += `<div class="chat-day" data-day="${day}"><span>Today</span></div>`;
        const grouped = day === lastDay && prev && prev.dataset.senderId === String(message.sender_id);
        html += messageHtml(message, String(message.sender_id) === String(page.currentUserId), grouped);

        this.area.insertAdjacentHTML('beforeend', html);
        this.scrollToBottom();
    }
}

// Mirrors resources/views/messages/partials/message.blade.php
function messageHtml(m, mine, grouped) {
    const path = m.file_path || m.image;
    const name = m.file_name || (path ? path.split('/').pop() : '');
    // Inline only for raster images (Message::INLINE_IMAGE_TYPES); others are offered as downloads
    const isImage = path && (m.file_type ? INLINE_IMAGE_TYPES.includes(m.file_type) : /\.(jpe?g|png|gif|webp|bmp|avif)$/i.test(path));
    // Served by MessageController@showAttachment (participants only; no public/storage symlink needed)
    const fileUrl = path ? (m.attachment_url || url(`messages/attachment/${m.id}`)) : '';
    const attrs = path ? `data-attachment-url="${esc(fileUrl)}" data-attachment-name="${esc(name)}" data-download-url="${esc(m.download_url || url(`messages/attachment/${m.id}/download`))}"` : '';
    let attachment = '';
    if (path && isImage) {
        attachment = `<button type="button" class="chat-attachment chat-attachment--image" ${attrs} data-attachment-type="image" aria-label="Open image ${esc(name)}"><img src="${esc(fileUrl)}" alt="${esc(name)}" loading="lazy" decoding="async"></button>`;
    } else if (path) {
        attachment = `<button type="button" class="chat-attachment chat-attachment--file" ${attrs} data-attachment-type="file"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>${esc(name)}</span></button>`;
    }

    return `
        <div class="chat-msg ${mine ? 'is-mine' : 'is-theirs'} ${grouped ? 'is-grouped' : ''}" data-message-id="${esc(m.id)}" data-sender-id="${esc(m.sender_id)}">
            ${mine ? '' : `<span class="chat-avatar chat-avatar--sm" aria-hidden="true">${initial(m.sender?.name)}</span>`}
            <div class="chat-msg__body">
                ${m.content ? `<div class="chat-bubble">${esc(m.content)}</div>` : ''}
                ${attachment}
                <time class="chat-msg__time" datetime="${esc(m.created_at)}">${timeOf(m.created_at)}</time>
            </div>
        </div>`;
}

// ---------------------------------------------------------------- attachment viewer
function setupViewer() {
    const modalEl = document.getElementById('attachmentViewerModal');
    if (!modalEl) return;
    const body = document.getElementById('attachmentViewerContent');
    const title = document.getElementById('attachmentViewerTitle');
    const download = document.getElementById('downloadAttachmentBtn');
    const zoomIn = document.getElementById('zoomInBtn');
    const zoomOut = document.getElementById('zoomOutBtn');
    let scale = 1;
    let img = null;
    const applyZoom = () => { if (img) img.style.transform = `scale(${scale})`; };

    zoomIn.addEventListener('click', () => { scale = Math.min(4, scale * 1.25); applyZoom(); });
    zoomOut.addEventListener('click', () => { scale = Math.max(0.5, scale / 1.25); applyZoom(); });

    // A thumbnail that fails to load (file removed, no access) becomes a placeholder instead of a broken image.
    // 'error' doesn't bubble, so listen in the capture phase; this also covers messages added after page load.
    document.addEventListener('error', (e) => {
        const thumb = e.target;
        if (!(thumb instanceof HTMLImageElement) || !thumb.closest('.chat-attachment--image')) return;
        const button = thumb.closest('.chat-attachment--image');
        button.classList.add('is-broken');
        thumb.replaceWith(Object.assign(document.createElement('span'), {
            className: 'chat-attachment__fallback',
            innerHTML: '<i class="bi bi-image" aria-hidden="true"></i> Image unavailable',
        }));
    }, true);

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-attachment-url]');
        if (!trigger) return;
        const { attachmentUrl, attachmentName, attachmentType, downloadUrl } = trigger.dataset;
        title.textContent = attachmentName || 'Attachment';
        download.href = downloadUrl || attachmentUrl;
        scale = 1;

        if (attachmentType === 'image') {
            body.innerHTML = '';
            img = document.createElement('img');
            img.src = attachmentUrl;
            img.alt = attachmentName || '';
            img.addEventListener('error', () => { body.innerHTML = '<p class="text-muted m-4">This image could not be loaded.</p>'; });
            body.appendChild(img);
        } else {
            img = null;
            body.innerHTML = `
                <div class="chat-viewer__file">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    <p class="mb-3">${esc(attachmentName)}</p>
                    <a href="${esc(downloadUrl || attachmentUrl)}" class="btn btn-success"><i class="bi bi-download me-1"></i> Download file</a>
                </div>`;
        }
        zoomIn.hidden = zoomOut.hidden = attachmentType !== 'image';
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
}

// ---------------------------------------------------------------- "new message" modal
function setupStartModal() {
    const form = document.getElementById('startConversationForm');
    if (!form) return;

    // Enter sends, Shift + Enter adds a line (like the thread composer)
    form.querySelector('textarea[name="message"]')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (form.reportValidity()) form.requestSubmit();
        }
    });

    // Managers: search approved clients/suppliers
    const search = document.getElementById('participant_search_start');
    const results = document.getElementById('participant_search_results_start');
    const clientId = document.getElementById('client_id_hidden_start');
    const supplierId = document.getElementById('supplier_id_hidden_start');
    if (!search || !results) return;

    const clearPick = () => {
        [clientId, supplierId].forEach((el) => { el.value = ''; el.removeAttribute('name'); });
        search.setCustomValidity('Choose a client or supplier from the list.');
    };
    clearPick();

    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        clearPick();
        const term = search.value.trim();
        if (term.length < 2) { results.hidden = true; results.innerHTML = ''; return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(`${page.participantSearchUrl}?search=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' } });
                const { data = [] } = await res.json();
                results.innerHTML = data.length
                    ? data.map((item) => {
                        const kind = String(item.designation || '').toLowerCase();
                        return `<button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2" data-id="${esc(item.id)}" data-kind="${esc(kind)}" data-label="${esc(item.text)}">
                            <span class="chat-avatar chat-avatar--sm" aria-hidden="true">${initial(item.text)}</span>
                            <span class="flex-grow-1 text-start"><strong>${esc(item.text)}</strong>${item.contact_person ? `<small class="d-block text-muted">${esc(item.contact_person)}</small>` : ''}</span>
                            <span class="badge ${kind === 'client' ? 'bg-info' : 'bg-success'}">${esc(item.designation)}</span>
                        </button>`;
                    }).join('')
                    : '<div class="list-group-item text-muted">No clients or suppliers found.</div>';
                results.hidden = false;
            } catch (e) {
                results.innerHTML = '<div class="list-group-item text-danger">Search failed. Please try again.</div>';
                results.hidden = false;
            }
        }, 250);
    });

    results.addEventListener('click', (e) => {
        const item = e.target.closest('[data-id]');
        if (!item) return;
        clearPick();
        const target = item.dataset.kind === 'supplier' ? supplierId : clientId;
        target.name = item.dataset.kind === 'supplier' ? 'supplier_id' : 'client_id';
        target.value = item.dataset.id;
        search.value = item.dataset.label;
        search.setCustomValidity('');
        results.hidden = true;
        form.querySelector('textarea[name="message"]')?.focus();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#participant_search_start, #participant_search_results_start')) results.hidden = true;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupSidebar();
    setupViewer();
    setupStartModal();
    if (document.getElementById('messageForm')) window.messengerThread = new Thread();
});
