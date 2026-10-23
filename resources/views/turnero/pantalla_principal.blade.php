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
    <script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/icheck.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/simple-keyboard@latest/build/index.js"></script>
    <script src="https://kit.fontawesome.com/6c429f978d.js" crossorigin="anonymous"></script>
    <style>
        .table-letras {
            margin: auto;
            border-collapse: separate;
            border-spacing: 0 3em;
        }

        .table-letras td {
            padding: 0 3px;
        }

        .table-letras tr {
            margin-top: 15px !important;
        }

        .table-center {
            margin: auto;
            border-collapse: separate;
            border-spacing: 0 1em;
        }

        .table-center td {
            padding: 0 15px;
        }

        .table-center tr {
            margin-top: 15px !important;
        }

        .numero_general {
            width: 80px;
            height: 80px;
            font-size: 30px;
            color: black;
            border-radius: 5px;
            border: 1px solid #39c;
            background-color: white;
            font-weight: bold;
        }

        .swal-wide {
            margin-top: -40% !important;
            width: 260px !important;
            height: 160px !important;
            position: relative !important;
        }

        .swal-wid1 {
            margin-top: -40% !important;
            width: 260px !important;
            height: 160px !important;
            position: relative !important;
        }
    </style>
</head>

<body style="margin: 0 !important;" id="body">
    <input type="hidden" name="sala" id="sala" value="{{$sala}}">
    <input type="hidden" name="hospital" id="hospital" value="{{$hospital}}">
    <input type="hidden" name="eleccion" id="eleccion" value="{{$eleccion}}">
    <div class="container-fluid">
        <form id="formulario_envio" role="form" method="POST" action="{{ route('imprimir_boleto_turnero') }}">
            {{ csrf_field() }}
            <input type="hidden" name="id_registro" id="id_registro">
        </form>
        <div class="row" style="text-align: center;">
            <div class="col-md-2" style="padding: 0 !important; margin: 0 !important">
                <img style="float: left;height:100%;width:100%" src="{{asset('/imagenes/parte_alta.png')}}">
            </div>
            <div class="col-md-12" style="text-align:left;">
                <button type="button" onclick="recargar()" style="height:50px;width:80px;background-color: #EF771D;border-radius:10px;color:white;border: 1px solid #39c;"><i class="fa fa-undo" aria-hidden="true"></i> Cancelar</button>
                <button type="button" onclick="retroceder()" style="height:50px;width:80px;background-color: #EF771D;border-radius:10px;color:white;border: 1px solid #39c;"><i class="fa fa-reply-all" aria-hidden="true"></i> Retroceder</button>
            </div>
            <div class="col-md-12" style="margin-top: 15%;">
                <label style="text-align:center;color:#4F80FF;font-size:bold;font-size: 30px;" for="">BIENVENIDOS A IECED</label>
                <label style="color:#EF771D;font-size:bold;font-size: 20px;text-align: center;" for="">Gracias por confiar en IECED y en su grupo de profesionales,ingrese su número de cédula o pasaporte</label>
                <input id="campo" placeholder="Ingrese la cédula o pasaporte del paciente" style="text-align:center;font-size:20px;border-radius:25pc;border-color:#EF771D;width:60%;height: 50px;padding:10px;" />
            </div>
            <div id="numeros_letras" style="display:none">
                <table id="table" class="table-letras" style="margin-top: 3%;text-align:center">
                    <tbody>
                        <tr>
                            <td><input class="numero numero_general" value="q" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="w" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="e" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="r" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="t" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="y" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="u" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="i" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="o" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="p" onclick="number(this)" type="button"></td>
                        </tr>
                        <tr>
                            <td><input class="numero numero_general" value="a" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="s" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="d" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="f" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="g" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="h" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="j" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="k" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="l" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="ñ" onclick="number(this)" type="button"></td>
                        </tr>
                        <tr>
                            <td><button class="numero_general"><i class="fa fa-arrow-up" onclick="mayusculas()" aria-hidden="true"></i></button></td>
                            <td><input class="numero numero_general" value="z" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="x" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="c" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="v" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="b" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="n" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero_general" value="m" onclick="number(this)" type="button"></td>
                            <td><button onclick="eliminar()" class="numero_general"><i class="fa fa-window-close-o" aria-hidden="true"></i></button></td>
                            <td><button onclick="aparecer()" class="numero_general">123
                                </button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12" style="margin-top: 2%;" id="numeros">
                <table id="table" class="table-center">
                    <tbody>
                        <tr>
                            <td><input class="numero numero numero_general" value="1" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="2" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="3" onclick="number(this)" type="button"></td>
                        </tr>
                        <tr>
                            <td><input class="numero numero numero_general" value="4" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="5" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="6" onclick="number(this)" type="button"></td>
                        </tr>
                        <tr>
                            <td><input class="numero numero numero_general" value="7" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="8" onclick="number(this)" type="button"></td>
                            <td><input class="numero numero numero_general" value="9" onclick="number(this)" type="button"></td>
                        </tr>
                        <tr>
                            <td><button class="numero numero_general" onclick="aparecer()">abc
                                </button></td>
                            <td><input class="numero numero numero_general" value="0" onclick="number(this)" type="button"></td>
                            <td><button class="numero numero_general" onclick="eliminar()"><i class="fa fa-window-close-o" aria-hidden="true"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 col-lg-12" style="text-align: center;">
                <input id="enviarTod" value="Continuar" onclick="aceptar()" type="button" style="height:70px;width:130px;background-color: #EF771D;border-radius:10px;color:white;border: 1px solid #39c;">
            </div>
            <div class="col-md-12 col-lg-12" style="padding: 0;position: absolute;bottom: 0;">
                <img style="height:100%;width:100%" src="{{asset('/imagenes/turnerogrande.png')}}">
            </div>
        </div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    var hospital = document.getElementById('hospital').value;
    var sala = document.getElementById('sala').value;
    var letres = true;

    function aparecer() {
        if (letres == true) {
            document.getElementById("numeros").style.display = "none";
            document.getElementById("numeros_letras").style.display = "block";
            letres = false;
        } else {
            document.getElementById("numeros").style.display = "block";
            document.getElementById("numeros_letras").style.display = "none";
            letres = true;
        }
    }

    function recargar() {
        location.reload();
    }

    function retroceder() {

        window.location = "{{url('turnero/tabla')}}/" + hospital + '/' + sala;
    }

    function eliminar() {

        var campo = document.getElementById("campo");
        var res = campo.value.substring(0, campo.value.length - 1);
        campo.value = res;
    }

    var passVar = true;

    function mayusculas() {
        if (passVar == true) {
            var fun = $("#table").children().children().children().find('.numero').css("text-transform", "uppercase");
            var masc = $("#table").children().children().children().find('.numero');
            for (var i = 0; i < masc.length; i++) {
                var t = masc[i].value.toUpperCase();
                masc[i].value = t;
            }
            passVar = false;
        } else {
            var fun = $("#table").children().children().children().find('.numero').css("text-transform", "lowercase");
            var masc = $("#table").children().children().children().find('.numero');
            for (var i = 0; i < masc.length; i++) {
                var t = masc[i].value.toLowerCase();
                masc[i].value = t;
            }
            passVar = true;
        }
    }

    function number(numero) {
        var campo = document.getElementById('campo');
        campo.value = campo.value + numero.value;
    }

    function aceptar() {
        let cedpas = document.getElementById('campo').value;
        if (cedpas.length == 10) {
            $.ajax({
                url: "{{route('verficacion_boleto')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    cedula: cedpas,
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        document.getElementById("enviarTod").disabled = true;
                        guardar(cedpas, document.getElementById("sala").value, document.getElementById("hospital").value, document.getElementById("eleccion").value);  
                    } else {
                        Swal.fire({
                            title: 'Paciente Agendado',
                            text: data.msj,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Confirmo'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                imprimir(data.body.id)
                            }else{
                                location.reload();
                            }
                        })

                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });
        } else {
            alerta("error", "La cédula es obligatoria");
        }
    }


    function guardar(cedula, sala, hospital, eleccion) {
        $.ajax({
            url: "{{route('turnero_teclado_documentos')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'cedpas': cedula,
                'sala': sala,
                'hospital': hospital,
                'eleccion': eleccion,
                'tipo' : 0,
            },
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    imprimir(data.body.id);
                } else {
                    const myTimeout = setTimeout(alerta('error', data.msj), 1500);
                    location.reload();
                }

            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
    }

    function alerta(tipo, msj) {
        Swal.fire({
            position: 'top',
            icon: tipo,
            title: msj,
            showConfirmButton: false,
            timer: 2500
        })
    }

    function imprimir(id_registro) {
        $("#id_registro").val(id_registro);
        $("#formulario_envio").submit();
    }
</script>