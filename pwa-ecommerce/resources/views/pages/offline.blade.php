@extends('layouts.simple', [
    'pageTitle' => 'Offline Detected',
    'backUrl' => route('home'),
    'showBackButton' => true,
    'showNavbarToggler' => true
])

@section('title', 'Tiệm Nhà Duy | Mất Kết Nối Internet')
@section('meta_description', 'Bạn đang ngoại tuyến. Vui lòng kiểm tra kết nối internet để tiếp tục truy cập Tiệm Nhà Duy.')

@section('content')
<!-- Offline Area -->
<div class="container">
    <div class="offline-area-wrapper py-3 d-flex align-items-center justify-content-center">
        <div class="offline-text text-center">
            <img class="mb-4 px-4" 
                 src="{{ asset('frontend/img/bg-img/no-internet.png') }}" 
                 alt="No Internet Connection"
                 loading="lazy">
            <h5>No Internet Connection!</h5>
            <p>Seems like you're offline, please check your internet connection. This page doesn't support when you offline!</p>
            <a class="btn btn-primary btn-lg" href="{{ route('home') }}">Back Home</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Additional scripts for offline page if needed --}}
@endpush

