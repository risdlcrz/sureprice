<!-- Analytics Header -->
<div class="analytics-header py-3 bg-white border-bottom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Analytics</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.analytics') }}">Analytics Dashboard</a></li>
                    @if(Route::currentRouteName() == 'suppliers.rankings')
                        <li class="breadcrumb-item active" aria-current="page">Supplier Rankings</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>
</div> 