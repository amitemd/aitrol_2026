@extends('contable.cierre_caja.base')
@section('action-content')
@php
$fecha = date('Y-m-d');
@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12">
                <div class="col-md-10">
                    <h4 style="text-align: left;">REGISTROS DE DOCUMENTOS SUBIDOS</h4>
                </div>
                <div class="col-md-2">
                    <button type="button"class="btn btn-danger btn-xs" onclick="history.back()">Regresar</button>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <form method="POST" action="{{route('cambiar_buscar_documento')}}">
                            {{ csrf_field() }}
                            <div class="form-group col-md-3">
                                <label for="fecha" class="col-md-3 control-label">Fecha</label>
                                <div class="col-md-9">
                                    <input style="text-align: center;line-height:10px;" type="date" name="fecha" id="fecha" class="form-control" value="{{$fecha}}">
                                </div>
                            </div>
                            <div class="form-group  col-md-3">
                                <label for="tipo" class="col-md-2 control-label">Tipo</label>
                                <div class="col-md-9">
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach($maestroDocumento as $val)
                                        <option value="{{$val->id}}">{{$val->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-1 col-xs-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario que Cargo</th>
                                    <th>Tipo documento</th>
                                    <th>fecha de carga</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>

                            @foreach($each as $value )


                            <tr>
                                <td>
                                    {{$value->id}}
                                </td>
                                <td>
                                    @if(isset($value->usuariocrea)) {{$value->usuariocrea->nombre1}} {{$value->usuariocrea->nombre2}} {{$value->usuariocrea->apellido1}} {{$value->usuariocrea->apellido2}} @endif
                                </td>
                                <td>
                                    @if(isset($value->documentos)) {{$value->documentos->nombre}} @endif
                                </td>
                                <td>
                                    {{$value->created_at}}
                                </td>
                                <td>
                                    <a onclick="eliminar({{$value->id}})" class="btn btn-danger btn-xs">Eliminar</a>
                                    <a target="_blank" href="{{route('vizualizar_pdf_docs_subidos',['id'=>$value->id])}}" class="btn btn-primary btn-xs">Vizualizar</a>
                                </td>

                            </tr>


                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} 1 / {{count($each)}} de {{$each->total()}} {{trans('contableM.registros')}}</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $each->appends(Request::all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    const eliminar = (id) => {
        let opcion  = prompt("Porfavor Ingrese una justificación");
        if (opcion) {
          $.ajax({
              type: 'get',
              url: "{{route('cambiar_estado_documentos')}}",
              datatype: 'json',
              data: {
                  'id': id,
                  'opcion':opcion
              },
              success: function(data) {
                  console.log(data);
                  swal("Eliminado!", "Eliminado Correctamente", "success");
                  location.reload();
              },
              error: function(data) {
                  console.log(data);
              }
          });
        }else{
              swal("Error!", "Justificación vacia", "error");
        }

    }
</script>
@endsection
