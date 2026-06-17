@extends('layouts.app')
@section('title', ' الإعلانات')
@section('Breadcrumb', 'إدارة الإعلانات')
@section('addButton')
    @include('pages.Ads.create-ad-modal')
    @include('pages.Ads.edit-ad-modal')
    
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection
@section('style')
@endsection

@section('content')

    <livewire:ads.index />

@endsection

@section('script')

@endsection