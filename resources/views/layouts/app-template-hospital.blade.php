<!DOCTYPE html>
<!--
  TEMPLATE ACHILAN
-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hospital</title>
    <!-- core:css -->
    <link rel="stylesheet" href="{{asset('/assets/vendors/core/core.css')}}">
	<!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="{{asset('/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
	<!-- end plugin css for this page -->
	<!-- inject:css -->
	<link rel="stylesheet" href="{{asset('/assets/fonts/feather-font/css/iconfont.css')}}">
	<link rel="stylesheet" href="{{asset('/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
	<!-- endinject -->
  <!-- Layout styles -->  
	<link rel="stylesheet" href="{{asset('/assets/css/demo_5/style.css')}}">

  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{asset('/hc4/img/logo.png')}}" />
  <link href="{{ asset("/assets/vendors/datatable.net-bs4/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="{{asset('/assets/vendors/fullcalendar/main.min.css')}}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

</head>
<body>


<div class="main-wrapper"> 
    @include('layouts.sidebarh')
    <!-- Sidebar -->
    <div class="page-wrapper">
      <div class="page-content">
        @yield('content')
      </div>
    </div>
</div>
<!-- core:js -->

	<!-- endinject -->
  <!-- plugin js for this page -->
  <script src="{{asset('/assets/vendors/core/core.js')}}"></script>
  <script src="{{asset('/assets/vendors/moment/moment.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/jquery-ui/jquery-ui.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/chartjs/Chart.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/fullcalendar/main.min.js')}}"></script>

  <script src="{{asset('/assets/vendors/jquery.flot/jquery.flot.js')}}"></script>
  <script src="{{asset('/assets/vendors/jquery.flot/jquery.flot.resize.js')}}"></script>
  <script src="{{asset('/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/progressbar/progressbar.min.js')}}"></script>
  <script src="{{asset('/assets/vendors/datatables.net/jquery.dataTables.js')}}"></script>
	<!-- end plugin js for this page -->
	<!-- inject:js -->
  <script src="{{asset('/assets/vendors/datatables.net/jquery.dataTables.js')}}"></script>  
  <script src="{{asset('/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js')}}"></script>  
  <script src="{{asset('/assets/js/data-table.js')}}"></script>
	<script src="{{asset('/assets/vendors/feather-icons/feather.min.js')}}"></script>
	<script src="{{asset('/assets/js/template.js')}}"></script>
	<!-- endinject -->
  <!-- custom js for this page -->
  <script src="{{asset('/assets/js/dashboard.js')}}"></script>
  <script src="{{asset('/assets/js/datepicker.js')}}"></script>
  <script src="{{asset('/assets/js/fullcalendar.js')}}"></script>

    <!-- endinject -->
</body>
</html>