@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Task</h3>
                    <div class="card-tools">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Timeline
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-timeline.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_id">Contract <span class="text-danger">*</span></label>
                                    <select name="contract_id" id="contract_id" class="form-control @error('contract_id') is-invalid @enderror" required>
                                        <option value="">Select Contract</option>
                                        @foreach($contracts as $contract)
                                            <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                                                {{ $contract->contract_number }} - {{ $contract->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contract_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room</label>
                                    <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror">
                                        <option value="">Select Room</option>
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
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
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
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
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
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Create Task</button>
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
    // Load rooms when contract is selected
    $('#contract_id').change(function() {
        var contractId = $(this).val();
        if (contractId) {
            $.get('/api/contracts/' + contractId + '/rooms', function(rooms) {
                $('#room_id').empty().append('<option value="">Select Room</option>');
                rooms.forEach(function(room) {
                    $('#room_id').append('<option value="' + room.id + '">' + room.name + '</option>');
                });
            });
        } else {
            $('#room_id').empty().append('<option value="">Select Room</option>');
        }
    });

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
});
</script>
@endsection 