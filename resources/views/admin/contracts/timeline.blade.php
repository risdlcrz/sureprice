@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Project Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($contracts as $contract)
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        {{ $contract->start_date->format('M d, Y') }} - {{ $contract->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="timeline-content">
                                        <h4>{{ $contract->client->name }}</h4>
                                        <p>Contractor: {{ $contract->contractor->name }}</p>
                                        <p>Status: {{ ucfirst($contract->status) }}</p>
                                        <div class="timeline-progress">
                                            @php
                                                $totalDays = $contract->start_date->diffInDays($contract->end_date);
                                                $elapsedDays = $contract->start_date->diffInDays(now());
                                                $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
                                            @endphp
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $progress }}%" 
                                                     aria-valuenow="{{ $progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($progress, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}

.timeline-date {
    font-weight: bold;
    color: #666;
    margin-bottom: 10px;
}

.timeline-content {
    padding: 10px 0;
}

.timeline-content h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.timeline-progress {
    margin-top: 15px;
}

.progress {
    height: 20px;
    background-color: #f5f5f5;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    background-color: #007bff;
    color: white;
    text-align: center;
    line-height: 20px;
    transition: width 0.6s ease;
}
</style>
@endsection 