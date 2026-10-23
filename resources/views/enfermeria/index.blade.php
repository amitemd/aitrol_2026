@extends('enfermeria.base')
@section('action-content')
<!-- Main content -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">

  <div class="box">
    <div class="box-header">
      <div class="col-md-12">
        <form id="formulario_enfermeria" action="{{route('enfermeria.index')}}" method="POST" >
          {{ csrf_field() }}
          <div class="row">
            <div class="form-group col-md-5 col-xs-6" >
              <label for="nombres" class="col-md-3 control-label">Paciente</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input @if(isset($request['nombres'])) value="{{$request['nombres']}}" @endif type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-5 col-xs-6" >
              <label for="cedula" class="col-md-3 control-label">Cedula</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input @if(isset($request['cedula'])) value="{{$request['cedula']}}" @endif type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Numero de Cedula" onchange="fecha_buscador()">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-5">
              <label class="col-md-3 control-label">Fecha Inicio</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type=text value="" name="fecha" class="form-control" id="fecha"  required>
                </div>
              </div>
            </div>
            <div class="form-group col-md-5" >
              <label class="col-md-3 control-label">Fecha Fin </label>
              <div class="col-md-9">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" value="" name="fechafin" class="form-control" id="fechafin" required>
                </div>
              </div>
            </div>
            <div class="form-group col-md-2" >
              <button type="submit" class="col-md-12 btn btn-primary">Buscar</button>
            </div>
            <!--<div class="form-group col-md-4" >
              <label class="col-md-4 control-label">Paciente </label>
              <div class="col-md-8">
                <div class="input-group ">
                    <input type="text" value="" name="paciente" class="form-control" id="paciente" required>
                </div>
              </div>
            </div>-->
          </div>
        </form>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

      @if($procedimientos->count() > 0)
      <div class="box box-primary " >
        <div class="box-header">
          <div class="row">
            <div class="col-md-12">
                <h3 class="box-title"><a href="javascript:void($('#consult').click());"><b>Procedimientos</b></a></h3>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="consult">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr role="row">
                      <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Cédula</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Apellidos</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombres</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Fecha de Nacimiento</th>
                      <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Seguro Convenio</th>
                      <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Doctor</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Hora </th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Tipo</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Preparacion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($procedimientos as $value)
                      <tr>
                        <td>{{$value->paciente->id_usuario}}</td>
                         <td> {{ $value->paciente->apellido1}} {{ $value->paciente->apellido2}}</td>
                        <td>{{ $value->paciente->nombre1}}
                        {{ $value->paciente->nombre2}} </td>
                        <td>{{$value->paciente->fecha_nacimiento}}</td>
                        <td>{{$value->seguro->nombre}}</td>
                        <td>Dr. {{$value->doctor1->nombre1}} {{$value->doctor1->apellido1}}</td>
                        <td>{{$value->fechaini}}</td>
                        <td>@if($value->proc_consul==0) consulta @else procedimiento @endif</td>
                        <td> <a type="button" class="btn btn-success btn-sm col-md-10" href="{{route('enfermeria.procedimiento',['id'=>$value->id])}}" name="preparacion">Signos Vitales</a>
                          <a type="button" class="btn btn-warning btn-sm col-md-10" href="{{route('enfermeria.insumos',['id'=>$value->id])}}#recibir_equipo" name="preparacion">Insumos Usados</a></td>
                      </tr>
                    @endforeach
                </tbody>
               </table>
              </div>
             </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($procedimientos)}} de {{$procedimientos->total()}} registros</div>
              </div>

              <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                   {{ $procedimientos->appends(['fecha' => $fecha, 'fechafin' => $fechafin, 'cedula' => $request['cedula'], 'nombres' => $request['nombres']])->links()}}
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      @if($consulta->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-12">
                <h3 class="box-title"><a href="javascript:void($('#consult').click());"><b>Consultas</b></a></h3>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="consult">
                  <i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr role="row">
                      <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Cédula</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Apellidos</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombres</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Fecha de Nacimiento</th>
                      <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Seguro Convenio</th>
                      <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Doctor</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Hora </th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Tipo</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Preparacion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($consulta as $value)
                      <tr>
                        <td>{{$value->paciente->id_usuario}}</td>
                         <td> {{ $value->paciente->apellido1}} {{ $value->paciente->apellido2}}</td>
                        <td>{{ $value->paciente->nombre1}}
                        {{ $value->paciente->nombre2}} </td>
                        <td>{{$value->paciente->fecha_nacimiento}}</td>
                        <td>{{$value->seguro->nombre}}</td>
                        <td>Dr. {{$value->doctor1->nombre1}} {{$value->doctor1->apellido1}}</td>
                        <td>{{$value->fechaini}}</td>
                        <td>@if($value->proc_consul==0) consulta @else procedimiento @endif</td>
                        <td> <a type="button" class="btn btn-success btn-sm col-md-10" href="{{route('enfermeria.procedimiento',['id'=>$value->id])}}#recibir" name="preparacion">Signos Vitales</a></td>
                      </tr>
                    @endforeach
                </tbody>
               </table>
              </div>
             </div>
           <div class="row">
                        <div class="col-sm-5">
                          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($consulta)}} de {{$consulta->total()}} registros</div>
                        </div>
                    <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                         {{ $consulta->appends(['fecha' => $fecha, 'fechafin' => $fechafin, 'cedula' => $request['cedula'], 'nombres' => $request['nombres']])->links()}}
                        </div>
                    </div>
                    </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
  <!-- /.box-body -->
</section>
    <!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'

        });
        $("#fecha").on("dp.change", function (e) {
          $('#fechafin').data("DateTimePicker").minDate(e.date);
          fecha_buscador();
          //alert('entra:inicio');
        });

        $('#fechafin').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fechafin}}'
        });
        $("#fechafin").on("dp.change", function (e) {
          //alert('entra:fin');
          fecha_buscador();
        });
    });
</script>
<script type="text/javascript" >
    function fecha_buscador(){
      //alert('entra');
      $('#formulario_enfermeria').submit();
    }
</script>

@endsection
