@extends('layouts.app')
@section('title', 'إدارة الطلبات العامة')
@section('Breadcrumb', 'إدارة الطلبات العامة')
@section('addButton')
<a href="{{ route('request.create') }}"
    class="bg-brand-500 hover:bg-warning-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px] inline-flex items-center justify-center transition shadow-xs">
    إنشاء رحلة جديدة
</a>
<x-modals.success-modal />
<x-modals.error-modal />
@endsection

@section('content')
    <livewire:request.index />
@endsection
