@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('Breadcrumb', 'إدارة المستخدمين')
@section('addButton')
    @include('pages.users.create-user-modal')
    <x-modals.success-modal />
    <x-modals.error-modal />

@endsection
@section('content')

    <livewire:users.index />

@endsection

@section('script')

@endsection
