<!DOCTYPE html>
<html class="light-style layout-menu-fixed" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title') | gnuGrid - Best loan management app on the planet </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://gnugridcrb.com/">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <!-- Include Styles -->
    @include('layouts/sections/styles')
    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
    @livewireStyles
    @livewireScripts
    @livewireChartsScripts
    <!-- Add Sortable.js (you can install it via npm or use a CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body>
    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->
    <!-- Include Scripts -->
    @include('layouts/sections/scripts')
    @yield('scripts')
</body>

</html>
