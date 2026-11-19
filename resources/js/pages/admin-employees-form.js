// Employee form: contractor address fields are shown (and required) only for the contractor role.
const role = document.getElementById('role');
const fields = document.getElementById('contractor-fields');

if (role && fields) {
    const toggle = () => {
        const contractor = role.value === 'contractor';
        fields.hidden = !contractor;
        fields.disabled = !contractor; // disabled inputs aren't submitted or validated
        fields.querySelectorAll('[data-contractor-required]').forEach((input) => { input.required = contractor; });
    };
    role.addEventListener('change', toggle);
    toggle();
}
