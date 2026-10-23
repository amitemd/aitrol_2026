@extends('laboratorio.orden.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
 
    .hover-effect:hover {
        background-color: white;
        font-weight: bold;
    }

</style>

@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp



<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<div class="modal fade" id="log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-lg" role="document" style="height: 100px;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content" >
    <div class="row">
        <div class="col-md-12">
          <form class="form-vertical" role="form" method="POST" action="{{ route('nuevoResultado.realizar_new_lista') }}">
              {{ csrf_field() }}
              
              <div class="box-body col-md-12">

                  <div class="form-group col-md-3{{ $errors->has('fecha_desde') ? ' has-error' : '' }}">
                      <label for="fecha_desde" class="col-md-12 control-label">Desde</label>

                      <div class="col-md-12">
                          <input class="form-control" type="date" name="fecha_desde" id="fecha_desde" value="{{$request->fecha_desde}}" >
                          @if ($errors->has('fecha_desde'))
                          <span class="help-block">
                              <strong>{{ $errors->first('fecha_desde') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('fecha_hasta') ? ' has-error' : '' }}">
                      <label for="fecha_hasta" class="col-md-12 control-label">Hasta</label>

                      <div class="col-md-12">
                          <input class="form-control" type="date" name="fecha_hasta" id="fecha_hasta" value="{{$request->fecha_hasta}}" >
                          @if ($errors->has('fecha_hasta'))
                          <span class="help-block">
                              <strong>{{ $errors->first('fecha_hasta') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('tipo') ? ' has-error' : '' }}">
                      <label for="tipo" class="col-md-12 control-label">Tipo</label>

                      <div class="col-md-12">
                          <select id="tipo" name="tipo" class="form-control" >
                              <option @if($request->tipo=='0') selected @endif value="0"> Pendiente resultado</option>
                              <option @if($request->tipo=='1') selected @endif value="1"> pendiente certificar</option>
                              <option @if($request->tipo=='2') selected @endif value="2"> Todo</option>
                          </select>  
                          @if ($errors->has('tipo'))
                          <span class="help-block">
                              <strong>{{ $errors->first('tipo') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('nombre_grupo') ? ' has-error' : '' }}">
                      <label for="nombre_grupo" class="col-md-12 control-label">Agrupador</label>

                      <div class="col-md-12">
                          <select id="nombre_grupo" name="nombre_grupo" class="form-control select2" >
                          <option value="">...Seleccione...</option>
                            @foreach($agrupadores as $agrupador)
                                <option @if($request->nombre_grupo==$agrupador->nombre) selected @endif value="{{$agrupador->nombre}}">{{$agrupador->nombre}}</option>
                            @endforeach
                            @foreach($agrupadores_labs as $agrupadorLabs)
                                <option @if($request->nombre_grupo==$agrupador->nombre) selected @endif value="{{$agrupadorLabs->nombre}}">{{$agrupadorLabs->nombre}}</option>
                            @endforeach
                      
                          </select>
                          @if ($errors->has('nombre_grupo'))
                          <span class="help-block">
                              <strong>{{ $errors->first('nombre_grupo') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('nombre_examen') ? ' has-error' : '' }}">
                      <label for="nombre_examen" class="col-md-12 control-label">Examen</label>

                      <div class="col-md-12">
                          <input class="form-control" type="text" name="nombre_examen" id="nombre_examen" value="{{$request->nombre_examen}}" >
                          @if ($errors->has('nombre_examen'))
                          <span class="help-block">
                              <strong>{{ $errors->first('nombre_examen') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('id_orden') ? ' has-error' : '' }}">
                      <label for="id_orden" class="col-md-12 control-label"># Orden</label>

                      <div class="col-md-12">
                          <input class="form-control" type="text" name="id_orden" id="id_orden" value="{{$request->id_orden}}" >
                          @if ($errors->has('id_orden'))
                          <span class="help-block">
                              <strong>{{ $errors->first('id_orden') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group col-md-3{{ $errors->has('paciente') ? ' has-error' : '' }}">
                      <label for="paciente" class="col-md-12 control-label">Paciente</label>
                      <div class="col-md-12">
                          <input value="@if($request->paciente!=''){{$request->paciente}}@endif" type="text" class="form-control input-sm" name="paciente" id="paciente" placeholder="{{trans('laboratorio.nombres_apellidos')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                          @if ($errors->has('paciente'))
                          <span class="help-block">
                              <strong>{{ $errors->first('paciente') }}</strong>
                          </span>
                          @endif
                      </div>
                  </div>

                  <div class="col-md-2"><br>
                    <button type="submit" class="btn btn-primary btn-sm" >
                        Buscar
                    </button>
                    <button type="submit" formaction="{{route('nuevoResultado.reporte_excel')}}" formmethod="post" class="btn btn-success btn-sm" >
                        Exportar Excel
                    </button>
                </div>
           
              </div>
              
          </form>
        </div>  
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1"><b>No.</b></div>
                            <div class="col-md-1"><b>orden</b></div>
                            <div class="col-md-5"><b>Paciente</b></div>
                            <div class="col-md-3"><b>Fecha</b></div>
                            <div class="col-md-1"><b>Seguro</b></div>
                            <div class="col-md-1"><b>Accion</b></div>    
                        </div>
                    </div>        
                    @php $cantidad = 0; @endphp
                    @foreach ($resultados as $xresultado)
                    @php $orden = Sis_medico\Examen_Orden::find( $xresultado->id ); $cantidad ++;  @endphp 
                    <div class="col-md-12">
                        <div class="row hover-effect" >
                            <div class="col-md-1">{{ $cantidad }}</div>
                            <div class="col-md-1">{{ $orden->id }}</div>
                            <div class="col-md-5">{{ $orden->paciente->id }} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }} </div>
                            <div class="col-md-3">{{ $orden->fecha_orden }}</div>
                            <div class="col-md-1">{{ $orden->seguro->nombre }}</div>
                            <div class="col-md-1">    
                                <button class="btn btn-success btn-xs" onclick="cargar_resultados('{{$orden->id}}')"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-danger btn-xs" onclick="$('#resultado{{$orden->id}}').html('');"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-warning btn-xs" title="Ver Log" onclick="ver_log('{{$orden->id}}')" data-toggle="modal" data-target="#log"><i class="fa fa-file"></i></button>
                            </div>
                      </div>  
                    </div>
                    <div class="col-md-12" id="resultado{{$orden->id}}">
                        
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $('.select2').select2({
            width: '100%', // Establece el ancho del select
            placeholder: 'Seleccione...', // Mensaje de placeholder
            search: true // Habilita la función de búsqueda
        });
    });

  function cargar_resultados(id){
    
    data = '<div class="col-md-6 col-md-offset-2" id="espera_gif'+id+'" ><img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 30%;"></div>';

    $("#resultado"+id).html(data);
    //$('#espera_gif' + id).show();

    $.ajax({
      type: 'post',
      url:"{{route('nuevoResultado.realizar_new')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: {
        'id': id, 
        'nombre_grupo': $('#nombre_grupo').val(), 
        'nombre_examen': $('#nombre_examen').val(), 
        'tipo': $('#tipo').val(), 
        'fecha_desde': '{{$request->fecha_desde}}' , 
        'fecha_hasta': '{{$request->fecha_hasta}}',
        'id_orden': $('#id_orden').val(), 
        },
      success: function(data){
        $("#resultado"+id).html(data);
       
      },
      error: function(data){
        console.log(data);
      }
    });

  }

  
  function cargar_resultados_no_reload(id){

        $.ajax({
        type: 'post',
        url:"{{route('nuevoResultado.realizar_new')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {'id': id, 'nombre_grupo': $('#nombre_grupo').val(), 'nombre_examen': $('#nombre_examen').val(), 'tipo': $('#tipo').val(), 'fecha_desde': '{{$request->fecha_desde}}' , 'fecha_hasta': '{{$request->fecha_hasta}}' , 'id_orden':$('#id_orden').val()},
        success: function(data){
            $("#resultado"+id).html(data);
        
        },
        error: function(data){
            console.log(data);
        }
        });

    }

  
    function ver_log(id){
        $.ajax({
            type: 'post',
            url:"{{route('nuevoResultado.cargar_log')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {
                'id': id,
                },
            success: function(data){
                $('.modal-content').html(data);
                $("#log").modal("show");
            },
            error: function(data){
                console.log(data);
            }
        });
    }
</script>
@endsection
