@extends('contable.porcentaje_imp_renta.base')
@section('action-content')

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active">Porcentaje IR</li>    
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>PORCENTAJE IR</b></h5>
            </div>
            <div class="col-md-1 text-right">
              <button type="button" onclick="location.href='{{route('porcentaje_imp_renta.crear')}}'" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>Agregar Procentaje IR
              </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR PORCENTAJE DE IR</label>
          </div>
        </div>
        <div class="box-body dobra">
          <form method="POST" id="buscar_porcentaje_impuesto_renta" action="{{ route('porcentaje_imp_renta.buscar') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-1 col-xs-2">
               <label class="texto" for="buscar_anio">Año: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_anio" name="buscar_anio" value="@if(isset($searchingVals)){{$searchingVals['anio']}}@endif" autocomplete="off" placeholder="Ingrese año..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="buscar_porcentaje">Porcentaje IR:</label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_porcentaje" name="buscar_porcentaje" value="@if(isset($searchingVals)){{$searchingVals['porcentaje']}}@endif" autocomplete="off" placeholder="Ingrese el Porcentaje IR..." />
            </div>
            <div class="col-xs-2">
              <button type="submit" id="buscarCodigoTipo" class="btn btn-primary btn-gray">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
              </button>
            </div>
          </form>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" >LISTADO DE PORCENTAJE IR</label>
          </div>
        </div>
        <div class="box-body dobra">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr class="well-dark" >
                          <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">% IR</th>
                          <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año</th>
                          <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach ($porcentaje_ir as $value)
                            <tr class="well">
                              <td>@if(!is_null($value->porcentaje)){{$value->porcentaje}}@endif</td>
                              <td>@if(!is_null($value->anio)){{$value->anio}}@endif</td>
                              <td>
                                @if($value->estado == 1)
                                  Activo
                                @elseif($value->estado == 0)
                                  Inactivo
                                @endif
                              </td>
                              <td style="text-align: center;">
                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                  <a href="{{ route('porcentaje_imp_renta.editar', ['id' => $value->id])}}" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($porcentaje_ir->currentPage() - 1) * $porcentaje_ir->perPage())}} / {{count($porcentaje_ir) + (($porcentaje_ir->currentPage() - 1) * $porcentaje_ir->perPage())}} de {{$porcentaje_ir->total()}} registros
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{$porcentaje_ir->appends(Request::only(['anio','porcentaje']))->links() }}
                    </div>
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
    })
  </script> 
@endsection