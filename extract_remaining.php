<?php

/**
 * Helper script for extracting remaining embedded CSS and JS
 * This provides a template for the extraction process
 */

echo "CSS and JS Extraction Helper\n";
echo "===========================\n\n";

echo "For each file with embedded CSS/JS, follow this process:\n\n";

echo "1. Create CSS file: resources/css/{filename}.css\n";
echo "2. Create JS file: resources/js/{filename}.js\n";
echo "3. Extract content from <style> tags\n";
echo "4. Extract content from <script> tags\n";
echo "5. Replace with @vite directives\n\n";

echo "Example pattern:\n";
echo "---------------\n";
echo "Original:\n";
echo "@push('styles')\n";
echo "<style>\n";
echo "/* CSS content */\n";
echo "</style>\n";
echo "@endpush\n\n";

echo "After extraction:\n";
echo "@push('styles')\n";
echo "@vite(['resources/css/{filename}.css'])\n";
echo "@endpush\n\n";

echo "For JavaScript, replace Blade variables with window variables:\n";
echo "Original: JSON.parse('{!! addslashes(json_encode($data)) !!}')\n";
echo "After: window.dataVariable || {}\n\n";

echo "Files to process:\n";
echo "================\n";

$files = [
    'auth/register.blade.php',
    'auth/forgot-password.blade.php',
    'auth/reset-password.blade.php',
    'auth/verify-email.blade.php',
    'auth/pending-approval.blade.php',
    'auth/account-rejected.blade.php',
    'admin/companies/show.blade.php',
    'admin/price-analysis.blade.php',
    'admin/suppliers/recommendations.blade.php',
    'admin/transactions/index.blade.php',
    'admin/suppliers/pending-updates.blade.php',
    'admin/suppliers/rankings.blade.php',
    'admin/contracts/editor.blade.php',
    'client/dashboard.blade.php',
    'client/quotation/create.blade.php',
    'client/quotation/view.blade.php',
    'finance/dashboard.blade.php',
    'finance/payments.blade.php',
    'inventory/index.blade.php',
    'inventory/create.blade.php',
    'landing/catalogue.blade.php',
    'legal/terms-conditions.blade.php',
    'legal/privacy-policy.blade.php',
    'manager/dashboard.blade.php',
    'manager/notification-center.blade.php',
    'payments/index.blade.php',
    'payments/partials/admin_verify_modal.blade.php',
    'procurement/suppliers/general-recommendation.blade.php',
    'procurement/suppliers-rankings.blade.php',
    'procurement/project-dashboard.blade.php',
    'procurement/procurement-dashboard.blade.php',
    'procurement/analytics-dashboard.blade.php',
    'procurement/inventory-dashboard.blade.php',
    'procurement/analytics/price-analysis.blade.php',
    'procurement/analytics/budget-allocation.blade.php',
    'projects/index.blade.php',
    'projects/create.blade.php',
    'projects/edit.blade.php',
    'receipts/template.blade.php',
    'supplier/materials/create.blade.php',
    'supplier/notification-center.blade.php',
    'supplier/quotation-respond.blade.php',
    'transactions/index.blade.php',
    'transactions/past.blade.php',
    'warehouse/material-requests.blade.php',
    'warehouse/deliveries/index.blade.php',
    'warehouse/deliveries/show.blade.php',
    'warehouse/inventory/index.blade.php',
    'warehouse/reports/analytics.blade.php',
    'warehouse/reports/analytics-pdf.blade.php',
    'warehouse/reports/deliveries-web.blade.php',
    'warehouse/reports/index.blade.php',
    'warehouse/reports/inventory-pdf.blade.php',
    'warehouse/reports/inventory-web.blade.php',
    'warehouse/reports/movements-web.blade.php',
    'warehouse/reports/usage-web.blade.php',
    'warranty-requests/index.blade.php',
    'warranty-requests/show.blade.php',
    'components/search-select.blade.php',
    'include/sidebars/default.blade.php',
    'messages/show.blade.php',
    'project-timeline/create.blade.php',
    'project-timeline/edit.blade.php',
    'project-timeline/show.blade.php',
    'contracts/partials/workflow-status.blade.php'
];

foreach ($files as $file) {
    echo "- resources/views/{$file}\n";
}

echo "\nTotal files remaining: " . count($files) . "\n";
echo "\nRemember to:\n";
echo "- Keep external CDN links\n";
echo "- Update JavaScript to use window variables\n";
echo "- Test functionality after each extraction\n";
echo "- Use Vite directives for asset compilation\n"; 