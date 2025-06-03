@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Material Details</h4>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Code</dt>
                <dd class="col-sm-9">{{ $material->code }}</dd>

                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $material->name }}</dd>

                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $material->category->name ?? '' }}</dd>

                <dt class="col-sm-3">Unit</dt>
                <dd class="col-sm-9">{{ $material->unit }}</dd>

                <dt class="col-sm-3">Base Price</dt>
                <dd class="col-sm-9">₱{{ number_format($material->base_price, 2) }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $material->description }}</dd>

                <dt class="col-sm-3">Specifications</dt>
                <dd class="col-sm-9">{{ $material->specifications }}</dd>

                <dt class="col-sm-3">Images</dt>
                <dd class="col-sm-9">
                    @if($material->images && count($material->images) > 0)
                        @foreach($material->images as $image)
                            <img src="{{ Storage::url($image->path) }}" alt="Material Image" class="img-thumbnail mr-2 mb-2" style="max-width: 120px;">
                        @endforeach
                    @else
                        <span class="text-muted">No images</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Suppliers</dt>
                <dd class="col-sm-9">
                    @if($material->suppliers && count($material->suppliers) > 0)
                        <ul>
                        @foreach($material->suppliers as $supplier)
                            <li>{{ $supplier->company_name ?? $supplier->name }}</li>
                        @endforeach
                        </ul>
                    @else
                        <span class="text-muted">No suppliers</span>
                    @endif
                </dd>
            </dl>
            <a href="{{ route('materials.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection 