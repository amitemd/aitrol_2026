@extends('hospital_admin.app-template2')
@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header" style="margin: 8px;color: white; text-align: center; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right,#004AC1,#00C8EC,#004AC1);">
    <h1><a href="{{route('hospital_admin.index')}}"><img src="{{asset('/')}}hc4/img/hospital.png" style=" width: 4%;"> &nbsp;&nbsp;<b style="font-family: Montserrat Medium; color: white;">GESTI&Oacute;N HOSP&Iacute;TALARIA-ADMINISTRACI&Oacute;N</b></a></h1>
  </section>  
  <div class="container-fluid" >
    
      @yield('action-content')  
    
  </div>
</div>

@endsection