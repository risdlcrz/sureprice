@extends('layouts.app')

@push('styles')
    @vite(['resources/css/client/quotation/create.css'])
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-center client-page-title">
            Request a Quotation
            @if(is_array($category))
                - {{ implode(', ', $category) }}
            @elseif(!empty($category))
                - {{ $category }}
            @endif
        </h1>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        @if(!empty($guestQuotationData) && !empty($guestQuotationData['rooms']))
                            <div class="alert alert-info mb-3" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                You’re logged in. Your quotation form has been restored. Click <strong>Submit Quotation Request</strong> below to submit.
                            </div>
                        @endif
                        <form method="POST" action="{{ route('client.quotation.store') }}" id="quotationForm" novalidate>
                            @csrf
                            <!-- Room/Area Details (step2 logic) -->
                            <div class="section-container" id="roomSection">
                                <h5 class="section-title">Room/Area Details</h5>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" id="addRoomBtn">
                                            <i class="fas fa-plus"></i> Add Room/Area
                                        </button>
                                    </div>
                                </div>
                                <div id="roomDetails">
                                    <!-- Rooms will be added here dynamically (step2 logic) -->
                                </div>
                            </div>

                            <!-- Cost Breakdown & Supplier Selection (step2 + supplier features) -->
                            <div class="section-container" id="breakdownSection">
                                <h5 class="section-title">Cost Breakdown & Supplier Selection</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="breakdownTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room</th>
                                                <th>Scope</th>
                                                <th>Category</th>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>Unit Cost</th>
                                                <th>Quantity</th>
                                                <th>Base Total Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically filled by JS (step2 logic) -->
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="7" class="text-end fw-bold">Overall Total</td>
                                                <td class="fw-bold" id="overallTotalCell">₱0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Submit Quotation Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Recommendation Modal (as before) -->
<div class="modal fade" id="supplierRecommendationModal" tabindex="-1" aria-labelledby="supplierRecommendationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierRecommendationModalLabel">Supplier Recommendation for <span id="modalMaterialName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="supplier-recommendation-form" class="row g-3 mb-3">
                    <input type="hidden" name="material_id" id="modalMaterialId">
                    <div class="col-12">
                        <label for="rec_mode" class="form-label">Recommendation Mode</label>
                        <select class="form-select" id="rec_mode" name="mode">
                            <option value="best_score">Best Overall Score</option>
                            <option value="on_time_delivery">Best On-Time Delivery</option>
                            <option value="lowest_defect">Lowest Defect Rate</option>
                            <option value="cheapest">Cheapest</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Show Recommendations</button>
                    </div>
                </form>
                <div id="supplier-recommendation-results"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>window.__page = Object.assign(window.__page || {}, { create0_0: @json($scopeTypesByCode), create0_1: @json($materialSuppliers), create0_2: @json($guestQuotationData ?? []) });</script>
@vite('resources/js/pages/client-quotation-create.js')
@endpush 