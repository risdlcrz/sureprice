{{-- Shown for unexpected server errors when APP_DEBUG is off. Never shows the exception: details are in storage/logs under the reference. --}}
@extends('errors::minimal')

@section('title', 'Something went wrong')
@section('code', '500')
@section('message')
    Something went wrong. Please try again or contact support at {{ config('app.support_email') }}
    (ref. {{ \App\Support\SafeErrors::reference() }}).
@endsection
