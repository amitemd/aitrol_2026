<!DOCTYPE html>
@php
$c=DB::table('ho_parametros')->where('id_usuario',Auth::user()->id)->first();
$class="light-layout";
$classicon="moon";
if(!is_null($c)){
$class= $c->class;
$classicon=$c->noclass;
if($class=="dark-layout"){
$classicon="sun";
}else{
$classicon="moon";
}

}
@endphp
<html class="{{$class}} loading" lang="en" data-textdirection="ltr">
<script>
    (function () {
        try {
            var savedHospitalTheme = window.localStorage.getItem('hospital-theme') || 'light-layout';
            window.localStorage.setItem('hospital-theme', savedHospitalTheme);
            if (savedHospitalTheme === 'dark-layout' || savedHospitalTheme === 'light-layout') {
                document.documentElement.classList.remove('dark-layout', 'light-layout', 'semi-dark-layout', 'bordered-layout');
                document.documentElement.classList.add(savedHospitalTheme);
            }
        } catch (error) {}
    })();
</script>
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="">  
    <title>SIAM- HOSPITAL</title>
    <link rel="apple-touch-icon" href="{{ asset('ho/app-assets/images/ico/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="128x128" href="{{ asset('assets/images/aitrol-favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/charts/apexcharts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/extensions/toastr.min.css')}}">
    <!-- END: Vendor CSS-->
    <script src="https://kit.fontawesome.com/4eb420ba86.js" crossorigin="anonymous"></script>

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.17/sweetalert2.min.css" href="style.css">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/calendars/fullcalendar.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/components.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/themes/semi-dark-layout.css')}}">
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/pages/dashboard-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/charts/chart-apex.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

    <!-- END: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/pages/app-calendar.css')}}">
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('ho/assets/css/style.css')}}">
    @include('layouts.hospital-modern-styles')

    {{--
        La ficha de emergencia carga sus secciones mediante JavaScript desde el
        mismo servidor. No debe depender de un CDN: si el servidor no tiene
        salida a Internet, los botones clínicos quedarían visibles pero sin
        ninguna acción.
    --}}
    <script src="{{ asset('/bower_components/jquery/dist/jquery.min.js') }}"></script>
    

    <!-- BEGIN: Page Vendor JS-->

    <!-- END: Custom CSS-->

</head>
<style>
  .ui-menu-item
        {
           color: black!important;
        }
    .modal-open{
        zoom: 100%!important;
    }
    .hospital-theme-toggle {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin: 0;
        padding: 0 !important;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: inherit;
        cursor: pointer;
        appearance: none;
        transition: background-color .2s ease, color .2s ease;
    }
    .hospital-theme-toggle:hover,
    .hospital-theme-toggle:focus {
        background: rgba(8, 119, 190, .1);
        color: #0877be;
        outline: none;
    }
    .hospital-theme-toggle .ficon,
    .hospital-theme-toggle svg {
        width: 20px;
        height: 20px;
        pointer-events: none;
    }
</style>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu navbar-floating footer-static hospital-modern-ui {{ Request::is('hospital/inicio') ? 'hospital-home-route' : 'hospital-internal-route' }} {{ isset($data) ? 'hospital-patient-route' : '' }}" data-open="hover" data-menu="horizontal-menu" data-col="" data-framework="laravel" data-asset-path="{{ asset('ho/app-assets') }}/">

    <!-- BEGIN: Header-->

    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @include('layouts.app-header-h')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            @if(!Request::is('hospital/inicio'))
                @include('layouts.hospital-breadcrumb')
            @endif
            @yield('content')
        </div>



    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT SIAAM &copy; 2021</span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->

    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!--<script src="{{asset('ho/app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>-->
    <!-- END: Page JS-->
    <script src="{{asset('ho/app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- BEGIN Vendor JS-->
    <script src="{{asset('ho/app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/calendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{asset('ho/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    @include('layouts.select2-global')

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->

    <script src="{{asset('ho/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
    <script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

        });
        function getLoader(data){
            $.ajax({
            type: 'get',
            url:"{{route('hospital.enableDark')}}",
            data:{'class':data},
            datatype: 'json',
            success: function(data){
                console.log(data)
            },
            error: function(data){
                //console.log(data);
            }
            }); 
        }

        (function () {
            var toggle = document.getElementById('hospital-theme-toggle');
            var root = document.documentElement;
            if (!toggle || !root) {
                return;
            }

            function currentTheme() {
                return root.classList.contains('dark-layout') ? 'dark-layout' : 'light-layout';
            }

            function renderTheme(theme) {
                var dark = theme === 'dark-layout';
                root.classList.remove('dark-layout', 'light-layout', 'semi-dark-layout', 'bordered-layout');
                root.classList.add(dark ? 'dark-layout' : 'light-layout');

                document.querySelectorAll('.header-navbar').forEach(function (navbar) {
                    navbar.classList.toggle('navbar-dark', dark);
                    navbar.classList.toggle('navbar-light', !dark);
                });

                toggle.setAttribute('aria-pressed', dark ? 'true' : 'false');
                toggle.setAttribute('aria-label', dark ? 'Activar modo claro' : 'Activar modo oscuro');
                toggle.setAttribute('title', dark ? 'Activar modo claro' : 'Activar modo oscuro');
                toggle.innerHTML = '<i class="ficon" data-feather="' + (dark ? 'sun' : 'moon') + '"></i>';
                if (window.feather) {
                    window.feather.replace({ width: 18, height: 18 });
                }
            }

            function persistTheme(theme) {
                try {
                    window.localStorage.setItem('hospital-theme', theme);
                    window.localStorage.setItem('light-layout-current-skin', theme);
                } catch (error) {}

                if (window.fetch) {
                    window.fetch('{{ route('hospital.enableDark') }}?class=' + encodeURIComponent(theme), {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).catch(function () {});
                } else if (window.jQuery) {
                    window.jQuery.get('{{ route('hospital.enableDark') }}', { class: theme });
                }
            }

            renderTheme(currentTheme());
            toggle.onclick = function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                var nextTheme = currentTheme() === 'dark-layout' ? 'light-layout' : 'dark-layout';
                renderTheme(nextTheme);
                persistTheme(nextTheme);
                return false;
            };
        })();

        function alertas(icon, title, msj) {
            Swal.fire({
                icon: icon,
                title: title,
                html: msj
            })
        }

        function validarDatos() {
            let campo = document.querySelectorAll(".validar")
            let validar = false;

            for (let i = 0; i < campo.length; i++) {

                if (campo[i].value.trim() <= 0) {
                    campo[i].style.border = '2px solid #CD6155';
                    campo[i].style.borderRadius = '4px';
                    validar = true;
                } else {
                    campo[i].style.border = '1px solid #d2d6de';
                    campo[i].style.borderRadius = '0px';
                }
            }
            return validar;
        }
    </script>
    @include('layouts.medical-tour', ['medicalTourArea' => 'hospital'])
    @include('layouts.patient-library-floating')
</body>
<!-- END: Body 
        <div>
         include('layouts.boxesh')
        </div>
-->

</html>
