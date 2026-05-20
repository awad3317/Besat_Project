@extends('layouts.app')
@section('title', 'إدارة البنوك')
@section('Breadcrumb', 'إدارة البنوك')
@section('addButton')
    @include('pages.banks.create-bank-modal')
    @include('pages.banks.edit-bank-modal')
    <x-modals.success-modal />
    <x-modals.error-modal />

@endsection
@section('content')

    <livewire:banks.index />

@endsection

@section('script')

@endsection