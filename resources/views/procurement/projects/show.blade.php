@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$project->name">
        <span class="text-muted me-2">Project #{{ $project->project_number }}</span>
        <a href="{{ route('procurement.projects.tasks', $project) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-list-task"></i> Tasks</a>
        <a href="{{ route('procurement.projects.procurement', $project) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-cart"></i> Procurement</a>
        <a href="{{ route('procurement.projects.analytics', $project) }}" class="btn btn-outline-info btn-sm"><i class="bi bi-graph-up"></i> Analytics</a>
    </x-page-header>

    <div class="row">
        <div class="col-md-4">@include('projects.partials.overview')</div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Recent Procurement Activities</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Type</th><th>Reference</th><th>Date</th><th>Status</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse($project->contract?->purchaseOrders?->take(5) ?? [] as $po)
                                <tr>
                                    <td>Purchase Order</td>
                                    <td>{{ $po->po_number }}</td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td><x-status-badge :status="$po->status" /></td>
                                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <x-empty-row cols="5">No recent procurement activities.</x-empty-row>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Recent Tasks</div>
                <div class="card-body">
                    @include('projects.partials.tasks-table', ['tasks' => $project->tasks->take(5), 'actions' => false, 'empty' => 'No recent tasks.'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
