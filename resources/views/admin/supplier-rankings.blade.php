@extends('layouts.app')

@section('content')
<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container py-4">
    <!-- Beautiful Success Message -->
    @if (session('success'))
        <div class="alert alert-success beautiful-alert alert-dismissible fade show" id="successAlert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <!-- Error Messages -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Category Selection Dropdown -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="d-flex align-items-center gap-3" id="categoryForm">
                        <label class="form-label mb-0"><strong>Rank By:</strong></label>
                        <select name="category" class="form-select" id="categorySelect">
                            @foreach ($validCategories as $value => $label)
                                <option value="{{ $value }}" {{ $category === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <select name="order" class="form-select" id="orderSelect">
                            <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Highest First</option>
                            <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>Lowest First</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rankings Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">Supplier {{ $validCategories[$category] }} Rankings</h4>
            <div class="chart-container">
                <canvas id="rankingsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Add Supplier Button -->
    <div class="text-end mb-4">
        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import"></i> Import Suppliers
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Add Supplier
        </button>
    </div>

    <!-- Supplier List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Rank</th>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Type</th>
                            <th style="width: 100px;">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr class="supplier-row" data-supplier-id="{{ $supplier['id'] }}">
                                <td>
                                    <span class="badge bg-success rounded-pill">#{{ $supplier['rank'] }}</span>
                                </td>
                                <td>
                                    <a href="#" class="supplier-link text-primary text-decoration-none">
                                        {{ $supplier['company'] }}
                                    </a>
                                </td>
                                <td>{{ $supplier['contact_person'] ?? 'N/A' }}</td>
                                <td>{{ $supplier['email'] ?? 'N/A' }}</td>
                                <td>{{ $supplier['mobile_number'] ?? 'N/A' }}</td>
                                <td>{{ $supplier['supplier_type'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $supplier['rating'] >= 4.5 ? 'success' : 
                                        ($supplier['rating'] >= 4.0 ? 'info' : 
                                         ($supplier['rating'] >= 3.0 ? 'warning' : 'danger'))
                                    }}">
                                        {{ number_format($supplier['rating'], 1) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('supplier-rankings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Account Information -->
                        <div class="form-section card mb-4 p-4 bg-light">
                            <h4 class="section-title">Account Information</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="Username">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Basic Information</h4>
                            <hr>
                            <div class="mb-3">
                                <input type="text" id="company" name="company" class="form-control" placeholder="Supplier Name / Company Name" required>
                            </div>
                            <div class="mb-3">
                                <select name="supplier_type" id="supplier_type" class="form-select" required>
                                    <option value="">Type of Supplier</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Company">Company</option>
                                    <option value="Contractor">Contractor</option>
                                    <option value="Material Supplier">Material Supplier</option>
                                    <option value="Equipment Rental">Equipment Rental</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="text" id="business_reg_no" name="business_reg_no" class="form-control" placeholder="Business Registration Number (if applicable)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">DTI/SEC Registration (Upload)</label>
                                <input type="file" id="dti_sec_registration" name="dti_sec_registration" class="form-control">
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Contact Details</h4>
                            <hr>
                            <div class="mb-3">
                                <input type="text" name="contact_person" class="form-control" placeholder="Contact Person Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="designation" class="form-control" placeholder="Designation / Role">
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="mobile_number" class="form-control" placeholder="Mobile Number" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="telephone_number" class="form-control" placeholder="Telephone Number (optional)">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="street" class="form-control" placeholder="Street">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="city" class="form-control" placeholder="City/Municipality">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="province" class="form-control" placeholder="Province">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="zip_code" class="form-control" placeholder="ZIP Code">
                            </div>
                        </div>

                        <!-- Primary Products and Services -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Primary Products/Services Offered</h4>
                            <hr>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Building Materials</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="Cement"> Cement</div>
                                    <div><input type="checkbox" name="materials[]" value="Steel Bars"> Steel Bars</div>
                                    <div><input type="checkbox" name="materials[]" value="Gravel"> Gravel</div>
                                    <div><input type="checkbox" name="materials[]" value="Sand"> Sand</div>
                                    <div><input type="checkbox" name="materials[]" value="Hollow Blocks"> Hollow Blocks</div>
                                    <div><input type="checkbox" name="materials[]" value="Wood/Lumber"> Wood/Lumber</div>
                                    <div><input type="checkbox" name="materials[]" value="Plywood"> Plywood</div>
                                    <div><input type="checkbox" name="materials[]" value="Metal Roofing"> Metal Roofing</div>
                                </div>
                            </div>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Finishing Materials</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="Paint"> Paint</div>
                                    <div><input type="checkbox" name="materials[]" value="Tiles"> Tiles</div>
                                    <div><input type="checkbox" name="materials[]" value="Glass"> Glass</div>
                                    <div><input type="checkbox" name="materials[]" value="Aluminum"> Aluminum</div>
                                    <div><input type="checkbox" name="materials[]" value="Ceiling Boards"> Ceiling Boards</div>
                                    <div><input type="checkbox" name="materials[]" value="Wallpaper"> Wallpaper</div>
                                    <div><input type="checkbox" name="materials[]" value="Vinyl Flooring"> Vinyl Flooring</div>
                                    <div><input type="checkbox" name="materials[]" value="Carpet"> Carpet</div>
                                </div>
                            </div>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Plumbing & Electrical</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="PVC Pipes"> PVC Pipes</div>
                                    <div><input type="checkbox" name="materials[]" value="Electrical Wires"> Electrical Wires</div>
                                    <div><input type="checkbox" name="materials[]" value="Lighting Fixtures"> Lighting Fixtures</div>
                                    <div><input type="checkbox" name="materials[]" value="Plumbing Fixtures"> Plumbing Fixtures</div>
                                    <div><input type="checkbox" name="materials[]" value="Sanitary Wares"> Sanitary Wares</div>
                                    <div><input type="checkbox" name="materials[]" value="HVAC Systems"> HVAC Systems</div>
                                </div>
                            </div>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Hardware & Tools</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="Hand Tools"> Hand Tools</div>
                                    <div><input type="checkbox" name="materials[]" value="Power Tools"> Power Tools</div>
                                    <div><input type="checkbox" name="materials[]" value="Safety Equipment"> Safety Equipment</div>
                                    <div><input type="checkbox" name="materials[]" value="Hardware Supplies"> Hardware Supplies</div>
                                    <div><input type="checkbox" name="materials[]" value="Construction Equipment"> Construction Equipment</div>
                                </div>
                            </div>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Services</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="Scaffolding Rental"> Scaffolding Rental</div>
                                    <div><input type="checkbox" name="materials[]" value="Equipment Rental"> Equipment Rental</div>
                                    <div><input type="checkbox" name="materials[]" value="Skilled Labor"> Skilled Labor</div>
                                    <div><input type="checkbox" name="materials[]" value="Interior Design"> Interior Design</div>
                                    <div><input type="checkbox" name="materials[]" value="Architectural Services"> Architectural Services</div>
                                    <div><input type="checkbox" name="materials[]" value="Engineering Services"> Engineering Services</div>
                                    <div><input type="checkbox" name="materials[]" value="Project Management"> Project Management</div>
                                </div>
                            </div>
                            <div class="category-box mb-3">
                                <div><strong class="text-success">Specialty Items</strong></div>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div><input type="checkbox" name="materials[]" value="Doors & Windows"> Doors & Windows</div>
                                    <div><input type="checkbox" name="materials[]" value="Kitchen Cabinets"> Kitchen Cabinets</div>
                                    <div><input type="checkbox" name="materials[]" value="Countertops"> Countertops</div>
                                    <div><input type="checkbox" name="materials[]" value="Built-in Furniture"> Built-in Furniture</div>
                                    <div><input type="checkbox" name="materials[]" value="Acoustic Panels"> Acoustic Panels</div>
                                    <div><input type="checkbox" name="materials[]" value="Fire Protection"> Fire Protection</div>
                                    <div><input type="checkbox" name="materials[]" value="Security Systems"> Security Systems</div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Banking -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Terms & Banking</h4>
                            <hr>
                            <div class="mb-3">
                                <select name="payment_terms" class="form-select">
                                    <option value="">Preferred Payment Terms</option>
                                    <option value="7 days">7 days</option>
                                    <option value="15 days">15 days</option>
                                    <option value="30 days">30 days</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="vat_registered" class="form-select">
                                    <option value="Yes">VAT Registered?</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="use_sureprice" class="form-select">
                                    <option value="Yes">Willing to provide quotations through SurePrice?</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Upload Documents -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Upload Documents</h4>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Business Permit / Mayor's Permit</label>
                                <input type="file" name="mayors_permit" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valid ID (Owner or Rep)</label>
                                <input type="file" name="valid_id" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company Profile / Portfolio (Optional)</label>
                                <input type="file" name="company_profile" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sample Price List (Optional)</label>
                                <input type="file" name="price_list" class="form-control">
                            </div>
                        </div>

                        <!-- Bank & Payout Details -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Bank & Payout Details (Optional)</h4>
                            <hr>
                            <div class="mb-3">
                                <input type="text" name="bank_name" class="form-control" placeholder="Bank Name">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="account_name" class="form-control" placeholder="Account Name">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="account_number" class="form-control" placeholder="Account Number">
                            </div>
                        </div>

                        <!-- Agreement & Consent -->
                        <div class="form-section mb-4">
                            <h4 class="section-title">Agreement & Consent</h4>
                            <hr>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms">
                                <label class="form-check-label" for="agree_terms">
                                    I agree to the Terms and Conditions and Privacy Policy
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="consent_contact" id="consent_contact">
                                <label class="form-check-label" for="consent_contact">
                                    I consent to be contacted for verification and partnership opportunities
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Supplier Details Modal -->
    <div class="modal fade" id="supplierDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h4 class="section-title">Basic Information</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Company Name:</strong> <span id="view_company"></span></p>
                                <p><strong>Type of Supplier:</strong> <span id="view_supplier_type"></span></p>
                                <p><strong>Business Registration No:</strong> <span id="view_business_reg_no"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="form-section">
                        <h4 class="section-title">Contact Details</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Contact Person:</strong> <span id="view_contact_person"></span></p>
                                <p><strong>Designation:</strong> <span id="view_designation"></span></p>
                                <p><strong>Email:</strong> <span id="view_email"></span></p>
                                <p><strong>Mobile Number:</strong> <span id="view_mobile_number"></span></p>
                                <p><strong>Telephone Number:</strong> <span id="view_telephone_number"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Street:</strong> <span id="view_street"></span></p>
                                <p><strong>City:</strong> <span id="view_city"></span></p>
                                <p><strong>Province:</strong> <span id="view_province"></span></p>
                                <p><strong>ZIP Code:</strong> <span id="view_zip_code"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Products and Services -->
                    <div class="form-section">
                        <h4 class="section-title">Primary Products and Services</h4>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div id="view_materials" class="materials-list">
                                    <!-- Materials will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Banking -->
                    <div class="form-section">
                        <h4 class="section-title">Terms & Banking</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Payment Terms:</strong> <span id="view_payment_terms"></span></p>
                                <p><strong>VAT Registered:</strong> <span id="view_vat_registered"></span></p>
                                <p><strong>Use SurePrice:</strong> <span id="view_use_sureprice"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Bank Name:</strong> <span id="view_bank_name"></span></p>
                                <p><strong>Account Name:</strong> <span id="view_account_name"></span></p>
                                <p><strong>Account Number:</strong> <span id="view_account_number"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="form-section">
                        <h4 class="section-title">Documents</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <strong>DTI/SEC Registration:</strong>
                                    <a id="view_dti_sec_registration_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                                <p>
                                    <strong>Accreditation Documents:</strong>
                                    <a id="view_accreditation_docs_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                                <p>
                                    <strong>Mayor's Permit:</strong>
                                    <a id="view_mayors_permit_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong>Valid ID:</strong>
                                    <a id="view_valid_id_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                                <p>
                                    <strong>Company Profile:</strong>
                                    <a id="view_company_profile_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                                <p>
                                    <strong>Price List:</strong>
                                    <a id="view_price_list_link" href="#" target="_blank" class="document-link">View Document</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation History -->
                    <div class="form-section">
                        <h4 class="section-title">Evaluation History</h4>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Engagement</th>
                                        <th>Delivery Speed</th>
                                        <th>Performance</th>
                                        <th>Quality</th>
                                        <th>Cost Variance</th>
                                        <th>Sustainability</th>
                                        <th>Final Score</th>
                                    </tr>
                                </thead>
                                <tbody id="view_evaluation_history">
                                    <!-- Evaluation history will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="delete_supplier_form" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger me-2" id="delete_supplier_btn">Delete Supplier</button>
                    </form>
                    <button type="button" class="btn btn-warning me-2" id="evaluate_supplier_btn">Evaluate Supplier</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_supplier_btn">Edit Supplier</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 mb-3">Are you sure you want to <span class="fw-bold text-danger">delete this supplier</span>?<br>This action <span class="fw-bold">cannot be undone</span>.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Suppliers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('supplier-rankings.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluate Supplier Modal -->
    <div class="modal fade" id="evaluateSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evaluate Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="evaluate_supplier_form" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Evaluation Alerts -->
                        @if ($errors->any() && session('show_evaluate_modal'))
                            <div class="alert alert-danger beautiful-alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('evaluate_success'))
                            <div class="alert alert-success beautiful-alert">
                                {{ session('evaluate_success') }}
                            </div>
                        @endif
                        <!-- Evaluation Form Fields -->
                        <div class="mb-3">
                            <label for="engagement_score" class="form-label">Engagement</label>
                            <input type="number" step="0.1" min="0" max="5" name="engagement_score" id="engagement_score" class="form-control" required value="{{ old('engagement_score') }}">
                        </div>
                        <div class="mb-3">
                            <label for="delivery_speed_score" class="form-label">Delivery Speed</label>
                            <input type="number" step="0.1" min="0" max="5" name="delivery_speed_score" id="delivery_speed_score" class="form-control" required value="{{ old('delivery_speed_score') }}">
                        </div>
                        <div class="mb-3">
                            <label for="performance_score" class="form-label">Performance</label>
                            <input type="number" step="0.1" min="0" max="5" name="performance_score" id="performance_score" class="form-control" required value="{{ old('performance_score') }}">
                        </div>
                        <div class="mb-3">
                            <label for="quality_score" class="form-label">Quality</label>
                            <input type="number" step="0.1" min="0" max="5" name="quality_score" id="quality_score" class="form-control" required value="{{ old('quality_score') }}">
                        </div>
                        <div class="mb-3">
                            <label for="cost_variance_score" class="form-label">Cost Variance</label>
                            <input type="number" step="0.1" min="0" max="5" name="cost_variance_score" id="cost_variance_score" class="form-control" required value="{{ old('cost_variance_score') }}">
                        </div>
                        <div class="mb-3">
                            <label for="sustainability_score" class="form-label">Sustainability</label>
                            <input type="number" step="0.1" min="0" max="5" name="sustainability_score" id="sustainability_score" class="form-control" required value="{{ old('sustainability_score') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Evaluation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize the rankings chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rankingsChart').getContext('2d');
        
        // Get supplier data
        const suppliers = @json($suppliers);

        // Prepare chart data
        const labels = suppliers.map(s => s.company);
        const scores = suppliers.map(s => s.rating);
        const colors = scores.map(score => 
            score >= 4.5 ? '#198754' :  // success
            score >= 4.0 ? '#0dcaf0' :  // info
            score >= 3.0 ? '#ffc107' :  // warning
            '#dc3545'                   // danger
        );

        // Create the chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $validCategories[$category] }}',
                    data: scores,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 0.5
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Score: ${context.parsed.y.toFixed(1)}`;
                            }
                        }
                    }
                }
            }
        });
    });

    // Handle category and order changes
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            document.getElementById('categoryForm').submit();
        });
    }
    const orderSelect = document.getElementById('orderSelect');
    if (orderSelect) {
        orderSelect.addEventListener('change', function() {
            document.getElementById('categoryForm').submit();
        });
    }

    // Function to format date
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Function to populate supplier details modal
    function populateSupplierDetails(supplier) {
        // Basic Information
        document.getElementById('view_company').textContent = supplier.company;
        document.getElementById('view_supplier_type').textContent = supplier.supplier_type;
        document.getElementById('view_business_reg_no').textContent = supplier.business_reg_no;

        // Contact Details
        document.getElementById('view_contact_person').textContent = supplier.contact_person;
        document.getElementById('view_designation').textContent = supplier.designation;
        document.getElementById('view_email').textContent = supplier.email;
        document.getElementById('view_mobile_number').textContent = supplier.mobile_number;
        document.getElementById('view_telephone_number').textContent = supplier.telephone_number;
        document.getElementById('view_street').textContent = supplier.street;
        document.getElementById('view_city').textContent = supplier.city;
        document.getElementById('view_province').textContent = supplier.province;
        document.getElementById('view_zip_code').textContent = supplier.zip_code;

        // Primary Products and Services
        const materialsList = document.getElementById('view_materials');
        materialsList.innerHTML = '';
        if (supplier.materials && supplier.materials.length > 0) {
            const ul = document.createElement('ul');
            supplier.materials.forEach(material => {
                const li = document.createElement('li');
                li.textContent = material.material_name;
                ul.appendChild(li);
            });
            materialsList.appendChild(ul);
        } else {
            materialsList.textContent = 'No materials listed';
        }

        // Terms & Banking
        document.getElementById('view_payment_terms').textContent = supplier.payment_terms;
        document.getElementById('view_vat_registered').textContent = supplier.vat_registered ? 'Yes' : 'No';
        document.getElementById('view_use_sureprice').textContent = supplier.use_sureprice ? 'Yes' : 'No';
        document.getElementById('view_bank_name').textContent = supplier.bank_name;
        document.getElementById('view_account_name').textContent = supplier.account_name;
        document.getElementById('view_account_number').textContent = supplier.account_number;

        // Documents
        const documentLinks = {
            'view_dti_sec_registration_link': supplier.dti_sec_registration,
            'view_accreditation_docs_link': supplier.accreditation_docs,
            'view_mayors_permit_link': supplier.mayors_permit,
            'view_valid_id_link': supplier.valid_id,
            'view_company_profile_link': supplier.company_profile,
            'view_price_list_link': supplier.price_list
        };

        for (const [linkId, documentPath] of Object.entries(documentLinks)) {
            const link = document.getElementById(linkId);
            if (documentPath) {
                link.href = `/storage/${documentPath}`;
                link.style.display = 'inline';
            } else {
                link.style.display = 'none';
            }
        }

        // Evaluation History
        const evaluationHistory = document.getElementById('view_evaluation_history');
        evaluationHistory.innerHTML = '';
        if (supplier.evaluations && supplier.evaluations.length > 0) {
            supplier.evaluations.forEach(evaluation => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(evaluation.evaluation_date)}</td>
                    <td>${evaluation.engagement_score}</td>
                    <td>${evaluation.delivery_speed_score}</td>
                    <td>${evaluation.performance_score}</td>
                    <td>${evaluation.quality_score}</td>
                    <td>${evaluation.cost_variance_score}</td>
                    <td>${evaluation.sustainability_score}</td>
                    <td>${evaluation.final_score}</td>
                `;
                evaluationHistory.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="8" class="text-center">No evaluation history available</td>';
            evaluationHistory.appendChild(row);
        }

        // Set up edit button
        const editButton = document.getElementById('edit_supplier_btn');
        editButton.onclick = () => {
            window.location.href = `/sureprice/public/supplier-rankings/${supplier.id}/edit`;
        };
    }

    // Add click event listeners to supplier rows
    document.addEventListener('DOMContentLoaded', function() {
        const supplierRows = document.querySelectorAll('.supplier-row');
        supplierRows.forEach(row => {
            row.addEventListener('click', function() {
                const supplierId = this.dataset.supplierId;
                fetch(`${window.location.origin}/sureprice/public/supplier-rankings/${supplierId}`)
                    .then(response => response.json())
                    .then(supplier => {
                        populateSupplierDetails(supplier);
                        // Set delete form action
                        const deleteForm = document.getElementById('delete_supplier_form');
                        deleteForm.action = `${window.location.origin}/sureprice/public/supplier-rankings/${supplierId}`;
                        // Set edit button action
                        const editButton = document.getElementById('edit_supplier_btn');
                        editButton.onclick = () => {
                            window.location.href = `${window.location.origin}/sureprice/public/supplier-rankings/${supplierId}/edit`;
                        };
                        const modal = new bootstrap.Modal(document.getElementById('supplierDetailsModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching supplier details:', error);
                        alert('Error loading supplier details. Please try again.');
                    });
            });
        });
    });

    // Handle materials input
    document.addEventListener('DOMContentLoaded', function() {
        const materialInput = document.getElementById('material_input');
        const addMaterialBtn = document.getElementById('add_material_btn');
        const materialsList = document.getElementById('materials_list');
        const materialsInput = document.getElementById('materials_input');
        let materials = [];

        function updateMaterialsList() {
            materialsList.innerHTML = '';
            materials.forEach((material, index) => {
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center gap-2 mb-2';
                div.innerHTML = `
                    <span class="badge bg-primary">${material}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-material" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                materialsList.appendChild(div);
            });
            materialsInput.value = JSON.stringify(materials);
        }

        addMaterialBtn.addEventListener('click', function() {
            const material = materialInput.value.trim();
            if (material && !materials.includes(material)) {
                materials.push(material);
                updateMaterialsList();
                materialInput.value = '';
            }
        });

        materialInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addMaterialBtn.click();
            }
        });

        materialsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-material')) {
                const index = e.target.dataset.index;
                materials.splice(index, 1);
                updateMaterialsList();
            }
        });
    });

    // Delete Supplier with custom modal
    let currentSupplierId = null;
    document.addEventListener('DOMContentLoaded', function() {
        // Set up delete button
        document.getElementById('delete_supplier_btn').onclick = function() {
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        };
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.getElementById('delete_supplier_form').submit();
        };
        // Set up evaluate button
        document.getElementById('evaluate_supplier_btn').onclick = function() {
            const modal = new bootstrap.Modal(document.getElementById('evaluateSupplierModal'));
            modal.show();
        };
        // Set supplier id for delete/evaluate
        const supplierRows = document.querySelectorAll('.supplier-row');
        supplierRows.forEach(row => {
            row.addEventListener('click', function() {
                currentSupplierId = this.dataset.supplierId;
                // ... existing code ...
            });
        });
        // Set form action for evaluation
        document.getElementById('evaluate_supplier_form').onsubmit = function(e) {
            e.preventDefault();
            if (currentSupplierId) {
                this.action = `/supplier-rankings/${currentSupplierId}/evaluate`;
                this.submit();
            }
        };
    });

    // Auto-show Evaluate modal if there are errors or success
    @if ($errors->any() && session('show_evaluate_modal'))
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('evaluateSupplierModal'));
            modal.show();
            setTimeout(() => {
                document.querySelector('#evaluateSupplierModal .beautiful-alert').scrollIntoView({behavior: 'smooth'});
            }, 300);
        });
    @endif
    @if (session('evaluate_success'))
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('evaluateSupplierModal'));
            modal.show();
            setTimeout(() => {
                document.querySelector('#evaluateSupplierModal .beautiful-alert').scrollIntoView({behavior: 'smooth'});
            }, 300);
        });
    @endif

    // Auto-dismiss success alert after 3 seconds
    if (document.getElementById('successAlert')) {
        setTimeout(function() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                alert.classList.remove('show');
            }
        }, 3000);
    }
</script>
@endpush

@push('styles')
<style>
    .form-section {
        margin-bottom: 2rem;
        padding: 2rem 2rem 1.5rem 2rem;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .form-section.card {
        background: #f8f9fa;
        border: none;
        box-shadow: none;
    }
    .section-title {
        color: #23612c;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .form-label {
        font-weight: 500;
        color: #23612c;
    }
    .form-control, .form-select {
        border-radius: 8px;
        min-height: 48px;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .form-check-label {
        font-size: 1rem;
    }
    .form-check-input {
        margin-top: 0.2rem;
    }
    .d-flex.flex-wrap.gap-3.mb-2 > div {
        min-width: 180px;
        margin-bottom: 0.5rem;
    }
    .text-success {
        color: #23612c !important;
        font-weight: 600;
        margin-top: 1rem;
    }
    hr {
        margin: 1rem 0 1.5rem 0;
        border-top: 2px solid #e9ecef;
    }
    .modal-content {
        border-radius: 16px;
    }
    .modal-header, .modal-footer {
        border: none;
        background: #f8f9fa;
        border-radius: 16px 16px 0 0;
    }
    .modal-footer {
        border-radius: 0 0 16px 16px;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 2rem;
    }
    .supplier-link {
        color: #0d6efd;
        text-decoration: none;
        cursor: pointer;
    }
    .supplier-link:hover {
        text-decoration: underline;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }
    .remove-material {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    #materials_list {
        max-height: 200px;
        overflow-y: auto;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background-color: #fff;
    }
    .category-box {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 1.25rem 1.5rem 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .category-box strong {
        font-size: 1.08rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .d-flex.flex-wrap.gap-3.mb-2 > div {
        min-width: 180px;
        margin-bottom: 0.5rem;
    }
    .beautiful-alert {
        border-radius: 12px;
        font-size: 1.1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        padding: 1.25rem 2rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(90deg, #e9f7ef 0%, #d4edda 100%);
        color: #23612c;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .alert-success.beautiful-alert {
        background: linear-gradient(90deg, #e9f7ef 0%, #d4edda 100%);
        color: #23612c;
    }
    #deleteConfirmModal .modal-header {
        background: linear-gradient(90deg, #ff5858 0%, #ffb199 100%);
        color: #fff;
    }
    #deleteConfirmModal .modal-footer .btn-danger {
        background: #d90429;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
    }
    #deleteConfirmModal .modal-footer .btn-secondary {
        font-weight: 500;
    }
</style>
@endpush
@endsection