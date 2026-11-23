// Employee form: contractor address fields are shown (and required) only for the contractor role.
const role = document.getElementById('role');
const fields = document.getElementById('contractor-fields');

if (role && fields) {
    const toggle = () => {
        const contractor = role.value === 'contractor';
        fields.hidden = !contractor;
        fields.disabled = !contractor; // disabled inputs aren't submitted or validated
        fields.querySelectorAll('[data-contractor-required], [data-address-control]').forEach((input) => {
            input.required = contractor && !('optional' in input.dataset);
        });
    };
    role.addEventListener('change', toggle);
    toggle();
    // The address script may swap selects for text inputs or mark the region optional after loading.
    new MutationObserver(toggle).observe(fields, { subtree: true, childList: true, attributeFilter: ['data-optional'] });
}
