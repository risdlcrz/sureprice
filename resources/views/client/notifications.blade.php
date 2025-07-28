@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Notifications</h1>
    
    @if(auth()->user()->unreadNotifications->count() > 0)
        <div class="alert alert-info">
            <i class="fas fa-bell"></i>
            You have {{ auth()->user()->unreadNotifications->count() }} unread notification(s).
            <a href="{{ route('client.notifications.mark-all-read') }}" class="btn btn-sm btn-outline-primary ms-2">
                Mark All as Read
            </a>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Recent Notifications</h5>
        </div>
        <div class="card-body">
            @if(auth()->user()->notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach(auth()->user()->notifications()->paginate(20) as $notification)
                        <div class="list-group-item border-0 {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if(!$notification->read_at)
                                            <span class="badge bg-primary me-2">New</span>
                                        @endif
                                        
                                        @switch($notification->data['status'] ?? '')
                                            @case('verified')
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="fw-bold text-success">Payment Verified</span>
                                                @break
                                            @case('rejected')
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                <span class="fw-bold text-danger">Payment Rejected</span>
                                                @break
                                            @case('info_requested')
                                                <i class="fas fa-question-circle text-info me-2"></i>
                                                <span class="fw-bold text-info">Information Requested</span>
                                                @break
                                            @default
                                                <i class="fas fa-bell text-primary me-2"></i>
                                                <span class="fw-bold">Payment Update</span>
                                        @endswitch
                                    </div>
                                    
                                    <h6 class="mb-1">
                                        Payment #{{ $notification->data['payment_number'] ?? 'N/A' }}
                                    </h6>
                                    
                                    <p class="text-muted mb-2">
                                        Amount: ₱{{ number_format($notification->data['amount'] ?? 0, 2) }}
                                    </p>
                                    
                                    @if(isset($notification->data['message']) && $notification->data['message'])
                                        <div class="alert alert-light border">
                                            <small class="text-muted">
                                                {!! nl2br(e($notification->data['message'])) !!}
                                            </small>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                        
                                        <div>
                                            @if($notification->data['payment_id'] ?? false)
                                                <a href="{{ route('client.payments.show', $notification->data['payment_id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Payment
                                                </a>
                                            @endif
                                            
                                            @if(!$notification->read_at)
                                                <a href="{{ route('client.notifications.mark-read', $notification->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-check"></i> Mark Read
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ auth()->user()->notifications()->paginate(20)->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No notifications yet</h5>
                    <p class="text-muted">You'll see payment updates and other important notifications here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}
.list-group-item {
    border-radius: 8px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}
.list-group-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>
@endpush 