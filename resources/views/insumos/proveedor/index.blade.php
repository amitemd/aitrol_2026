@extends('insumos.proveedor.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('winsumos.lista_proveedores')}}</h3>
        </div>
        <div class="col-sm-2">
          <a class="btn btn-primary" href="{{ route('proveedor.create') }}">{{trans('winsumos.agregar_proveedor')}}</a>
        </div>
        <div class="col-sm-2">
          <a class="btn btn-primary" href="{{ route('tipo_proveedor.index') }}">{{trans('winsumos.tipos_proveedores')}}</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('proveedor.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' =>  __('winsumos.Buscar')])
          @component('layouts.two-cols-search-row', ['items' => [__('winsumos.ruc'),
                __('winsumos.razon_social')], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id_proveedor'] : '', isset($searchingVals) ? $searchingVals['razonsocial'] : '']])
          @endcomponent
          
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                 <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">{{trans('winsumos.logo')}}</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('winsumos.ruc')}}</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.razon_social')}}</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.nombre_comercial')}}</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">{{trans('winsumos.correo')}}</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">{{trans('winsumos.tipos_proveedores')}}</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">{{trans('winsumos.accion')}}</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($proveedores as $empresa)
                <tr role="row" class="odd">
                  <td><input type="hidden" name="carga" value="@if($empresa->logo=='') {{$empresa->logo='avatar.jpg'}} @endif">
                  <img src="{{asset('/logo').'/'.$empresa->logo}}"  alt="Logo Image"  style="width:80px;height:80px;" id="logo_empresa" >
                  </td>
                  <td class="sorting_1">{{ $empresa->id_proveedor }}</td>
                  <td> {{ $empresa->razonsocial }}</td>
                  <td> {{ $empresa->nombrecomercial }}</td>
                  <td>{{ $empresa->email }}</td>
                  <td>{{ $empresa->nombre }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('proveedor.edit', ['id' => $empresa->id_proveedor]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        {{trans('winsumos.actualizar')}}
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 / {{count($proveedores)}} de {{$proveedores->total()}} {{trans('winsumos.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $proveedores->links() }}
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