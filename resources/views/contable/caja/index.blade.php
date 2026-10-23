@extends('contable.caja.base')
@section('action-content')

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active">Punto de Emision</li>    
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <!--<h8 class="box-title">Punto Emisión</h8>-->
              <h5><b>PUNTO EMISION</b></h5>
            </div>
            <div class="col-md-1 text-right">
              <button type="button" onclick="location.href='{{route('puntoemision.crear')}}'" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>Agregar Punto Emision
              </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR PUNTO EMISION</label>
          </div>
        </div>
        <div class="box-body dobra">
          <form method="POST" id="buscad_punt_emis" action="{{ route('puntoemision.buscar') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-1 col-xs-2">
               <label class="texto" for="buscar_codigo">Código: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_codigo" name="buscar_codigo" value="@if(isset($searchingVals)){{$searchingVals['codigo_caja']}}@endif"  autocomplete="off" placeholder="Ingrese codigo..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="buscar_nombre">Nombre: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre_caja']}}@endif" autocomplete="off" placeholder="Ingrese el nombre..." />
            </div>
            <div class="col-xs-2">
              <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
              </button>
            </div>
          </form>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" >LISTADO PUNTO EMISION</label>
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
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Punto de Emisión</th>
                          <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                          <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Establecimiento</th>
                          <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                          <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach ($caja as $value)

                            @php
                            
                              $obtener_sucursal = Sis_medico\Ct_Sucursales::find($value->id_sucursal);
                              $obtener_nombre = Sis_medico\Empresa::find($value->id_empresa);
                             
                            @endphp
                            <tr class="well">
                              <td>@if(!is_null($value->codigo_caja)){{$value->codigo_caja}}@endif</td>
                              <td>@if(!is_null($value->nombre_caja)){{$value->nombre_caja}}@endif</td>
                              <td>@if(!is_null($obtener_sucursal->nombre_sucursal)){{$obtener_sucursal->nombre_sucursal}}@endif</td>
                              <td>@if($value->estado == 1)
                                    Activo
                                  @elseif($value->estado == 0)
                                    Inactivo
                                  @endif
                              </td>
                              <td align="center">
                                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                  @if($value->estado == '1')
                                    <a href="{{ route('puntoemision.editar', ['id' => $value->id,'id_emp' => $empresa->id]) }}" class="btn btn-success btn-gray">
                                      <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('deinfotributaria.index', ['id' => $value->id ]) }}" style="width: 100px;text-align:left;margin-left: 0" class="btn btn-primary">
                          <i class="fa fa-pencil"> Info Tributaria</i>
                                    </a>
                                  @endif
                              </td>
                            </tr>
                          @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($caja->currentPage() - 1) * $caja->perPage())}} / {{count($caja) + (($caja->currentPage() - 1) * $caja->perPage())}} de {{$caja->total()}} registros
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $caja->appends(Request::only(['codigo_caja','nombre_caja']))->links() }}
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
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script> 
@endsection