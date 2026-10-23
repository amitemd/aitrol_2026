@extends('paciente.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de pacientes</h3>
        </div>
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('pacientes.search_consulta')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="cedula" class="col-md-3 control-label">Cédula</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" >
            <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
              </div>  
            </div>
          </div>
        </div>

        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="nombres" class="col-md-3 control-label">Paciente</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
              </div>
            </div>  
          </div>
        </div>

        <div class="form-group col-md-1 col-xs-4" >
          <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
        </div>
         
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12" style="">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="10%">Cédula</th>
                <th width="30%">Nombres</th>
            
                <th width="5%" >Teléfonos de Contacto</th>
                <th width="20%">Persona de Contacto</th>
                <th width="5%" >Parentesco</th>
                <th width="10%">Teléfono Familiar</th>
                <th width="10%">Paciente desde</th>
                <th width="10%">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($paciente as $value)
                <tr >
                  <td>{{ $value->id}}</td>
                  <td>{{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif {{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif </td>
           
                  <td>{{ $value->telefono1.' / '.$value->telefono2 }}</td>
                  <td>{{$value->apellido1familiar}} @if($value->apellido2familiar!='(N/A)'){{$value->apellido2familiar}} @endif {{$value->nombre1familiar}} @if($value->nombre2familiar!='(N/A)'){{$value->nombre2familiar}}@endif</td>
                  <td>@if ($value->parentesco=='0') {{"Principal"}} @else{{ $value->parentesco}} @endif</td>
                  <td>{{ $value->telefono3}}</td>
                  <td>{{ substr($value->created_at,0,10) }}</td>
                  <td >  
                  <div class="row">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('paciente.historia', ['id' => $value->id]) }}" class="btn btn-warning btn-xs" target="_blank" style="width: 75px">
                    Historia Clínica
                    </a>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('paciente.historial_agenda', ['id_paciente' => $value->id]) }}" class="btn btn-success btn-xs" style="width: 75px; ">
                    Dctos.Agenda
                    </a>

                    </div>

                    
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($paciente)}} de {{$paciente->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $paciente->appends(Request::only(['id', 'apellidos', 'nombres','cedula']))->links()}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

  <script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>
@endsection