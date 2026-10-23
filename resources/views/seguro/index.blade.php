@extends('seguro.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Seguros</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('form_enviar_seguro.create')}}">Agregar nuevo Seguro</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('seguro.search')}}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Nombre del Seguro'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripcion</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending">Color</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Fecha de Creado</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($seguro as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->nombre}}</td>
                  <td >{{ $value->descripcion}}</td>
                  <td>@if($value->tipo == 0)
                  Publico
                  @elseif($value->tipo == 1)
                  Privado
                  @else
                  Ninguno
                  @endif</td>
                  <td style="background-color: {{ $value->color }}">{{ $value->color }}</td>
                  <td >{{ $value->created_at }}</td>
                  <td>  
                    @if($value->tipo != 2)
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('form_enviar_seguro.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                        </a>
                        <a href="{{ route('seguro.subseguro', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Subseguros
                        </a>
                    @else
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('form_enviar_seguro.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($seguro)}} de {{count($seguro)}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $seguro->appends(Request::only(['nombredelseguro']))->links() }}
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