@php($u = auth()->user())
@if($u && $u->user_type === 'company')
    @php($u->loadMissing('company'))
@endif
@if(request()->is('admin/analytics*'))
    @include('include.sidebars.analytics')
@elseif($u && $u->role === 'warehousing')
    @include('include.sidebars.warehousing')
@elseif($u && $u->user_type === 'employee' && in_array($u->role, ['procurement', 'contractor'], true))
    @include('include.sidebars.procurement')
@else
    @include('include.sidebars.default')
@endif
