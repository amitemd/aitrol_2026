@extends('archivo_plano.plantillas.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title">Lista Plantillas</h3>
        </div>
        <div class="col-sm-6">
          <a class="btn btn-primary" href="{{route('plantillas.crear')}}">Agregar Nueva Plantilla</a>
        </div>
    </div>
  </div> 
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <!--AQUI VA EL BUSCADOR-->


<form method="POST" action="{{route('plantillas.buscar')}}">
  {{ csrf_field() }}
  <div class="col-md-6">
    <div class="col-md-3">
      <label class="control-label">Nombre:</label>
    </div>
    <div class="col-md-4">
            <input type="text" name="descripcion" value="{{$plantilla}}" class="form-control"  />
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
  </div>
    
</form>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Código</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripción</th>
                  <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Procedimiento</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Estado</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Acción</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($plantillas as $plantilla)
                <tr role="row" class="odd">
                  <td> {{ $plantilla->codigo }}</td>
                  <td> {{ $plantilla->descripcion }}</td>
                  <td>
                    @if(!is_null($plantilla->procedimiento))
                    {{$plantilla->procedimiento->nombre}}
                    @endif
                  </td>
                  <td> @if($plantilla->estado==1) Activo @else Inactivo @endif</td>
                  <td>
                    <!--input type="hidden" name="_token" value="{{ csrf_token() }}"-->
                    <a href="{{ route('plantillas.edit', ['id' => $plantilla->id])}}" class="btn btn-warning ">
                      Editar
                    </a>
                    <!-- <a href="{{ route('plantillas.items', ['id' => $plantilla->id])}}" class="btn btn-success ">
                        Ver
                        </a> -->
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($plantillas)}} de {{count($plantillas)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $plantillas->links() }}
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

@endsection