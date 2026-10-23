@extends('contable.empleados.base')
@section('action-content')

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item active">Vendedor - Recaudador</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <!--<h8 class="box-title">Lista de Empleados</h8>-->
        <h5><b>VENDEDOR - RECAUDADOR</b></h5>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('empleados.create')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Vendedor / Recaudador
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR  VENDEDOR - RECAUDADOR</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" action="{{route('empleados_search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2 {{ $errors->has('apellido') ? ' has-error' : '' }}">
          <label class="texto" for="apellido">Apellidos</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="apellido" name="apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" autofocus placeholder="Ingrese Apellido..." />
          @if ($errors->has('apellido'))
          <span class="help-block">
            <strong>{{ $errors->first('apellido') }}</strong>
          </span>
          @endif
        </div>
        <div class="form-group col-md-1 col-xs-2 {{ $errors->has('id') ? ' has-error' : '' }}">
          <label class="texto" for="id">Cédula:</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" autocomplete="off" placeholder="Ingrese la Cedula..." />
          @if ($errors->has('id'))
          <span class="help-block">
            <strong>{{ $errors->first('id') }}</strong>
          </span>
          @endif
        </div>
        <div  class="form-group col-md-4{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}">
          <label for="id_tipo_usuario"  class="col-md-4 control-label texto">Tipo Usuario</label>
          <div class="col-md-8">
            <select id="id_tipo_usuario" name="id_tipo_usuario" class="form-control">
              <option value="">Seleccione ...</option>
              @foreach ($tipousuarios as $tipousuario)
              @if ($tipousuario->estado != 0)
              <option value="{{$tipousuario->id}}">{{$tipousuario->nombre}}</option>
              @endif
              @endforeach
            </select>
            @if ($errors->has('id_tipo_usuario'))
            <span class="help-block">
              <strong>{{ $errors->first('id_tipo_usuario') }}</strong>
            </span>
            @endif
          </div>
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
        <label class="color_texto">LISTADO  VENDEDOR - RECAUDADOR</label>
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
                    <tr class="well-dark">
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Cédula</th>
                      <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Apellidos</th>
                      <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Nombres</th>
                      <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Tipo Usuario</th>
                      <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Email</th>
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                    <tr role="row" class="odd" style="background:#F5F5F5">
                      <td class="sorting_1">{{ $user->id }}</td>
                      <td> {{ $user->apellido1 }} {{ $user->apellido2 }}</td>
                      <td> {{ $user->nombre1 }} {{ $user->nombre2 }}</td>
                      <td> @foreach ($tipousuarios as $tipousuario)
                        @if($tipousuario->id == $user->id_tipo_usuario)
                        {{$tipousuario->nombre}}
                        @endif
                        @endforeach</td>
                      <td>{{ $user->email }}</td>
                      <td style="text-align: center;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('empleados_editar', $user->id)}}" class="btn btn-success btn-gray">
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($users)}} de {{$users->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $users->appends(Request::only(['id', 'apellido', 'id_tipo_usuario']))->links()}}
                </div>
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
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })
</script>

@endsection