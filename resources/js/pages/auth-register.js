// Sign-up page (auth/register): step-by-step navigation, account-type dependent fields, client-side checks.
import '../upload-limits.js';
const form = document.getElementById('registrationForm');
const main = form?.closest('.register-main');

if (form) {
    const steps = [...form.querySelectorAll('[data-step]')];
    const links = [...document.querySelectorAll('[data-step-link]')];
    const prevBtn = form.querySelector('[data-step-prev]');
    const nextBtn = form.querySelector('[data-step-next]');
    const submitBtn = form.querySelector('[data-step-submit]');
    const progress = document.getElementById('stepProgress');
    let current = 0;
    let furthest = 0;

    // ----- Account type: "supplier", "client", "client-individual" or "client-company" -----
    const accountState = () => {
        const role = form.querySelector('[name="designation"]:checked')?.value || '';
        const clientType = form.querySelector('[name="client_type"]:checked')?.value || '';
        return role === 'client' && clientType ? `client-${clientType}` : role;
    };
    // A token matches the state itself or anything below it ("client" matches "client-company").
    const matches = (tokens, state) => tokens.split(/\s+/).some((t) => state === t || state.startsWith(`${t}-`));

    const isShown = (el) => !el.closest('[hidden]');

    function applyAccountType() {
        const state = accountState();
        form.querySelectorAll('[data-when]').forEach((el) => {
            el.hidden = !matches(el.dataset.when, state);
        });
        form.querySelectorAll('[data-required-when]').forEach((input) => {
            input.required = matches(input.dataset.requiredWhen, state);
            input.closest('[data-required-field]')?.classList.toggle('is-required', input.required);
        });
        toggleOtherType();
    }

    const otherType = document.getElementById('other_supplier_type');
    const otherTypeGroup = document.getElementById('other_supplier_type_group');
    function toggleOtherType() {
        const isOther = document.getElementById('supplier_type').value === 'Other';
        otherTypeGroup.hidden = !isOther;
        otherType.required = isOther;
    }

    form.addEventListener('change', (e) => {
        if (['designation', 'client_type'].includes(e.target.name)) applyAccountType();
        if (e.target.id === 'supplier_type') toggleOtherType();
    });

    // ----- Field checks beyond the HTML attributes -----
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    const mobile = document.getElementById('mobile_number');

    function customChecks(input) {
        if (input === confirmation) {
            input.setCustomValidity(confirmation.value && confirmation.value !== password.value ? 'Passwords do not match.' : '');
        }
        if (input === mobile) {
            mobile.setCustomValidity(mobile.value && !/^09\d{9}$/.test(mobile.value) ? 'Enter an 11-digit mobile number starting with 09.' : '');
        }
        if (input.type === 'file' && input.files?.[0]) {
            const f = input.files[0];
            const okType = /\.(pdf|jpe?g|png)$/i.test(f.name);
            const okSize = f.size <= Number(input.dataset.maxSize || Infinity);
            input.setCustomValidity(!okType ? 'Only PDF, JPG or PNG files are allowed.' : (!okSize ? 'The file must be 10 MB or smaller.' : ''));
        } else if (input.type === 'file') {
            input.setCustomValidity('');
        }
    }

    // One client-side message per field, below the field (or below the card group for radio cards).
    const feedback = new Map();
    function feedbackFor(input) {
        if (!feedback.has(input.name)) {
            const fb = document.createElement('div');
            fb.className = 'invalid-feedback d-block';
            fb.dataset.client = '';
            const grid = input.closest('.choice-grid');
            if (grid) grid.after(fb);
            else (input.closest('.mb-3, .form-check') || input.parentElement).append(fb);
            feedback.set(input.name, fb);
        }
        return feedback.get(input.name);
    }

    function showValidity(input) {
        customChecks(input);
        const valid = input.checkValidity();
        const group = input.type === 'radio' ? form.querySelectorAll(`[name="${input.name}"]`) : [input];
        group.forEach((el) => el.classList.toggle('is-invalid', !valid));
        input.closest('.choice-grid')?.classList.toggle('is-invalid', !valid);
        // Once the user has touched the field, the server's message for it is stale.
        const grid = input.closest('.choice-grid');
        const stale = grid ? [grid.nextElementSibling] : [...(input.closest('.mb-3, .form-check')?.querySelectorAll('.invalid-feedback') || [])];
        stale.forEach((el) => { if (el?.matches('.invalid-feedback:not([data-client])')) el.remove(); });
        const fb = feedbackFor(input);
        fb.textContent = valid ? '' : messageFor(input);
        fb.hidden = valid;
        return valid;
    }

    function messageFor(input) {
        if (input.validity.valueMissing) {
            if (input.type === 'radio') return 'Please choose an option.';
            if (input.type === 'file') return 'Please upload this document.';
            if (input.type === 'checkbox') return 'You must agree to continue.';
            return input.tagName === 'SELECT' ? 'Please select an option.' : 'This field is required.';
        }
        if (input.validity.tooShort) return `Use at least ${input.minLength} characters.`;
        if (input.validity.typeMismatch && input.type === 'email') return 'Enter a valid email address.';
        if (input.validity.patternMismatch && input.title) return input.title;
        return input.validationMessage;
    }

    const fieldsIn = (step) => [...step.querySelectorAll('input, select, textarea')]
        .filter((el) => el.name && el.type !== 'hidden' && !el.disabled && isShown(el));

    function validateStep(index) {
        const seen = new Set();
        let firstInvalid = null;
        for (const input of fieldsIn(steps[index])) {
            if (input.type === 'radio') {
                if (seen.has(input.name)) continue;
                seen.add(input.name);
            }
            if (!showValidity(input) && !firstInvalid) firstInvalid = input;
        }
        if (firstInvalid) firstInvalid.focus({ preventScroll: true });
        firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return !firstInvalid;
    }

    form.addEventListener('input', (e) => {
        if (e.target === mobile) mobile.value = mobile.value.replace(/\D/g, '').slice(0, 11);
        if (e.target === password) updateMeter();
        if (e.target.classList.contains('is-invalid') || e.target === confirmation) showValidity(e.target);
    });
    form.addEventListener('change', (e) => {
        if (e.target.name && (e.target.type === 'file' || e.target.classList.contains('is-invalid'))) showValidity(e.target);
    });
    form.addEventListener('focusout', (e) => {
        const t = e.target;
        if (t.matches('input:not([type=radio]):not([type=checkbox]):not([type=file]), textarea') && t.value) showValidity(t);
    });

    // ----- Password strength + show/hide -----
    const meter = document.getElementById('passwordMeter');
    function updateMeter() {
        const v = password.value;
        const score = [v.length >= 8, v.length >= 12, /[a-z]/.test(v) && /[A-Z]/.test(v), /\d/.test(v), /[^A-Za-z0-9]/.test(v)]
            .filter(Boolean).length;
        const colors = ['#dc3545', '#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];
        meter.style.width = v ? `${Math.max(score, 1) * 20}%` : '0';
        meter.style.backgroundColor = colors[score];
    }
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.togglePassword);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    // ----- Review summary -----
    const valueOf = (name) => {
        const el = form.querySelector(`[name="${name}"]:checked`) || form.querySelector(`[name="${name}"]:not([type=radio])`);
        if (!el || el.closest('[hidden]')) return '';
        if (el.type === 'file') return el.files?.[0]?.name || '';
        if (el.tagName === 'SELECT') return el.value ? el.selectedOptions[0].textContent : '';
        return el.value.trim();
    };
    const labelFor = (name, value) => form.querySelector(`[name="${name}"][value="${value}"]`)?.closest('label')?.querySelector('.choice-card__title')?.textContent;

    function renderReview() {
        const role = valueOf('designation');
        const clientType = valueOf('client_type');
        const type = valueOf('supplier_type') === 'Other' ? valueOf('other_supplier_type') : valueOf('supplier_type');
        const address = [valueOf('street'), valueOf('barangay'), valueOf('city'), valueOf('state'), valueOf('postal')].filter(Boolean).join(', ');
        const docs = ['dti_sec_registration', 'business_permit_mayor_permit', 'valid_id_owner_rep', 'accreditations_certifications',
            'company_profile_portfolio', 'sample_price_list'].filter((n) => valueOf(n)).length;
        const rows = [
            ['Account type', [labelFor('designation', role), clientType && labelFor('client_type', clientType)].filter(Boolean).join(' · ')],
            ['Username', valueOf('username')],
            ['Email', valueOf('email')],
            ['Company', valueOf('company_name')],
            ['Type of company', type],
            ['Contact person', valueOf('contact_person')],
            ['Mobile', valueOf('mobile_number')],
            ['Address', address],
            ['Documents', docs ? `${docs} file${docs > 1 ? 's' : ''} attached` : 'None attached'],
        ];
        const list = document.getElementById('reviewList');
        list.replaceChildren(...rows.filter(([, v]) => v).flatMap(([k, v]) => {
            const dt = document.createElement('dt');
            const dd = document.createElement('dd');
            dt.textContent = k;
            dd.textContent = v;
            return [dt, dd];
        }));
    }

    // ----- Steps -----
    function show(index, { focus = true } = {}) {
        current = Math.max(0, Math.min(index, steps.length - 1));
        furthest = Math.max(furthest, current);
        steps.forEach((s, i) => s.classList.toggle('is-active', i === current));
        links.forEach((l, i) => {
            l.classList.toggle('is-current', i === current);
            l.classList.toggle('is-done', i !== current && i <= furthest);
            l.toggleAttribute('aria-current', i === current);
        });
        prevBtn.hidden = current === 0;
        nextBtn.hidden = current === steps.length - 1;
        submitBtn.hidden = current !== steps.length - 1;
        progress.textContent = `Step ${current + 1} of ${steps.length}`;
        if (current === steps.length - 1) renderReview();
        if (focus) main.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    nextBtn.addEventListener('click', () => {
        if (validateStep(current)) show(current + 1);
    });
    prevBtn.addEventListener('click', () => show(current - 1));
    links.forEach((link, i) => link.addEventListener('click', () => {
        if (i <= furthest && (i < current || validateStep(current))) show(i);
    }));

    form.addEventListener('submit', (e) => {
        for (let i = 0; i < steps.length; i++) {
            if (!validateStep(i)) {
                e.preventDefault();
                show(i, { focus: false });
                validateStep(i);
                return;
            }
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Creating account…';
    });

    // ----- Start -----
    applyAccountType();
    form.classList.add('is-wizard');
    main.classList.add('is-wizard');

    // After a failed submit, open the first step with a server error and mark the others.
    const errorSteps = steps.map((s) => s.querySelector('.is-invalid, .invalid-feedback.d-block') !== null);
    errorSteps.forEach((has, i) => links[i]?.classList.toggle('has-error', has));
    const firstError = errorSteps.indexOf(true);
    if (firstError >= 0) {
        furthest = steps.length - 1;
        show(firstError, { focus: false });
    } else {
        show(0, { focus: false });
    }
}
