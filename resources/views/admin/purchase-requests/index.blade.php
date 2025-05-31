@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Purchase Requests</h1>
                <a href="{{ route('purchase-request.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Purchase Request
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Requester</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">No purchase requests found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 