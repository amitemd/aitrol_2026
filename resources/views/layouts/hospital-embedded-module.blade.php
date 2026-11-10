<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Módulo clínico')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('ho/assets/css/style.css') }}">
    <style>
        html, body { min-height: 100%; }
        body { margin: 0; background: #f5faff; color: #19405b; }
        .hospital-embedded-container { min-height: 100vh; overflow-x: hidden; }
    </style>
    @yield('styles')
    <script src="{{ asset('/bower_components/jquery/dist/jquery.min.js') }}"></script>
</head>
<body>
    <main class="hospital-embedded-container">
        @yield('content')
    </main>
    <script src="{{ asset('ho/app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('ho/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    @include('layouts.select2-global')
    @yield('scripts')
</body>
</html>
