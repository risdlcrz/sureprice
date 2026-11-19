// Purchase order form: add/remove item rows (cloned from <template id="item-row-template">).
const container = document.getElementById('items-container');
const template = document.getElementById('item-row-template');

if (container && template) {
    let nextIndex = container.querySelectorAll('.item-row').length;
    const addRow = () => container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));

    document.getElementById('add-item')?.addEventListener('click', addRow);

    container.addEventListener('click', (e) => {
        const button = e.target.closest('.remove-item');
        // keep at least one row: an order needs an item
        if (button && container.querySelectorAll('.item-row').length > 1) button.closest('.item-row').remove();
    });

    // Default the unit from the chosen material
    container.addEventListener('change', (e) => {
        if (!e.target.matches('.material-select')) return;
        const unit = e.target.selectedOptions[0]?.dataset.unit;
        const input = e.target.closest('.item-row')?.querySelector('.unit');
        if (unit && input && !input.value) input.value = unit;
    });

    if (!container.querySelector('.item-row')) addRow();
}
