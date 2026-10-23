

@extends('adelantado.base')
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
button{
  width: 100%;
}


</style>
@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
  $id_fellow = Auth::user()->id;
@endphp
<div class="container-fluid">
  <div class="row">
<style type="text/css">
    .dt-buttons {
        width: 10% !important;

    }
   </style>
    <div class="col-md-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">

        </div>
        <div class="box-body">
          <form method="POST" action="{{ route('adelantado.search') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="proc_consul" class="col-md-3 control-label">Tipo</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="proc_consul" id="proc_consul" onchange="buscar();">
                  <option @if($proc_consul=='2') selected @endif value="2" >Todos</option>
                  <option @if($proc_consul=='0') selected @endif value="0" >Consultas</option>
                  <option @if($proc_consul=='1') selected @endif value="1" >Procedimientos</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha" class="col-md-3 control-label">Desde</label>
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

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
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
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">Cédula</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" >
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">Paciente</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_seguro" class="col-md-3 control-label">Seguro</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
                  <option value="">Seleccione ...</option>
                @foreach($seguros as $seguro)
                  <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach
                </select>
              </div>
            </div>
             <div class="form-group col-md-10"  >

            </div>



           <div class="form-group col-md-1 col-xs-4"  >
                <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar" >
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </div>


            <!--div class="form-group col-md-1 col-xs-6" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('consultam.reporte_excel') }}"><span class="fa fa-download" aria-hidden="true"> Excel</button>
            </div-->

            <!--div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('adelantado.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar</button>
            </div-->

          </form>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
         <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th>FECHA CONSULTA</th>
                    <th>CEDULA</th>
                    <th>PACIENTE</th>
                    <th>SEGURO</th>
                     <th>SEGURO POR GESTIONAR</th>
                    @if($proc_consul=='0' ||$proc_consul=='1' || $proc_consul=='2')<th>PROCEDIMIENTO SOLICITADO</th>@endif
                    <th>FECHA DE PROCEDIMIENTO PROGRAMADO</th>
                    <th>MEDICO QUE REFIERE</th>
                    <th>TIPO DE TRATAMIENTO</th>
                    <th>TIPO CONVENIO</th>
                    <th>RESPONSABLE</th>
                    <th>MEDICO GENERAL</th>
                    <th>CODIGO</th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($agendas as $agenda)
                @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
                      $p_color1="black"; if($agenda->estado_cita != 0){ if($agenda->paciente_dr == 1) { $p_color1=$agenda->d1color; } else{ $p_color1=$agenda->scolor;} };
                      $p_color2="black"; if($agenda->d1color!=''){ $p_color2=$agenda->d1color;}
                      $historia_clinica = Sis_medico\Historiaclinica::where('id_agenda',$agenda->id)->first();
                      $empresa=null;
                      if($agenda->id_empresa!=null){
                        $empresa = Sis_medico\Empresa::find($agenda->id_empresa);
                      }


                 @endphp
                  <tr >
                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->created_at,0,10) }}</a></td>
                     <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->id_paciente }}</a></td>
                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->pnombre1 }} @if($agenda->pnombre2=='N/A'||$agenda->pnombre2=='(N/A)') @else{{ $agenda->pnombre2 }} @endif {{ $agenda->papellido1 }} @if($agenda->papellido2=='N/A'||$agenda->papellido2=='(N/A)') @else{{ $agenda->papellido2 }} @endif</a>@if($agenda->vip=='1')<span class="alert-danger" style="padding: 1px;">VIP</span>@endif <span style="padding: 1px;"></span> @if($agenda->paciente_dr=='1')<span class="alert-success" style="padding: 1px;">PART</span>@endif @if($agenda->cortesia=='SI')<span class="alert-warning" style="padding: 1px;">CORTESIA</span>@endif</td>
                  @php
                        $hc_seguro = null;
                        $hc_proc = null;
                        $hc = null;

                        $hc = Sis_medico\historiaclinica::where('id_agenda',$agenda->id)->first();

                        if(!is_null($hc)){
                           $hc_proc = Sis_medico\hc_procedimientos::where('id_hc',$hc->hcid)->first();

                        }

                        if(!is_null($hc_proc)){

                          if($hc_proc->id_seguro!=null){

                            $hc_seguro = Sis_medico\Seguro::find($hc_proc->id_seguro)->nombre;
                          }
                        }
                        $consulta = Sis_medico\Agenda::where('fechaini', '<=', $agenda->created_at)
                          ->where('id_paciente', $agenda->id_paciente)
                          ->where('espid', '<>', 8)
                          ->where('id_doctor1', '<>', '4444444444')
                          ->orderBy('fechaini', 'Desc')
                          ->first();

                    @endphp

                 <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">

                   @if(is_null($historia_clinica))
                        {{$agenda->senombre }}

                        @if($agenda->consultorio==1 && $agenda->senombre=='IESS')
                        <br/> IESS CONSULTORIO
                        @endif

                      @else
                        @if($agenda->omni=='OM')
                          {{$hc_seguro}}
                        @else
                          {{$agenda->senombre}}
                        @endif

                        @if($agenda->consultorio==1 && $agenda->senombre=='IESS')
                        <br/> IESS CONSULTORIO
                        @endif
                      @endif


                    </a>
                  </td>
                  @php

                    $seguro_gestion = Sis_medico\Seguro::find($agenda->seguro_gestionado);
                
                  @endphp

                  <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">
  
                   @if(!is_null ($seguro_gestion)){{$seguro_gestion->nombre}} @endif
                    </a>
                  </td>


                  <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">
                      @if($agenda->proc_consul=='0')
                        @if($agenda->espid=='8')
                          INTERCONSULTA CARDIOLOGICA
                        @else
                          CONSULTA
                        @endif
                      @elseif($agenda->proc_consul=='1')
                          @if(!is_null($agenda->probservacion))
                            {{$agenda->probservacion}}
                          @endif
                          @if(!$agprocedimientos->isEmpty())
                            @foreach($agprocedimientos as $agendaproc)
                            + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}}
                            @endforeach
                          @endif
                      @else
                        @if($agenda->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                          @if($agenda->omni=='OM')
                            VISITA OMNI
                          @else
                            VISITA
                          @endif
                        @endif
                      @endif
                    </a>
                  </td>

                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->fechaini,0,10) }}</a></td>


                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">@if(!is_null($consulta)) Dr. {{$consulta->doctor1->nombre1}} {{$consulta->doctor1->apellido1}} @endif</a></td>


                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};"> @if($agenda->tipo_cita=='1') Consecutivo @else ($agebda->tipo_cita=='0') Primera vez @endif</a></td>

                     @php
                        $hc_seguro = null;
                        $hc_proc = null;
                        $hc = null;

                        $hc = Sis_medico\historiaclinica::where('id_agenda',$agenda->id)->first();

                        if(!is_null($hc)){
                           $hc_proc = Sis_medico\hc_procedimientos::where('id_hc',$hc->hcid)->first();

                        }

                        if(!is_null($hc_proc)){

                          if($hc_proc->id_seguro!=null){

                            $hc_seguro = Sis_medico\Seguro::find($hc_proc->id_seguro)->nombre;
                          }
                        }

                    @endphp

                 <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">

                      @if($empresa!=null)
                          {{$empresa->nombre_corto}}
                        @endif


                    </a></td>

                    <td><a href="{{ route('adelantado.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">{{ substr($agenda->aunombre1,0,1) }}{{ $agenda->auapellido1 }}</a></td>

                    <td >
                    @if($agenda->proc_consul == '0')
                      @php
                      $hist_clinica = Sis_medico\Historiaclinica::where('id_agenda',$agenda->id)->first();
                      @endphp
                      @if(!is_null($hist_clinica))
                        @if(!is_null($hist_clinica->hc_procedimientos))
                          @if(!is_null($hist_clinica->hc_procedimientos->id_doctor_examinador))
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $hist_clinica->hc_procedimientos->doctor->nombre1 }} {{ $hist_clinica->hc_procedimientos->doctor->apellido1 }}</a>
                          @elseif(!is_null($hist_clinica->id_doctor1))
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $hist_clinica->doctor_1->nombre1 }} {{ $hist_clinica->doctor_1->apellido1 }}</a>
                          @else
                            <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                          @endif
                        @else
                          <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                        @endif
                      @else
                         <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                      @endif
                    @else
                      <a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a>
                    @endif
                    </td>
                    <td></td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
         </div>
      </div>
    </div>
  </div>
</div>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

  <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">


  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha}}',

            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });
  });


  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });


  $('#example2').DataTable({
      dom: 'Bfrtip',
      paging    : true,
      "pageLength": 25,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]],
      buttons: [
                'excelHtml5',

            ]
    });


function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}


 </script>

@endsection
