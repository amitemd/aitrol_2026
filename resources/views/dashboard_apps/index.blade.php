@extends('layouts.app-template-apps')
@section('content')
<div class="col-md-12">
  <div class="card">
    <div class="card-header">
      Informe Usuarios Conectados
    </div>
    <div class="card-body">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-primary p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-users text-primary font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">{{$totales_conectados}}</h2>
            <p class="mb-0">Subscribers Gained</p>
          </div>
          <div class="card-content">
            <div id="subscribe-gain-chart"></div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <table class="table table-hover-animation mb-0">
          <thead>
            <tr>
              <th>Id</th>
              <th>Avatar</th>
              <th>Nombre</th>
              <th>Total Conexiones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($datos as $data)
            <tr>
              <td>{{$data->id}}</td>

              <td>
                <div class="avatar mr-1 avatar-lg">
                  @if($data->imagen_url!=' ' && $data->imagen_url!=null)
                  <img src="{{asset('avatars/'.$data->imagen_url)}}" alt="avtar img holder">
                  @else
                  <img src="{{asset('avatars/avatar.jpg')}}" alt="avtar img holder">
                  @endif
                </div>
              </td>
              <td>{{$data->apellido1}} {{$data->nombre1}}</td>
              <td>{{$data->total}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($datos->currentPage() - 1) * $datos->perPage())}} / {{count($datos) + (($datos->currentPage() - 1) * $datos->perPage())}} de {{$datos->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $datos->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','ct_c.id_asiento_cabecera']))->links() }}
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection