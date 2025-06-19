@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Messages</h5>
                    @if(auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                            New Message
                        </button>
                    @endif
                </div>

                <div class="card-body">
                    @if($conversations->isEmpty())
                        <p class="text-center">No conversations yet.</p>
                    @else
                        <div class="list-group">
                            @foreach($conversations as $conversation)
                                <a href="{{ route('messages.show', $conversation) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @if(auth()->user()->user_type === 'admin')
                                                {{ $conversation->client->name ?? 'Unknown Client' }}
                                            @else
                                                {{ $conversation->admin->name ?? 'Unknown Admin' }}
                                            @endif
                                        </h6>
                                        <small>{{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'No messages' }}</small>
                                    </div>
                                    <p class="mb-1">
                                        @if($conversation->messages->count() > 0)
                                            {{ Str::limit($conversation->messages->first()->content, 50) }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                    @if($conversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                                        <span class="badge bg-primary rounded-pill">
                                            {{ $conversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
    <!-- New Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newMessageModalLabel">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">Select Admin</label>
                            <select class="form-select" id="admin_id" name="admin_id" required>
                                <option value="">Choose an admin...</option>
                                @foreach(\App\Models\User::where('user_type', 'admin')->where('role', 'admin')->get() as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection 