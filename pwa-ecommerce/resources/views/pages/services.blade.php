@extends('layouts.app')

@section('title', 'Dịch vụ')
@section('meta_description', 'Các dịch vụ của Tiệm Nhà Duy: thiết kế website, phát triển tool, tư vấn kỹ thuật.')

@section('content')
    @include('components.hero')
    @include('components/services')
    @include('components/features')
@endsection


