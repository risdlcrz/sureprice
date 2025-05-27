<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Companies - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/dbadmin.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .document-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            margin: 10px 0;
        }
        .document-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        @include('include.header')
    </div>

    <div class="content">
        <div class="container mt-4">
            <h2 class="mb-4">Pending Company Approvals</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td>{{ $company->company_name }}</td>
                                <td>{{ $company->contact_person }}</td>
                                <td>{{ $company->email }}</td>
                                <td>{{ $company->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($company->documents->count() > 0)
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#documentsModal{{ $company->id }}">
                                            View Documents ({{ $company->documents->count() }})
                                        </button>
                                    @else
                                        No documents
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $company->id }}">
                                        Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $company->id }}">
                                        Reject
                                    </button>
                                </td>
                            </tr>

                            <!-- Documents Modal -->
                            <div class="modal fade" id="documentsModal{{ $company->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $company->company_name }}'s Documents</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                @foreach($company->documents as $document)
                                                    <div class="col-md-6">
                                                        <div class="document-card">
                                                            <h6>{{ ucfirst(str_replace('_', ' ', $document->type)) }}</h6>
                                                            @if(in_array($document->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/jpg']))
                                                                <img src="{{ asset('storage/' . $document->path) }}" class="document-preview" alt="{{ $document->type }}">
                                                            @elseif($document->mime_type === 'application/pdf')
                                                                <iframe src="{{ asset('storage/' . $document->path) }}" class="document-preview"></iframe>
                                                            @else
                                                                <a href="{{ asset('storage/' . $document->path) }}" class="btn btn-primary" download="{{ $document->original_name }}">
                                                                    <i class="fas fa-download"></i> Download {{ $document->original_name }}
                                                                </a>
                                                            @endif
                                                            <div class="mt-2">
                                                                <a href="{{ asset('storage/' . $document->path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                                    View Full Document
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal{{ $company->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve {{ $company->company_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to approve this company?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('admin.companies.approve', $company) }}" method="POST">
                                                @csrf
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $company->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject {{ $company->company_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.companies.reject', $company) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No pending companies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $companies->links() }}
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>