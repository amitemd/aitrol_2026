<!DOCTYPE html>
@php
$c=DB::table('ho_parametros')->where('id_usuario',Auth::user()->id)->first();
$class="dark-layout";
$classicon="sun";
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
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="">  
    <title>SIAM- HOSPITAL</title>
    <link rel="apple-touch-icon" href="{{asset('ho/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/hc4/img/logo.png')}}">
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
    <link type="text/html" href="{{asset('ho/app-assets/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/assets/css/style.css')}}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    

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
</style>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="">

    <!-- BEGIN: Header-->

    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @include('layouts.app-header-h')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper" @if(isset($data)) style="margin-top: 10%" @endif>
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
        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        $(document).ready(function() {
          
           //console.log('ss');
           if(!isMobile.any()){
                if ($(window).width() > 1440) {
                document.body.style.zoom = "80%";
                }
                else {
                document.body.style.zoom = "60%";
                }
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
</body>
<!-- END: Body 
        <div>
         include('layouts.boxesh')
        </div>
-->

</html>