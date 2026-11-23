// In-page preview of the Construction Contract Agreement.
//
// openContractPreview({ title, subtitle, url | html + css, pdfUrl, fileName, footer })
//   url      – page that returns the document (contracts.print?embed=1); its <style> and <body> are loaded
//   html/css – the document markup and styles directly (unsaved drafts from the editor)
//   pdfUrl   – server PDF for "Save as PDF"; without it the button opens the print dialog instead
// The document goes into a shadow root so its styles and the app's styles never mix. While the modal is
// open, @media print (contract-preview-modal.css) hides everything except the document.
//
// Links with [data-contract-preview] open it on click (their href stays as the no-JS fallback):
//   <a href="…/print" data-contract-preview data-embed-url="…?embed=1" data-pdf-url="…" data-title="…">
import '../../css/pages/contract-preview-modal.css';

const ICONS = {
    close: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>',
    print: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 9V3h10v6M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v7H7z"/></svg>',
    pdf: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zM14 3v5h5M12 11v6M9 14l3 3 3-3"/></svg>',
};

let modal = null;       // the single overlay element, created on first use
let lastFocus = null;   // element to give focus back to on close
let pageStyle = null;   // <style> with @page rules, present only while open
let savedTitle = null;

function build() {
    modal = document.createElement('div');
    modal.className = 'cp-modal';
    modal.hidden = true;
    modal.innerHTML = `
        <div class="cp-dialog" role="dialog" aria-modal="true" aria-labelledby="cp-title">
            <div class="cp-toolbar">
                <div>
                    <div class="cp-title" id="cp-title"></div>
                    <div class="cp-hint"></div>
                </div>
                <div class="cp-actions">
                    <button type="button" class="cp-btn" data-cp-close>${ICONS.close} Close Preview</button>
                    <button type="button" class="cp-btn" data-cp-print>${ICONS.print} Print</button>
                    <a class="cp-btn primary" data-cp-pdf href="#">${ICONS.pdf} Save as PDF</a>
                    <button type="button" class="cp-x" data-cp-close aria-label="Close preview">${ICONS.close}</button>
                </div>
            </div>
            <div class="cp-scroll"><div class="cp-sheet"></div></div>
        </div>`;
    document.body.appendChild(modal);

    const sheet = modal.querySelector('.cp-sheet');
    sheet.attachShadow({ mode: 'open' });

    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.closest('[data-cp-close]')) close();       // backdrop or a close button
    });
    modal.querySelector('[data-cp-print]').addEventListener('click', () => window.print());
    modal.querySelector('[data-cp-pdf]').addEventListener('click', (e) => {
        const link = e.currentTarget;
        if (link.dataset.fallback !== undefined) {
            // No server PDF (unsaved draft): the print dialog's "Save as PDF" destination does it.
            e.preventDefault();
            savedTitle = document.title;
            document.title = link.dataset.fileName || document.title;   // default file name in the dialog
            window.print();
        }
    });
    window.addEventListener('afterprint', () => {
        if (savedTitle !== null) { document.title = savedTitle; savedTitle = null; }
    });
}

function onKeydown(e) {
    if (e.key === 'Escape') { e.preventDefault(); close(); return; }
    if (e.key !== 'Tab') return;
    // keep keyboard focus inside the dialog
    const focusable = [...modal.querySelectorAll('button, a[href]')].filter((el) => !el.hidden);
    const first = focusable[0], last = focusable[focusable.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
}

function setDocument(css, html) {
    const root = modal.querySelector('.cp-sheet').shadowRoot;
    root.innerHTML = `<style>${css}</style><div class="doc-root">${html}</div>`;
    modal.querySelector('.cp-scroll').scrollTop = 0;
}

function setStatus(text) {
    modal.querySelector('.cp-sheet').shadowRoot.innerHTML =
        `<style>.cp-status{padding:3rem 1rem;text-align:center;font-family:Arial,sans-serif;color:#333}</style><div class="cp-status">${text}</div>`;
}

export function openContractPreview({ title = 'Print Preview', subtitle = '', url = null, html = '', css = '', pdfUrl = null, fileName = 'Contract', footer = '' } = {}) {
    if (!modal) build();

    modal.querySelector('.cp-title').textContent = title;
    modal.querySelector('.cp-hint').textContent = subtitle
        || (pdfUrl ? 'Print sends it to a printer · Save as PDF downloads the agreement as a PDF file.'
                   : 'Save as PDF opens the print dialog — choose “Save as PDF” as the destination.');

    const pdf = modal.querySelector('[data-cp-pdf]');
    pdf.dataset.fileName = fileName;
    if (pdfUrl) { pdf.href = pdfUrl; pdf.setAttribute('download', ''); delete pdf.dataset.fallback; }
    else { pdf.href = '#'; pdf.removeAttribute('download'); pdf.dataset.fallback = ''; }

    // Page size, margins and running footer for printing from this page (must be in the document, not the shadow root)
    pageStyle?.remove();
    pageStyle = document.createElement('style');
    const footerText = (footer || title).replace(/["\\]/g, '');
    pageStyle.textContent = `@media print { @page { size: 8.5in 11in; margin: 0.65in 0.7in;
        @bottom-left { content: "${footerText}"; font: 7.5pt 'Times New Roman', serif; color: #333; }
        @bottom-right { content: "Page " counter(page) " of " counter(pages); font: 7.5pt 'Times New Roman', serif; color: #333; } } }`;
    document.head.appendChild(pageStyle);

    lastFocus = document.activeElement;
    modal.hidden = false;
    document.body.classList.add('cp-open');
    document.addEventListener('keydown', onKeydown);
    modal.querySelector('[data-cp-close]').focus();

    if (url) {
        setStatus('Loading preview…');
        fetch(url, { headers: { Accept: 'text/html' }, credentials: 'same-origin' })
            .then((res) => { if (!res.ok) throw new Error(res.status); return res.text(); })
            .then((text) => {
                const doc = new DOMParser().parseFromString(text, 'text/html');
                const styles = [...doc.querySelectorAll('style')].map((s) => s.textContent).join('\n');
                setDocument(styles, doc.body.innerHTML);
            })
            .catch(() => setStatus('The preview could not be loaded. Close it and try again.'));
    } else {
        setDocument(css, html);
    }
}

export function close() {
    if (!modal || modal.hidden) return;
    modal.hidden = true;
    document.body.classList.remove('cp-open');
    document.removeEventListener('keydown', onKeydown);
    pageStyle?.remove();
    pageStyle = null;
    lastFocus?.focus?.();
}

// Links that open the preview in place
document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-contract-preview]');
    if (!link || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;   // modified clicks keep the full-page fallback
    e.preventDefault();
    openContractPreview({
        title: link.dataset.title || 'Print Preview',
        url: link.dataset.embedUrl,
        pdfUrl: link.dataset.pdfUrl || null,
        fileName: link.dataset.fileName || 'Contract',
        footer: link.dataset.footer || '',
    });
});
