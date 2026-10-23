
@extends('hc_admision.fullcontrol.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>

<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>

@php $tipo_usuario = Auth::user()->id_tipo_usuario; @endphp
<script type="text/javascript">  

    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
            $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',            
            minDate: '{{$fecha_hasta}}',
            });

        $("#fecha_hasta").on("dp.change", function (e) {
            fechacalendario();
        });
        
        $(".clickable-row").click(function() {
            window.location = $(this).data("href");
        });
    });    

    $(function() { // document ready
        
          
    });


    function fechacalendario() {
        var dato = document.getElementById('fecha').value;
        $('#enviar_fecha').click();
    }

     

    var vartiempo = setInterval(function(){ location.reload(); }, 300000);
</script>

<style>

  body {
    margin: 0;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    /*max-width: 900px;*/
    margin: 50px auto;
  }

  .table-hover>tbody>tr:hover{
          background-color: #ccffff !important;
        }

</style>

<section class="content" >
    
    <div class="box">
        <div class="box-header">
            <div class="form-group col-md-12" >
                <!--label class="col-md-1 control-label">Fecha</label-->
                <div class="col-md-12">
                    <!--form method="POST" action="{{ route('historia_clinica.fullcontrol')}}" > 
                        {{ csrf_field() }}
                        <div class="col-md-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="" name="fecha" class="form-control input-sm" id="fecha" onchange="fechacalendario();" autocomplete="off">
                            </div>    
                        </div>
                        
                        <input type="submit" id="enviar_fecha" style="display: none;">
                        
                    </form-->
                    <form method="POST" action="{{ route('historia_clinica.fullcontrol') }}" >
                      {{ csrf_field() }}
                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                          <label for="fecha" class="col-md-3 control-label" style="padding:0px;">Desde</label>
                          <div class="col-md-9">
                              <div class="input-group date">
                                  <div class="input-group-addon">
                                      <i class="fa fa-calendar"></i>
                                  </div>
                                  <input type="text" class="form-control input-sm" name="fecha" id="fecha">
                                  <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                                  </div>   
                              </div>
                          </div>  
                      </div>

                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                          <label for="fecha_hasta" class="col-md-3 control-label" style="padding-left: 0;" >Hasta</label>
                          <div class="col-md-9">
                              <div class="input-group date">
                                  <div class="input-group-addon">
                                      <i class="fa fa-calendar"></i>
                                  </div>
                                  <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                                  <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                                  </div>   
                              </div>
                          </div>  
                      </div>

                      <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                        <label for="nombres" class="col-md-3 control-label">Paciente</label>
                        <div class="col-md-9">
                          <div class="input-group">
                            <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                            </div>
                          </div>  
                        </div>
                      </div>
                   
                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                        
                        <label for="proc_consul" class="col-md-3 control-label">Tipo</label>
                        <div class="col-md-9">
                          <select class="form-control form-control-sm input-sm" name="proc_consul" id="proc_consul">
                            <option @if($proc_consul=='2') selected @endif value="2" >Todos</option>
                            <option @if($proc_consul=='0') selected @endif value="0" >Consultas</option>
                            <option @if($proc_consul=='1') selected @endif value="1" >Procedimientos</option>
                            <option @if($proc_consul=='3') selected @endif value="3" >Visita OMNI</option> 
                            <option @if($proc_consul=='4') selected @endif value="4" >Procedimiento OMNI</option>   
                          </select>
                        </div>                      
                       
                      </div>

                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                        
                          <label for="espid" class="col-md-3 control-label" style="padding-left: 0;">Especialidad</label>
                          <div class="col-md-9">
                            <select class="form-control form-control-sm input-sm" name="espid" id="espid">
                              <option value="">Todos ...</option>
                            @foreach($especialidades as $especialidad)
                              <option @if($especialidad->id==$id_especialidad) selected @endif value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                            @endforeach  
                            </select>
                          </div>                    
                        
                      </div>

                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;" >
      
                          <label for="id_doctor1" class="col-md-3 control-label">Doctor</label>
                          <div class="col-md-9">
                            <select class="form-control form-control-sm input-sm" name="id_doctor1" id="id_doctor1">
                              <option value="">Seleccione ...</option>
                            @foreach($doctores as $doctor)
                              <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->nombre1}}</option>
                            @endforeach  
                            </select>
                          </div>      
                      
                      </div>

                      <div class="form-group col-md-3 col-xs-6" >

                          <label for="id_seguro" class="col-md-3 control-label">Seguro</label>
                          <div class="col-md-9">
                            <select class="form-control form-control-sm input-sm" name="id_seguro" id="id_seguro">
                              <option value="">Seleccione ...</option>
                            @foreach($seguros as $seguro)
                              <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                            @endforeach  
                            </select>
                          </div>                  

                      </div>

                      <div class="form-group col-md-1 col-xs-2" >
                        <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                          <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
                      </div>

                      <div class="form-group col-md-3 col-xs-6">
                        <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('historia_clinica.reporte_hc')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Historia Clinica</button>
                      </div>

                      <div class="form-group col-md-3 col-xs-6" >
                        <button type="submit" class="btn btn-success btn-sm" formaction="{{route('historia_clinica.reporte_hc_iess')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"> Historia Clinica IESS</button>
                      </div>
                     
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive col-md-12 col-xs-12">
          <table id="example2" class="table table-striped table-hover" style="font-size: 12px;">
            @if($agendas_pac!=[])
            <thead>
              <th width="5%">Cédula</th>
              <th width="15%">Apellidos</th>
              <th width="15%">Nombres</th>
              <th width="10%">Fecha Nacimiento</th>
              <th width="5%">Seguro/Convenio</th>
              @if($nombres!=null)
              <!--th width="30%">Última visita</th-->
              @else
              
              @endif
              <th width="15%">Doctor</th>
              <th width="15%">Hora</th>
              <th width="15%">Tipo</th>
              <th width="20%">Cortesia</th>
              <th width="20%">Estado</th>
            </thead>
            <tbody>
              @php $nomb_proc = null; $agprocedimientos  = null; $apellidos = null; @endphp
            @foreach ($agendas_pac as $pac)
              <?php /* 
              @if($nombres!=null)
                @php 

                  $agenda_last= DB::table('agenda as a')
                  ->where('a.id_paciente',$pac->id)
                  ->join('historiaclinica as h','h.id_agenda','a.id')
                  ->where('a.espid','<>','10')
                  ->orderBy('a.fechaini','desc')
                  ->join('seguros as s','s.id','h.id_seguro')
                  ->join('empresa as em','em.id','a.id_empresa')
                  ->select('h.*','s.nombre','a.fechaini','a.proc_consul','a.cortesia','em.nombre_corto')
                  ->first();

                  if(!is_null($agenda_last)){
                        $dia =  Date('N',strtotime($agenda_last->fechaini)); $mes =  Date('n',strtotime($agenda_last->fechaini));    
                  }else{
                    $dia = 0; $mes= 0;  
                  }
                  //dd($agenda_last);
                  $hc_proc_p = Sis_medico\Aud_Hc_Procedimientos::where('id_hc',$pac->hcid)->first();

                @endphp
              @endif  */ ?>

              @php
                $contador_cie10 = 0;
                /*
                if($nombres!=null){
                  if(!is_null($agenda_last)){
                    $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$agenda_last->hcid)->get()->count();  
                  }
                }else{
                  $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();
                }*/
                $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();

                $tipo_usuario = Auth::user()->id_tipo_usuario;

                $hc_proc_p = Sis_medico\Aud_Hc_Procedimientos::where('id_hc',$pac->hcid)->first();

                $agenda_last= DB::table('agenda as a')
                  ->where('a.id_paciente',$pac->id)
                  ->join('historiaclinica as h','h.id_agenda','a.id')
                  ->where('a.espid','<>','10')
                  ->orderBy('a.fechaini','desc')
                  ->join('seguros as s','s.id','h.id_seguro')
                  ->join('empresa as em','em.id','a.id_empresa')
                  ->select('h.*','s.nombre','a.fechaini','a.proc_consul','a.cortesia','em.nombre_corto')
                  ->first();
              //dd($hc_proc_p);
              @endphp
              <?php /*
              @if($nombres!=null) 
                @if(!is_null($agenda_last))
                  <tr @if($tipo_usuario == '1') 
                          @if ($hc_proc_p != null) href="{{ route('auditoria_admision.duplicar_registros', ['id' => $agenda_last->id_agenda])}}' ]) }}" 
                          @endif
                          onclick="javascript:confirmar_duplicado('{{$agenda_last->id_agenda}}');"  
                          @else class='clickable-row' 
                          data-href='{{ route("agenda.detalle", ['id' => $agenda_last->id_agenda])}}' 

                  @endif @if($agenda_last->cortesia=='SI') style="background-color: #ccffcc;" @endif>
                @else
                  <tr>  
                @endif
              @else
                <tr  @if($tipo_usuario == '1') 

                  @if ($hc_proc_p!=null) onclick="javascript:confirmado_duplicado('{{$pac->id_agenda}}');"
                    @else
                    onclick="javascript:confirmar_duplicado('{{$pac->id_agenda}}');"
                  @endif 
                  @else class='clickable-row' 
                  data-href='{{ route("agenda.detalle", ['id' => $pac->id_agenda])}}' 
                
                @endif  @if($pac->cortesia=='SI') style="background-color: #ccffcc;" 
                @endif>
              @endif  */ ?>
              <tr  @if($tipo_usuario == '11') 
                  @if($pac->hcid!=null)
                    @if ($hc_proc_p!=null) onclick="javascript:confirmado_duplicado('{{$pac->id_agenda}}');"
                      @else
                      onclick="javascript:confirmar_duplicado('{{$pac->id_agenda}}');"
                    @endif 
                  @else
                    onclick="javascript:alert('Agenda no Admisionada');"  
                  @endif  
                  @else class='clickable-row' 
                  data-href='{{ route("agenda.detalle", ['id' => $pac->id_agenda])}}' 
                
                @endif  @if($pac->cortesia=='SI') style="background-color: #ccffcc;" 
                @endif>
                <td>{{$pac->id}}</td>
                <td>{{$pac->apellido1}} {{$pac->apellido2}}</td>
                <td>{{$pac->nombre1}} {{$pac->nombre2}}</td>
                <td>{{$pac->fecha_nacimiento}} </td>

                @php
                  $hc_seguro = ''; 
                  /*if($nombres==null){*/

                    $nombre_seg = $seguros->find($pac->seguro_nom)->nombre; 
                    $hc_proc = Sis_medico\hc_procedimientos::where('id_hc',$pac->hcid)->first();
                  
                    $hc_proc_p = Sis_medico\Aud_Hc_Procedimientos::where('id_hc',$pac->hcid)->first();
                    if(!is_null($hc_proc)){
                      if($hc_proc->id_seguro!=null){
                        $hc_seguro = Sis_medico\Seguro::find($hc_proc->id_seguro)->nombre;
                      }
                      
                    }
              
                  /* } */
                @endphp
                <?php /*
                <td>@if($nombres!=null) @if(!is_null($agenda_last)) {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}} @endif @else @if($pac->omni=='OM') {{$hc_seguro}}/{{$pac->nombre_corto}} @else {{$nombre_seg}}/{{$pac->nombre_corto}} @endif @endif</td> */ ?>
                <td>@if($pac->omni=='OM') {{$hc_seguro}}/{{$pac->nombre_corto}} @else {{$nombre_seg}}/{{$pac->nombre_corto}} @endif</td> 
                <?php /*
                @if($nombres!=null)
                  <td>@if(!is_null($agenda_last)) @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda_last->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda_last->fechaini,0,4)}} @endif</td>
                @else
                  @php $doctor = $doctores->find($pac->doctor); @endphp
                  <td>@if(!is_null($doctor)) {{$doctor->apellido1}} {{$doctor->nombre1}} @endif</td>
                  <td @if($fecha!=$fecha_hasta) style="font-size: 11px;" @endif>@if($fecha!=$fecha_hasta) {{substr($pac->fechaini,0,10)}}: @endif {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}</td>
                @endif */ ?>

                @php $doctor = $doctores->find($pac->doctor); $procedimiento_nombre = null; @endphp
                <td>@if(!is_null($doctor)) {{$doctor->apellido1}} {{$doctor->nombre1}} @endif</td>
                <td @if($fecha!=$fecha_hasta) style="font-size: 11px;" @endif>@if($fecha!=$fecha_hasta) {{substr($pac->fechaini,0,10)}}: @endif {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}</td>

                <?php /*@if($nombres!=null)
                  @php $tipo_u = Auth::user()->id_tipo_usuario; @endphp
                  <td><b>@if(!is_null($agenda_last)) @if($agenda_last->proc_consul=='0')CONSULTA @elseif($agenda_last->proc_consul=='1')PROCEDIMIENTO @elseif($agenda_last->proc_consul=='4') VISITA @endif @else @if($tipo_u=='3')<a href="{{route('sin_agenda.crear_evolucion',['id' => $pac->id, 'ag' => 'no' ])}}"><button class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-plus"></span> Agregar Visita</button></a>@endif @endif</b></td>
                  <td style="font-size: 11px;"><b>@if(!is_null($agenda_last))@if($agenda_last->cortesia=='SI')<span style="color: red"> SI</span> @else NO @endif @endif</b></td>
                  <td style="font-size: 11px;">
                    <b>
                      @if($contador_cie10 >'1')
                        ATENDIDO
                      @else
                        NO ATENDIDO
                      @endif 
                    </b>
                  </td>
                @else */ ?>
                  <td style="font-size: 11px;">
                    <b>           

                    <!--  -->

                    @if($pac->proc_consul=='0')
                      CONSULTA 
                    @elseif($pac->proc_consul=='1') 
                      @if(isset($agendas_proc[$pac->id_agenda])) 
                        {{$agendas_proc[$pac->id_agenda]['0']}} 
                      @else 
                        @php
                            if($nombres!=null) {
                                $nomb_proc = \Sis_medico\Procedimiento::where('id',$pac->id_procedimiento)->first();
                                $agprocedimientos = \Sis_medico\AgendaProcedimiento::where('id_agenda',$pac->id_agenda)->get();
                            }
                        @endphp
                        @if(!is_null($nomb_proc))
                          {{$nomb_proc->nombre}}
                        @endif
                        @if(!is_null($agprocedimientos))
                          @foreach($agprocedimientos as $agendaproc)
                            + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} 
                          @endforeach 
                        @endif
                      @endif 
                    @else
                      @if($pac->observaciones == 'PROCEDIMIENTO CREADO POR EL DOCTOR')  
                        @php
                          if(!(($nombres!=null)||($apellidos!=null))) {
                            if($pac->hcid != ''){

                              $hc_proce = \Sis_medico\hc_procedimientos::where('id_hc', $pac->hcid)->first();

                              if(!is_null($hc_proce)){ 
                                $proc_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_proce->id)->first();
                              }

                              if(!is_null($proc_final)){ 
                                $procedimiento_nombre = \Sis_medico\Procedimiento::where('id', $proc_final->id_procedimiento)->first();
                              }
                            }
                          }
                        @endphp
                        @if(!is_null($procedimiento_nombre)) 

                          {{$procedimiento_nombre->nombre}} 
                        @else
                          PROCEDIMIENTO 
                        @endif
                      @elseif($pac->observaciones == 'EVOLUCION CREADA POR EL DOCTOR')
                          @if($pac->omni=='OM')
                            VISITA OMNI
                          @else
                            VISITA
                          @endif
                      @endif
                    @endif
                    
                    </b>
                  </td>
                  
                  <td style="font-size: 11px;"><b>@if($pac->cortesia=='SI')<span style="color: red"> SI</span> @else NO @endif</b></td>
                  <td style="font-size: 11px;">
                    <b>
                      
                      @if($pac->omni=='OM')
                        @if($pac->estado_cita=='4')
                          INGRESO
                        @elseif($pac->estado_cita=='5')
                          ALTA
                        @elseif($pac->estado_cita=='6')
                          EMERGENCIA  
                        @endif
                      @else
                        @if($contador_cie10 >= '1')
                          ATENDIDO
                        @else
                          NO ATENDIDO
                        @endif

                      @endif
                                      
                    </b>
                  </td>
                  
                
                <?php /* @endif */ ?>    
              </tr>
            @endforeach
            </tbody>
            @endif
          </table>
          {{$agendas_pac->count()}} registros
        </div>  
            <!--div id="calendar" ></div-->
        </div>
    </div>
</section>

<script type="text/javascript">
    $(function () {
      $('#example2').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : false,
          'ordering'    : true,
          'info'        : false,
          'autoWidth'   : false,
          @if($tipo_usuario == '11') 
          'order'       : [[ 6, "desc" ]]
          @else
          'order'       : [[ 6, "asc" ]]
          @endif
        });
    });  

    function confirmar_duplicado(id_agenda){
      
        //alert("entra");
        swal.fire({
          title: 'Está seguro que desea duplicar los registros?',
          //text: "You won't be able to revert this!",
          icon: "warning",
          type: 'warning',
          showCancelButton: true,
          cancelButtonColor: '#d33',
          buttons: true,
        
        }).then((result) => {
          if (result.value) {
            duplicar_registros(id_agenda);
          }
        })
        
    }

    function duplicar_registros(id_agenda){
      $.ajax({
        type: 'get',
        datatype: 'json',
        url : "{{url('auditoria/admision/duplicar_registros')}}/"+id_agenda,
        success: function(data){
          if(data.estado == "ok"){
            location.href = "{{url('auditoria_agenda/horario/doctores')}}/"+data.id_agenda;
          }
        },
        error: function(data){
          //console.log(data);
        }
      });
    }

    function confirmado_duplicado(id_agenda){

      $.ajax({
        type: 'get',
        datatype: 'json',
        url : "{{url('auditoria/admision/duplicar_registros')}}/"+id_agenda,
        success: function(data){
          if(data.estado == "ok"){
            location.href = "{{url('auditoria_agenda/horario/doctores')}}/"+data.id_agenda;
          }
        },
        error: function(data){
          //console.log(data);
        }
      });
    }


</script>


@endsection
