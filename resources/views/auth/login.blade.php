@if($_SERVER['SERVER_NAME'] == 'humanlabs.aitrol.com' && !isset($_SERVER['HTTPS']))
    @php
        header("Location: https://humanlabs.aitrol.com");
        die();
    @endphp
@endif

@if(isset($_SERVER['SERVER_NAME']))
    @if($_SERVER['SERVER_NAME'] == '186.70.157.2')

        <!DOCTYPE html>
        <html>

        @include('layouts.partials.htmlheader')
        <body>
            <div id="app" v-cloak>
                <!-- Main content -->
                <section class="content">
                    <!-- Your Page Content Here -->
                    <div class="error-page">
                        <h2 class="headline text-yellow"> 404</h2>
                        <div class="error-content">
                            <h3><i class="fa fa-warning text-yellow"></i> Oops! Página no encontrada.</h3>
                            <p>
                                SE HA REDIRECCIONADO A UN NUEVO DOMINIO dar clic en el enlace de abajo.<br>
                                <a style="font-size: 40px;" href='{{ url("https://ieced.siaam.ec") }}'>ieced.siaam.ec</a>
                            </p>
                        </div><!-- /.error-content -->
                    </div><!-- /.error-page -->
                </section>
            </div>
            @section('scripts')
                @include('layouts.partials.scripts')
            @show
        </body>
        </html>
        @php
            exit();

        @endphp
    @elseif($_SERVER['SERVER_NAME'] == 'ieced.siaam.ec' && !isset($_SERVER['HTTPS']))
        @php
            header("Status: 301 Moved Permanently");
            header("Location: https://ieced.siaam.ec");
            exit;
        @endphp
    @endif
@endif

@extends('layouts.app')
@section('content')
@php

 $ingreso =1;
 if(isset($_SERVER['SERVER_NAME'])){
     if($_SERVER['SERVER_NAME'] == 'labs.siaam.ec'){
         $ingreso = 0;
     }
 }


@endphp
<script type="text/javascript">
    console.log('{{$_SERVER['SERVER_NAME']}}');
</script>

<div>
    <!--opcional-->
    <br>
</div>

<div class="container">
    <div class="row" >
        {{--<div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default ">
                <div class="panel-heading" align="center">
                    @if($ingreso == 1)
                    <img style="width:75%; margin-top: 20px;" src="{{asset('/imagenes')}}/nuevo_logo.png" >
                    @else
                    <img style="width:35%; margin-top: 20px;" src="{{asset('/imagenes')}}/login2.png" >
                    <img style="width:45%; margin-top: 20px;" src="{{asset('/imagenes')}}/nuevo_logo.png" >
                    @endif
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                    {!! csrf_field() !!}
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} {{ $errors->has('0') ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input placeholder="Email ID" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required >
                                @if ($errors->has('0'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('1') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div  class="col-md-8 col-md-offset-2">
                                <input id="password" placeholder="Contraseña" type="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="checkbox btn btn-link">
                                    <label >
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme
                                    </label>
                                    <a class="btn btn-link" style="margin-left: 15px;" href="{{ route('password.request') }}">
                                         Olvidó su Contraseña?
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-primary col-md-10" style="background-color: #097ff5;" >
                                    Iniciar Sesion
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>--}}

<div class="col-md-12 border-box" style="background-color: #ffff; padding: 15px;">
      <div class="col-md-6 informativo" >
        <div>
            <img src="https://ieced.siaam.ec/sis_medico/public/imagenes/nuevo_logo.png" style="width: 50%; margin-top: 20px;">
        </div>
        <div class="center_grid" style="height: 80%;">
            <div class="center_grid" style="width: 35vh; height: 35vh; background: white; border-radius: 11px;">
                <img src="{{asset('img1.png')}}" alt="income" id="income" style="width: 100%;">
            </div>
            <div style="display: flex;">
                <input type="hidden" id="contador" value="1">
                 <div onclick="sliderImg(1)" class="linea uno"></div>
                 <div onclick="sliderImg(2)" class="linea dos"></div>
                 <div onclick="sliderImg(3)" class="linea tres"></div>
            </div>
            <div style="text-align: center;">
                <h2 style="color: #DDDDDD; font-weight: bold;" id="title" >Control business finances</h2>
                <div class="text-center">
                    <h4 style="color: #DDDDDD;" id="descripcion">The new Aitrol helps you control your income and expense from your business.</h4>
                </div>
            </div>
           
            
        </div>
       
    </div>


    <div class="col-md-6 center_grid">
            <div  class="center_grid login-form">
                <div style="height: 530px; width:100%; ">
                    <div style="text-align: center; " class="logo_login">
                        <img src="https://ieced.siaam.ec/sis_medico/public/imagenes/nuevo_logo.png" class="img-login">
                    </div>
                    <div class="text-center">
                        <h1 class="f-negra sign-in" style="font-weight: bold;">Sign In</h1>
                    </div>
                    <div class="text-center">
                        <p class="f-negra" style="font-size: 16px;">Enter your credentials to access your account.</p>
                    </div>
                    <div class="center_grid ancho_formulario">
                        <form class="form-horizontal" style="width: 100%;" role="form" method="POST" action="{{ route('login') }}">
                            {!! csrf_field() !!}
                            <div class="formulario_login" style="width:100%;">
                                <div>
                                    <input class="form-control" style="width:100%; padding: 20px; background-color:#ffff; border-radius: 4px; caret-color: #097ff5" type="text" id="email" name="email" placeholder="Email">
                                    @if ($errors->has('0'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('1') }}</strong>
                                        </span>
                                    @endif
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <input class="form-control" style="width:100%; padding: 20px; background-color:#ffff; border-radius: 4px;" type="password" id="email" name="password" placeholder="">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="checkbox btn btn-link" style="display: flex; justify-content: space-between;">
                                    <label class="f-negra f-weight-bol">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Keep me signed in
                                    </label>
                                    <a class="btn btn-link" style="margin-left: 15px;" href="{{ route('password.request') }}">Need help?</a>
                                </div>

                                <div>
                                    <button type="submit" class="btn-acceso">Sign-In</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>
@endsection
