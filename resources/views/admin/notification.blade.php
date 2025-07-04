@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Centralized Notification Hub</h1>
<div class="container-fluid main-bg py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg animated-fadein">
                <div class="card-header py-3 bg-white" style="border-radius: 1.25rem 1.25rem 0 0; border-bottom: 1px solid #e9ecef;">
                    <h6 class="m-0 font-weight-bold text-primary" style="font-size:1.2rem;">All Notifications</h6>
                </div>
                <div class="card-body">
                    @if(isset($notifications) && $notifications->isNotEmpty())
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action animated-fadein">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1" style="font-weight:600; color:#198754;">
                                            {{ $notification->type ?? 'Notification' }}
                                            <span class="badge bg-success ms-2" style="font-size:0.9em; border-radius:0.7em; box-shadow:0 1px 4px #38b6ff22;">New</span>
                                        </h5>
                                        <small style="color:#6c757d; font-size:0.98em;">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1" style="font-size:1.08em; color:#495057;">
                                        {{ is_array($notification->data) ? ($notification->data['message'] ?? json_encode($notification->data)) : $notification->data }}
                                    </p>
                                </div>
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