<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/dbadmin.css'])
</head>
<body>
    
    <div class="sidebar">
        @include('include.header')
    </div>

    <div class="content">
        <h1 class="text-center my-4">Analytics Dashboard</h1>

        <div class="top-controls">
            <!-- Optional top controls (if any) go here -->
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Card 1 -->
            <div class="col">
                <div class="card" onclick="window.location.href='{{ route('admin.purchase-order') }}';" style="cursor:pointer;">
                    <img src="{{ Vite::asset('resources/images/analyticsdash1.jpeg') }}" alt="Active Purchase Order" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Active Purchase Order</h5>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col">
                <div class="card" onclick="window.location.href='{{ route('admin.budget-allocation') }}';" style="cursor:pointer;">
                    <img src="{{ Vite::asset('resources/images/analyticsdash2.jpg') }}" alt="Budget Allocation" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Budget Allocation and Expenditures</h5>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col">
                <div class="card" onclick="window.location.href='{{ route('supplier-rankings.index') }}';" style="cursor:pointer;">
                    <img src="{{ Vite::asset('resources/images/analyticsdash3.jpg') }}" alt="Supplier Rankings" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Supplier Ranking and Performance</h5>
                    </div>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="col">
                <div class="card" onclick="window.location.href='{{ route('admin.price-analysis') }}';" style="cursor:pointer;">
                    <img src="{{ Vite::asset('resources/images/analyticsdash4.jpg') }}" alt="Price Analysis" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Price Trend Analysis</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
