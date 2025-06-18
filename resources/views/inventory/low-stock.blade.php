@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Low Stock Items</h2>
    <div class="alert alert-info">This is a placeholder for low stock inventory items. You can customize this view to display actual data.</div>
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary mt-3">Back to Inventory</a>
</div>
@endsection 