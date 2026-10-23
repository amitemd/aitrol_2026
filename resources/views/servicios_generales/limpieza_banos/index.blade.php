@extends('servicios_generales.limpieza_banos.base')
@section('action-content')

@php
$t = date('Y-m-d');
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
@endphp
<style type="text/css">
  th,
  td {
    text-align: center;
  }
  .imgf{
    cursor: pointer;
  }
</style>
<div class="modal fade" id="editar" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 80%;">
    </div>
  </div>
</div>
<div class="modal fade" id="foto" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 80%;">
    </div>
  </div>
</div>

<div id="fotito">
</div>
<div class="modal fade" id="foto2" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 80%;">
    </div>
  </div>
</div>

<div id="fotito2">
</div>


<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="col-md-12">
        <div class="col-md-10">
          <h4 style="text-align: left;">{{trans('tecnicof.cleaningrecords')}}</h4>
        </div>
      </div>
    </div>
    <div class="box-body">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <form id="form_fecha" method="POST" action="{{route('limpieza_banos.buscar_fecha')}}">
              {{ csrf_field() }}

              <div class="form-group  col-xs-4">
                <label for="fecha" class="col-md-6 control-label">{{trans('tecnicof.from')}}</label>
                <div class="col-md-6">
                  <input style="text-align: center;line-height:10px;" type="date" name="desde" id="desde" class="form-control" value="{{$fecha_desde}}">
                </div>
              </div>
              <div class="form-group  col-xs-4">
                <label for="fecha" class="col-md-6 control-label">{{trans('tecnicof.to')}}</label>
                <div class="col-md-6">
                  <input style="text-align: center;line-height:10px;" type="date" name="hasta" id="hasta" class="form-control" value="{{$fecha_hasta}}">
                </div>
              </div>
              <div class="form-group col-md-1 col-xs-1">
                <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
              </div>
              <div class="form-group col-md-1 col-xs-1">
               {{-- <!--a href="{{route('limpieza_banos.create', ['id_sala' => $sala->id])}}" class="btn btn-primary"><i aria-hidden="true"></i> Registrar</a-->--}}
              </div>
            </form>
		@php
                  $permisos = \Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
                @endphp
            @if($rolUsuario == 1 || $rolUsuario == 11 ||  $id_auth=='1111111115' || !is_null($permisos))
            <div class="form-group col-md-1 col-xs-1">
              <form method="GET" action="{{route('limpieza_banos.nuevo_reporte')}}">
                <input type="hidden" value="{{$fecha_desde}}" name="fecha_desde">
                <input type="hidden" value="{{$fecha_hasta}}" name="fecha_hasta">
                {{--<!--input type="hidden" value="{{$sala->id}}" name="id_sala"-->--}}
                <button type="submit" class="btn btn-success"><i aria-hidden="true"></i> Reportes</button>
              </form>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row" id="listado">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
              <thead>
                <tr>
                  <th>{{trans('tecnicof.date')}}</th>
                  <th>Hora evidencia antes</th>
                  <th>Hora evidencia después</th> 
                  <th>Responsable</th>
                  <th>Evidencia antes</th>
                  <th>Evidencia después</th>
                  <th>Observaciones</th>
                  <th>Accion</th>
                </tr>
              </thead>

              @foreach($control_limp as $value )

              @php
              $fecha = substr($value->fecha, 0, 11);
              $frecuencia1 = substr($value->frecuencia1, 11, 20);
              $emplace=asset('/');
              $remplace= str_replace('public','storage',$emplace);
              $created_at = substr($value->created_at, 11, 20);
              $updated_at = substr($value->updated_at, 11, 20);

              @endphp
              <tr>
                <td>{{$fecha}}</td>
                <td>{{$frecuencia1}}</td>
                <td>@if(!is_null($value->evidencia_desp)){{$updated_at}}@endif</td>
                <td>@if(!is_null($value->responsable) || !empty($value->responsable)){{$value->encargado->nombre1}} {{$value->encargado->nombre2}} {{$value->encargado->apellido1}} {{$value->encargado->apellido2}}@endif</td>
                <td> @if(!is_null($value->evidencia_antes) && !empty($value->evidencia_antes))
                  <img id="{{$value->id}}" data-name="1" class="imgf foto" src="{{$remplace.'app/avatars/'.$value->evidencia_antes}}" width="50" alt="">@endif
                
                </td>
                <td>@if(!is_null($value->evidencia_antes) && !empty($value->evidencia_desp))
                  <img id="{{$value->id}}" data-name="2"  class="imgf foto" src="{{$remplace.'app/avatars/'.$value->evidencia_desp}}" width="50" alt="">@endif
                
                
                </td>
                <td>{{$value->observacion}} <input type="hidden" value="{{$value->id}}"></td>
                <td id="buton{{$value->id}}"> 
                  @if(($value->evidencia_antes)!="" && ($value->evidencia_desp)!="")
                  <button type="button" class="btn btn-warning btn-xs">Completado</button>
                  @else
                  <a id="editar" class="btn btn-info btn-xs" href="{{ route('limpieza_banos.edit', ['id' => $value->id]) }}"><span>Editar Registro</span></a>
                  @endif
                </td>
              </tr>
                @endforeach
            </table>
                  <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} 1 / {{count($control_limp)}} de {{$control_limp->total()}} {{trans('contableM.registros')}}</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $control_limp->appends(Request::all())->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

</section>

<script>
  function agreagar(id) {
    $.ajax({
      url: "{{route('limpieza_banos.agregarhoras')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      data: {
        'id': id,
      },
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data == 'ok') {
          location.reload();
        }
      },
      error: function(xhr, status) {
        alert('Existió un problema');
      },
    });
  }

  var elementos = document.getElementsByClassName("foto");
  for (var i = 0; i < elementos.length; i++) {
    elementos[i].addEventListener('click', modalfoto);
  }


  function modalfoto() {
    //console.log(this);
    $.ajax({
      type: 'get',
      url: "{{route('limpieza_banos.modal_foto')}}",
      data: {
        'id': this.id,
        'tipo': this.dataset.name,
      },
      datatype: 'html',
      success: function(data) {

        $("#fotito").html(data);
        $("#fotito").children().modal();

      },
      error: function(data) {
        // console.log(data);
      }

    });
  }
</script>
<!--
<script text="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': true,
      'lengthChange': false,
      'searching': false,
      'responsive': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'sInfoEmpty': false,
      'sInfoFiltered': false,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });
  });
</script>
-->
@endsection