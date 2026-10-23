@extends('servicios_generales.limpieza_horarios.base')
@section('action-content')
<section class="">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Servicios Generales</a></li>
                <li class="breadcrumb-item"><a href="../ambiente">Registro de Limpieza y Desinfección de Salas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Registro</li>
            </ol>
        </nav>
        <form id="formulario" class="form-vertical" role="form">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$id}}">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-9">
                        <h5><b>Registro de Limpieza y Desinfección de Salas</b></h5>
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" onclick="editar();" class="btn btn-primary">
                            <i class="glyphicon glyphicon-registration-mark" aria-hidden="true"></i>&nbsp;&nbsp;Editar
                        </button>
                    </div>
                    <div class="col-md-1 text-right">
                        <button onclick="goBack()" class="btn btn-danger">
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                        </button>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="box-body">
                    <div class="col-md-12">
                        <h5 style="color: red;"><b>ANTES DE USAR EL EQUIPO</b></h5>
                    </div>
                    <div class="form-group  col-xs-4">
                        <label for="fecha" class="col-md-6 texto">Fecha</label>
                        <div class="col-md-6">
                            <input id="fecha" name="fecha" value="{{$horario->fecha}}" type="date" class="form-control" placeholder="fecha" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="area" class="col-md-6 texto">Area</label>
                        <div class="col-md-6">
                            <select id="area" name="area" class="form-control area" required autofocus>
                                <option value="">Seleccione</option>
                                @foreach($sala as $val)
                                <option {{$horario->id_sala == $val->id ? 'selected' : ''  }} value="{{$val->id}}">{{$val->nombre_sala}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group  col-xs-4">
                        <label for="observaciones" class="col-md-6 texto">Observaciones</label>
                        <div class="col-md-6">
                            <input type="text" value="{{$horario->observaciones}}" class="form-control" id="observaciones" name="observaciones" placeholder="observaciones" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="encargado" class="col-md-6 texto">Encargado</label>
                        <div class="col-md-6">
                            <input type="text" value="{{$horario->id_encargado}}" onmouseover="ok();" class="form-control" id="encargado" name="encargado">
                        </div>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="desindectante" class="col-md-6 texto">Desinfectante</label>
                        <div class="col-md-6">
                            <input id="desindectante" value="{{$horario->desinfectante}}" name="desindectante" type="text" class="form-control" placeholder="desindectante" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="frecuencia" class="col-md-6 texto">Frecuencia</label>
                        <i onclick="agregar()" class="fa fa-plus" aria-hidden="true"></i>
                        <div class="col-md-6">
                            @foreach($var as $val)
                            <input  style="margin-top: 2%;" value="{{$val->frecuencia}}" name="frecuencia[]" type="number" class="form-control" placeholder="frecuencia" required autofocus>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                        </div>
                        <div id="campo" class="col-md-6">

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script>
    var cont = 0;

    function agregar() {

        cont++;

        $("#campo").append('<input name="frecuencia[]" class="form-control" style="margin-top:7%;"  id="frecuencia' + cont + '" type ="number"/>')

    }

    function editar() {

        var fecha = $("#fecha").val();
        var area = $("#area").val();
        var frecuencia = $("#frecuencia").val();
        var observaciones = $("#observaciones").val();
        var encargado = $("#encargado").val();
        var desindectante = $("#desindectante").val();

        var msj = "";
        if (fecha == "") {
            msj += "La fecha esta vacia <br/>";
        }
        if (area == "") {
            msj += "La area esta vacia <br/>";
        }
        if (frecuencia == "") {
            msj += "La frecuencia esta vacia <br/>";
        }
        if (observaciones == "") {
            msj += "Las observaciones estan vacias <br/>";
        }
        if (encargado == "") {
            msj += "El encargado esta vacio  <br/>";
        }
        if (desindectante == "") {
            msj += "El desindectante esta vacio <br/>";
        }

        if (msj != "") {

            swal({
                title: "Error!",
                html: msj,
                type: "error",
            });

        } else {
            $.ajax({
                url: "{{route('mantenimientohorario.update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('mantenimientohorario.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 2000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }


    }
</script>
@endsection