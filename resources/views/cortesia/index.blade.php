

@extends('cortesia.base')
@section('action-content')


<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-8">
      <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Pacientes con Cortesia</h3>
      </div>          
      <div class="box-body">
        <!--AQUI VA EL BUSCADOR-->
        <form method="POST" action="{{ route('cortesia.search') }}" >
          {{ csrf_field() }}
          @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Cédula'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '']])
          @endcomponent
          @endcomponent
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr>
                  <th>Cédula</th>
                  <th>Nombre Paciente</th>
                  <th>Cortesia </th>
                  <th>Ilimitada </th>
                  <th>Cambiar</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($cortesia_pacientes as $valor)
                <tr>
                  <td>{{ $valor->id }}</td>
                  <td>{{ $valor->nombre1 }} @if($valor->nombre2=='N/A') @else{{ $valor->nombre2 }} @endif{{ $valor->apellido1 }} @if($valor->apellido2=='N/A') @else{{ $valor->apellido2 }}@endif</td>
                  <td>{{ $valor->cortesia }}</td>
                  <td>{{ $valor->ilimitado }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('cortesia.editarcortesia', ['id' => $valor->id, 'i' => 1]) }}" class="btn btn-warning col-md-4 col-sm-10 col-xs-10 btn-margin">
                        Cortesia
                        </a>
                        <a href="{{ route('cortesia.editarcortesia', ['id' => $valor->id, 'i' => 2]) }}" class="btn btn-warning col-md-4 col-sm-10 col-xs-10 btn-margin">
                        Ilimitado
                        </a>
                  </td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($cortesia_pacientes)}} de {{$cortesia_pacientes->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $cortesia_pacientes->links() }}
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Agregar Pacientes con Cortesia</h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal" role="form" method="POST" action="{{ route('cortesia.store') }}">
            {{ csrf_field() }}
                   
            <!--cedula-->
            <div class="form-group col-md-12{{ $errors->has('id') ? ' has-error' : '' }}">
              <label for="id" class="col-md-2 control-label">Cédula </label>
              <div class="col-md-10">
                <input maxlength="10" id="id" type="text" class="form-control" name="id" value="@if(old('id')!=''){{ old('id') }}@elseif($id!=''){{$id}}@endif" onchange="teclaEnter2(event);" required autofocus placeholder="Ingrese la Cédula del Paciente">
                @if ($errors->has('id'))
                <span class="help-block">
                  <strong>{{ $errors->first('id') }}</strong>
                </span>
                @endif
              </div>    
            </div>
        
            <!--nombre-->
            @if(!is_null($paciente))
            <div class="form-group col-md-12">           
              <label for="nombre" class="col-md-2 control-label">Nombre </label>
              <div class="col-md-10">
                <input maxlength="10" id="nombre" type="text" class="form-control" name="nombre" value="{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}} " readonly>
              </div>       
            </div>
            @else
            <div class="form-group col-md-12" style="color:red;"> 
            @if($id!='' && !$errors->has('id'))
            {{"Paciente No Existe en el Sistema"}}
            @endif
            </div>
            @endif
                   
            <!--cortesia-->
            <div class="form-group col-md-6{{ $errors->has('cortesia') ? ' has-error' : '' }}">
              <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
              <div class="col-md-8">
                <select id="cortesia"  class="form-control" name="cortesia" required autofocus>
                  <option @if(!is_null($paciente)) @if($paciente->cortesia=='SI')   selected @endif @endif value="SI">SI</option>
                  <option @if(!is_null($paciente)) @if($paciente->cortesia=='NO')   selected @endif @endif value="NO">NO</option>
                </select>    
                @if ($errors->has('cortesia'))
                <span class="help-block">
                  <strong>{{ $errors->first('cortesia') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <!--ilimitado-->
            <div class="form-group col-md-6{{ $errors->has('ilimitado') ? ' has-error' : '' }}">
              <label for="ilimitado" class="col-md-4 control-label">Ilimitada</label>
              <div class="col-md-8">
                <select id="ilimitado"  class="form-control" name="ilimitado" required autofocus>
                  <option @if(!is_null($paciente)) @if($paciente->ilimitado=="NO") selected @endif @endif value="NO">NO</option>
                  <option @if(!is_null($paciente)) @if($paciente->ilimitado=="SI") selected @endif @endif value="SI">SI</option>
                  
                </select>    
                @if ($errors->has('ilimitado'))
                  <span class="help-block">
                    <strong>{{ $errors->first('ilimitado') }}</strong>
                  </span>
                @endif
              </div>
            </div>
                        
          <div class="form-group col-md-12">
            <div class="col-md-6 col-md-offset-4">
              <button type="submit" class="btn btn-primary" @if(is_null($paciente)) disabled @endif>
                Agregar
              </button>
            </div>
          </div>      
        </form>
      </div>
      </div>
    </div>  
    
  </div>
</div>  
 
    


  <script type="text/javascript">

    function teclaEnter2(e)
    {
    vcedula = document.getElementById("id").value;
        
        vcedula =  vcedula.trim();
        if (vcedula != ""){
             
             
             location.href = "{{ route('cortesia.crear3') }}/"+vcedula;

        }

    }

  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

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