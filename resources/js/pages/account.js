// Account settings: profile photo picker. The server has no image library (no GD), so the photo is
// centre-cropped to a square and scaled down here before upload; the server still validates it.
const input = document.getElementById('photoInput');

if (input) {
    const preview = document.getElementById('photoPreview');
    const original = preview.innerHTML;
    const save = document.getElementById('photoSave');
    const cancel = document.getElementById('photoCancel');
    const help = document.getElementById('photoHelp');
    const helpText = help.textContent;
    const SIZE = 512;
    const TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    const reset = () => {
        input.value = '';
        preview.innerHTML = original;
        save.hidden = cancel.hidden = true;
        help.textContent = helpText;
        help.classList.remove('text-danger');
    };
    const fail = (message) => {
        reset();
        help.textContent = message;
        help.classList.add('text-danger');
    };

    const loadImage = (file) => new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = URL.createObjectURL(file);
    });

    const squareBlob = (img, type) => new Promise((resolve) => {
        const side = Math.min(img.naturalWidth, img.naturalHeight);
        const out = Math.min(SIZE, side);
        const canvas = document.createElement('canvas');
        canvas.width = canvas.height = out;
        canvas.getContext('2d').drawImage(img, (img.naturalWidth - side) / 2, (img.naturalHeight - side) / 2, side, side, 0, 0, out, out);
        canvas.toBlob(resolve, type, 0.9);
    });

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file) { reset(); return; }
        if (!TYPES.includes(file.type)) { fail('Choose a JPG, PNG or WebP image.'); return; }
        if (file.size > 15 * 1024 * 1024) { fail('That image is too large — choose one under 15 MB.'); return; }

        let img;
        try {
            img = await loadImage(file);
        } catch (e) {
            fail('That image could not be opened. Try a different file.');
            return;
        }
        if (Math.min(img.naturalWidth, img.naturalHeight) < 64) { fail('The photo must be at least 64 × 64 pixels.'); return; }

        // Replace the chosen file with the cropped square (PNG keeps transparency; everything else becomes JPEG)
        const type = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
        const blob = await squareBlob(img, type);
        if (blob && window.DataTransfer) {
            const cropped = new File([blob], `profile.${type === 'image/png' ? 'png' : 'jpg'}`, { type });
            const dt = new DataTransfer();
            dt.items.add(cropped);
            input.files = dt.files;
        }
        URL.revokeObjectURL(img.src);

        const shown = document.createElement('img');
        shown.className = 'sp-avatar sp-avatar--xl';
        shown.alt = 'New profile photo preview';
        shown.src = URL.createObjectURL(input.files[0]);
        preview.replaceChildren(shown);
        save.hidden = cancel.hidden = false;
        help.textContent = 'Looks good? Save it — or pick another.';
        help.classList.remove('text-danger');
        save.focus();
    });

    cancel.addEventListener('click', reset);
}
