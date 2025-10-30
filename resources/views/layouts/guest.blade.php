<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }"
    x-bind:class="darkMode ? 'dark' : ''">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        @yield('title', 'بساط')
    </title>
    <link rel="icon" href="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" type="image/x-icon">
    <link href="{{ asset('tailadmin/build/style.css') }}" rel="stylesheet">


</head>

<body x-data="{ page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">
    @yield('content')
    <script defer src="{{ asset('tailadmin/build/bundle.js') }}"></script>
 
</body>

</html>
