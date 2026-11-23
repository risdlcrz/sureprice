{{-- Expired session / CSRF token, or a form post larger than PHP's post_max_size (PHP then drops the whole request). --}}
@extends('errors::minimal')

@section('title', 'Page expired')
@section('code', '419')
@section('message')
    This page expired or the upload was too large. Go back, refresh the page and try again.
@endsection
