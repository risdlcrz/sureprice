{{-- CSV import modal (users / companies). Uses $type. --}}
<x-modal-dialog id="importCsvModal" dialog-class="modal-lg">
    <x-slot:title>Import Data via CSV</x-slot:title>
            <div class="modal-body">
                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Instructions</h6>
                    <ol class="mb-0">
                        <li>Download the appropriate template file below</li>
                        <li>Fill in the required information in the template</li>
                        <li>Save the file as CSV format</li>
                        <li>Upload the filled template using the form below</li>
                    </ol>
                </div>
                
                <!-- Template Downloads -->
                <h6 class="mt-4">Download CSV Template:</h6>
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('information-management.template', ['type' => 'employee']) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-download me-1"></i> Employee Template
                    </a>
                    <a href="{{ route('information-management.template', ['type' => 'contractor']) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-download me-1"></i> Contractor Template
                    </a>
                </div>

                <hr>

                <!-- Upload Form -->
                <h6 class="mt-4">Upload Your CSV File:</h6>
                <form action="{{ route('information-management.import') }}" method="POST" enctype="multipart/form-data" id="csvUploadForm">
                    @csrf
                    <div class="mb-3">
                        <label for="import_type" class="form-label">Select Import Type:</label>
                        <select class="form-select" name="type" id="import_type" required>
                            <option value="employee">Employee</option>
                            <option value="contractor">Contractor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Choose CSV File:</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" required {{ \App\Support\Uploads::attributes('csv', 1) }}>
                    </div>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Important Notes</h6>
                        <ul class="mb-0">
                            <li>All fields marked with * are required</li>
                            <li>For employees, role must be either 'procurement' or 'warehousing'</li>
                            <li>Email addresses must be unique</li>
                            <li>Usernames must be unique</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import CSV</button>
                    </div>
                </form>
            </div>
</x-modal-dialog>
