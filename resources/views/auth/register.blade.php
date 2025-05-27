<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - GDC Admin Center</title>
  @vite(['resources/css/signup.css', 'resources/js/signup-alert.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <p>Register your company as a client or supplier</p>
      </div>
      
      <!-- Add prominent error display -->
      @if($errors->any())
      <div class="alert alert-danger">
          <h4><i class="fas fa-exclamation-triangle"></i> Registration Error</h4>
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif
      
      <form method="POST" action="{{ route('register.company') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="company">

        <!-- Account Information Section -->
        <div class="form-section">
          <h3><i class="fas fa-user-circle"></i> Account Information</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label for="company_username">Username</label>
              <div class="input-with-icon">
                <i class="fas fa-at"></i>
                <input type="text" id="company_username" name="username" value="{{ old('username') }}" placeholder="Choose a username" required />
              </div>
              @error('username')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
              <label for="company_password">Password</label>
              <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="company_password" name="password" placeholder="Create a password" required />
              </div>
              @error('password')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
              <label for="company_password_confirmation">Confirm Password</label>
              <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="company_password_confirmation" name="password_confirmation" placeholder="Confirm your password" required />
              </div>
            </div>
          </div>
        </div>

        <!-- Basic Information Section -->
        <div class="form-section">
          <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
          
          <div class="form-group">
            <label for="company_name">Company Name</label>
            <div class="input-with-icon">
              <i class="fas fa-building"></i>
              <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Enter company name" required />
            </div>
            @error('company_name')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="supplier_type">Type of Company</label>
              <div class="select-with-icon">
                <i class="fas fa-tag"></i>
                <select id="supplier_type" name="supplier_type" required>
                  <option value="">Select company type</option>
                  <option value="Individual" {{ old('supplier_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                  <option value="Contractor" {{ old('supplier_type') == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                  <option value="Material Supplier" {{ old('supplier_type') == 'Material Supplier' ? 'selected' : '' }}>Material Supplier</option>
                  <option value="Equipment Rental" {{ old('supplier_type') == 'Equipment Rental' ? 'selected' : '' }}>Equipment Rental</option>
                  <option value="Other" {{ old('supplier_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              @error('supplier_type')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group" id="other_supplier_type_group" style="{{ old('supplier_type') == 'Other' ? 'display:block' : 'display:none' }}">
              <label for="other_supplier_type">Specify Type</label>
              <div class="input-with-icon">
                <i class="fas fa-pen"></i>
                <input type="text" id="other_supplier_type" name="other_supplier_type" value="{{ old('other_supplier_type') }}" placeholder="Please specify company type" />
              </div>
              @error('other_supplier_type')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
          </div>
          
          <div class="form-group">
            <label for="designation">Company Role</label>
            <div class="select-with-icon">
              <i class="fas fa-user-tag"></i>
              <select id="designation" name="designation" required>
                <option value="">Select company role</option>
                <option value="client" {{ old('designation') == 'client' ? 'selected' : '' }}>Client</option>
                <option value="supplier" {{ old('designation') == 'supplier' ? 'selected' : '' }}>Supplier</option>
              </select>
            </div>
            @error('designation')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
          </div>
          
          <div class="form-group">
            <label for="business_reg_no">Business Registration Number</label>
            <div class="input-with-icon">
              <i class="fas fa-id-card"></i>
              <input type="text" id="business_reg_no" name="business_reg_no" value="{{ old('business_reg_no') }}" placeholder="Enter registration number (if applicable)" />
            </div>
            @error('business_reg_no')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
          </div>
          
          <div class="form-group file-upload-group">
  <label for="dti_sec_registration">DTI/SEC Registration</label>
  <div class="file-upload-wrapper">
    <label class="file-upload-label">
      <i class="fas fa-cloud-upload-alt"></i>
      <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
      <input type="file" id="dti_sec_registration" name="dti_sec_registration" accept=".pdf,.jpg,.png" required />
    </label>
    <div class="file-preview" id="dti_sec_preview"></div>
  </div>
  @error('dti_sec_registration')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
</div>

<!-- Contact Details Section -->
<div class="form-section">
  <h3><i class="fas fa-address-book"></i> Contact Details</h3>
  
  <div class="form-group">
    <label for="contact_person">Contact Person</label>
    <div class="input-with-icon">
      <i class="fas fa-user"></i>
      <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="Full name of contact person" required />
    </div>
    @error('contact_person')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
    <label for="company_email">Email Address</label>
    <div class="input-with-icon">
      <i class="fas fa-envelope"></i>
      <input type="email" id="company_email" name="email" value="{{ old('email') }}" placeholder="company.email@example.com" required />
    </div>
    @error('email')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-row">
    <div class="form-group">
      <label for="mobile_number">Mobile Number</label>
      <div class="input-with-icon">
        <i class="fas fa-mobile-alt"></i>
        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="e.g. 09123456789" required />
      </div>
      @error('mobile_number')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group">
      <label for="telephone_number">Telephone Number</label>
      <div class="input-with-icon">
        <i class="fas fa-phone"></i>
        <input type="text" id="telephone_number" name="telephone_number" value="{{ old('telephone_number') }}" placeholder="e.g. 8123456" />
      </div>
      @error('telephone_number')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
  </div>
  
  <div class="form-group">
    <label for="street">Street Address</label>
    <div class="input-with-icon">
      <i class="fas fa-map-marker-alt"></i>
      <input type="text" id="street" name="street" value="{{ old('street') }}" placeholder="Building/Street name" required />
    </div>
    @error('street')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-row">
    <div class="form-group">
      <label for="city">City/Municipality</label>
      <div class="input-with-icon">
        <i class="fas fa-city"></i>
        <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="City name" required />
      </div>
      @error('city')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group">
      <label for="province">Province</label>
      <div class="input-with-icon">
        <i class="fas fa-map"></i>
        <input type="text" id="province" name="province" value="{{ old('province') }}" placeholder="Province name" required />
      </div>
      @error('province')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
    
    <div class="form-group">
      <label for="zip_code">ZIP Code</label>
      <div class="input-with-icon">
        <i class="fas fa-mail-bulk"></i>
        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" placeholder="e.g. 1000" required />
      </div>
      @error('zip_code')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
    </div>
  </div>
</div>

<!-- Business Details Section -->
<div class="form-section">
  <h3><i class="fas fa-briefcase"></i> Business Details</h3>
  
  <div class="form-group">
    <label for="years_operation">Years in Operation</label>
    <div class="input-with-icon">
      <i class="fas fa-calendar-alt"></i>
      <input type="number" id="years_operation" name="years_operation" value="{{ old('years_operation') }}" placeholder="Number of years" min="0" />
    </div>
    @error('years_operation')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
    <label for="primary_products_services">Primary Products/Services</label>
    <div class="input-with-icon">
      <i class="fas fa-box"></i>
      <textarea id="primary_products_services" name="primary_products_services" placeholder="List your primary products or services (e.g., Cement, Gravel, Scaffolding Rental, Skilled Labor, etc.)" rows="4">{{ old('primary_products_services') }}</textarea>
    </div>
    @error('primary_products_services')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
    <label for="service_areas">Areas of Operation</label>
    <div class="input-with-icon">
      <i class="fas fa-map-marked-alt"></i>
      <textarea id="service_areas" name="service_areas" placeholder="List the areas where you operate">{{ old('service_areas') }}</textarea>
    </div>
    @error('service_areas')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
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
    @error('business_size')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group file-upload-group">
    <label for="accreditations_certifications">Accreditations/Certifications</label>
    <div class="file-upload-wrapper">
      <label class="file-upload-label">
        <i class="fas fa-cloud-upload-alt"></i>
        <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
        <input type="file" id="accreditations_certifications" name="accreditations_certifications" accept=".pdf,.jpg,.png" />
      </label>
      <div class="file-preview" id="accreditations_preview"></div>
    </div>
    @error('accreditations_certifications')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
</div>

<!-- Pricing & Terms Section -->
<div class="form-section">
  <h3><i class="fas fa-file-invoice-dollar"></i> Pricing & Terms</h3>
  
  <div class="form-group">
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
    @error('payment_terms')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
    <label for="vat_registered">VAT Registered?</label>
    <div class="select-with-icon">
      <i class="fas fa-receipt"></i>
      <select id="vat_registered" name="vat_registered" required>
        <option value="">-- Select --</option>
        <option value="1" {{ old('vat_registered') == '1' ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ old('vat_registered') == '0' ? 'selected' : '' }}>No</option>
      </select>
    </div>
    @error('vat_registered')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
  
  <div class="form-group">
    <label for="use_sureprice">Use SurePrice?</label>
    <div class="select-with-icon">
      <i class="fas fa-check-circle"></i>
      <select id="use_sureprice" name="use_sureprice" required>
        <option value="">-- Select --</option>
        <option value="1" {{ old('use_sureprice') == '1' ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ old('use_sureprice') == '0' ? 'selected' : '' }}>No</option>
      </select>
    </div>
    @error('use_sureprice')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
  </div>
</div>

  <!-- Documents Section -->
  <div class="form-section">
    <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
    
    <div class="form-row">
      <div class="form-group file-upload-group">
        <label for="business_permit_mayor_permit">Business Permit/Mayor's Permit</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="business_permit_mayor_permit" name="business_permit_mayor_permit" accept=".pdf,.jpg,.png" />
          </label>
          <div class="file-preview" id="business_permit_preview"></div>
        </div>
        @error('business_permit_mayor_permit')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
      
      <div class="form-group file-upload-group">
        <label for="valid_id_owner_rep">Valid ID (Owner/Rep)</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="valid_id_owner_rep" name="valid_id_owner_rep" accept=".pdf,.jpg,.png" />
          </label>
          <div class="file-preview" id="valid_id_preview"></div>
        </div>
        @error('valid_id_owner_rep')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group file-upload-group">
        <label for="company_profile_portfolio">Company Profile/Portfolio</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="company_profile_portfolio" name="company_profile_portfolio" accept=".pdf,.jpg,.png" />
          </label>
          <div class="file-preview" id="company_profile_preview"></div>
        </div>
        @error('company_profile_portfolio')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
      
      <div class="form-group file-upload-group">
        <label for="sample_price_list">Sample Price List</label>
        <div class="file-upload-wrapper">
          <label class="file-upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span class="file-upload-text">Choose file (PDF, JPG, PNG)</span>
            <input type="file" id="sample_price_list" name="sample_price_list" accept=".pdf,.jpg,.png" />
          </label>
          <div class="file-preview" id="price_list_preview"></div>
        </div>
        @error('sample_price_list')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
      </div>
    </div>
  </div>

        <!-- Bank Details Section -->
        <div class="form-section">
          <h3><i class="fas fa-university"></i> Bank Details (Optional)</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label for="bank_name">Bank Name</label>
              <div class="input-with-icon">
                <i class="fas fa-landmark"></i>
                <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" placeholder="e.g. BDO, Metrobank" />
              </div>
              @error('bank_name')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
              <label for="bank_account_name">Account Name</label>
              <div class="input-with-icon">
                <i class="fas fa-user-tie"></i>
                <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}" placeholder="Account holder name" />
              </div>
              @error('bank_account_name')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
              <label for="bank_account_number">Account Number</label>
              <div class="input-with-icon">
                <i class="fas fa-credit-card"></i>
                <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Account number" />
              </div>
              @error('bank_account_number')<span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
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
                I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
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
        <span class="error-message"><i class="fas fa-exclamation-circle"></i> You must agree to the terms and conditions</span>
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
</html>