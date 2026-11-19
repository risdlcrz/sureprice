// Extracted from resources/views/auth/register.blade.php
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
