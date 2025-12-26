@extends('layouts.app')
@section('title', 'كوبونات الخصم')
@section('Breadcrumb', 'كوبونات الخصم')
@section('addButton')
    @include('pages.coupons.create-coupon-modal')
    @include('pages.coupons.edit-coupon-modal')
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection
@section('style')

@endsection
@section('content')

    <livewire:discount-codes.index />



@endsection

@section('script')
    

@endsection