function addRow() {
    const table = document.getElementById("materials-table").getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();
    newRow.innerHTML = `
        <td></td>
        <td><input type="text" name="item_description[]" class="form-control"></td>
        <td><input type="number" name="quantity[]" class="form-control"></td>
        <td><input type="text" name="unit[]" class="form-control"></td>
        <td><input type="text" name="specs[]" class="form-control"></td>
    `;
}