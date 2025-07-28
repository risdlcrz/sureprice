# SurePrice Payment System

## Overview

This document describes the comprehensive client-side payment system and finance verification functionality implemented for the SurePrice application. The system allows clients to submit payments for verification by finance officers, with a complete workflow for payment processing.

## Features

### Client-Side Payment Features

1. **Payment Dashboard**
   - View all contracts with payment schedules
   - Track payment status (Pending, For Verification, Paid, Overdue)
   - Payment statistics and summaries
   - Search and filter functionality

2. **Payment Submission**
   - Submit payment proof (screenshots, receipts, bank statements)
   - Select payment method (Bank Transfer, Check, Cash, Online Payment, Mobile Banking)
   - Provide reference numbers and payment details
   - Add notes and additional information

3. **Payment Status Tracking**
   - Real-time status updates
   - Payment history and audit trail
   - Overdue payment indicators
   - Payment proof storage and viewing

4. **Notifications**
   - Email notifications for payment status changes
   - In-app notification center
   - Unread notification indicators
   - Payment verification confirmations

### Finance-Side Verification Features

1. **Verification Dashboard**
   - View all payments pending verification
   - Payment statistics and summaries
   - Filter by payment method, date range, amount range
   - Quick action buttons for verification

2. **Payment Verification**
   - Verify payment details match client submission
   - Confirm payment method, reference number, and amount
   - Upload additional finance proof
   - Mark payments as verified and paid

3. **Payment Rejection**
   - Reject payments with specific reasons
   - Provide detailed rejection explanations
   - Specify required actions from client
   - Clear client submission data for resubmission

4. **Information Requests**
   - Request additional information from clients
   - Set response deadlines and priority levels
   - Track information request status
   - Send notifications to clients

## Database Schema

### Payment Model Fields

```php
// Basic Payment Information
'payment_number', 'payable_type', 'payable_id', 'contract_id',
'purchase_order_id', 'amount', 'payment_method', 'payment_type',
'status', 'due_date', 'paid_date', 'reference_number', 'notes',

// Client Submission Fields
'client_payment_proof', 'client_payment_method', 'client_reference_number',
'client_paid_amount', 'client_paid_date', 'client_notes',

// Finance Verification Fields
'admin_payment_proof', 'admin_payment_method', 'admin_reference_number',
'admin_received_amount', 'admin_received_date', 'admin_notes',

// Rejection Fields
'rejection_reason', 'rejection_details', 'action_required',
'rejected_by', 'rejected_at',

// Information Request Fields
'info_request_type', 'specific_request', 'response_deadline',
'priority_level', 'info_requested_by', 'info_requested_at'
```

## Workflow

### Client Payment Submission Workflow

1. **Client Views Payment Schedule**
   - Navigate to "My Payments" dashboard
   - View contract payment schedules
   - Identify pending payments

2. **Client Submits Payment**
   - Click "Pay Now" on pending payment
   - Fill payment submission form:
     - Select payment method
     - Enter reference number
     - Specify amount paid
     - Upload payment proof
     - Add notes (optional)
   - Submit for verification

3. **Payment Status Changes**
   - Status changes from "Pending" to "For Verification"
   - Client receives confirmation
   - Payment appears in finance verification queue

### Finance Verification Workflow

1. **Finance Officer Reviews Payment**
   - Access verification dashboard
   - View payment details and client submission
   - Review payment proof and information

2. **Verification Actions**
   - **Verify Payment**: Confirm details and mark as paid
   - **Reject Payment**: Provide reason and return to pending
   - **Request More Info**: Ask for additional documentation

3. **Payment Processing**
   - Verified payments create transaction records
   - Contract status updated if all payments complete
   - Client notified of status change

## Routes

### Client Routes
```php
// Payment Routes
GET /client/payments - Payment dashboard
GET /client/payments/dashboard - Payment statistics
GET /client/payments/{payment} - Payment details
GET /client/notifications - Notification center

// Payment Submission
POST /payments/{payment}/submit-client-proof - Submit payment proof
```

### Finance Routes
```php
// Finance Dashboard
GET /finance/dashboard - Finance dashboard
GET /finance/payments - Payment management
GET /finance/verify-payments - Verification dashboard

// Payment Actions
POST /payments/{payment}/verify - Verify payment
POST /payments/{payment}/reject - Reject payment
POST /payments/{payment}/request-more-info - Request information
```

## Views

### Client Views
- `resources/views/client/payments/index.blade.php` - Payment dashboard
- `resources/views/client/payments/show.blade.php` - Payment details
- `resources/views/client/payments/partials/submit_payment_modal.blade.php` - Payment submission modal
- `resources/views/client/notifications.blade.php` - Notification center

### Finance Views
- `resources/views/finance/verify-payments.blade.php` - Verification dashboard
- `resources/views/finance/partials/verify_payment_modal.blade.php` - Verification modal
- `resources/views/finance/partials/reject_payment_modal.blade.php` - Rejection modal
- `resources/views/finance/partials/request_more_info_modal.blade.php` - Information request modal

## Controllers

### ClientPaymentController
- Handles client payment dashboard and views
- Payment statistics and filtering
- Payment detail display

### PaymentController
- Payment submission and verification logic
- Client proof submission
- Finance verification actions
- Transaction creation

### FinanceController
- Finance dashboard and verification management
- Payment filtering and statistics
- Verification workflow management

## Notifications

### PaymentStatusNotification
- Email notifications for payment status changes
- Database notifications for in-app alerts
- Customizable messages for different status types

### Notification Types
- **Verified**: Payment successfully verified
- **Rejected**: Payment rejected with reason
- **Info Requested**: Additional information needed

## Security Features

1. **Authorization**
   - Client middleware for client-only routes
   - Finance role middleware for verification actions
   - Payment ownership validation

2. **Data Validation**
   - File upload validation (size, type)
   - Payment amount validation
   - Reference number validation

3. **Audit Trail**
   - Complete payment history tracking
   - User action logging
   - Timestamp tracking for all actions

## Usage Examples

### Client Submitting Payment
1. Navigate to "My Payments"
2. Find pending payment
3. Click "Pay Now"
4. Fill payment form with proof
5. Submit for verification
6. Receive confirmation

### Finance Verifying Payment
1. Access verification dashboard
2. Review client submission
3. Verify payment details match
4. Click "Verify Payment"
5. Payment marked as paid
6. Client notified automatically

## Future Enhancements

1. **Payment Gateway Integration**
   - Direct payment processing
   - Real-time payment confirmation
   - Automated verification

2. **Advanced Reporting**
   - Payment analytics dashboard
   - Revenue tracking
   - Payment trend analysis

3. **Mobile Optimization**
   - Mobile-responsive payment forms
   - Mobile payment proof upload
   - Push notifications

4. **Automation Features**
   - Automatic payment reminders
   - Scheduled payment processing
   - Automated verification rules

## Support

For technical support or questions about the payment system, please contact the development team or refer to the Laravel documentation for framework-specific questions. 