@extends('hc4.app-template')
@section('content')

<style type="text/css">
    #navbar {
      overflow: hidden;
    }

    #navbar a {
      float: left;
      display: block;
      color: #f2f2f2;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 17px;
      z-index: 99999;
    }

    #navbar a:hover {
      background-color: #ddd;
      color: black;
    }

    #navbar a.active {
      background-color: #4CAF50;
      color: white;
    }

    .content {
      padding: 16px;
    }

    .sticky {
      position: fixed;
      top: 0;
      width: 100%;
    }

    .sticky + .content {
      padding-top: 60px;
    }

    hr{
	    height: 3px;
      width: 100%;
	    border: 0;
	    background-color: #6B6B6F;
      margin-top:2px;
    }

    .calendario{
      background-image: url("{{asset('/')}}hc4/img/btn-ca.png"),linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1); 
      border-radius: 10px;
      width: 100%;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }

    @media screen and (max-width:640px) {
      /* reglas CSS */
      .example-8 .navbar-brand {
        background: none;
        width: 200px;
        height: 50px;
        transform: translateX(-60%);
        left: 43%;
        color: ;
        position: absolute;
      }

      .cambiar{
        display: none !important;
      }
      .row_datos{
        padding-left: 5%;
        padding-right: 5%;
      }
      .boton{
        width: 100%;
      }
    }

  </style>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header" style="font-family: Helvetica;margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right,#124574,#0C8BEC,#124574);  margin-bottom: 15px;">
      <div class="row">
        <div class="col-md-12">
          <h1><img src="{{asset('/')}}hc4/img/art_doc.png" style="width: 36px;"> &nbsp;&nbsp;<b>@lang('hc4.areaMedica')</b></h1>
        </div>
        @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id == '9666666666')
        <div class="col-md-3">
          <a href="{{route('hospital.index')}}" class="btn btn-primary" style="background-color: #004AC1 !important;border: 2px solid white;">SISTEMA HOSPITALARIO</a>
        </div>
        @endif
      </div>


    </section>




    @yield('action-content')
    <!-- /.content -->
  </div>



@endsection