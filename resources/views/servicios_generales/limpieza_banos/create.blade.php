@extends('servicios_generales.limpieza_banos.base')
@section('action-content')
@php
$date = date('Y-m-d');
@endphp

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Servicios Generales</a></li>
            <li class="breadcrumb-item"><a href="../ambiente">Limpieza y Desinfección de Baños</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <form id="formulario" class="form-vertical" role="form" method="POST" action="{{route('limpieza_banos.subir_imagen')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-5">
                    <h5><b>Control de Limpieza y Desinfección de Baños Piso:</b></h5>
                </div>
                <div class="col-md-5">
                    <select disabled name="piso" id="piso" class="form-control" required >
                        <option value="">Seleccione</option>
                        @foreach($pisoBa as $value)
                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-danger">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>

            <div class="separator"></div>
            <div class="box-body">
                <div id="div_hc1" class="form-group col-lg-12 {{ $errors->has('archivo') ? ' has-error' : '' }}">

                    <label for="archivo" class="col-lg-6 control-label">Evidencia antes de la limpieza y desinfección del baño (img)</label>
                    <div class="col-lg-6" style="padding-left: 10px;padding-right: 5%;">
                        <input onchange="port()" id="file-input" name="evidencia_antes" type="file" / required>

                    </div>
                </div>
                <div id="div_hc2" class="form-group col-lg-12">

                    <label for="archivo" class="col-md-2 control-label">Tipo de Desinfección</label>
                    <div class="col-md-3" style="padding-left: 10px;padding-right: 5%;">
                        <select name="limpieza" id="limpieza" class="form-control" disabled required>
                            <option value="">Seleccione</option>
                            <option value="1">Concurrente</option>
                            <option value="2">Terminal</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-md-12">
                    <div class="col-md-2">
                        <label for="producto" class="col-md-2 control-label">Insumos utilizados</label>
                    </div>
                    <div class="col-md-3">
                        <select disabled name="producto[]" id="producto" class="form-control js-example-basic-multiple" multiple="multiple" required>
                            <option value="">Seleccione</option>
                            @foreach($productos as $val)
                            <option value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="insumos" class="col-md-2 control-label">Dotación</label>
                    <div class="col-md-3">
                        <select disabled name="insumos[]" id="insumos" class="form-control js-example-basic-multiple" multiple="multiple" required>
                            <option value="">Seleccione</option>
                            @foreach($insumos as $val)
                            <option value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-lg-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" id="titulo" class="col-lg-2 control-label">Observaciones</label>
                    <div class="col-lg-6" style="padding-left: 10px;padding-right: 5%;">
                        <input disabled id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        @if ($errors->has('observaciones'))
                        <span class="help-block">
                            <strong>{{ $errors->first('observaciones') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" id="guardar">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
    function guardar() {
        document.getElementById("guardar").disabled = true;
        $fecha = $("#fecha").val();
        $hora = $('#hora').val();
        $nombre_piso = $("#nombre_piso").val();
        $desinfectante = $("#desinfectante").val();
        $observaciones = $("#observaciones").val();
        if ($desinfectante == "" || $hora == "" || $fecha == "" || $observaciones == "" || $nombre_piso == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            $.ajax({
                url: "{{route('limpieza_banos.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('limpieza_banos.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

    }

    function cambio() {

        var id = $("#nombre_piso").val();
        console.log(id);
        $.ajax({
            url: "{{route('limpieza_control.marca')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': id,
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                document.getElementById("modelo").value = data;
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }

    const port = () => {
        document.getElementById("piso").disabled = false;
        document.getElementById("producto").disabled = false;
        document.getElementById("observaciones").disabled = false;
        document.getElementById("insumos").disabled = false;
        document.getElementById("limpieza").disabled = false;
    }
    function goBack() {
      history.back()
    }
</script>

@endsection