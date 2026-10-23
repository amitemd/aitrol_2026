@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
  /* unvisited link */
  a:link {
    color: black;
  }

  /* visited link */
  a:visited {
    color: lightgreen;
  }

  /* mouse over link */
  a:hover {
    color: blue;
  }

  * {
    font-family: var(--fuentePrincipal);
  }
</style>
@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_fellow = Auth::user()->id;
@endphp

<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="box">
      <div class="box-header"></div>
      <div class="box-body">
        <form method="POST" action="{{ route('reporte.certificacion.index') }}">
          {{ csrf_field() }}
          <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="col-md-3 control-label">{{trans('econsultam.Desde')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" value="{{$fecha}}">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '';"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="col-md-3 control-label">{{trans('econsultam.Hasta')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = '';"></i>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="paciente" class="col-md-3 control-label">{{trans('agenda.paciente')}}</label>
            <div class="col-md-9">
              <div class="input-group">
                <select id="paciente" name="paciente" class="form-control input-sm">
                  <option value="">{{trans('agenda.seleccione')}}...</option>
                  @foreach($pacientes as $value)
                    <option @if(!is_null($paciente)) @if($paciente->id == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}} {{$value->apellido2}} </option>
                  @endforeach
                </select>
                <div class="input-group-addon" onclick="borrar_seleccion('paciente')">
                  <i class="glyphicon glyphicon-remove-circle" onclick="borrar_seleccion('paciente')"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="parametro" class="col-md-3 control-label">{{trans('deParametros.Parametros')}}</label>
            <div class="col-md-9">
              <div class="input-group">
                <select id="parametro" name="parametro" class="form-control input-sm">
                  <option value="">{{trans('agenda.seleccione')}}...</option>
                  @foreach($parametros as $value)
                    <option @if(!is_null($parametro_obj)) @if($parametro_obj->id == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
                <div class="input-group-addon" onclick="borrar_seleccion('parametro')">
                  <i class="glyphicon glyphicon-remove-circle" onclick="borrar_seleccion('parametro')"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-1 col-xs-4">
            <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
          </div>

          <div class="form-group col-md-1 col-xs-6">
            <button type="submit" class="btn btn-success btn-sm" formaction="{{ route('reporte.reporteTiempoCertificacion') }}"><span class="fa fa-file-o" aria-hidden="true">&nbsp;Excel</button>
          </div>
        </form>

        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          <div class="table-responsive col-md-12 col-xs-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr style="background:#52B1ED;">
                  <th>{{trans('laboratorio.orden')}} #</th>
                  <th>{{trans('laboratorio.paciente')}}</th>
                  <th>{{trans('laboratorio.examen')}}</th>
                  <th>{{trans('deParametros.Parametros')}}</th>
                  <th>{{trans('laboratorio.fecha_registro')}}</th>
                  <th>{{trans('laboratorio.fecha_lectura')}}</th>
                  <th>{{trans('laboratorio.fecha_certificacion')}}</th>
                  <th>Diff real</th>
                  <th>{{trans('laboratorio.tiempo_estimado')}}</th>
                  <th>Diff estimado</th>
                  <th>Cumple Tiempos</th>
                </tr>
              </thead>
              <tbody>

                @foreach ($datos as $dato)
                <tr>
                  <td>{{$dato->id_orden}}</td>
                  <td>{{$dato->nombre1.' '.$dato->apellido1}}</td>
                  <td>{{$dato->examen}}</td>
                  <td>{{$dato->parametro_nombre}}</td>
                  <td>{{$dato->fecha_registro}}</td>
                  <td>{{$dato->fecha_lectura}}</td>
                  <td>{{$dato->fecha_certificacion}}</td>
                  <td>
                    <?php
                    $diff_tiempo = 0;
                    $ini = new DateTime($dato->fecha_registro);
                    if(!is_null($dato->fecha_certificacion))
                        $fin = new DateTime($dato->fecha_certificacion);
                    else
                        $fin = new DateTime($dato->fecha_lectura);
                    
                    //dd($ini,$fin,$dato->fecha_registro,$dato->fecha_certificacion,$fecha,$fecha_hasta);
                    $diff_tiempo = $ini->diff($fin);
                    $diferencia = "";
                    //dd($diff_tiempo,$ini,$fin);
                    $meses = $diff_tiempo->m;
                    $dias = $diff_tiempo->d;
                    $horas = sprintf("%02d", $diff_tiempo->h);
                    $minutos = sprintf("%02d", $diff_tiempo->i);
                    $segundos = sprintf("%02d", $diff_tiempo->s);
                    if($meses>0)
                        $diferencia = $meses . 'm' . $dias . 'd' . $horas . ':'. $minutos . ':' . $segundos;
                    else if($dias>0)
                        $diferencia = $dias . 'd'. $horas . ':' . $minutos . ':' . $segundos;
                    else if($horas>0)
                        $diferencia = $horas . ':' . $minutos . ':' . $segundos;
                    else if($minutos>0)
                        $diferencia = $minutos . ':' . $segundos;
                    else
                        $diferencia = $segundos . 'seg';
                    
                    $tiempo = trim($dato->tiempos);
                    if(str_contains($dato->tiempos,'-')){
                        $tiempo = trim(explode('-',$dato->tiempos)[1]);
                    }
                    preg_match('/(\d+)\s*(\w+)/', $tiempo, $matches);
                    $num_dias = 0;
                    if(count($matches)==3){
                        $cantidad = intval($matches[1]);
                        $unidad = strtolower($matches[2]);
                        // Mapear las unidades a los intervalos de fecha de PHP
                        switch ($unidad) {
                            case 'dia':
                            case 'dias':
                                $num_dias = $cantidad;
                                break;
                            case 'semana':
                            case 'semanas':
                                $num_dias = $cantidad*7;
                                break;
                            case 'mes':
                            case 'meses':
                                $num_dias = $cantidad*30;
                                break;
                            case 'año':
                            case 'años':
                                $num_dias = $cantidad*365;
                                break;
                            default:
                                // Si la unidad no es reconocida, se asume que es en días
                                $num_dias = $cantidad;
                                break;
                        }
                    }
                    
                    $fin_estimado = new DateTime($dato->fecha_registro);
                    $fin_estimado->modify("+$num_dias days");

                    // Calcular la diferencia entre las fechas
                    $diff_real = $ini->diff($fin);
                    $diff_estimado = $ini->diff($fin_estimado);
                    
                    // Convertir las diferencias a segundos
                    $segundos_real = $diff_real->days * 24 * 3600 + $diff_real->h * 3600 + $diff_real->i * 60 + $diff_real->s;
                    $segundos_estimado = $diff_estimado->days * 24 * 3600 + $diff_estimado->h * 3600 + $diff_estimado->i * 60 + $diff_estimado->s;
                    $diferenciaTiempo = abs($segundos_real - $segundos_estimado);
                    // Convertir la diferencia en segundos a días, horas, minutos y segundos
                    $dias_diferencia = floor($diferenciaTiempo / (3600 * 24));
                    $horas_diferencia = floor(($diferenciaTiempo % (3600 * 24)) / 3600);
                    $minutos_diferencia = floor(($diferenciaTiempo % 3600) / 60);
                    $segundos_diferencia = $diferenciaTiempo % 60;
                    // Comparar las diferencias y mostrar el resultado adecuado
                    if ($dias_diferencia > 0) {
                        $diferenciaIntervalos = "$dias_diferencia días";
                    } elseif ($horas_diferencia > 0) {
                        $diferenciaIntervalos = "$horas_diferencia horas";
                    } elseif ($minutos_diferencia > 0) {
                        $diferenciaIntervalos = "$minutos_diferencia minutos";
                    } else {
                        $diferenciaIntervalos = "$segundos_diferencia segundos";
                    }
                    ?>
                    {{$diferencia}}
                  </td>
                  <td>{{$dato->tiempos}}</td>
                  <td>
                    {{$diferenciaIntervalos}}
                  </td>
                  <td>
                    @if($diff_real->d <= $diff_estimado->d)
                      SI
                    @else
                      NO
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-5 col-xs-12">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
            Mostrando {{1+($datos->currentPage()-1)*$datos->perPage()}} / @if(($datos->currentPage()*$datos->perPage())<$datos->total()){{($datos->currentPage()*$datos->perPage())}} @else {{$datos->total()}} @endif de {{$datos->total()}} registros
          </div>
          </table>
        </div>

        <div class="col-md-7 col-xs-12">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $datos->appends(Request::only(['id_orden','nombre1', 'apellido1', 'parametro', 'fecha_registro', 'fecha_certificacion']))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
    });


    $("#paciente").select2({
      noResults: function() {
        return "No hay resultado....";
      },
      searching: function() {
        return "Buscando...";
      },
      placeholder: "Seleccione" + "...",
      ajax: {
        url: "{{route('reporte.certificacion.buscar_paciente')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        type: "post",
        dataType: "json",
        delay: 250,
        data: function(params) {
          return {
            term: params.term,
          };
        },
        processResults: function(response) {
          return {
            results: response,
          };
        },
        error(error) {
          console.log(error);
        },
        cache: true,
      },
    });

    $("#parametro").select2({
      noResults: function() {
        return "No hay resultado....";
      },
      searching: function() {
        return "Buscando...";
      },
      placeholder: "Seleccione" + "...",
      ajax: {
        url: "{{route('reporte.certificacion.buscar_parametro')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        type: "post",
        dataType: "json",
        delay: 250,
        data: function(params) {
          return {
            term: params.term,
          };
        },
        processResults: function(response) {
          return {
            results: response,
          };
        },
        error(error) {
          console.log(error);
        },
        cache: true,
      },
    });

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false,
      'order': [
        [1, "asc"]
      ]
    });
  });

  function borrar_seleccion(component){
    $('#'+component).val(null).change();
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>
@endsection