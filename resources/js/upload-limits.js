// Upload limits in the browser, for every <input type="file"> carrying the attributes from App\Support\Uploads::attributes():
//   data-upload-types="jpg,png,…"  data-upload-max-kb="10240"  data-upload-max-files="5"  data-upload-hint="JPG, PNG … · up to 10 MB"
// - Shows the hint (allowed formats + size/count) under the input, unless data-upload-hint="" or the input is hidden.
// - On selection, drops files of the wrong type or size and rejects selections over the count, with an inline
//   error (visible inputs) or a toast (hidden inputs such as a paperclip button). Runs in the capture phase, before
//   page scripts, so they only ever see the accepted files. The server validates again (App\Support\Uploads).
// Imported by resources/js/app-layout.js (app layout) and pages/auth-register.js (registration).

import '../css/upload-limits.css';

const SELECTOR = 'input[type="file"][data-upload-max-kb]';

const mb = (kb) => {
    const value = kb / 1024;
    return `${Number.isInteger(value) ? value : value.toFixed(1)} MB`;
};
const fileSize = (bytes) => (bytes >= 1024 * 1024 ? `${(bytes / 1024 / 1024).toFixed(1)} MB` : `${Math.ceil(bytes / 1024)} KB`);
const extOf = (name) => (name.includes('.') ? name.split('.').pop().toLowerCase() : '');
const isHidden = (el) => el.type === 'hidden' || el.classList.contains('d-none') || el.classList.contains('visually-hidden') || el.offsetParent === null;

let uid = 0;
const idFor = (input, suffix) => `${input.id || `upload-${++uid}`}-${suffix}`;

function hintEl(input) {
    const text = input.dataset.uploadHint;
    if (!text || isHidden(input)) return null;
    let hint = input.parentElement.querySelector(`[data-upload-hint-for="${input.name}"]`);
    if (!hint) {
        hint = document.createElement('div');
        hint.className = 'form-text upload-hint';
        hint.id = idFor(input, 'hint');
        hint.dataset.uploadHintFor = input.name;
        hint.textContent = `Allowed: ${text}`;
        input.insertAdjacentElement('afterend', hint);
        input.setAttribute('aria-describedby', [input.getAttribute('aria-describedby'), hint.id].filter(Boolean).join(' '));
    }
    return hint;
}

function clearErrors(input) {
    input.classList.remove('is-invalid');
    input.removeAttribute('aria-invalid');
    input.parentElement?.querySelector(`[data-upload-error-for="${input.name}"]`)?.remove();
}

function showErrors(input, messages) {
    if (isHidden(input)) {
        toast(messages);
        return;
    }
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');
    const box = document.createElement('div');
    box.className = 'invalid-feedback d-block upload-error';
    box.setAttribute('role', 'alert');
    box.dataset.uploadErrorFor = input.name;
    box.innerHTML = messages.map((m) => `<div>${escapeHtml(m)}</div>`).join('');
    (hintEl(input) || input).insertAdjacentElement('afterend', box);
}

function toast(messages) {
    let host = document.getElementById('upload-toasts');
    if (!host) {
        host = document.createElement('div');
        host.id = 'upload-toasts';
        host.className = 'upload-toasts';
        host.setAttribute('aria-live', 'assertive');
        document.body.appendChild(host);
    }
    const item = document.createElement('div');
    item.className = 'upload-toast';
    item.setAttribute('role', 'alert');
    item.innerHTML = `<strong>File not added</strong>${messages.map((m) => `<div>${escapeHtml(m)}</div>`).join('')}`;
    host.appendChild(item);
    setTimeout(() => item.remove(), 6000);
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/** Returns { accepted: File[], errors: string[] } for a selection. */
export function checkFiles(input, files) {
    const types = (input.dataset.uploadTypes || '').split(',').filter(Boolean);
    const maxKb = Number(input.dataset.uploadMaxKb) || Infinity;
    const maxFiles = Number(input.dataset.uploadMaxFiles) || 1;
    const errors = [];
    const accepted = [];

    files.forEach((file) => {
        const ext = extOf(file.name);
        if (types.length && !types.includes(ext)) {
            errors.push(`"${file.name}" is not an allowed file type (${input.dataset.uploadHint?.split(' · ')[0] || types.join(', ')}).`);
        } else if (file.size > maxKb * 1024) {
            errors.push(`"${file.name}" is ${fileSize(file.size)}; the limit is ${mb(maxKb)}.`);
        } else if (file.size === 0) {
            errors.push(`"${file.name}" is empty.`);
        } else {
            accepted.push(file);
        }
    });

    if (accepted.length > maxFiles) {
        errors.push(`You selected ${accepted.length} files; you can upload at most ${maxFiles} at a time.`);
        accepted.length = 0;   // ambiguous which to keep: ask the user to choose again
    }

    return { accepted, errors };
}

function onChange(e) {
    const input = e.target;
    if (!(input instanceof HTMLInputElement) || !input.matches(SELECTOR)) return;

    clearErrors(input);
    const files = [...(input.files || [])];
    if (!files.length) return;

    const { accepted, errors } = checkFiles(input, files);
    if (!errors.length) return;

    // Keep only the accepted files (DataTransfer is the only way to edit a FileList)
    const keep = new DataTransfer();
    accepted.forEach((file) => keep.items.add(file));
    input.files = keep.files;

    showErrors(input, errors);
    // The event continues with only the accepted files, so page scripts (previews, the chat composer) stay in sync,
    // e.g. an emptied selection clears a previously shown attachment.
}

function init(root = document) {
    root.querySelectorAll(SELECTOR).forEach(hintEl);
}

document.addEventListener('change', onChange, true);
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => init());
else init();
// Inputs inside modals become visible later: add their hint when the modal opens
document.addEventListener('shown.bs.modal', (e) => init(e.target));
