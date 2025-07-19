<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - GDC Admin Center</title>
  <link rel="stylesheet" href="{{ asset('css/signup.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Enhanced error styling */
    .form-group.has-error input,
    .form-group.has-error select,
    .form-group.has-error textarea {
      border-color: #e74c3c !important;
      box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25) !important;
    }
    
    .form-group.has-error .input-with-icon,
    .form-group.has-error .select-with-icon {
      border-color: #e74c3c !important;
    }
    
    .error-message {
      color: #e74c3c;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    /* Enhanced file upload styling */
    .file-upload-wrapper {
      position: relative;
    }
    
    .file-upload-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1rem;
      border: 2px dashed #ddd;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }
    
    .file-upload-label:hover {
      border-color: #02912d;
      background: #f0f8f0;
    }
    
    .file-upload-label.has-file {
      border-color: #02912d;
      background: #f0f8f0;
    }
    
    .file-upload-text {
      color: #555;
      font-weight: normal;
      transition: all 0.3s ease;
    }
    
    .file-upload-text.has-file {
      color: #02912d;
      font-weight: 500;
    }
    
    .file-preview {
      margin-top: 0.5rem;
      display: none;
    }
    
    .file-preview.show {
      display: block;
    }
    
    .file-preview-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.5rem;
      background: #e8f5e8;
      border: 1px solid #02912d;
      border-radius: 4px;
      margin-bottom: 0.25rem;
    }
    
    .file-preview-name {
      font-size: 0.875rem;
      color: #02912d;
      font-weight: 500;
    }
    
    .file-preview-remove {
      cursor: pointer;
      color: #e74c3c;
      font-weight: bold;
      font-size: 1.2rem;
      padding: 0 0.25rem;
    }
    
    .file-preview-remove:hover {
      color: #c0392b;
    }
    
    /* Optional text styling */
    .optional-text {
      color: #6c757d;
      font-size: 0.875rem;
      font-style: italic;
    }
    
    /* File requirements notice styling */
    .file-requirements-notice {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
      border-radius: 6px;
      padding: 0.75rem 1rem;
      margin: 0.5rem 0 0 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .file-requirements-notice i {
      font-size: 1.2rem;
      color: #856404;
    }
    
    /* Terms and conditions styling */
    .terms-links {
      color: #02912d;
      text-decoration: none;
      font-weight: 500;
    }
    
    .terms-links:hover {
      text-decoration: underline;
      color: #026f22;
    }
    
    /* Enhanced alert styling */
    .alert {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      border: 1px solid;
    }
    
    .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }
    
    .alert-danger h4 {
      margin-bottom: 0.5rem;
      color: #721c24;
    }
    
    .alert-danger ul {
      margin-bottom: 0;
      padding-left: 1.5rem;
    }
    
    .alert-danger li {
      margin-bottom: 0.25rem;
    }
    
    /* Duplicate error specific styling */
    .duplicate-error {
      background-color: #fff3cd;
      border-color: #ffeaa7;
      color: #856404;
      padding: 0.75rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      border-left: 4px solid #f39c12;
    }
    
    .duplicate-error strong {
      color: #856404;
    }
    
    /* Visually hidden file input for accessibility and browser validation */
    .visually-hidden-file {
      opacity: 0;
      width: 1px;
      height: 1px;
      position: absolute;
      z-index: -1;
    }

    .notification {
      position: fixed;
      top: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      min-width: 350px;
      max-width: 90vw;
      background: #fff;
      color: #e74c3c;
      border: 1px solid #e74c3c;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      padding: 1rem 1.5rem;
      border-radius: 8px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      animation: fadeInDown 0.4s;
    }
    @keyframes fadeInDown {
      from { opacity: 0; top: 0; }
      to { opacity: 1; top: 30px; }
    }
    .notification.success {
      color: #02912d;
      border-color: #02912d;
    }
    .notification .notification-content {
      flex: 1;
    }
  </style>
</head>
<body>

  <!-- Notification System -->
  @if (session('success'))
    <div class="notification success">
      <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
      </div>
      <div class="progress-bar"></div>
    </div>
  @endif

  @if ($errors->any())
    <div class="notification error">
      <div class="notification-content">
        <i class="fas fa-exclamation-circle"></i>
        <span>Registration failed. Please check the form for errors.</span>
        <ul style="margin: 0 0 0 1.5rem; padding: 0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      <div class="progress-bar"></div>
    </div>
  @endif

  <!-- Top Navigation -->
  <div class="top-bar">
    <img src="{{ asset('Images/gdc_logo.png') }}" alt="GDC Logo">
    <span class="top-title">GDC Admin Center</span>
  </div>

    <!-- Company Sign Up Form -->
        <div class="signup-box" id="company-form">
      <div class="form-header">
        <h2><i class="fas fa-building"></i> Company Registration</h2>
      <div class="approval-warning" id="approvalNotice" style="margin: 0.5rem 0 0 0; background: #e8f5e9; color: #256029; border: 1px solid #b2dfdb; border-radius: 6px; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem; position: relative;">
        <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
        <span style="flex:1"><strong>Notice:</strong> All client and supplier accounts must be reviewed and approved by an administrator before you can access the system. You will receive an email once your account is approved.</span>
        <button type="button" aria-label="Dismiss notice" onclick="document.getElementById('approvalNotice').style.display='none'" style="background: none; border: none; color: #256029; font-size: 1.2rem; cursor: pointer; position: absolute; top: 8px; right: 12px; line-height: 1;">&times;</button>
      </div>
      
      <!-- Dynamic file upload requirements notice -->
      <div class="file-requirements-notice" id="fileRequirementsNotice" style="display: none;">
        <i class="fas fa-info-circle"></i>
        <span id="fileRequirementsText"></span>
      </div>
      </div>
      
      <!-- Enhanced error display -->
      @if($errors->any())
      <div class="alert alert-danger">
          <h4><i class="fas fa-exclamation-triangle"></i> Registration Error</h4>
          
          <!-- Check for duplicate errors specifically -->
          @php
            $duplicateErrors = collect($errors->all())->filter(function($error) {
                return str_contains(strtolower($error), 'already been taken') || 
                       str_contains(strtolower($error), 'duplicate') ||
                       str_contains(strtolower($error), 'unique');
            });
          @endphp
          
          @if($duplicateErrors->count() > 0)
            <div class="duplicate-error">
              <strong><i class="fas fa-exclamation-triangle"></i> Duplicate Registration Detected</strong><br>
              The following information is already registered in our system:
              <ul>
                @foreach($duplicateErrors as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <small>If you believe this is an error, please contact support or try using different information.</small>
            </div>
          @endif
          
          <ul>
              @foreach($errors->all() as $error)
                  @if(!str_contains(strtolower($error), 'already been taken') && 
                      !str_contains(strtolower($error), 'duplicate') &&
                      !str_contains(strtolower($error), 'unique'))
                      <li>{{ $error }}</li>
                  @endif
              @endforeach
          </ul>
      </div>
      @endif
      
      <form method="POST" action="{{ route('register.company') }}" enctype="multipart/form-data" id="registrationForm">
        @csrf
        <input type="hidden" name="type" value="company">

      <!-- Company Role Section (moved to top) -->
      <div class="form-section">
        <div class="form-group @error('designation') has-error @enderror">
          <label for="designation">Company Role <span class="text-danger">*</span></label>
          <div class="select-with-icon">
            <i class="fas fa-user-tag"></i>
            <select id="designation" name="designation" required>
              <option value="">Select company role</option>
              <option value="client" {{ old('designation') == 'client' ? 'selected' : '' }}>Client</option>
              <option value="supplier" {{ old('designation') == 'supplier' ? 'selected' : '' }}>Supplier</option>
            </select>
          </div>
          @error('designation')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
        </div>

        <!-- Client Type Dropdown (only if Client is selected) -->
        <div class="form-group" id="client_type_group" style="display:none;">
          <label for="client_type">Are you registering as an Individual or a Company? <span class="text-danger">*</span></label>
          <div class="select-with-icon">
            <i class="fas fa-user"></i>
            <select id="client_type" name="client_type">
              <option value="">Select type</option>
              <option value="individual" {{ old('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
              <option value="company" {{ old('client_type') == 'company' ? 'selected' : '' }}>Company</option>
            </select>
          </div>
        </div>
      </div>

        <!-- Account Information Section -->
        <div class="form-section">
          <h3><i class="fas fa-user-circle"></i> Account Information</h3>
          
          <div class="form-row">
            <div class="form-group @error('username') has-error @enderror">
              <label for="company_username">Username <span class="text-danger">*</span></label>
              <div class="input-with-icon">
                <i class="fas fa-at"></i>
                <input type="text" id="company_username" name="username" value="{{ old('username') }}" placeholder="Choose a username" required />
              </div>
              @error('username')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group @error('password') has-error @enderror">
              <label for="company_password">Password <span class="text-danger">*</span></label>
              <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="company_password" name="password" placeholder="Create a password" required />
              </div>
              @error('password')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group @error('password_confirmation') has-error @enderror">
              <label for="company_password_confirmation">Confirm Password <span class="text-danger">*</span></label>
              <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="company_password_confirmation" name="password_confirmation" placeholder="Confirm your password" required />
              </div>
              @error('password_confirmation')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <!-- Basic Information Section -->
        <div class="form-section">
          <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
          
        <div class="form-group @error('company_name') has-error @enderror" id="company_name_group">
            <label for="company_name">Company Name <span class="text-danger" id="company_name_required">*</span></label>
            <div class="input-with-icon">
              <i class="fas fa-building"></i>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Enter company name" />
            </div>
            @error('company_name')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
          </div>
          
          <div class="form-row">
            <div class="form-group @error('supplier_type') has-error @enderror">
              <label for="supplier_type">Type of Company <span class="text-danger">*</span></label>
                @php
                    $company_types = [
                        'Construction & Engineering',
                        'Architecture & Design',
                        'Real Estate & Property Development',
                        'Manufacturing',
                        'Wholesale & Distribution',
                        'Retail & E-Commerce',
                        'Information Technology & Software',
                        'Telecommunications',
                        'Healthcare & Medical',
                        'Logistics & Transportation',
                        'Energy & Utilities',
                        'Financial Services',
                        'Legal & Compliance',
                        'Education & Training',
                        'Marketing & Advertising',
                        'Hospitality & Tourism',
                        'Government & Public Sector',
                        'Nonprofit / NGO',
                        'Other'
                    ];
                @endphp
              <div class="select-with-icon">
                <i class="fas fa-tag"></i>
                <select id="supplier_type" name="supplier_type" required>
                  <option value="">Select company type</option>
                    @foreach($company_types as $type)
                        <option value="{{ $type }}" {{ old('supplier_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
              </div>
              @error('supplier_type')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group @error('other_supplier_type') has-error @enderror" id="other_supplier_type_group" style="{{ old('supplier_type') == 'Other' ? 'display:block' : 'display:none' }}">
              <label for="other_supplier_type">Specify Type</label>
              <div class="input-with-icon">
                <i class="fas fa-pen"></i>
                <input type="text" id="other_supplier_type" name="other_supplier_type" value="{{ old('other_supplier_type') }}" placeholder="Please specify company type" />
              </div>
              @error('other_supplier_type')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
          </div>
          
        <div class="form-group file-upload-group @error('dti_sec_registration') has-error @enderror" id="dti_sec_registration_group">
          <label for="dti_sec_registration">DTI/SEC Registration <span class="text-danger" id="dti_required_star">*</span> <span id="dti_optional_text" class="optional-text" style="display:none;">(Optional for clients)</span></label>
            <div class="file-upload-wrapper">
              <label class="file-upload-label" for="dti_sec_registration">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
                <input type="file" id="dti_sec_registration" name="dti_sec_registration" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
              </label>
              <div class="file-preview" id="dti_sec_preview"></div>
            </div>
            @error('dti_sec_registration')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
          </div>
        </div>

<!-- Contact Details Section -->
<div class="form-section">
  <h3><i class="fas fa-address-book"></i> Contact Details</h3>
  
  <div class="form-group @error('contact_person') has-error @enderror">
    <label for="contact_person">Contact Person <span class="text-danger">*</span></label>
    <div class="input-with-icon">
      <i class="fas fa-user"></i>
      <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="Full name of contact person" required />
    </div>
    @error('contact_person')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('email') has-error @enderror">
    <label for="company_email">Email Address <span class="text-danger">*</span></label>
    <div class="input-with-icon">
      <i class="fas fa-envelope"></i>
      <input type="email" id="company_email" name="email" value="{{ old('email') }}" placeholder="company.email@example.com" required />
    </div>
    @error('email')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-row">
    <div class="form-group @error('mobile_number') has-error @enderror">
      <label for="mobile_number">Mobile Number <span class="text-danger">*</span></label>
      <div class="input-with-icon">
        <i class="fas fa-mobile-alt"></i>
        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="e.g. 09123456789" required />
      </div>
      @error('mobile_number')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group @error('telephone_number') has-error @enderror">
      <label for="telephone_number">Telephone Number</label>
      <div class="input-with-icon">
        <i class="fas fa-phone"></i>
        <input type="text" id="telephone_number" name="telephone_number" value="{{ old('telephone_number') }}" placeholder="e.g. 8123456" />
      </div>
      @error('telephone_number')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
  </div>
  
  <div class="form-group @error('street') has-error @enderror">
    <label for="street">Street Address <span class="text-danger">*</span></label>
    <div class="input-with-icon">
      <i class="fas fa-map-marker-alt"></i>
      <input type="text" id="street" name="street" value="{{ old('street') }}" placeholder="Building/Street name" required />
    </div>
    @error('street')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('barangay') has-error @enderror">
    <label for="barangay">Barangay <span class="text-danger">*</span></label>
    <div class="input-with-icon">
      <i class="fas fa-map-marker-alt"></i>
      <input type="text" id="barangay" name="barangay" value="{{ old('barangay') }}" placeholder="Barangay name" required />
    </div>
    @error('barangay')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>

  <div class="form-row">
    <div class="form-group @error('city') has-error @enderror">
      <label for="city">City/Municipality <span class="text-danger">*</span></label>
      <div class="input-with-icon">
        <i class="fas fa-city"></i>
        <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="City name" required />
      </div>
      @error('city')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group @error('state') has-error @enderror">
      <label for="province">Province <span class="text-danger">*</span></label>
      <div class="input-with-icon">
        <i class="fas fa-map"></i>
        <input type="text" id="province" name="state" value="{{ old('state') }}" placeholder="Province name" required />
      </div>
      @error('state')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group @error('postal') has-error @enderror">
      <label for="zip_code">ZIP Code <span class="text-danger">*</span></label>
      <div class="input-with-icon">
        <i class="fas fa-mail-bulk"></i>
        <input type="text" id="zip_code" name="postal" value="{{ old('postal') }}" placeholder="e.g. 1000" required />
      </div>
      @error('postal')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
  </div>
</div>

<!-- Business Details Section -->
<div class="form-section">
  <h3><i class="fas fa-briefcase"></i> Business Details</h3>
  
  <div class="form-group @error('years_operation') has-error @enderror">
    <label for="years_operation">Years in Operation</label>
    <div class="input-with-icon">
      <i class="fas fa-calendar-alt"></i>
      <input type="number" id="years_operation" name="years_operation" value="{{ old('years_operation') }}" placeholder="Number of years" min="0" />
    </div>
    @error('years_operation')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('primary_products_services') has-error @enderror">
    <label for="primary_products_services">Primary Products/Services</label>
    <div class="input-with-icon">
      <i class="fas fa-box"></i>
      <textarea id="primary_products_services" name="primary_products_services" placeholder="List your primary products or services (e.g., Cement, Gravel, Scaffolding Rental, Skilled Labor, etc.)" rows="4">{{ old('primary_products_services') }}</textarea>
    </div>
    @error('primary_products_services')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('service_areas') has-error @enderror">
    <label for="service_areas">Areas of Operation</label>
    <div class="input-with-icon">
      <i class="fas fa-map-marked-alt"></i>
      <textarea id="service_areas" name="service_areas" placeholder="List the areas where you operate">{{ old('service_areas') }}</textarea>
    </div>
    @error('service_areas')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('business_size') has-error @enderror">
    <label for="business_size">Business Size</label>
    <div class="select-with-icon">
      <i class="fas fa-chart-bar"></i>
      <select id="business_size" name="business_size">
        <option value="">Select business size</option>
        <option value="Solo" {{ old('business_size') == 'Solo' ? 'selected' : '' }}>Solo</option>
        <option value="Small Enterprise" {{ old('business_size') == 'Small Enterprise' ? 'selected' : '' }}>Small Enterprise</option>
        <option value="Medium" {{ old('business_size') == 'Medium' ? 'selected' : '' }}>Medium</option>
        <option value="Large" {{ old('business_size') == 'Large' ? 'selected' : '' }}>Large</option>
      </select>
    </div>
    @error('business_size')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group file-upload-group @error('accreditations_certifications') has-error @enderror">
    <label for="accreditations_certifications">Accreditations/Certifications</label>
    <div class="file-upload-wrapper">
      <label class="file-upload-label" for="accreditations_certifications">
        <i class="fas fa-cloud-upload-alt"></i>
        <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
        <input type="file" id="accreditations_certifications" name="accreditations_certifications" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
      </label>
      <div class="file-preview" id="accreditations_preview"></div>
    </div>
    @error('accreditations_certifications')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
</div>

<!-- Pricing & Terms Section -->
<div class="form-section">
  <h3><i class="fas fa-file-invoice-dollar"></i> Pricing & Terms</h3>
  
  <div class="form-group @error('payment_terms') has-error @enderror">
    <label for="payment_terms">Payment Terms</label>
    <div class="select-with-icon">
      <i class="fas fa-calendar-check"></i>
      <select id="payment_terms" name="payment_terms">
        <option value="">Select preferred terms</option>
        <option value="7 days" {{ old('payment_terms') == '7 days' ? 'selected' : '' }}>7 days</option>
        <option value="15 days" {{ old('payment_terms') == '15 days' ? 'selected' : '' }}>15 days</option>
        <option value="30 days" {{ old('payment_terms') == '30 days' ? 'selected' : '' }}>30 days</option>
      </select>
    </div>
    @error('payment_terms')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('vat_registered') has-error @enderror">
    <label for="vat_registered">VAT Registered? <span class="text-danger">*</span></label>
    <div class="select-with-icon">
      <i class="fas fa-receipt"></i>
      <select id="vat_registered" name="vat_registered" required>
        <option value="">-- Select --</option>
        <option value="1" {{ old('vat_registered') == '1' ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ old('vat_registered') == '0' ? 'selected' : '' }}>No</option>
      </select>
    </div>
    @error('vat_registered')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group @error('use_sureprice') has-error @enderror">
    <label for="use_sureprice">Use SurePrice? <span class="text-danger">*</span></label>
    <div class="select-with-icon">
      <i class="fas fa-check-circle"></i>
      <select id="use_sureprice" name="use_sureprice" required>
        <option value="">-- Select --</option>
        <option value="1" {{ old('use_sureprice') == '1' ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ old('use_sureprice') == '0' ? 'selected' : '' }}>No</option>
      </select>
    </div>
    @error('use_sureprice')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
</div>

  <!-- Documents Section -->
  <div class="form-section">
    <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
    <p class="text-muted mb-3"><small>Please upload the following documents. Required documents are marked with <span class="text-danger">*</span></small></p>
    
    <div class="form-row">
      <div class="form-group file-upload-group @error('business_permit_mayor_permit') has-error @enderror">
        <label for="business_permit_mayor_permit">Business Permit/Mayor's Permit <span class="text-danger" id="business_permit_required">*</span> <span id="business_permit_optional_text" class="optional-text" style="display:none;">(Optional for clients)</span></label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label" for="business_permit_mayor_permit">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="business_permit_mayor_permit" name="business_permit_mayor_permit" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
          </label>
          <div class="file-preview" id="business_permit_preview"></div>
        </div>
        @error('business_permit_mayor_permit')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
      
      <div class="form-group file-upload-group @error('valid_id_owner_rep') has-error @enderror">
        <label for="valid_id_owner_rep">Valid ID (Owner/Rep) <span class="text-danger" id="valid_id_required">*</span> <span id="valid_id_optional_text" class="optional-text" style="display:none;">(Optional for clients)</span></label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label" for="valid_id_owner_rep">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="valid_id_owner_rep" name="valid_id_owner_rep" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
          </label>
          <div class="file-preview" id="valid_id_preview"></div>
        </div>
        @error('valid_id_owner_rep')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group file-upload-group @error('company_profile_portfolio') has-error @enderror">
        <label for="company_profile_portfolio">Company Profile/Portfolio</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label" for="company_profile_portfolio">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="company_profile_portfolio" name="company_profile_portfolio" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
          </label>
          <div class="file-preview" id="company_profile_preview"></div>
        </div>
        @error('company_profile_portfolio')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
      
      <div class="form-group file-upload-group @error('sample_price_list') has-error @enderror" id="sample_price_list_group" style="display:none;">
        <label for="sample_price_list">Sample Price List</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label" for="sample_price_list">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="sample_price_list" name="sample_price_list" accept=".pdf,.jpg,.png" class="visually-hidden-file" />
          </label>
          <div class="file-preview" id="price_list_preview"></div>
        </div>
        @error('sample_price_list')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
    </div>
  </div>

        <!-- Bank Details Section -->
        <div class="form-section">
          <h3><i class="fas fa-university"></i> Bank Details (Optional)</h3>
          
          <div class="form-row">
            <div class="form-group @error('bank_name') has-error @enderror">
              <label for="bank_name">Bank Name</label>
              @php
                  $banks = ['BDO', 'BPI', 'MetroBank', 'PNB', 'Security Bank', 'Union Bank', 'RCBC', 'China Bank'];
              @endphp
              <div class="select-with-icon">
                <i class="fas fa-landmark"></i>
                <select id="bank_name" name="bank_name">
                    <option value="">Select a bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank }}" {{ old('bank_name') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
              </div>
              @error('bank_name')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group @error('bank_account_name') has-error @enderror">
              <label for="bank_account_name">Account Name</label>
              <div class="input-with-icon">
                <i class="fas fa-user-tie"></i>
                <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}" placeholder="Account holder name" />
              </div>
              @error('bank_account_name')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group @error('bank_account_number') has-error @enderror">
              <label for="bank_account_number">Account Number</label>
              <div class="input-with-icon">
                <i class="fas fa-credit-card"></i>
                <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Account number" />
              </div>
              @error('bank_account_number')<span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <!-- Agreement Section -->
        <div class="form-section">
    <h3><i class="fas fa-handshake"></i> Agreement & Consent</h3>
    
    <div class="agreement-consent">
        <label class="agreement-label">
            <input type="checkbox" name="agree_terms" required {{ old('agree_terms') ? 'checked' : '' }}>
            <span class="checkbox-custom"></span>
            <span class="agreement-text">
                I agree to the <a href="{{ route('terms.conditions') }}" target="_blank" class="terms-links">Terms and Conditions</a> and <a href="{{ route('privacy.policy') }}" target="_blank" class="terms-links">Privacy Policy</a>
            </span>
        </label>
        
        <label class="agreement-label">
            <input type="checkbox" name="agree_contact" {{ old('agree_contact') ? 'checked' : '' }}>
            <span class="checkbox-custom"></span>
            <span class="agreement-text">
                I consent to be contacted for verification and partnership opportunities
            </span>
        </label>
    </div>
    
    @error('agree_terms')
        <span class="error-message" data-server-error><i class="fas fa-exclamation-circle"></i> You must agree to the terms and conditions</span>
    @enderror
</div>

        <div class="form-footer">
          <button type="submit" class="submit-btn">
            <i class="fas fa-building"></i> Register Company
          </button>
          
          <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login here</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>
<script src="{{ asset('js/signup-alert.js') }}"></script>
</html>

<script>
// Dynamic show/hide and required logic
function updateFormFields() {
  const role = document.getElementById('designation').value;
  const clientTypeGroup = document.getElementById('client_type_group');
  const clientType = document.getElementById('client_type');
  const clientTypeValue = clientType.value;
  const companyNameGroup = document.getElementById('company_name_group');
  const companyNameInput = document.getElementById('company_name');
  const companyNameRequired = document.getElementById('company_name_required');
  const dtiGroup = document.getElementById('dti_sec_registration_group');
  const dtiInput = document.getElementById('dti_sec_registration');
  const dtiStar = document.getElementById('dti_required_star');
  const dtiOptionalText = document.getElementById('dti_optional_text');
  
  // File upload elements
  const businessPermitInput = document.getElementById('business_permit_mayor_permit');
  const businessPermitRequired = document.getElementById('business_permit_required');
  const businessPermitOptionalText = document.getElementById('business_permit_optional_text');
  const validIdInput = document.getElementById('valid_id_owner_rep');
  const validIdRequired = document.getElementById('valid_id_required');
  const validIdOptionalText = document.getElementById('valid_id_optional_text');
  
  // File requirements notice
  const fileRequirementsNotice = document.getElementById('fileRequirementsNotice');
  const fileRequirementsText = document.getElementById('fileRequirementsText');

  // Reset all fields first
  companyNameInput.required = false;
  companyNameRequired.style.display = 'none';
  dtiInput.required = false;
  dtiStar.style.display = 'none';
  dtiOptionalText.style.display = 'none';
  clientType.required = false;
  
  // Reset file upload requirements
  businessPermitInput.required = false;
  businessPermitRequired.style.display = 'none';
  businessPermitOptionalText.style.display = 'inline';
  validIdInput.required = false;
  validIdRequired.style.display = 'none';
  validIdOptionalText.style.display = 'inline';

  if (role === 'client') {
    // Show client type selection and make it required
    clientTypeGroup.style.display = '';
    clientType.required = true;
    
    // For clients, all file uploads are optional
    businessPermitInput.required = false;
    businessPermitRequired.style.display = 'none';
    businessPermitOptionalText.style.display = 'inline';
    validIdInput.required = false;
    validIdRequired.style.display = 'none';
    validIdOptionalText.style.display = 'inline';
    
    // Show file requirements notice for clients
    fileRequirementsNotice.style.display = 'flex';
    fileRequirementsText.textContent = 'For clients, all document uploads are optional. You can provide them later if needed.';
    
    if (clientTypeValue === 'individual') {
      // Individual client - hide company name, show DTI as optional
      companyNameGroup.style.display = 'none';
      dtiGroup.style.display = '';
      dtiInput.required = false;
      dtiStar.style.display = 'none';
      dtiOptionalText.style.display = 'inline';
    } else if (clientTypeValue === 'company') {
      // Company client - show company name, show DTI as optional
      companyNameGroup.style.display = '';
      companyNameInput.required = true;
      companyNameRequired.style.display = '';
      dtiGroup.style.display = '';
      dtiInput.required = false;
      dtiStar.style.display = 'none';
      dtiOptionalText.style.display = 'inline';
    } else {
      // No client type selected yet - show company name but not required, show DTI as optional
      companyNameGroup.style.display = '';
      dtiGroup.style.display = '';
      dtiInput.required = false;
      dtiStar.style.display = 'none';
      dtiOptionalText.style.display = 'inline';
    }
  } else if (role === 'supplier') {
    // Supplier - always show company name and DTI required, and all file uploads required
    clientTypeGroup.style.display = 'none';
    companyNameGroup.style.display = '';
    companyNameInput.required = true;
    companyNameRequired.style.display = '';
    dtiGroup.style.display = '';
    dtiInput.required = true;
    dtiStar.style.display = '';
    dtiOptionalText.style.display = 'none';
    
    // For suppliers, all file uploads are required
    businessPermitInput.required = true;
    businessPermitRequired.style.display = '';
    businessPermitOptionalText.style.display = 'none';
    validIdInput.required = true;
    validIdRequired.style.display = '';
    validIdOptionalText.style.display = 'none';
    
    // Show file requirements notice for suppliers
    fileRequirementsNotice.style.display = 'flex';
    fileRequirementsText.textContent = 'For suppliers, all required documents must be uploaded for approval.';
  } else {
    // No role selected - hide everything
    clientTypeGroup.style.display = 'none';
    companyNameGroup.style.display = '';
    dtiGroup.style.display = '';
    fileRequirementsNotice.style.display = 'none';
  }

  // Show/hide Sample Price List field
  const samplePriceListGroup = document.getElementById('sample_price_list_group');
  if (role === 'supplier') {
    samplePriceListGroup.style.display = '';
  } else {
    samplePriceListGroup.style.display = 'none';
  }
}

// Add event listeners
document.getElementById('designation').addEventListener('change', updateFormFields);
document.getElementById('client_type').addEventListener('change', updateFormFields);

// Handle "Other" company type field visibility
document.getElementById('supplier_type').addEventListener('change', function() {
  const otherTypeGroup = document.getElementById('other_supplier_type_group');
  const otherTypeInput = document.getElementById('other_supplier_type');
  
  if (this.value === 'Other') {
    otherTypeGroup.style.display = 'block';
    otherTypeInput.required = true;
  } else {
    otherTypeGroup.style.display = 'none';
    otherTypeInput.required = false;
    otherTypeInput.value = '';
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateFormFields);

function updateCompanyTypeOptions() {
  const role = document.getElementById('designation').value;
  const supplierTypeSelect = document.getElementById('supplier_type');
  
  // Store the currently selected value
  const currentlySelected = supplierTypeSelect.value;
  
  const defaultOptions = [
    'Construction & Engineering',
    'Architecture & Design',
    'Real Estate & Property Development',
    'Manufacturing',
    'Wholesale & Distribution',
    'Retail & E-Commerce',
    'Information Technology & Software',
    'Telecommunications',
    'Healthcare & Medical',
    'Logistics & Transportation',
    'Energy & Utilities',
    'Financial Services',
    'Legal & Compliance',
    'Education & Training',
    'Marketing & Advertising',
    'Hospitality & Tourism',
    'Government & Public Sector',
    'Nonprofit / NGO',
    'Other'
  ];
  
  // Always show default options for both supplier and client
  supplierTypeSelect.innerHTML = '<option value="">Select company type</option>';
  defaultOptions.forEach(type => {
    const opt = document.createElement('option');
    opt.value = type;
    opt.textContent = type;
    supplierTypeSelect.appendChild(opt);
  });
  
  // Restore the previously selected value if it exists in default options
  if (currentlySelected && currentlySelected !== '') {
    const optionExists = Array.from(supplierTypeSelect.options).some(option => option.value === currentlySelected);
    if (optionExists) {
      supplierTypeSelect.value = currentlySelected;
    }
  }
}
// Add event listeners
if (document.getElementById('designation')) {
  document.getElementById('designation').addEventListener('change', updateCompanyTypeOptions);
}
</script>