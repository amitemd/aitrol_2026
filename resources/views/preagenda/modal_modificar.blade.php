@extends('agenda.base')
@section('action-content')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Editar Sala: </h4>
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form id="formulario">
                            <input type="hidden" value="{{$id}}" id="id" name="id">
                            <div class=" form-group col-md-6 ">
                                <label for=" est_amb_hos" class="col-md-12 control-label">Nombre</label>
                                <div class="col-md-12">
                                    <input type="text" disabled class="form-control" value="{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}">
                                </div>
                            </div>
                            <div class="form-group col-md-6 ">
                                <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                                <div class="col-md-12">
                                    <input type="text" disabled class="form-control" value="{{$nombresala->nombre_sala}}">
                                </div>
                            </div>
                            <div class="form-group col-md-6 ">
                                <label class="col-md-12 control-label">Inicio</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{$sala->fechaini}}" name="inicio" class="form-control pull-right" id="inicio" disabled>
                                    </div>

                                    <span class="help-block">
                                        <strong></strong>
                                    </span>

                                    <span class="help-block">
                                        <strong></strong>
                                    </span>

                                </div>
                            </div>
                            <!--fin-->
                            <div class="form-group col-md-6">
                                <label class="col-md-12 control-label">Fin</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{$sala->fechafin}}" name="fin" class="form-control pull-right" id="fin" disabled>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="nombre_reunion" class="control-label">Nombre Reunion</label>
                                    <div class="col-6">
                                        <input type="text" id="nombre_reunion" value="{{$sala->procedencia}}" class="form-control" name="nombre_reunion" />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="observaciones" style="margin-left:5px;" class="col-md-12 control-label">Estado</label>
                                    <div class="col-6">
                                        <select style="margin-left: 14px;" name="estado" id="estado" class="form-control">
                                            <option value="1">@if(($sala->estado)==1)Ocupada @endif</option>
                                            <option value="0">Suspender</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="observaciones" style="margin-left: 14px;" class="control-label">Observaciones</label>
                                <div class="col-md-12">
                                    <textarea maxlength="200" id="observaciones" class="form-control" name="observaciones"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center;">
                                    <button type="button" onclick="guardar()" class="btn btn-primary">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script type="text/javascript">
        function guardar() {
            $.ajax({
                url: "{{route('guardar_modificaciones_sala')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    //console.log("asdas");
                    swal("Correcto!", "Actualizado", "success");
                    var url = '{{url("agenda_procedimiento/pentax_procedimiento")}}';
                    window.location = url;
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    </script>
    @endsection