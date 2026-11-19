{{-- Company details + status modal. Param: $company --}}
<x-modal-dialog id="viewModal{{ $company->id }}" dialog-class="modal-lg">
    <x-slot:title>Company Information</x-slot:title>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-primary">Company Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Company Name</th>
                                        <td>{{ $company->company_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Designation</th>
                                        <td>{{ ucfirst($company->designation) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact Person</th>
                                        <td>{{ $company->contact_person }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $company->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile Number</th>
                                        <td>{{ $company->mobile_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telephone Number</th>
                                        <td>{{ $company->telephone_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Business Reg. No.</th>
                                        <td>{{ $company->business_reg_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier Type</th>
                                        <td>{{ $company->supplier_type }}</td>
                                    </tr>
                                    @if($company->other_supplier_type)
                                        <tr>
                                            <th>Other Supplier Type</th>
                                            <td>{{ $company->other_supplier_type }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>VAT Registered</th>
                                        <td>{{ $company->vat_registered ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Use Sureprice</th>
                                        <td>{{ $company->use_sureprice ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Terms</th>
                                        <td>{{ $company->payment_terms }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <x-status-badge :status="$company->status" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Status Change Buttons -->
                        <div class="mb-3">
                            <h6 class="text-primary">Change Status</h6>
                            <form action="{{ route('admin.companies.approve', $company->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm mb-1" {{ $company->status === 'approved' ? 'disabled' : '' }}>Approve</button>
                            </form>
                            <form action="{{ route('admin.companies.reject', $company->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="rejection_reason" value="Manual admin rejection">
                                <button type="submit" class="btn btn-danger btn-sm mb-1" {{ $company->status === 'rejected' ? 'disabled' : '' }}>Reject</button>
                            </form>
                            <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn btn-warning btn-sm mb-1" {{ $company->status === 'pending' ? 'disabled' : '' }}>Set to Pending</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
</x-modal-dialog>
