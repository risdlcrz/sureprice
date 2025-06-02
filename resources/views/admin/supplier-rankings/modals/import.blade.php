<!-- Import Suppliers Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Suppliers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('supplier-rankings.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" accept=".xlsx,.xls" required>
                        <div class="form-text">Please upload an Excel file (.xlsx or .xls) containing supplier data.</div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('supplier-rankings.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div> 