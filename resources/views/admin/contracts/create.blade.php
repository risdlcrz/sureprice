@extends('layouts.app')
@section('content')
    @include('admin.contracts.editor', ['quotationRequests' => $quotationRequests, 'contractors' => $contractors])
@endsection 