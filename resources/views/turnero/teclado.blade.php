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

</head>

<body>
    @if($msj != "")
    <div class="alert alert-danger" style="height: 10%;text-align: center;margin:5%;line-height:50px;" role="alert">
        <span style="color:white;font-weight: bold;font-size:18px;">{{$msj}}</span>
    </div>
    @else
    <input type="hidden" name="sala" id="sala" value="{{$sala->id}}">
    <input type="hidden" name="hospital" id="hospital" value="{{$hospital->id}}">
    <form class="form-vertical" id="formulario_envio_sin_cedula" role="form" method="POST" action="{{ route('turnero_index_sincedula') }}">
        {{ csrf_field() }}
        <input type="hidden" name="sala_id_2" id="sala_id_2">
        <input type="hidden" name="hospital_id_2" id="hospital_id_2">
        <input type="hidden" name="eleccion_2" id="eleccion_2">
    </form>
    <form class="form-vertical" id="formulario_envio" role="form" method="POST" action="{{ route('turnero_index') }}">
        {{ csrf_field() }}
        <input type="hidden" name="sala_id" id="sala_id">
        <input type="hidden" name="hospital_id" id="hospital_id">
        <input type="hidden" name="eleccion" id="eleccion">
    </form>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2" style="padding: 0 !important; margin: 0 !important">
                <img style="float: left;height:100%;width:100%" src="{{asset('/imagenes/parte_alta.png')}}">
            </div>
            <div class="col-md-12" style="text-align:left;">
                <button type="button" onclick="recargar()" style="height:50px;width:80px;background-color: #EF771D;border-radius:10px;color:white;border: 1px solid #39c;"><i class="fa fa-undo" aria-hidden="true"></i>Actualizar</button>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12" style="text-align:center;margin-top:3%">
                <label for="" style="font-size: 45px;margin-top:5%">Seleccion gestión de servicio:</label>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12" style="margin-top: 4%;">
                <div class="col-md-6 col-sm-4 col-xs-12" style="text-align:right;">
                    <img id="procedimiento" class="centrado" style="width: 90%;height:270px;margin-left:80px " src="{{asset('/imagenes/procedimiento.png')}}">
                </div>
                <div class="col-md-6 col-sm-4 col-xs-12" style="text-align: left;">
                    <img id="consulta" class="centrado" style="width: 80%;" src="{{asset('/imagenes/consulta.png')}}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12" style="margin-top: 4%;">
                <div class="col-md-6 col-sm-4 col-xs-12" style="text-align: right;">
                    <img id="otros" class="centrado" style="width: 80%;" src="{{asset('/imagenes/otros.png')}}">
                </div>
                <div class="col-md-6 col-sm-4 col-xs-12" style="text-align: left;">
                    <img id="examenes" class="centrado" style="width: 80%;" src="{{asset('/imagenes/examenes.png')}}">
                </div>
            </div>
            <div class="col-md-12 col-lg-12" style="padding: 0;position: absolute;bottom: 0;">
                <img style="height:100%;width:100%" src="{{asset('/imagenes/turnerogrande.png')}}">
            </div>
        </div>
        @endif
</body>

</html>

<script type="text/javascript">
    function recargar() {
        location.reload();
    }
    $(document).on('click', 'img', function() {
        let eleccion = $(this).attr('id');
        let hospital = document.getElementById('hospital').value;
        let sala = document.getElementById('sala').value;
        if (eleccion == 'procedimiento' || eleccion == 'consulta') {
            $('#eleccion').val(eleccion);
            $('#sala_id').val(sala);
            $('#hospital_id').val(hospital);
            $('#formulario_envio').submit();
        } else if (eleccion == 'otros' || eleccion == 'examenes') {
            $('#eleccion_2').val(eleccion);
            $('#sala_id_2').val(sala);
            $('#hospital_id_2').val(hospital);
            $('#formulario_envio_sin_cedula').submit();
        }
    });
</script>