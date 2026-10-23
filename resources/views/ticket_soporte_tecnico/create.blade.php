@extends('ticket_soporte_tecnico.base')
@section('action-content')
@php
    $date = date('Y-m-d');
    $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
<style>
    #result {
        border: 1px dotted #ccc;
        padding: 3px;
    }

    #result ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    #result ul li {
        padding: 5px 0;
    }

    #result ul li:hover {
        background: #eee;
    }
</style>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Soporte tecnico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>Ticket de Soporte Tecnico</b></h5>
            </div>

            <div class="col-md-3 text-right">
                <button id="boton" class="btn btn-danger">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="separator"></div>
        <div class="box-body">
            <form id="formulario" class="form-vertical" role="form">
                {{ csrf_field() }}
                <div class="form-group col-md-4">
                    <label for="area" class="col-md-3 control-label">Area</label>
                    <div class="col-md-9">
                        <input id="area" type="text" class="form-control" name="area" value="{{ old('area') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        @if ($errors->has('area'))
                        <span class="help-block">
                            <strong>{{ $errors->first('area') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="requerimientos" class="col-md-4 control-label">requerimientos</label>
                    <div class="col-md-8">
                        <input id="requerimientos" type="text" class="form-control" name="requerimientos" value="{{ old('requerimientos') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        @if ($errors->has('requerimientos'))
                        <span class="help-block">
                            <strong>{{ $errors->first('requerimientos') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @if($rolUsuario == 1 || !is_null($usuario_tabla))
                <div class="form-group col-md-4">
                    <label for="usuario" class="col-md-3 control-label">Usuario</label>
                    <div class="col-md-9">
                        <select id="usuario" name="usuario" class="form-control select2_cuentas" style="width: 100%;" required>
                            <option value="">Seleccione...</option>
                            @foreach($usuarios as $value)
                            <option value="{{$value->id}}">{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}}</option>
                            @endforeach
                        </select>
                        <div id="result">
                        </div>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <div class="col-md-12" style="text-align: center;">
                        <button id="botonGuardar" onclick="guardar(event)" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    document.getElementById("result").style.visibility = "hidden";

    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });


    });

    function guardar(e) {
        e.preventDefault();
        var newImg = new Image;
        newImg.src = 'http://http://192.168.59.38/sis_medico_prb/public/imagenes/progress.gif/public/imagenes/progress.gift';
        $area = $("#area").val();
        $requerimientos = $("#requerimientos").val();
        $usuario = $("#usuario").val();
        if ($area == "" || $requerimientos == "" || $usuario == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            document.getElementById("botonGuardar").disabled = true;
            document.getElementById("botonGuardar").innerHTML = 'Cargando...';
            $.ajax({
                url: "{{route('ticket_soporte_tecnico.guardar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('ticket_soporte_tecnico.index')}}"
                    if (data == 'ok') {

                        swal("Guardado!", "Correcto", "success");
                        //window.location = url;
                        timer: 10000
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }
    document.getElementById("boton").addEventListener('click', function() {

        window.history.back();
    });

    //autocompletar
    document.getElementById("usuario").addEventListener('keydown', function() {
        let usuario = document.getElementById("usuario").value;
        if (usuario.length > 3) {
            $.ajax({
                url: "{{route('ticket_soporte_tecnico.autocompletar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'data': usuario,
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    // console.log(data.length);
                    var resultado = document.getElementById("result");
                    var list = '';
                    if (data.length > 0) {
                        document.getElementById("result").style.visibility = "visible";
                        for (let i = 0; i < data.length; i++) {
                            list += '<li onclick="oprimir(this)" id=' + data[0]['id'] + '>' + data[0]['nombre1'] + ' ' + data[0]['nombre2'] + ' ' + data[0]['apellido1'] + ' ' + data[0]['apellido2'] + '</li>';
                        }
                        resultado.innerHTML = '<ul>' + list + '</ul>';
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    });

    function oprimir(valor) {
        document.getElementById("result").style.visibility = "hidden";
        document.getElementById("usuario").value = valor.id;
        document.getElementById("boton").style.margin = "-350px";
    }
    function goBack() {
        history.back()
    }
</script>

@endsection
