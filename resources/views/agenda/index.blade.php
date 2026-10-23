

@extends('agenda.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- Ventana modal editar -->
<div class="modal fade" id="editMaxPacientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>







<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-4">
          <h3 class="box-title">Lista de Doctores</h3>
        </div>
        <div class="form-group col-md-4 col-sm-4">  
            <a href="{{ route('preagenda.pentax') }}" class="btn btn-primary col-md-8">Procedimientos</a>
        </div>
        <div class="form-group col-md-4 col-sm-4">  
            <a href="{{ route('hospitalizados.index') }}" class="btn btn-primary col-md-8">Control Hospitalizados</a>
        </div>
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <!--form method="POST" action="{{ route('agenda.search') }}">
        {{ csrf_field() }}
        
          <div class="form-group col-md-4 col-sm-4{{ $errors->has('apellido') ? ' has-error' : '' }}">
            <label for="apellido" class="col-md-4 control-label">@lang('agenda.apellidos')</label>
            <div class="col-md-8">
              <input id="apellido" type="text" class="form-control input-sm" name="apellido" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                @if ($errors->has('apellido'))
                    <span class="help-block">
                        <strong>{{ $errors->first('apellido') }}</strong>
                    </span>
                @endif
              </div>  
          </div>
          
          <div class="form-group col-md-4 col-sm-4{{ $errors->has('id') ? ' has-error' : '' }}">
            <label for="id" class="col-md-4 control-label">@lang('agenda.cedula')</label>
            <div class="col-md-8">
              <input id="id" type="text" class="form-control input-sm" name="id" value="" autofocus>
                @if ($errors->has('id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('id') }}</strong>
                    </span>
                @endif
              </div>  
          </div>
          
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
              @lang('agenda.buscar')
            </button>
          </div> 
      </form-->
      <div class="col-md-12">
      @foreach ($users as $user)
        @if($user->id=='1307189140' || $user->id=='1314490929' || $user->id=='4444444444')
        @php $espe = Sis_medico\User_espe::where('usuid',$user->id)->join('especialidad as e','e.id','user_espe.espid')->select('e.*')->get(); @endphp
        <div class="col-md-4" style="padding: 5px;">
          <div class="box box-success">
            <div class="col-md-12" style="padding: 0;font-size: 13px;">
              <b>@if($user->id!='4444444444')Dr(a).@endif {{$user->apellido1}} @if($user->apellido2!='(N/A)'){{ $user->apellido2 }}@endif</b>
              <b>{{$user->nombre1}} @if($user->nombre2!='(N/A)'){{ $user->nombre2 }}@endif</b>
              @foreach($espe as $e)
                <li>{{$e->nombre}}</li>
              @endforeach 
            </div>
            <div class="col-md-5" style="padding: 0;">
              <a href="{{ route('agenda.agenda', ['id' => $user->id])}}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:100%;height:auto;" id="fotografia_usuario" ></a> 
            </div>
            <div class="col-md-7" style="padding: 5px;">
              
              <div class="col-md-12">
                
                <a href="{{ route('agenda.agenda', ['id' => $user->id])}}" class="btn btn-warning col-md-12 btn-xs">
                              Agendar
                              </a> 
                                            
              </div>
            
              <div class="col-md-12">
                             
                <a href="{{ route('doctor.max', ['id' => $user->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-info col-md-12 btn-xs">
                              Máximos
                              </a> 
                                        
              </div>
           
              <div class="col-md-12">
                             
                <a href="{{ route('horario.doctor', ['id' => $user->id]) }}" class="btn btn-success col-md-12 btn-xs">
                  {{trans('agenda.horariolaboral')}}
                </a>
              </div>
            </div>
            <div class="col-md-12" style="padding: 0;font-size: 13px;">
              <b>{{trans('agenda.maximoconsultas')}}:</b>{{$user->max_consulta}} - <b>{{trans('agenda.maximoprocedimientos')}}:</b>{{$user->max_procedimiento}}
            </div>



              
             
          </div>
        </div> 
        @endif
      @endforeach
      </div>
      @php $staff = Sis_medico\Doctor_Tiempo::orderBy('ip_creacion','asc')->get(); @endphp
      <div>&nbsp;</div>
      <h4>Doctores del Staff</h4>
      <div class="col-md-12">
      @foreach ($staff as $stf) 
        @php 
          $xuser = $users->find($stf->id_doctor); 
          if(!is_null($xuser)){
            $espe = Sis_medico\User_espe::where('usuid',$xuser->id)->join('especialidad as e','e.id','user_espe.espid')->select('e.*')->get();
          }
        @endphp
        @if(!is_null($xuser))
        @if($xuser->id=='1307189140' || $xuser->id=='1314490929' || $xuser->id=='4444444444')
        @else
        <div class="col-md-3" style="padding: 5px;">
          <div class="box box-success">
            <div class="col-md-12" style="padding: 0;font-size: 13px;">
              <b>@if($xuser->id!='4444444444')Dr(a).@endif {{$xuser->apellido1}} @if($xuser->apellido2!='(N/A)'){{ $xuser->apellido2 }}@endif</b>
              <b>{{$xuser->nombre1}} </b>
              @foreach($espe as $e)
                <li style="font-size: 14px;">{{$e->nombre}}</li>
              @endforeach
            </div>
            <div class="col-md-5" style="padding: 0;">
               <a href="{{ route('agenda.agenda', ['id' => $xuser->id])}}"><input type="hidden" name="carga" value="@if($xuser->imagen_url==' ') {{$xuser->imagen_url='avatar.jpg'}} @endif">
                <img src="{{asset('/avatars').'/'.$xuser->imagen_url}}"  alt="xUser Image"  style="width:100%;height:150px;" id="fotografia_usuario" ></a>   
            </div>
            <div class="col-md-7" style="padding: 5px;">
              <div class="col-md-12">
                <a href="{{ route('agenda.agenda', ['id' => $xuser->id])}}" class="btn btn-warning col-md-12 btn-xs"> Agendar</a> 
              </div>
              <div class="col-md-12">
                <a href="{{ route('doctor.max', ['id' => $xuser->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-info col-md-12 btn-xs">
                  Máximos
                </a>                        
              </div>
              <div class="col-md-12">
                <a href="{{ route('horario.doctor', ['id' => $xuser->id]) }}" class="btn btn-success col-md-12 btn-xs">
                  {{trans('agenda.horariolaboral')}}
                </a>                             
              </div>  
            </div> 
            <div class="col-md-12" style="padding: 0;font-size: 13px;">
              <b>@lang('agenda.maximoconsultas'):</b>{{$xuser->max_consulta}} - <b>@lang('agenda.maximoprocedimientos'):</b>{{$xuser->max_procedimiento}}
            </div>
          </div>
        </div>
        @endif
        @endif 
        
      @endforeach
      </div>
      <div>&nbsp;</div>
      <h4>@lang('agenda.doctor') @lang('agenda.externos')</h4>
      <div class="col-md-12">
      @foreach ($users as $user)
        @if($user->id!='1307189140' && $user->id!='1314490929' && $user->id!='4444444444')
          @php 
            $xstaff = Sis_medico\Doctor_Tiempo::where('id_doctor',$user->id)->first();            
            $espe = Sis_medico\User_espe::where('usuid',$user->id)->join('especialidad as e','e.id','user_espe.espid')->select('e.*')->get();
          @endphp
          @if(is_null($xstaff))
          <div class="col-md-3" style="padding: 5px;">
            <div class="box box-success">

              <div class="col-md-12" style="padding: 0;font-size: 13px;">
                <b>@if($user->id!='4444444444')Dr(a).@endif {{$user->apellido1}} @if($user->apellido2!='(N/A)'){{ $user->apellido2 }}@endif</b>
                <b>{{$user->nombre1}} @if($user->nombre2!='(N/A)'){{ $user->nombre2 }}@endif</b>
                @foreach($espe as $e)
                  <li>{{$e->nombre}}</li>
                @endforeach
              </div>
              <div class="col-md-5" style="padding: 0;">
                <a href="{{ route('agenda.agenda', ['id' => $user->id])}}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:100%;height:150px;" id="fotografia_usuario" ></a>
              </div>
              <div class="col-md-7" style="padding: 5px;">
                <div class="col-md-12">
                 
                  <a href="{{ route('agenda.agenda', ['id' => $user->id])}}" class="btn btn-warning col-md-12 btn-xs">
                                Agendar
                                </a> 
                                         
                </div>
               
                <div class="col-md-12">
                 
                 
                  <a href="{{ route('doctor.max', ['id' => $user->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-info col-md-12 btn-xs">
                                Máximos
                                </a> 
                                         
                </div>
               
                <div class="col-md-12">
                 
                 
                  <a href="{{ route('horario.doctor', ['id' => $user->id]) }}" class="btn btn-success col-md-12 btn-xs">
                                {{trans('agenda.horariolaboral')}}
                                </a>                             
                </div>
              </div>  
              <div class="col-md-12" style="padding: 0;font-size: 13px;">
                <b>Max.Con:</b>{{$user->max_consulta}} - <b>Max.Proc:</b>{{$user->max_procedimiento}}
              </div>  
                
              
              
            </div>
          </div>
          @endif 
        @endif  
        
      @endforeach
      </div>
    <?php /*  
    <!--div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Imágen</th>
                <th>Cédula</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Email</th>
                <th>Máximo Consultas</th>
                <th>Máximo Procedimientos</th>
                <th>Acción</th>                    
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
                  <tr>
                    <td><a href="{{ route('agenda.agenda', ['id' => $user->id])}}"><input type="hidden" name="carga" value="@if($user->imagen_url==' ') {{$user->imagen_url='avatar.jpg'}} @endif">
                    <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:80px;height:80px;" id="fotografia_usuario" ></a></td>  
                    <td>{{$user->id}}</td>
                    <td>{{$user->apellido1}} {{ $user->apellido2 }}</td>
                    <td>{{$user->nombre1}} {{ $user->nombre2 }}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->max_consulta}}</td>
                    <td>{{$user->max_procedimiento}}</td>
                    <td><input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('agenda.agenda', ['id' => $user->id]) }}" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                          Agendar
                          </a>                                        
                          
                       
                        <a href="{{ route('doctor.max', ['id' => $user->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">
                          Máximos
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($users)}} de {{$users->total()}}registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $users->links() }}
          </div>
        </div>
      </div>
    </div--> */ ?>
  </div>
  
</div>
    </section>
   
  </div>





<script type="text/javascript">


  $(document).ready(function() 
    {
        $(".breadcrumb").append('<li class="active">Agenda</li>');
    });

  $('#editMaxPacientes').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

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