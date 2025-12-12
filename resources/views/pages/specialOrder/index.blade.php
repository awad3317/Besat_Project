@extends('layouts.app')
@section('title', 'إدارة الطلبات الخاصة')
@section('Breadcrumb', 'إدارة الطلبات الخاصة')
@section('addButton')
  @include('pages.specialOrder.create-special-order-modal')
  {{-- @include('pages.Vehicles.edit-vehicle-modal') --}}
  <x-modals.success-modal />
  <x-modals.error-modal />

@endsection
@section('style')

@endsection
@section('content')
  

@endsection

@section('script')

@endsection