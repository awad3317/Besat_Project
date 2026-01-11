@extends('layouts.app')

@section('title', 'إدارة النظام')
@section('Breadcrumb', 'إدارة النظام')

@section('content')

    {{-- Livewire Component for Settings --}}

<livewire:systems.settings />

@endsection

@section('script')
@endsection