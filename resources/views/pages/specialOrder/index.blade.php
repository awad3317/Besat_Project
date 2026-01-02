@extends('layouts.app')
@section('title', 'إدارة الطلبات الخاصة')
@section('Breadcrumb', 'إدارة الطلبات الخاصة')
@section('addButton')
  {{-- @include('pages.or$orders.edit-vehicle-modal') --}}
  <x-modals.success-modal />
  <x-modals.error-modal />

@endsection
@section('style')

@endsection
@section('content')

  <livewire:special-orders.index />

@endsection

@section('script')

@endsection