<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07547e">
    <base href="{{ url('./') }}">
    <link rel="icon" type="image/png" href="{{ url('v3/') }}/dist/img/AdminLTELogo.png">
    @yield('title')
    @yield('css')
    <link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/modern-ui.css') }}">
    <link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/v3-density.css') }}">
    <link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/support-assistant.css') }}">
    <link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/v3-dark-mode.css') }}">
    <script src="{{ url('v3/') }}/plugins/jquery/jquery.min.js"></script>
    <script>
        window.AitrolV3I18n={!! json_encode(trans('v3/accessibility'), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!};
        window.AitrolV3TableLabels={
            source:{!! json_encode(trans('v3/new_tabla', [], 'es'), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!},
            target:{!! json_encode(trans('v3/new_tabla'), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}
        };
    </script>
    <script defer src="{{ \Sis_medico\Support\V3Asset::url('v3/dist/js/modern-ui.js') }}"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm aitrol-v3 {{ Auth::check() && Auth::user()->isSystemUser() ? 'aitrol-table-export-enabled' : 'aitrol-table-export-disabled' }}" data-table-export="{{ Auth::check() && Auth::user()->isSystemUser() ? '1' : '0' }}">
    <script>(function(){try{if(window.localStorage.getItem('aitrol-v3-theme')==='dark'){document.body.classList.add('dark-mode');}}catch(e){}})();</script>
    <a class="skip-link" href="#contenido-principal">{{ trans('v3/accessibility.skip_content') }}</a>
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center" aria-label="Cargando">
            <img class="animation__shake" src="{{ url('v3/') }}/dist/img/AdminLTELogo.png" alt="Aitrol" height="60" width="60">
        </div>
        @yield('header')
        @yield('menu')
        <main class="content-wrapper" id="contenido-principal" tabindex="-1" data-module="{{ strtoupper(str_replace('_', ' ', config('data.controlador') ?: 'Gestión')) }}">
            @yield('contenido')
        </main>
        @yield('footer')
        @yield('variables')
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>
    @include('v3.layouts.view_support_assistant')
    @include('layouts.patient-library-floating')
    @yield('js')
    <script defer src="{{ url('v3/') }}/dist/js/support-assistant.js?v={{ @filemtime(public_path('v3/dist/js/support-assistant.js')) }}"></script>
</body>
</html>
