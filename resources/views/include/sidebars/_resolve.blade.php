@php($u = auth()->user())
@if($u && $u->user_type === 'company')
    @php($u->loadMissing('company'))
@endif
@if(request()->is('admin/project*'))
    @include('include.sidebars.project')
@elseif(request()->is('admin/analytics*'))
    @include('include.sidebars.analytics')
@elseif($u && $u->role === 'warehousing')
    @include('include.sidebars.warehousing')
@elseif($u && $u->user_type === 'employee' && $u->role === 'procurement')
    @include('include.sidebars.procurement')
@else
    @include('include.sidebars.default')
@endif
