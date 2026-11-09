@extends('hc_admision.fullcontrol.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
  /* unvisited link */
  a:link {
    color: black;
  }

  /* visited link */
  a:visited {
    color: lightgreen;
  }

  /* mouse over link */
  a:hover {
    color: blue;
  }

  * {
    font-family: var(--fuentePrincipal);
  }
</style>
@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_fellow = Auth::user()->id;
@endphp
<div class="modal fade" id="Subir" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content" id="imprimir3">
        </div>
    </div>
</div>

  <div class="row">
    <div class="col-md-12 col-xs-12">
      <div class="box">
        <div class="box-header"></div>
        <div class="box-body">
          <form method="POST" action="{{ route('receta.nuevaFarmaciaIndex') }}">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                <label for="fecha" class="col-md-3 control-label">{{trans('econsultam.Desde')}}</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" value="{{$request->fecha}}">
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                <label for="fecha_hasta" class="col-md-3 control-label">{{trans('econsultam.Hasta')}}</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$request->fecha_hasta}}">
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group  col-xs-6 col-md-3{{ $errors->has('cedula') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="cedula" class="col-md-3 control-label">Cedula</label>
                  <div class="col-md-9">
                      <input value="@if($request->cedula!=''){{$request->cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="cedula del paciente" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                      @if ($errors->has('cedula'))
                      <span class="help-block">
                          <strong>{{ $errors->first('cedula') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>

              <div class="form-group  col-xs-6 col-md-3{{ $errors->has('paciente') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="paciente" class="col-md-3 control-label">{{trans('econsultam.Paciente')}}</label>
                  <div class="col-md-9">
                      <input value="@if($request->paciente!=''){{$request->paciente}}@endif" type="text" class="form-control input-sm" name="paciente" id="paciente" placeholder="{{trans('laboratorio.nombres_apellidos')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                      @if ($errors->has('paciente'))
                      <span class="help-block">
                          <strong>{{ $errors->first('paciente') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>
            </div>
            
            <div class="row">
              <div class="form-group col-xs-6 col-md-3{{ $errors->has('seguro') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="seguro" class="col-md-3 control-label">{{trans('econsultam.Seguro')}}</label>
                  <div class="col-md-9">
                      <select id="seguro" name="seguro" class="form-control select2" >
                      <option value="">...Seleccione...</option>
                        @foreach($seguros as $seguro)
                            <option @if($request->seguro==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('seguro'))
                      <span class="help-block">
                          <strong>{{ $errors->first('seguro') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>

              <div class="form-group col-xs-6 col-md-3{{ $errors->has('tipo') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="tipo" class="col-md-3 control-label">Tipo</label>
                  <div class="col-md-9">
                      <select id="tipo" name="tipo" class="form-control" >
                          <option value="">--Seleccione--</option>
                          <option @if($request->tipo=='0') selected @endif value="0">Consulta</option>
                          <option @if($request->tipo=='1') selected @endif value="1">Procedimiento</option>
                      </select>  
                      @if ($errors->has('tipo'))
                      <span class="help-block">
                          <strong>{{ $errors->first('tipo') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>

              <div class="form-group col-xs-6 col-md-3{{ $errors->has('unidad') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="unidad" class="col-md-3 control-label">{{trans('econsultam.Unidad')}}</label>
                  <div class="col-md-9">
                      <select id="unidad" name="unidad" class="form-control select2" >
                      <option value="">...Seleccione...</option>
                        @foreach($unidades as $unidad)
                            <option @if($request->unidad==$unidad->id) selected @endif value="{{$unidad->id}}">{{$unidad->nombre_hospital}}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('unidad'))
                      <span class="help-block">
                          <strong>{{ $errors->first('unidad') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>

              <div class="form-group  col-xs-6 col-md-3{{ $errors->has('doctor') ? ' has-error' : '' }}"  style="padding-left: 0px;padding-right: 0px;">
                  <label for="doctor" class="col-md-3 control-label">{{trans('econsultam.Doctor')}}</label>
                  <div class="col-md-9">
                      <input value="@if($request->doctor!=''){{$request->doctor}}@endif" type="text" class="form-control input-sm" name="doctor" id="doctor" placeholder="{{trans('laboratorio.nombres_apellidos')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                      @if ($errors->has('doctor'))
                      <span class="help-block">
                          <strong>{{ $errors->first('doctor') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>
            </div>
            

            <div class="form-group col-md-1 col-xs-4">
              <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </div>
            <div class="form-group col-md-1 col-xs-4">
              <button type="submit" formaction="{{route('receta.excel_nota_entrega_nuevo')}}" class="btn btn-success btn-sm" id="boton_excel">
                <span class="fa fa-file-excel-o" aria-hidden="true"> Excel</span></button>
            </div>
          </form>

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr style="background:#52B1ED;" >
                    <th>{{trans('econsultam.Fecha')}} </th>
                    <th>Cedula</th>
                    <th>{{trans('econsultam.Paciente')}}</th>
                    <th>{{trans('econsultam.Seguro')}} / {{trans('agenda.empresa')}}</th>
                    <th>Consultorio / {{trans('econsultam.Unidad')}}</th>
                    <th>{{trans('econsultam.Tipo')}}</th>
                    <th>{{trans('econsultam.Doctor')}}</th>
                    <th>{{trans('econsultam.Procedimiento')}}</th>
                    <th># {{trans('econsultam.nota_entrega')}}</th>
                    <th>{{trans('econsultam.Estado')}}</th>
                    <th>{{trans('econsultam.accion')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($consulta as $value)
                      @php 
                        $receta = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->first();
                        $notaEntrega = null;
                        $doc_scan = null;
                        if($value->tipo == 'CONSULTA'){
                          $notaEntrega = \Sis_medico\Nota_entrega::where('id_hc', $value->hcid)->where('id_hc_receta',$receta->id)->first();
                        }
                        
                        if(!is_null($notaEntrega)){
                          $doc_scan = \Sis_medico\Nota_Entrega_Escaneados::where('id_nota_entrega', $notaEntrega->id)->where('estado', '1')->first();
                        }
                      @endphp
                      <tr>
                        <td>{{$value->fechaini}}</td>
                        <td>{{$value->cedula}}</td>
                        <td>{{$value->paciente}}</td>
                        <td>{{$value->seguro}} / {{$value->empresa}}</td>
                        <td>{{$value->sala}} / {{$value->hospital}}</td>
                        <td>{{$value->tipo}}</td>
                        <td>{{$value->doctor}}</td>
                        <td></td>
                        <td>@if(!is_null($notaEntrega)) {{$notaEntrega->numero}} @endif</td>
                        <td>
                          @if(!is_null($notaEntrega))
                            @if($notaEntrega->estado == 1)
                              @if(!is_null($doc_scan))
                                Archivo Subido
                              @else
                                Emitida
                              @endif
                            @elseif($notaEntrega->estado == 0) 
                              Anulada
                            @else
                              Pendiente
                            @endif
                          @else
                            @if($value->tipo == 'CONSULTA')
                              Pendiente
                            @endif 
                          @endif
                        </td>
                        <td>
                          @if($value->tipo == 'CONSULTA')
                            <a href="{{route('receta.listado_farmacia', ['id_hc' => $value->hcid])}}" style="color: white;" class="btn btn-xs @if(!is_null($notaEntrega)) @if($notaEntrega->estado === 1) btn-primary @else btn-warning @endif @else btn-warning @endif">Nota Entrega</a>
                            <a target="_blank" href="{{route('hc_receta.imprime', ['id' => $receta->id, 'tipo' => '2'])}}" class="btn btn-success btn-xs" style="color: white;" >Receta</a>
                            @if(!is_null($doc_scan) && $notaEntrega->estado == 1) 
                            <a target="_blank" href="{{route('receta.visualizar_nota_entrega_pdf_nuevo',['nombre'=>$doc_scan->archivo])}}" class="btn btn-danger btn-xs" style="color: white;">Ver</a>
                            @endif
                          @endif
                        </td>
                      </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-5 col-xs-12">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
              Mostrando {{1+($consulta->currentPage()-1)*$consulta->perPage()}} / @if(($consulta->currentPage()*$consulta->perPage())<$consulta->total()){{($consulta->currentPage()*$consulta->perPage())}} @else {{$consulta->total()}} @endif de {{$consulta->total()}} registros
            </div>
            </table>
          </div>

          <div class="col-md-7 col-xs-12">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $consulta->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
            </div>
          </div>

        </div>



      </div>
    </div>
  </div>





<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',


      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha").on("dp.change", function(e) {
      buscar();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      buscar();
    });



  $('#editMaxPacientes').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
    'order': [
      [1, "asc"]
    ]
  });


  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>
@endsection