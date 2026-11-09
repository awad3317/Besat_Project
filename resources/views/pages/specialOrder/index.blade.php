@extends('layouts.app')
@section('title', 'إدارة الطلبات الخاصة')
@section('Breadcrumb', 'إدارة الطلبات الخاصة')

@section('addButton')
  <div class="flex items-center gap-2">
    <a href="{{ route('specialOrder.create') }}"
       class="bg-brand-500 hover:bg-warning-600 h-10 rounded-lg px-5 py-2 text-sm font-medium text-white inline-flex items-center justify-center gap-2 transition shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>إنشاء طلب خاص</span>
    </a>
  </div>
  <x-modals.success-modal />
  <x-modals.error-modal />
@endsection

@section('content')
  <livewire:special-orders.index />
@endsection
