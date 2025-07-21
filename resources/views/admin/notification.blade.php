@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Centralized Notification Hub</h1>
<div class="container-fluid main-bg py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg animated-fadein">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center" style="border-radius: 1.25rem 1.25rem 0 0; border-bottom: 1px solid #e9ecef;">
                    <h6 class="m-0 font-weight-bold text-primary" style="font-size:1.2rem;">All Notifications</h6>
                    <div>
                        <form method="POST" action="{{ route('admin.notifications.markAllAsRead') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm me-2">Mark All as Read</button>
                        </form>
                        <form method="POST" action="{{ route('admin.notifications.clearRead') }}" class="d-inline" id="clearReadForm">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Clear Read Notifications</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($notifications) && $notifications->isNotEmpty())
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                @php
                                    try {
                                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        if (!is_array($data)) {
                                            $data = ['message' => $notification->data ?? 'Notification'];
                                        }
                                        
                                        $quotationId = $data['quotation_id'] ?? null;
                                        $link = $data['link'] ?? null;
                                        $title = $data['title'] ?? $notification->type;
                                        $message = $data['message'] ?? '';
                                        $purchaseRequestId = $data['purchase_request_id'] ?? null;
                                        $requestNumber = $data['request_number'] ?? null;
                                        
                                        if ($notification->type === 'ClientProceededQuotation' && $quotationId) {
                                            $link = route('material-requests.create') . '?quotation_id=' . $quotationId;
                                            $title = 'Material Request Needed';
                                        }
                                        
                                        // Handle purchase request notifications
                                        if (in_array($notification->type, [
                                            'Purchase Request Approval Needed',
                                            'Purchase Request Approved',
                                            'Purchase Request Supplier Approval Needed',
                                            'Purchase Request Supplier Approved',
                                            'Purchase Request Ready for PO'
                                        ])) {
                                            // Use the link from data if available
                                            if (!$link && $purchaseRequestId && is_numeric($purchaseRequestId)) {
                                                try {
                                                    $link = route('purchase-requests.show', $purchaseRequestId);
                                                } catch (\Exception $e) {
                                                    $link = route('purchase-requests.index');
                                                }
                                            } elseif (!$link) {
                                                $link = route('purchase-requests.index');
                                            }
                                            $title = $data['title'] ?? $notification->type;
                                            $message = $data['message'] ?? '';
                                        }
                                    } catch (\Exception $e) {
                                        $title = $notification->type ?? 'Notification';
                                        $message = 'Error processing notification data';
                                        $link = null;
                                    }
                                @endphp
                                <a href="{{ $link ?? '#' }}" class="list-group-item list-group-item-action animated-fadein mb-2 notification-item" style="border-radius: 0.9rem; text-decoration: none;" data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h5 class="mb-1" style="font-weight:600; color:#198754;">
                                            {{ $title }}
                                            @if(!$notification->read_at)
                                                <span class="badge bg-success ms-2" style="font-size:0.9em; border-radius:0.7em; box-shadow:0 1px 4px #38b6ff22;">New</span>
                                            @endif
                                        </h5>
                                        <small style="color:#6c757d; font-size:0.98em;">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(isset($data['supplier_name']))
                                        <div class="mb-1"><span class="fw-bold text-primary">Supplier:</span> {{ $data['supplier_name'] }}</div>
                                    @endif
                                    <p class="mb-1" style="font-size:1.08em; color:#495057;">
                                        {{ $message }}
                                    </p>
                                    @if($link)
                                        @if($notification->type === 'ClientProceededQuotation')
                                            <span class="btn btn-primary btn-sm">Create Material Request</span>
                                        @elseif(in_array($notification->type, [
                                            'Purchase Request Approval Needed',
                                            'Purchase Request Approved',
                                            'Purchase Request Supplier Approval Needed',
                                            'Purchase Request Supplier Approved',
                                            'Purchase Request Ready for PO'
                                        ]))
                                            <span class="btn btn-primary btn-sm">View Purchase Request</span>
                                        @else
                                            <span class="btn btn-primary btn-sm">View Details</span>
                                        @endif
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p>No notifications found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .main-bg {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}

.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 2rem;
    transition: box-shadow 0.2s;
}
.card:hover {
    box-shadow: 0 8px 32px rgba(44,62,80,0.12), 0 2px 8px rgba(44,62,80,0.08);
}

.animated-fadein {
    animation: fadeIn 0.7s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: none; }
}

.list-group-item {
    border-radius: 0.9rem;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(56,182,255,0.07);
    background: #fff;
    border: 1px solid #e9ecef;
    transition: box-shadow 0.2s, border 0.2s;
}
.list-group-item:hover {
    box-shadow: 0 2px 12px #38b6ff22;
    border: 1.5px solid #38b6ff44;
}

.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
.d-flex.w-100.justify-content-between {
    align-items: center;
}
.mb-1 {
    margin-bottom: 0.5rem !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark notifications as read when clicked
    const notificationLinks = document.querySelectorAll('.notification-item');
    
    notificationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const notificationId = this.getAttribute('data-notification-id');
            if (notificationId && !this.querySelector('.badge').classList.contains('d-none')) {
                // Mark as read via AJAX
                fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide the "New" badge
                        const badge = this.querySelector('.badge');
                        if (badge) {
                            badge.style.display = 'none';
                        }
                        // Update sidebar notification badge
                        const sidebarBadge = document.querySelector('.nav-buttons .btn .badge.bg-danger');
                        if (sidebarBadge) {
                            let count = parseInt(sidebarBadge.textContent.trim(), 10);
                            if (!isNaN(count) && count > 0) {
                                count--;
                                if (count > 0) {
                                    sidebarBadge.textContent = count;
                                } else {
                                    sidebarBadge.style.display = 'none';
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            }
        });
    });
    // Clear read notifications
    document.getElementById('clearReadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        }).then(res => res.json()).then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item .badge').forEach(function(badge) {
                    if (badge && badge.style.display !== 'none') {
                        badge.style.display = 'none';
                    }
                });
                document.querySelectorAll('.notification-item').forEach(function(item) {
                    if (!item.querySelector('.badge') || item.querySelector('.badge').style.display === 'none') {
                        item.remove();
                    }
                });
            }
        });
    });
});
</script>
@endpush 