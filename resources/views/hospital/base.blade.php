@extends('hospital.app-template')
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
  
  .sticky {
    position: fixed;
    top: 0;
    left: 0px;
    width: 100%;
    max-height: 125%;

  }

  .sticky + .content {
    padding-top: 55px;
  }

  .calendario{
    background-image: url("{{asset('/')}}hc4/img/Layer 3.png"),linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1); 
    border-radius: 30px;
    width: 100%;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    object-fit: scale-down;
  }

  @media screen and (max-width:700px) {
    /* reglas CSS */

    .cambiar{
      display: none !important;
    }

    .sticky {
      position: relative !important;
    }

  }

  .btn_ordenes{
		font-size: 10px ;
		width: 100%;
		background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
		color: white;
		text-align: center;
		height: 100%;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 5px;
    border-radius: 20px;
	}
  label{
    font-family: Montserrat Bold;
  }
</style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<div class="content">
  <!-- Content Header (Page header) -->
  <section class="content-header" style="font-family: Montserrat ExtraBold Italic; margin: 8px;color: white; text-align: center; padding: 10px; border-radius: 30px; background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);  margin-bottom: 15px;">
    <h1><a href="{{route('hospital.index')}}"><img src="{{asset('/')}}hc4/img/hospital.png" style="font-family: Montserrat ExtraBold Italic; width: 36px;"> &nbsp;&nbsp;<b style="font-family: Montserrat Medium; color: white;">GESTI&Oacute;N HOSP&Iacute;TALARIA</b></a></h1>
  </section>

  <div class="container-fluid">
    <div class="col-md-12 col-sm-12 col-12 container" id="ok" style="z-index: 999">
      <div class="row">
        
        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb" href="#">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;" >
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/Layer 867.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">IM&Aacute;GENES</label>
                  </div>
                </div>
            </div>
          </a>
        </div>
        
        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb form-control-lg" href="#">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;" >
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/laboratorio.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">LABORATORIO</label>
                  </div>
                </div>
            </div>
          </a>
        </div>

        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb" onclick ="location.href='{{route('hospital.farmacia')}}'">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;">
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/L1.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">FARMACIA</label>
                  </div>
                </div>
            </div>
          </a>
        </div>

        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb" onclick ="location.href='{{route('hospital.quirofano')}}'">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;">
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/Layer 2.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">QUIR&Oacute;FANO</label>
                  </div>
                </div>
            </div>
          </a>
        </div>

        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb" onclick ="location.href='{{route('hospital.gcuartos')}}'">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;">
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/gestiondecuarto.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">HABITACI&Oacute;N</label>
                  </div>
                </div>
            </div>
          </a>
        </div>

        <div class="col-md-2 col-sm-4 col-6" style="margin-bottom: 15px; height: 40px">
          <a class="btn btn_ordenes coloresb" onclick ="location.href='{{route('hospital.emergencia')}}'">
            <div class="col-12" style="margin-top: 5px;">
                <div class="row" style="padding-left: 15px; padding-right: 15px;">
                  <div class="col-md-2 col-sm-2 col-2" style="padding-left: 1px;" >
                    <img style="color:white;" width="25px" src="{{asset('/')}}hc4/img/emergencia.png">
                  </div>
                  <div class="col-md-6 col-sm-6 col-6" style="padding-left: 10%; margin-right: 10px">
                    <label style="font-size: 14px; color:white;">EMERGENCIA</label>
                  </div>
                </div>
            </div>
          </a>
        </div>

      </div>
    </div> 
  </div>

  @yield('action-content')  
  <!-- /.content -->

</div>

<script>
  window.onscroll = function() {myFunction()};

  var navbar = document.getElementById("ok");
  var sticky = navbar.offsetTop;
  function myFunction() {

    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }       
        
  }
</script>

@endsection