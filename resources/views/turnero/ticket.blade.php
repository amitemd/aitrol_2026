<!DOCTYPE html>
<html lang="en">

<head>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/icheck.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/index.js"></script>
    <script src="https://kit.fontawesome.com/6c429f978d.js" crossorigin="anonymous"></script>
    <title>Ticket</title>
    <style>
        .img {
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }
    </style>
</head>
@php
$fecha = substr($turnero->created_at, 0, 11);
$formatoFecha = date('d/m/Y', strtotime($fecha));
@endphp
<div class="container-fluid" id="imprimir">
    <input type="hidden" id="sala" value="{{$turnero->id_sala}}">
    <input type="hidden" id="hospital" value="{{$turnero->id_hospital}}">
    <div class="row" style="text-align: center;">
        <div class="col-md-2" style="padding: 0 !important; margin: 0 !important">
            <img style="float: left;height:100%;width:100%" src="{{asset('/imagenes/parte_alta.png')}}">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12" style="text-align: center;margin-top:5%; width:auto; height: auto;">
            <div id="imprimir_ticket">
                <div class="col-md-12" style="margin-top: 50px;">
                    <img style="text-align:center;width:140px;height:80px;" src="{{asset('/imagenes/etiqueta.png')}}">
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Turno: {{strtoupper(ucfirst(substr($turnero->letraproc, 0, 1)))}}-{{$turnero->turno}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Cédula: {{$turnero->cedula}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Tipo: {{ucfirst($turnero->letraproc)}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Fecha: {{$formatoFecha}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Hora:{{substr($turnero->created_at, 11, 20)}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Usuarios en Espera: {{$usuariosEspera}}</label>
                </div>
                <div class="col-md-12">
                    <label style="font-size: 20px;" for="">Por favor espere el llamado en pantalla</label>
                </div>
            </div>
            <div class="col-md-12">
                <img class="img" style="width: auto; height:auto;" src="{{asset('/imagenes/ticketbajo.png')}}">
            </div>
        </div>
        <div class="col-md-12 col-lg-12" style="padding: 0;position: absolute;bottom: 0;">
            <img style="height:auto;width:auto" src="{{asset('/imagenes/turnerogrande.png')}}">
        </div>
    </div>
    <div style="height:100px !important;" id="spacio">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.
    </div>
</div>

</html>

<script type="text/javascript">
    var sala = document.getElementById('sala').value;
    var hospital = document.getElementById('hospital').value;
    window.addEventListener('load', function() {
        var ficha = document.getElementById('imprimir_ticket');
        var spacio = document.getElementById('spacio');
        var ventimp = window.open('', '', 'height=350,width=700');
        ventimp.document.write('<div style="text-align:center;margin-top:-70px;margin-right:55;font-size:12px !important">' + ficha.innerHTML + '</div>' + '<div style="margin-top:60px;">' + spacio.innerHTML + '</div>');
        ventimp.document.close();
        ventimp.focus();
        ventimp.print();
        ventimp.close();
        window.location = "{{url('turnero/tabla')}}/" + hospital + '/' + sala;

    });
</script>