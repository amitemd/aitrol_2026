@extends('contable.tipo_acreedor.base')
@section('action-content')

<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-6">
            <h3 class="box-title">Lista de Tipos de Proveedores</h3>
          </div>
          <div class="col-sm-4">
            <a class="btn btn-primary btn-gray" href="{{ route('tipoacreedor.create') }}">Agregar Nuevo Tipo</a>
          </div>
          <div class="col-md-2" style="text-align: right;">
            <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm btn-gray">
              <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
          </div>
      </div>
    </div>
    <div class="box-body dobra">
        <div class="row">
          <div class="col-sm-6"></div>
          <div class="col-sm-6"></div>
        </div>
        <form method="POST" action="{{ route('tipoacreedor.search') }}">
           {{ csrf_field() }}
           @component('layouts.search', ['title' => 'Buscar'])
            @component('layouts.two-cols-search-row', ['items' => ['Nombre'], 
            'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['Nombre'] : '']])
            @endcomponent
            </br>
          @endcomponent
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                    <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Nombre</th>
                    <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripcion</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>

                  </tr>
                </thead>
                <tbody>
                @foreach ($tipos as $empresa)
                  <tr role="row" class="odd">
                    <td> {{ $empresa->nombre }}</td>
                    <td> {{ $empresa->descripcion }}</td>
                    <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('tipoacreedor.edit', ['id' => $empresa->id]) }}" class="btn btn-warning btn-gray col-md-8 col-sm-8 col-xs-8 btn-margin">
                          <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>  
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
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($tipos)}} de {{$tipos->total()}} registros
              </div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $tipos->links() }}
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</section>
@endsection