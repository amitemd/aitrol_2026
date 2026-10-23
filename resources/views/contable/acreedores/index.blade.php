@extends('contable.acreedores.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header header_new">
    <div class="row">
        <div class="col-sm-8"> 
          <h3 class="box-title">Lista de Proveedores</h3>
        </div>
        <div class="col-sm-2">
          <a class="btn btn-primary btn-gray" href="{{ route('acreedores_crear') }}">Agregar Nuevo Proveedor</a>
        </div>
        <div class="col-sm-2">
          <a class="btn btn-primary btn-gray" href="{{ route('tipoacreedor.index') }}">Tipos de Proveedores</a>
        </div>
    </div>
  </div>  
  <!-- /.box-header -->
  <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">PROVEEDORES</label>
          </div>
  </div>
  <div class="box-body dobra">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('acreedores_search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['RUC', 'Razon Social','Nombre Comercial'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['razonsocial'] : '', isset($searchingVals) ? $searchingVals['nombrecomercial']: '' ]])
          @endcomponent
          
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                 <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Logo</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">RUC</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Razón Social</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Nombre Comercial</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Tipo de Proveedor</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($proveedores as $proveedor)
                <tr role="row" class="odd">
                  <td><input type="hidden" name="carga" value="@if($proveedor->logo=='') {{$proveedor->logo='avatar.jpg'}} @endif">
                  <img src="{{asset('/logo').'/'.$proveedor->logo}}"  alt="Logo Image"  style="width:85px;height:35px;" id="logo_proveedor" >
                  </td>
                  <td class="sorting_1">{{ $proveedor->id }}</td>
                  <td> {{ $proveedor->razonsocial }}</td>
                  <td> {{ $proveedor->nombrecomercial }}</td>
                  <td>{{ $proveedor->email }}</td>
                  <td>{{ $proveedor->nombre }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                       
                        <a href="{{ route('acreedores_editar', ['id' => $proveedor->id]) }}"  class="btn btn-warning btn-gray col-md-8 col-sm-8 col-xs-8 btn-margin">
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($proveedores)}} de {{$proveedores->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $proveedores->appends(Request::only(['ruc', 'razonsocial','nombrecomercial']))->links() }}
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