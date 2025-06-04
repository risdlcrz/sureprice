<!-- Import Suppliers Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Suppliers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suppliers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="importFile" name="import_file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">Supported formats: CSV, Excel (.xlsx, .xls)</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">File Format Requirements:</h6>
                        <ul class="mb-0">
                            <li>First row should contain headers</li>
                            <li>Required columns: Company Name, Contact Person, Email, Mobile Number, Supplier Type</li>
                            <li>Optional columns: Rating, Notes</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.suppliers.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 