@extends('hc_admision.formulario013.base1')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
  .mce-content-body:hover {
    outline: 2px solid #2276d2 !important;
  }

  .parcial_border {
    border-top-color: #2276d2;
    border-bottom-color: #2276d2;
    border-top-width: 3px;
    border-bottom-width: 3px;
    border-top-style: solid;
    border-bottom-style: solid;
    width: 90%;
    margin-left: 5%;
  }

  .td-border-bottom{
    border-bottom: 1px solid;
  }
</style>
<div class="modal-header">
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">@lang('admision.protoperatorio')</h4>
</div>


<div class="modal-body">
  <div class="box-body" style="background-color: white;margin-left: 50px;margin-right: 50px;">
    <form class="form-vertical" id="form" name="form" role="form" method="POST">
      {{ csrf_field() }}
      <input id="id" type="hidden" name="id" value="{{ $datos->id }}">
      <input id="id_hc_procedimiento" type="hidden" name="id" value="{{ $datos->id_hc_procedimiento }}">
      <h4 style="text-align: center;"><b>@lang('admision.ultimasevol-')</b></h4>
      <div>
        <div class="parcial_border" >
          <table style="width: 100%;" cell-spacing="5" cell-padding="5">
            <thead>
              <tr align="center">
                <th style="text-align: center;">@lang('laboratorio.identificacion')</th>
                <th style="text-align: center;">@lang('laboratorio.paciente')</th>
                <th style="text-align: center;">@lang('laboratorio.fecha')</th>
                <th style="text-align: center;">@lang('laboratorio.procedimiento')</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="text-align: center;">{{$paciente->id}}</td>
                <td style="text-align: center;">{{$paciente->nombre1}} {{$paciente->apellido1}}</td>
                <td style="text-align: center;">@if(!is_null($protocolo)) {{$protocolo->fechaini}} @endif</td>
                <td style="text-align: center;">{{$texto}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <br>
      <div style="height: 300px;overflow:scroll;background-color: white;margin-left: 10%;width: 80%;">
        <table style="width: 100%;">
            <thead>
              <tr style="margin-bottom: 35px;">
                <th class="td-border-bottom" style="width: 100px;text-align: center;">@lang('laboratorio.fecha')</th>
                <th class="td-border-bottom" style="width: min-content;text-align: center;">@lang('laboratorio.evolucion')</th>
                <th class="td-border-bottom" style="width: 200px;">@lang('laboratorio.doctor')</th>
                <th class="td-border-bottom" style="width: min-content;">@lang('laboratorio.accion')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($historiaclinica as $h)
                <tr style="margin-bottom: 35px;">
                  <td class="td-border-bottom" style="text-align: left;width: 100px;">{{substr($h->created_at,0,10)}}</td>
                  <td class="td-border-bottom"><?php echo $h->cuadro_clinico; ?></td>
                  <td class="td-border-bottom" style="width: 200px;">{{$h->apellido1}} {{$h->nombre1}}</td>
                  <td class="td-border-bottom" style="text-align: center;"><button type="button" class="btn btn-success btn-xs" onclick="carga_evolucion('{{$h->id}}')"><i class="glyphicon glyphicon-ok-sign"></i></button></td>
                </tr>
              @endforeach
            </tbody>
        </table> 
      </div>
      <br>
      <div class="form-group col-md-12{{ $errors->has('empresa') ? ' has-error' : '' }}">
        <label for="empresa" class="col-md-4 control-label">{{trans('bitacora.empresa')}}</label>
        <div class="col-md-8">
          <select name="empresa" onchange="actualizar()" id="empresa" class="form-control input-sm">
            @foreach($empresas as $value)
            <option value="{{$value->id}}" @if($value->id == $datos->id_empresa)
              selected
              @endif
              >{{$value->nombrecomercial}}</option>
            @endforeach
          </select>
          @if ($errors->has('empresa'))
          <span class="help-block">
            <strong>{{ $errors->first('empresa') }}</strong>
          </span>
          @endif
        </div>
      </div>



      <div class="form-group col-md-12{{ $errors->has('resumen_clinico') ? ' has-error' : '' }}">

        <label for="tresumen_clinico" class="col-md-4 control-label">{{trans('bitacora.resumen_clinico')}}</label>
        <div class="col-md-8">
          <div id="tresumen_clinico" style="border: solid 1px; border-color: #CED9DA; overflow:auto !important;max-height: 150px;resize:none;background-color: white;">@if(!is_null($datos))<?php echo ($datos->resumen_clinico) ?> @endif</div>
          <input type="hidden" name="resumen_clinico" id="resumen_clinico">
        </div>

      </div>
      <div class="form-group col-md-12{{ $errors->has('estudio_solicitado') ? ' has-error' : '' }}">
        <label for="estudio_solicitado" class="col-md-4 control-label">{{trans('bitacora.estudio_solicitado')}}</label>
        <div class="col-md-8">
          <select onchange="actualizar()" name="estudio_solicitado" id="estudio_solicitado" class="form-control input-sm">
            @if($datos->estudio_solicitado == 0)
            <option value="0" selected>{{ trans('bitacora.hispatologia') }}</option>
            <option value="1">{{ trans('bitacora.citogia') }}</option>
            @elseif($datos->estudio_solicitado == 1)
            <option value="0">{{ trans('bitacora.hispatologia') }}</option>
            <option value="1" selected>{{ trans('bitacora.citogia') }}</option>
            @endif
          </select>@if ($errors->has('estudio_solicitado'))
          <span class="help-block">
            <strong>{{ $errors->first('estudio_solicitado') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="form-group col-md-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
        <label for="descripcion" class="col-md-4 control-label">{{trans('bitacora.descripcion_estudio')}}</label>
        <div class="col-md-8">
          <textarea id="descripcion" onchange="actualizar()" class="form-control input-sm" type="text" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase; resize:none;" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>{{ $datos->descripcion_estudio }}</textarea>
          @if ($errors->has('descripcion'))
          <span class="help-block">
            <strong>{{ $errors->first('descripcion') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="form-group col-md-12{{ $errors->has('prioridad') ? ' has-error' : '' }}">
        <label for="prioridad" class="col-md-4 control-label">{{trans('bitacora.prioridad')}}</label>
        <div class="col-md-8">
          <select name="prioridad" onchange="actualizar()" id="prioridad" class="form-control input-sm">
            @if($datos->prioridad == 0)
            <option value="0" selected>{{trans('bitacora.urgente')}}</option>
            <option value="1">{{trans('bitacora.rutina')}}</option>
            <option value="2">{{trans('bitacora.control')}}</option>
            @elseif($datos->prioridad == 1)
            <option value="0">{{trans('bitacora.urgente')}}</option>
            <option value="1" selected>{{trans('bitacora.rutina')}}</option>
            <option value="2">{{trans('bitacora.control')}}</option>

            @elseif($datos->prioridad == 2)
            <option value="0">{{trans('bitacora.urgente')}}</option>
            <option value="1">{{trans('bitacora.rutina')}}</option>
            <option value="2" selected>{{trans('bitacora.control')}}</option>
            @endif
          </select>
          @if ($errors->has('prioridad'))
          <span class="help-block">
            <strong>{{ $errors->first('prioridad') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="form-group col-md-12{{ $errors->has('tratamiento') ? ' has-error' : '' }}">
        <label for="tratamiento" class="col-md-4 control-label">{{trans('bitacora.tratamiento')}}</label>
        <div class="col-md-8">
          <textarea id="tratamiento" onchange="actualizar()" class="form-control input-sm" type="text" name="tratamiento" value="{{ old('tratamiento') }}" style="text-transform:uppercase; resize:none;" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>{{ $datos->tratamiento }}</textarea>
          @if ($errors->has('tratamiento'))
          <span class="help-block">
            <strong>{{ $errors->first('tratamiento') }}</strong>
          </span>
          @endif
        </div>
      </div>


      <div class="form-group col-md-12{{ $errors->has('fecha') ? ' has-error' : '' }}">
        <label for="fecha" class="col-md-4 control-label">{{trans('bitacora.fecha_toma')}}</label>
        <div class="col-md-8">
          <input id="fecha" type="date" onchange="actualizar()" class="form-control input-sm" name="fecha" value="{{ $fecha_toma }}">
          @if ($errors->has('fecha'))
          <span class="help-block">
            <strong>{{ $errors->first('fecha') }}</strong>
          </span>
          @endif
        </div>
      </div>
      <h4 style="text-align: center;"><b>@lang('laboratorio.biopsias-')</b></h4>
      <table class="table">
        <tr>
          <td style="width: 50%;">
            <table class="table">
              <caption style="text-align: center;">
                <h4>@lang('laboratorio.historialBiopsias')</h4>
              </caption>
              <thead>
                  <th style="text-align: center;">Descripcion</th>
                  <th style="text-align: center;">Observacion</th>
                  <th style="text-align: center;">@lang('laboratorio.doctor')</th>
                  <th style="text-align: center;">@lang('laboratorio.accion')</th>
              </thead>
              <tbody>
                @foreach($biopsias as $b)
                  <tr>
                    <td>{{$b->descripcion_frasco}}</td>
                    <td>{{$b->observacion}}</td>
                    @php $doctor = \Sis_medico\User::find($b->id_doctor); @endphp
                    <td>@if(!is_null($doctor)) {{$doctor->nombre1}} {{$doctor->apellido1}} @endif</td>
                    <td style="text-align: center;"><button type="button" class="btn btn-success btn-xs" onclick="storeAuditoriaBiopsia('{{$b->id}}')"><i class="glyphicon glyphicon-ok-sign"></i></button></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </td>
          <td style="width: 50%;">
            <table class="table">
              <caption style="text-align: center;">
                <h4>@lang('laboratorio.biopsias_agregadas')</h4>
              </caption>
              <thead>
                <th style="text-align: center;">Descripcion</th>
                <th style="text-align: center;">Observacion</th>
                <th style="text-align: center;">@lang('laboratorio.doctor')</th>
                <th style="text-align: center;">@lang('laboratorio.accion')</th>
              </thead>
              <tbody>
                @foreach($biopsias_auditoria as $b)
                  <tr>
                    <td>{{$b->descripcion_frasco}}</td>
                    <td>{{$b->observacion}}</td>
                    @php $doctor = \Sis_medico\User::find($b->id_doctor); @endphp
                    <td>@if(!is_null($doctor)) {{$doctor->nombre1}} {{$doctor->apellido1}} @endif</td>
                    <td style="text-align: center;"><button type="button" class="btn btn-danger btn-xs" onclick="delete_biopsias('{{$b->id}}')"><i class="glyphicon glyphicon-trash"></i></button></td>
                  </tr>
                @endforeach
              </tbody>
            </table> 
          </td>
        </tr>
      </table>
      <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
          <a target="_blank" href="{{route('nuevoArchivoPlano.formulario_013', ['id'=>$datos->id_hc_procedimiento])}} " class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('laboratorio.descargar') }} EXCEL</a>
          <!--a target="_blank" href="{{route('nuevoArchivoPlano.formulario_013_pdf', ['id'=>$datos->id_hc_procedimiento])}} " class="btn btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ trans('laboratorio.descargar') }} PDF</a-->
          <a target="_blank" href="{{route('formulario.013A', ['id'=>$datos->id_hc_procedimiento])}} " class="btn btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ trans('laboratorio.descargar') }} PDF</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script text="text/javascript">
  $(document).ready(function() {
    return tinymce.init({
      selector: '#tresumen_clinico',
      height: 100,
      width: 550,
      inline: true,
      menubar: false,
      content_style: ".mce-content-body {font-size:12px;}",
      plugins: ['image', 'charmap', 'uploadimage'],
      toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link uploadimage | charmap ',
      theme: 'modern',
      relative_urls: false,
      remove_script_host: false,
      document_base_url: (!window.location.origin ? window.location.protocol + '//' + window.location.host : window.location.origin) + '/',
      setup: function(editor) {
        editor.on('init', function(e) {
          var ed = tinyMCE.get('tresumen_clinico');
          //alert(ed.getContent());
          $("#resumen_clinico").val(ed.getContent());

        });
      },
      init_instance_callback: function(editor) {
        editor.on('Change', function(e) {
          var ed = tinyMCE.get('tresumen_clinico');
          $("#resumen_clinico").val(ed.getContent());
          actualizar();
        });
      }
    });
  });

  function actualizar() {
    $.ajax({
      //async: true,
      type: "POST",
      url: "{{route('formulario013.update')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: "json",
      data: $("#form").serialize(),
      success: function(data) {

      },
      error: function() {
        swal({
          title: "Error!",
          type: "error",
          html: data.msj
        });
      }
    });
  }

  function carga_evolucion(id){
    $.ajax({
        url:"{{url('archivo_plano/formulario013/evolucion/')}}/"+id,
        dataType: "json",
        type: 'get',
        success: function(data){
            anterior = tinyMCE.get('tresumen_clinico').getContent();
            tinyMCE.get('tresumen_clinico').setContent(anterior+'\n'+data.evolucion.cuadro_clinico);
            $("#resumen_clinico").val(anterior+'\n'+data.evolucion.cuadro_clinico);
            actualizar();
        }
    });
  }

  function storeAuditoriaBiopsia(id){
    actualizar();
    let id_hc_procedimiento = $('#id_hc_procedimiento').val();
    $.ajax({
      type: "get",
      url: "{{url('archivo_plano/formulario013/biopsia/store/')}}/"+id,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: "json",
      data: {
        'id': id,
        'id_hc_procedimiento': id_hc_procedimiento,
      },
      success: function(data) {
        if(data.status == 'success'){
          location.reload();
        }
        else{
          Swal.fire(`Error`,"error",data.msj);
        }
      },
      error: function() {
        swal({
          title: "Error!",
          type: "error",
          html: data.msj
        });
      }
    });
  }

  function delete_biopsias(id){
    actualizar();
    $.ajax({
        url:"{{url('archivo_plano/formulario013/biopsia/delete/')}}/"+id,
        dataType: "json",
        type: 'get',
        data: {
          'id': id,
        },
        success: function(data){
          if(data.status == 'success'){
            location.reload();
          }
          else{
            Swal.fire(`Error`,"error",data.msj);
          }
        }
    });
  }
</script>