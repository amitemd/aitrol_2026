@extends('consultam.base')
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
          <form method="POST" action="{{ route('consultam.search_reporte_tiempo') }}">
            {{ csrf_field() }}
            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha" class="col-md-3 control-label">{{trans('econsultam.Desde')}}</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
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
                  <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="analisis" class="col-md-3 control-label">{{trans('econsultam.analisis')}}</label>
              <div class="col-md-9">
                  <select class="form-control input-sm" name="analisis" id="analisis">
                    <option @if($analisis == 0) selected @endif value="0" >{{trans('econsultam.seleccione')}}...</option>
                    <option @if($analisis == 1) selected @endif value="1" >{{trans('econsultam.si')}}</option>
                    <option @if($analisis == 2) selected @endif value="2" >{{trans('econsultam.no')}}</option>
                  </select>
              </div>
            </div>

            <div class="form-group col-md-1 col-xs-4">
              <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </div>

            <div class="form-group col-md-1 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('consultam.reportetiempo') }}"><span class="fa fa-clock-o" aria-hidden="true">{{trans('econsultam.Tiempos')}}</button>
            </div>
          </form>

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr style="background:#52B1ED;" >
                    <th>{{trans('econsultam.fecha_agenda')}} </th>
                    <th>{{trans('econsultam.Cédula')}}</th>
                    <th>{{trans('econsultam.Paciente')}}</th>
                    <th>{{trans('econsultam.Doctor')}}</th>
                    <th>{{trans('econsultam.hora_consulta')}}</th>
                    <th>{{trans('econsultam.hora_toma_turno')}}</th>
                    <th>{{trans('econsultam.hora_admision')}}</th>
                    <th>{{trans('econsultam.analisis')}}</th>
                    <th>{{trans('econsultam.hora_entrada_consulta')}}</th>
                    <th>{{trans('econsultam.fin_consulta')}}</th>
                    <th>{{trans('econsultam.tiempo_consulta')}}</th>
                    <th>{{trans('econsultam.tiempo_espera')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @php $filas =0; @endphp
                  @foreach ($agendas as $dato)
                    @php
                      //$fechaini = explode(' ', $dato->fechaini);
                      $fechaini = date_format(new DateTime($dato->fechaini), 'Y-m-d');
                      $horaini = date_format(new DateTime($dato->fechaini), 'H:i');
                      $fechainimostrar = $horaini;
                      $duracion = strtotime($dato->hora_inicio) - strtotime($fechaini[1]);
                      $duracion = round($duracion * 100 / 60) / 100;
                      $suma = 0;
                      $total = 0;
                      // dd($duracion);
                      if ($duracion > 0) {
                        $total = $duracion + $total;
                        $suma = $duracion;
                      }
                      $dias_total = date("H:i", strtotime("00:00"));
                      $dias_total = date("H:i", strtotime("+" . intval($suma) . " minutes", strtotime($dias_total)));

                      //$fechainimostrar = explode(':', $fechaini[1]);
                      
                      if (is_null($dato->hora_inicio)) {

                      }
                      //$horainiciomostrar = explode(':', $dato->hora_inicio);
                      $horainiciomostrar = date_format(new DateTime($dato->hora_inicio), 'H:i');
                      if (!is_null($dato->hora_fin)) {
                        $horafinmostrar = explode(':', $dato->hora_fin);
                      }
                      
                      //$agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$dato->id)->get();
                      $p_color1="black"; if($dato->estado_cita != 0){ if($dato->paciente_dr == 1) { $p_color1=$dato->d1color; } else{ $p_color1=$dato->scolor;} };
                      $p_color2="black"; if($dato->d1color!=''){ $p_color2=$dato->d1color;}
                      $historia_clinica = Sis_medico\Historiaclinica::where('id_agenda',$dato->id)->first();
                      $empresa=null;
                      if($dato->id_empresa!=null){
                        $empresa = Sis_medico\Empresa::find($dato->id_empresa);
                      }
                      $hora_consulta = date_format(new DateTime($dato->fechaini), 'H:i');
                      if(!is_null($dato->hora_turno)){
                        $hora_turno = date_format(new DateTime($dato->hora_turno), 'H:i:s');
                      }
                      else{
                        $hora_turno = date_format(new DateTime('00:00'), 'H:i');
                      }
                      $hora_admision =  date_format(new DateTime($dato->hora_admision), 'H:i');
                      $tiempo_consulta = '';
                      if(!is_null($dato->hora_fin)){
                          $fin = new DateTime($dato->hora_fin);
                          $ini = new DateTime($dato->hora_inicio);
                          $tiempo_consulta = $fin->diff($ini);
                          $tiempo_consulta = str_pad($tiempo_consulta->h,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->i,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->s,2,'0',STR_PAD_LEFT);
                      }
                      if($analisis == 1){
                          $condicion = $hora_turno < $fechainimostrar;
                      }
                      else if($analisis == 2){
                          $condicion = $hora_turno > $fechainimostrar;
                      }
                      else{
                          $condicion = $hora_turno != $fechainimostrar;
                      }
                    @endphp
                    @if($condicion)
                      <tr @if($dias_total > '00:00') style="background:#D6F6FA;" @endif>
                        <td>{{ substr( $dato->fechaini, 0, 10 ) }}</td>
                        <td>{{$dato->id_paciente}}</td>
                        <td>{{$dato->nombre1 . ' ' . $dato->nombre2 . ' ' . $dato->apellido1 . ' ' . $dato->apellido2}}</td>
                        <td>
                          @if($dato->proc_consul == '0')
                          @php
                          $hist_clinica = Sis_medico\Historiaclinica::where('id_agenda',$dato->ag_id)->first();
                          @endphp
                          @if(!is_null($hist_clinica))
                          @if(!is_null($hist_clinica->hc_procedimientos))
                          @if(!is_null($hist_clinica->hc_procedimientos->id_doctor_examinador))
                          {{ $hist_clinica->hc_procedimientos->doctor->nombre1 }} {{ $hist_clinica->hc_procedimientos->doctor->apellido1 }}
                          @elseif(!is_null($hist_clinica->id_doctor1))
                          {{ $hist_clinica->doctor_1->nombre1 }} {{ $hist_clinica->doctor_1->apellido1 }}
                          @else
                          {{ $dato->dnombre1 }} {{ $dato->dapellido1 }}
                          @endif
                          @else
                          {{ $dato->dnombre1 }} {{ $dato->dapellido1 }}
                          @endif
                          @else
                          {{ $dato->dnombre1 }} {{ $dato->dapellido1 }}
                          @endif
                          @else
                          {{ $dato->dnombre1 }} {{ $dato->dapellido1 }}
                          @endif
                        </td>
                        <td>{{$hora_consulta}}</td>
                        <td>{{$hora_turno}}</td>
                        <td>{{$hora_admision}}</td>
                        <td>
                          @if($hora_turno <= $horainiciomostrar)
                            {{trans('econsultam.si')}}
                          @else
                            {{trans('econsultam.no')}}
                          @endif
                          </td>
                        <td>{{$horainiciomostrar}}</td>

                        @if(!is_null($dato->hora_fin))
                        <td>{{$horafinmostrar[0] . ':' . $horafinmostrar[1]}}</td>
                        @else
                        <td></td>
                        @endif
                        <td>{{$tiempo_consulta}}</td>
                        <td>{{$dias_total}}</td>
                      </tr>
                    @else
                      
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-5 col-xs-12">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($agendas->currentPage()-1)*$agendas->perPage()}} / @if(($agendas->currentPage()*$agendas->perPage())<$agendas->total()){{($agendas->currentPage()*$agendas->perPage())}} @else {{$agendas->total()}} @endif de {{$agendas->total()}} registros</div>
            </table>
          </div>

          <div class="col-md-7 col-xs-12">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $agendas->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
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

<script type="text/javascript">
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',


      defaultDate: '{{$fecha}}',

    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',


      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha").on("dp.change", function(e) {
      buscar();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      buscar();
    });
  });


  $('#editMaxPacientes').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
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


  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>
@endsection