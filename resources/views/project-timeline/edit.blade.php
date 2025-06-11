@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Task</h3>
                    <div class="card-tools">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Timeline
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-timeline.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contract</label>
                                    <input type="text" class="form-control" value="{{ $task->contract->contract_number }} - {{ $task->contract->title }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room</label>
                                    <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror">
                                        <option value="">Select Room</option>
                                        @foreach($task->contract->rooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id', $task->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scope_type_id">Scope Type</label>
                                    <select name="scope_type_id" id="scope_type_id" class="form-control @error('scope_type_id') is-invalid @enderror">
                                        <option value="">Select Scope Type</option>
                                        @if($task->room)
                                            @foreach($task->room->scopeTypes as $scopeType)
                                                <option value="{{ $scopeType->id }}" {{ old('scope_type_id', $task->scope_type_id) == $scopeType->id ? 'selected' : '' }}>
                                                    {{ $scopeType->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('scope_type_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assigned_to">Assign To</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">Task Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $task->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $task->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $task->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="delayed" {{ old('status', $task->status) == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="progress">Progress <span class="text-danger">*</span></label>
                                    <input type="number" name="progress" id="progress" class="form-control @error('progress') is-invalid @enderror" value="{{ old('progress', $task->progress) }}" min="0" max="100" required>
                                    @error('progress')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $task->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Task</button>
                                <a href="{{ route('project-timeline.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load scope types when room is selected
    $('#room_id').change(function() {
        var roomId = $(this).val();
        if (roomId) {
            $.get('/api/rooms/' + roomId + '/scope-types', function(scopeTypes) {
                $('#scope_type_id').empty().append('<option value="">Select Scope Type</option>');
                scopeTypes.forEach(function(scopeType) {
                    $('#scope_type_id').append('<option value="' + scopeType.id + '">' + scopeType.name + '</option>');
                });
            });
        } else {
            $('#scope_type_id').empty().append('<option value="">Select Scope Type</option>');
        }
    });

    // Set minimum end date based on start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });

    // Update status based on progress
    $('#progress').change(function() {
        var progress = parseInt($(this).val());
        if (progress >= 100) {
            $('#status').val('completed');
        } else if (progress > 0) {
            $('#status').val('in_progress');
        }
    });
});
</script>
@endsection 