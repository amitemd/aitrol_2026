

@extends('users-mgmt.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Usuarios</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('user-management.create') }}">Agregar Nuevo Usuario</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('user-management.search') }}">
          {{ csrf_field() }}
          <!--primer apellido-->
          <div class="form-group col-md-4{{ $errors->has('apellido') ? ' has-error' : '' }}">
            <label for="apellido" class="col-md-4 control-label">Apellidos</label>
            <div class="col-md-8">
              <input id="apellido" type="text" class="form-control input-sm" name="apellido" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                @if ($errors->has('apellido'))
                    <span class="help-block">
                        <strong>{{ $errors->first('apellido') }}</strong>
                    </span>
                @endif
              </div>  
          </div>
          <!--cedula-->
          <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
            <label for="id" class="col-md-4 control-label">Cédula</label>
            <div class="col-md-8">
              <input id="id" type="text" class="form-control input-sm" name="id" value="" autofocus>
                @if ($errors->has('id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('id') }}</strong>
                    </span>
                @endif
              </div>  
          </div>
          <!--id_tipo_usuario-->
          <div class="form-group col-md-4{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}">
            <label for="id_tipo_usuario" class="col-md-4 control-label">Tipo Usuario</label>
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
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
              Buscar
            </button>
          </div> 
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                 <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Imágen</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Cédula</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Apellidos</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombres</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo Usuario</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Email</th>
                <th  width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Acción</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr role="row" class="odd">
                  <td><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                  <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:80px;height:80px;" id="fotografia_usuario" >
                  </td>
                  <td class="sorting_1">{{ $user->id }}</td>
                  <td> {{ $user->apellido1 }} {{ $user->apellido2 }}</td>
                  <td> {{ $user->nombre1 }} {{ $user->nombre2 }}</td>
                  <td> @foreach ($tipousuarios as $tipousuario)
                          @if($tipousuario->id == $user->id_tipo_usuario) 
                            {{$tipousuario->nombre}}
                          @endif 
                      @endforeach</td>
                  
                  <td>{{ $user->email }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('user-management.destroy', ['id' => $user->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <!--<input type="hidden" name="_method" value="DELETE">-->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <a href="{{ route('user-management.edit', ['id' => $user->id]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                        Actualizar
                        </a>
                        <!--@if ($user->id_tipo_usuario != 1)-->
                                               <!--@endif
                        @if ($user->nombre1 != Auth::user()->nombre1)
                         <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                          Delete
                        </button>
                        @endif-->
                    </form>
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <!--<tr>
                <th width="10%" rowspan="1" colspan="1">Nombre de Usuario</th>
                <th width="20%" rowspan="1" colspan="1">Email</th>
                <th class="hidden-xs" width="20%" rowspan="1" colspan="1">Nombres</th>
                <th class="hidden-xs" width="20%" rowspan="1" colspan="1">Apellidos</th>
                <th rowspan="1" colspan="2">Acción</th>
              </tr>-->
            </tfoot>
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
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection