@extends('layouts.app')
@section('content')
<div class="container py-5 text-center">
    <h2>This page is no longer used.</h2>
    <p>Supplier creation is now handled via invitations.</p>
    <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary mt-3">
        Invite Supplier
    </a>
</div>
<script>setTimeout(function(){ window.location.href = "{{ route('supplier-invitations.create') }}"; }, 2500);</script>
@endsection 