@extends('ticket_soporte_tecnico.base')
@section('action-content')
@php
$date = date('Y-m-d');
$id_auth = Auth::user()->id;
$nombre = Sis_medico\User::where('id',$id_auth)->first();
@endphp

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Soporte tecnico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestión</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>Ticket de Soporte Tecnico</b></h5>
            </div>

            <div class="col-md-3 text-right">
                <button id="regresar" class="btn btn-danger">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="separator"></div>
        <div class="box-body">
            <form id="formulario">
                {{ csrf_field() }}
                <input type="hidden" value="{{$requerimiento->id}}" name="idrequerimiento">
                <div class="form-group col-md-6 col-xs-6">
                    <label for="area" class="col-md-3 control-label">Area</label>
                    <div class="col-md-9">
                        <input id="area" type="text" class="form-control" name="area" value="{{ $requerimiento->area }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                        @if ($errors->has('area'))
                        <span class="help-block">
                            <strong>{{ $errors->first('area') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6">
                    <label for="requerimientos" class="col-md-3 control-label">Requerimientos</label>
                    <div class="col-md-9">
                        <input id="requerimientos" type="text" class="form-control" name="requerimientos" value="{{ $requerimiento->requerimientos }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                        @if ($errors->has('requerimientos'))
                        <span class="help-block">
                            <strong>{{ $errors->first('requerimientos') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6">
                    <label for="requerimientos" class="col-md-3 control-label">Usuario</label>
                    <div class="col-md-9">
                        <input type="hidden" name="id_usuario" value="{{$requerimiento->usuario_solicitante}}">
                        <input id="usuario" type="text" readonly class="form-control" name="usuario" @if(!is_null($requerimiento->usuario_solicitante)) value="{{$requerimiento->nombre->nombre1}} {{$requerimiento->nombre->nombre2}} {{$requerimiento->nombre->apellido1}} {{$requerimiento->nombre->apellido2}}" @else value="{{$requerimiento->nombre1->nombre1}} {{$requerimiento->nombre1->nombre2}} {{$requerimiento->nombre1->apellido1}} {{$requerimiento->nombre1->apellido2}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                        @if ($errors->has('usuario'))
                        <span class="help-block">
                            <strong>{{ $errors->first('usuario') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6">
                    <label for="estado" class="col-md-3 control-label">Estado</label>
                    <div class="col-md-9">
                        <select class="form-control" id="cambio1" id="estado" name="estado" value="{{ $requerimiento->estado }}">
                            <option value="">Seleccione</option>
                            <option {{$requerimiento->estado == 0 ? 'selected' : ''  }} value="0">Inicial</option>
                            <option {{$requerimiento->estado == 1 ? 'selected' : ''  }} value="1">Proceso</option>
                            <option {{$requerimiento->estado == 2 ? 'selected' : ''  }} value="2">Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6" id="obs">
                    <label for="observacion" class="col-md-3 control-label">Observacion</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="campis" name="observacion" value="{{ $requerimiento->observacion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        @if ($errors->has('observacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('observacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" id="obs">
                    <label for="responsable" class="col-md-3 control-label">Responsable</label>
                    <div class="col-md-9">
                        <input type="hidden" name="responsable" id="responsable" value="{{$id_auth}}">
                        <input type="text" class="form-control" name="respon" value="{{$nombre->nombre1}} {{$nombre->apellido1}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly required>
                        @if ($errors->has('observacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('observacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <button id="enviar" type="button" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </div>
        </div>
        </form>
</section>

<script type="text/javascript">
    document.getElementById("cambio1").addEventListener('change', function() {
        let estado = document.getElementById("cambio1").value;
        // alert('ijjdfdjoe')
        if (estado == 1) {
            document.getElementById("obs").style.visibility = "hidden";
        } else if (estado == 2) {
            document.getElementById("obs").style.visibility = "visible";
        }
    });

    document.getElementById("enviar").addEventListener('click', function() {
        let estado = document.getElementById("cambio1").value;
        document.getElementById("enviar").disabled = true;
        $.ajax({
            url: "{{route('ticket_soporte_tecnico.admin_control')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data == 'ok') {
                    var url = "{{route('ticket_soporte_tecnico.index')}}"
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Ok',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    window.location = url;
                } else {

                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: 'Error',
                        showConfirmButton: false,
                        timer: 1500
                    })

                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    });
    document.getElementById("regresar").addEventListener('click', function() {

        window.history.back();

    });
</script>
@endsection