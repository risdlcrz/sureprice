{{-- Employee details modal. Param: $item (Employee) --}}
<x-modal-dialog id="viewModal{{ $item->id }}" dialog-class="modal-lg">
    <x-slot:title>Employee Information</x-slot:title>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-primary">Personal Information</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Full Name</th>
                                        <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td>{{ $item->username }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $item->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>
                                            <span class="role-badge role-{{ strtolower($item->role) }}">
                                                {{ ucfirst($item->role) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($item->role === 'contractor')
                                        <tr>
                                            <th>Company Name</th>
                                            <td>{{ $item->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $item->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Street</th>
                                            <td>{{ $item->street }}</td>
                                        </tr>
                                        <tr>
                                            <th>Barangay</th>
                                            <td>{{ $item->barangay }}</td>
                                        </tr>
                                        <tr>
                                            <th>City</th>
                                            <td>{{ $item->city }}</td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td>{{ $item->state }}</td>
                                        </tr>
                                        <tr>
                                            <th>Postal</th>
                                            <td>{{ $item->postal }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-primary">Activity Information</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Last Login</th>
                                        <td>{{ $item->last_login_at ? $item->last_login_at->diffForHumans() : 'Never' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email Verified</th>
                                        <td>
                                            @if($item->email_verified_at)
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    <i class="fas fa-times-circle"></i> Not Verified
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $item->created_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $item->updated_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
</x-modal-dialog>
