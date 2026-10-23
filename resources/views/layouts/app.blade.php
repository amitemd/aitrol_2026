<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset("/favicon.ico")}}" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
<style type="text/css">
  .navbar, .navbar-collapse, ul.nav {
    /*ackground: url({{asset('/imagenes')}}/cat-nav-content.png) repeat-x scroll 0 0 transparent;*/
  }


  .border-box{
    box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.35);
    -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.35);
    -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.35);

    padding: 18px 0px;
    border-radius: 7px;
    margin-bottom: 12px;
  }
  *{
    font-family: 'Roboto', sans-serif!important;
  }
  .informativo{
    border-radius: 10px;
    height: 80vh;
    background: rgb(20, 98, 172);
    background: linear-gradient(0deg, rgba(20, 98, 172,1) 20%, rgba(96, 188, 202,1) 93%);
  }
  .center_grid{
    display: grid;
    place-items: center;
  }
  .f-negra{
    color: #000;
  }
  .f-weight-bol{
    font-weight:bold;
  }

  .formulario_login>div{
    margin-bottom: 35px;
  }

  .btn-acceso{
    width:100%;
    background-color: #3974BA;
    color: white;
    padding: 13px;
    border-radius: 8px;
    border: 0px solid transparent;
    font-weight: bold;
  }

  .btn-acceso:hover{
    background: #2980b9;
  }

  .inputs{
    border: 1px solid #0000001F;
  }

  .inputs:focus{
    border: 1px solid red;
  }

  .logo_login{
    display: none;
  }

  .ancho_formulario{
    height: 80%;
  }

  .login-form{
    width: 90%; 
    height: 80vh;
  }

  @media screen and (max-width: 991px)  {
    .informativo{
       display: none;
    }
    .logo_login{
        display: block;
    }
    .ancho_formulario{
        height: 50%;
    }
    .login-form{
        width: 100%; 
        height: 85vh;
    }
    .col-md-6{
      width: 100%!important;
    }
  }

  @media screen and (max-width: 320px){
    .container {
      margin-right: 0!important; 
      margin-left: 0!important; 
      padding-left: 0!important; 
      padding-right: 0!important; 
    }
  }
  .img-login{
    width: 50%; 
    margin-top: 20px;
  }
  @media screen and (max-height: 558px){
    .img-login{
      width: 40%; 
      margin-top: 20px;
    }
  }
  @media screen and (max-height: 503px){
    .img-login{
      width: 32%; 
      margin-top: 20px;
    }
    .sign-in{
      font-size: 27px;
      margin: 5px;
    }
  }

  .linea{
    width: 10px;
    height: 10px;
    background-color: white;
    border-radius: 50%;
    cursor: pointer;
  }
  .dos{
    margin-left: 10px;
    margin-right: 10px;
  }

  .activado{
    width: 47px;
    border-radius: 10px;
  }



</style>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css') }}">
     <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous" -->
    <!-- Scripts -->
    <script>

      
        window.onload = () => {
            setInterval(() => {
            let contador  = document.getElementById("contador").value
              sliderImg(contador)
            }, 3000);
        }


        const sliderImg = (id) => {
          console.log(id)

          if(id == 1){
            document.getElementById("income").src = `{{asset('img1.png')}}`;
            document.getElementById("title").innerHTML = `Control business finances`;
            document.getElementById("descripcion").innerHTML = `The new Aitrol helps you control your income and expense from your business.`;
            document.getElementById("contador").value = 2;

            document.querySelector('.uno').classList.add("activado");
            document.querySelector('.dos').classList.remove("activado");
            document.querySelector('.tres').classList.remove("activado");


          }else if(id == 2){
            document.getElementById("income").src = `{{asset('img2.png')}}`;
            document.getElementById("title").innerHTML = `The all-in-one solution`;
            document.getElementById("descripcion").innerHTML = `Access all information you need, graphs, reports and reports and more`;
            document.getElementById("contador").value = 3;

            document.querySelector('.uno').classList.remove("activado");
            document.querySelector('.dos').classList.add("activado");
            document.querySelector('.tres').classList.remove("activado");

          }else if(id == 3){
            document.getElementById("income").src = `{{asset('img3.png')}}`;
            document.getElementById("title").innerHTML = `Manage operational work`;
            document.getElementById("descripcion").innerHTML = `Let Aitrol be the ally of all your business related operations.`;
            document.getElementById("contador").value = 1;

            document.querySelector('.uno').classList.remove("activado");
            document.querySelector('.dos').classList.remove("activado");
            document.querySelector('.tres').classList.add("activado");

          }

        }


        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
@php
    $bodyBackground = '#042647';
    if (Route::currentRouteName() === 'ventas.sin_egreso') {
        $bodyBackground = '#f4f6f9';
    }
@endphp
<body style="background-color: {{ $bodyBackground }};">
    <div id="app" >

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
     <!--script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script -->
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script-->
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script-->
</body>
</html>
