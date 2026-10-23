<!DOCTYPE html>
<html>

<head>
    <title>Turnero</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style>
        @media only screen and (max-width: 600px) {
            .cont-img {
                height: 600px !important;
                filter: brightness(1);
            }
        }

        @media only screen and (max-width: 2560px) {
            .cont-img {
                height: 1150px !important;
                filter: brightness(1);
            }
        }

        @media only screen and (max-width: 1740px) {
            .cont-img {
                height: 630px !important;
                filter: brightness(1);
            }
        }

        @media only screen and (max-width: 1440px) {
            .cont-img {
                height: 550px !important;
                filter: brightness(1);
            }
        }

        .cont-img_div {
            width: 100%;
            background-position: center;
            /* Center the image */
            background-repeat: no-repeat;
            /* Do not repeat the image */
            background-size: cover;
            /* Resize the background image to cover the entire container */
        }

        .cont-img {
            height: 550px !important;
            filter: brightness(0.8);
        }

        .parpadea {

            animation-name: parpadeo;
            animation-duration: 2s;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;

            -webkit-animation-name: parpadeo;
            -webkit-animation-duration: 3s;
            -webkit-animation-timing-function: ease-in-out;
            -webkit-animation-iteration-count: infinite;
        }

        @-moz-keyframes parpadeo {
            0% {
                opacity: 1.0;
            }

            50% {
                opacity: 0.0;
            }

            100% {
                opacity: 1.0;
            }
        }

        @-webkit-keyframes parpadeo {
            0% {
                opacity: 1.0;
            }

            50% {
                opacity: 0.0;
            }

            100% {
                opacity: 1.0;
            }
        }

        @keyframes parpadeo {
            0% {
                opacity: 1.0;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1.0;
            }
        }

        #contenedor {
            text-align: left;
            width: 100%;
            margin: auto;
        }

        #lateral {
            z-index: 2;
            position: absolute;
            overflow: hidden;
            margin-left: 40%;
            width: 90%;
            /* Este será el ancho que tendrá tu columna */
            /* Aquí pon el color del fondo que quieras para este lateral */
            float: right;
            /* Aquí determinas de lado quieres quede esta "columna" */
        }

        #principal {
            z-index: 1;
            position: static;
            width: 80% !important;
            float: left;
            background-color: #FFFFFF;
        }

        #ima1 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE_Mesa de trabajo 1 copia 2.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima2 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE_Mesa de trabajo 1 copia 3.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima3 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE_Mesa de trabajo 1 copia.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima4 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-04.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima5 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-05.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima6 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-06.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima7 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-07.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima8 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-08.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima9 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-09.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima10 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-10.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima11 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-11.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima12 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-12.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima13 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-13.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima14 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-14.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima15 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-15.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima16 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-16.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }

        #ima17 {
            background: url("{{asset('/imagenes/pantallas/MEZZANINE-17.jpg')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: left;
        }
    </style>
</head>
@php
$date = date('Y-m-d');
@endphp

<body>
    <div id="contenedor" class="clearfix">
        <input type="hidden" id="fecha" name="fecha" value="{{$date}}">

        <div id="lateral">
            <div style="text-align: center;z-index:2;position:absolute;margin-left:26%;margin-top:30px;">
                <h1 style="color:red;font-weight:bold;font-size:75px;" class="parpadea">Turno</h1>
                <div style="color: red;font-weight:bold;margin-top:-55px">
                    <div id="turnosAct" style="text-align:center;margin-top:20px;">
                    </div>
                </div>
            </div>
            <div style="text-align: center;z-index:2;position:absolute;margin-left:27%;margin-top:17%">
                <span style="color: red;font-weight:bold;font-size:75px;" class="parpadea" id="modul"></span>
            </div>
            <div style="margin-left: 16%;position:absolute;margin-top:20%" id="turnitos">

            </div>
            <img id="img" alt="imagen" src="{{asset('/imagenes/lateral.png')}}">
        </div>
        <div id="principal">
            <div>
                <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel" data-interval="10000">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div id="ima1" class="d-block w-100 nueva">
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div id="ima2" class="d-block w-100">
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img id="ima3" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima4" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima5" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima6" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima7" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima8" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima9" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima10" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima11" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima12" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima13" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima14" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima15" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima16" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img id="ima17" class="d-block w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript">
    var fecha = document.getElementById("fecha").value;
    $(document).ready(function() {
        setInterval(function() {
            var array = [];
            $.ajax({
                url: "{{route('nuevo_turnos_pantalla')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'GET',
                async: false,
                dataType: 'html',
                success: function(datahtml) {
                    $("#turnosAct").html(datahtml).fadeIn();;
                    $.ajax({
                        url: "{{route('turno_lista_pantalla')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        type: 'GET',
                        dataType: 'html',
                        success: function(datahtml) {
                            $("#turnitos").html(datahtml).fadeIn();

                        },
                        error: function(xhr, status) {
                            alert('Existió un problema');
                        },
                    });
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });

        }, 8000);

    });
    $(document).ready(function($) {
        var ventana_alto = $(window).height();
        var ventana = $(window).width();
        for (let index = 1; index <= 17; index++) {
            $("#ima" + index).height(ventana_alto);
        }
        $("#img").height(ventana_alto);
        $("#img").width(ventana);
    });
</script>