@extends('layouts.app')
@section('title', 'إدارة السائقين')
@section('Breadcrumb', 'إدارة السائقين')
@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />

@endsection
@section('content')

    <livewire:drivers.index />
    
@endsection

@section('script')
<script>
    window.addEventListener('page-reload', event => {
        window.location.reload();
    });
</script>
@endsection