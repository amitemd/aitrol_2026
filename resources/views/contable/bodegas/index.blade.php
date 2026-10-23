@extends('contable.bodegas.base')
@section('action-content')
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item active">Bodegas</li>    
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
        <div class="col-md-9">
          <h5><b>BODEGAS</b></h5>
        </div>
        <div class="col-md-1 text-right">
          <button type="button" onclick="location.href='{{route('bodegas.crear')}}'" class="btn btn-success btn-gray">
                <i aria-hidden="true"></i>Agregar Nueva Bodegas
          </button>
        </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR DE BODEGAS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscad_ bodegas" action="{{route('bodegas.buscar')}}">
        {{csrf_field()}}
        <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="buscar_codigo">Nombre: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" autocomplete="off" placeholder="Ingrese Nombre.." />
        </div>
        <div class="form-group col-md-1 col-xs-1">
               <label class="texto" for="id_cliente">Hospital:</label>
            </div>
            <div class="form-group col-md-3 col-xs-3">
              <select class="form-control select2" style="width: 100%;" name="id_hospital" id="id_cliente">
              <option value="">Seleccione...</option>
              @foreach($hospital as $value)
                <option  @if(isset($searchingVals)) {{ $value->id == $searchingVals['id_hospital'] ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombre_hospital}}</option>
              @endforeach
              </select>
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id_cliente">Departamento:</label>
        </div>
        <div class="form-group col-md-3 col-xs-3">
          <select class="form-control select2" style="width: 100%;" name="id_departamento" id="id_departamento">
              <option value="">Seleccione...</option>
              <option value="1">COMPRA</option>
              <option value="2">CONTABILIDAD</option>
          </select>
        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscar_bodega" class="btn btn-primary btn-gray">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" >LISTADO DE BODEGAS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-row">
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class="well-dark" >
                      <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hospital</th>
                      <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Departamento</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                      <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                      <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($bodegas as $value)
                      <tr class="well">
                        <td>@if($value->id_hospital == 1)
                            CITIMED	
                            @elseif($value->id_hospital == 2)
                            CITIMED CIRUGIA
                            @elseif($value->id_hospital == 3)
                              OTROS
                            @endif
                        </td>
                        <td>@if($value->departamento == 1)
                              COMPRA
                            @elseif($value->departamento == 2)
                              CONTABILIDAD
                            @endif
                        </td>
                        <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                        <td>@if($value->estado == 1)
                              Activo
                            @elseif($value->estado == 0)
                              Inactivo
                            @endif
                        </td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          @if($value->estado == '1')
                            <a href="{{ route('bodegas.editar', ['id' => $value->id,'id_emp' => $empresa->id]) }}" class="btn btn-success btn-gray">
                              <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                            </a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>
  </div>
</section>
  <script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    });
  </script> 

@endsection