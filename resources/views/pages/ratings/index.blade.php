@extends('layouts.app')
@section('title', 'تقييمات العملاء')
@section('Breadcrumb', 'تقييمات العملاء')
@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('content')
    <livewire:ratings.index />
@endsection
