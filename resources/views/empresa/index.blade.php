

@extends('empresa.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Empresas</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('empresa.create') }}">Agregar Nueva Empresa</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('empresa.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['RUC', 'Razon Social'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['razonsocial'] : '']])
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
                 <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Logo</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">RUC</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Razón Social</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Nombre Comercial</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>

              </tr>
            </thead>
            <tbody>
            @foreach ($empresas as $empresa)
                <tr role="row" class="odd">
                  <td><input type="hidden" name="carga" value="@if($empresa->logo=='') {{$empresa->logo='avatar.jpg'}} @endif">
                  <img src="{{asset('/logo').'/'.$empresa->logo}}"  alt="Logo Image"  style="width:80px;height:80px;" id="logo_empresa" >
                  </td>
                  <td class="sorting_1">{{ $empresa->id }}</td>
                  <td> {{ $empresa->razonsocial }}</td>
                  <td> {{ $empresa->nombrecomercial }}</td>
                  <td>{{ $empresa->email }}</td>
                  <td align="center">
                        <a href="{{ route('empresa.edit', ['id' => $empresa->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                    </a>
                    <a href="{{ route('maestrosed.edit', ['id' => $empresa->id]) }}" class="btn btn-success col-md-10 col-sm-10 col-xs-10 btn-margin">
                      Documento Electrónico
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($empresas)}} de {{count($empresas)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $empresas->appends(Request::only(['ruc','razonsocial']))->links() }}
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