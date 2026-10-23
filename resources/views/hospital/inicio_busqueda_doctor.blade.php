<style type="text/css">

.btn-detalle{
    font-size: 10px ;
    width: 100%;
    height: 100%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
}
#reuniones .row{
  width: 100%;
}
.row_completo .row{
  width: 100%;
}
</style>

 
<div class="box box" style="border-radius: 8px;" id="area_busqueda_doctor">
  <div class="box-header with-border" style="background-color: #004AC1;color: white; font-size: 14px; padding: 8px;">
    <div class="row">
      <div class="col-4">
        <span>RESULTADO DE LA B&Uacute;SQUEDA</span>
      </div>
      <div class="col-3">
        <button type="button" id="agenda_semana" class="btn btn-danger" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> Agenda por semanas\mes</button>
      </div>
      <div class="col-3" style="text-align: center;">
            <a class="btn btn-danger" onclick="cargar_nuevopaciente();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;">  Agregar Nuevo Paciente</a>
      </div>
    </div>        
  </div>
  <div class="box-body" style="border: 2px solid #004AC1;padding-left: 0px;padding-right: 0px;" id="modificar">
    <div class=" col-md-12" > 
      @if($agendas_reuniones->count() > 0)
      <div class="box box-primary" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#reu').click());"><b>Reuniones</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>tiene {{$agendas_reuniones->count()}} agendadas en el día</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="reu">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">
            <div class="table-responsive col-md-12">
              <table  id="reuniones" class="table table-hover dataTable" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_reuniones!=[])
                <thead >
                  <tr >
                      <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >Tipo</th>
                      <th width="55%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >Nombre</th>
                      <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" scope="col" class="color titulo" >Lugar</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_reuniones as $pac)
                  <tr role="row" class="odd">
                    <td class="color">{{$pac->procedencia}}</td>
                    <td class="color">{{$pac->observaciones}}</td>
                    @php
                      $sala = \Sis_medico\Sala::find($pac->id_sala);
                    @endphp
                    <td class="color"> {{$sala->nombre_sala}} / {{$sala->hospital->nombre_hospital}}</td>

                  </tr> 
                  @endforeach
                </tbody>
                @endif
              </table>
            </div> 
          </div>
        </div>  
      </div> 
      @endif
      @if($agendas_pac->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#consult').click());"><b>Consultas</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>tiene {{$agendas_pac->count()}} agendadas en el día</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="consult">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">

            <div class="table-responsive col-md-12">
              <table  id="example2{{rand()}} row_completo" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_pac!=[])
                <thead style="">
                  <tr style=" ">
                      <th scope="col" class="color titulo" >C&eacute;dula</th>
                      <th scope="col" class="color titulo" >Apellidos</th>
                      <th scope="col" class="color titulo" >Nombres</th>
                      <th scope="col" class="color titulo" >Fecha Nacimiento</th>
                      <th scope="col" class="color titulo" >Seguro/Convenio</th>
                      <th scope="col" class="color titulo" >Doctor</th>
                      <th scope="col" class="color titulo" >Hora</th>
                      <th scope="col" class="color titulo" >Tipo</th>
                      <th scope="col" class="color titulo" >Cortesia</th>                
                      <th scope="col" class="color titulo" >Estado</th>
                      <th scope="col" class="color titulo" >Acci&oacute;n</th> 
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac as $pac)
                    
                    <?php $agenda_last= DB::table('agenda as a')
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
                    ?>

                    <tr>
                    <td class="color">{{$pac->id}}</td>
                    <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
                    <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
                    <td class="color">{{$pac->fecha_nacimiento}}</td>
                    <td class="color">
                      @if(!is_null($agenda_last)) 
                        {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                      @endif
                    </td>
                    <td class="color">{{$pac->dnombre1}} {{$pac->dapellido1}}</td>
                    <td class="color">
                      {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}
                    </td>
                    <td class="color">
                      @if($pac->proc_consul=='0')
                        CONSULTA 
                      @elseif($pac->proc_consul=='1') 
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}} 
                        @else 
                          PROCEDIMIENTO 
                        @endif 
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red"> 
                          SI
                         </span> 
                        @else
                         <span class="color"> 
                          NO
                         </span> 
                        @endif
                      </b>
                    </td>
                    @php
                      $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $pac->id_agenda)->first();
                      $nueva_agenda = \Sis_medico\Agenda::find($pac->id_agenda)->first();
                    @endphp
                    <td class="color">@if(!is_null($verificar)) Admisionado @else @if($nueva_agenda->estado_cita == 0) Por Confirmar @elseif($nueva_agenda->estado_cita == 1) Confirmada @elseif($nueva_agenda->estado_cita == 2) Reagendado @endif @endif</td>

                    <td>@if(!is_null($verificar))
                      <a class="btn btn-info btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                      Ver Detalle Completo</a>@endif
                    </td>  

                  </tr> 
                  @endforeach
                </tbody>
                @endif
              </table>
            </div> 
          </div>
        </div>  
      </div> 
      @endif

      @if($agendas_pac_procedimientos->count() > 0)
      <div class="box box-primary collapsed-box" >
        <div class="box-header">
          <div class="row">
            <div class="col-md-4">
                <h3 class="box-title"><a href="javascript:void($('#proced').click());"><b>Procedimientos</b></a></h3>
            </div>
            <div class="col-md-4">
                <b>tiene {{$agendas_pac_procedimientos->count()}} agendadas en el día</b>
            </div>
          </div>
          <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="proced">
                  <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class=" col-md-12" style="padding: 0;">

            <div class="table-responsive col-md-12">
              <table  id="example2{{date('his')}} row_completo" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
                @if($agendas_pac_procedimientos!=[])
                <thead style="">
                  <tr style=" ">
                      <th scope="col" class="color titulo" >C&eacute;dula</th>
                      <th scope="col" class="color titulo" >Apellidos</th>
                      <th scope="col" class="color titulo" >Nombres</th>
                      <th scope="col" class="color titulo" >Fecha Nacimiento</th>
                      <th scope="col" class="color titulo" >Seguro/Convenio</th>
                      <th scope="col" class="color titulo" >Doctor</th>
                      <th scope="col" class="color titulo" >Hora</th>
                      <th scope="col" class="color titulo" >Tipo</th>
                      <th scope="col" class="color titulo" >Cortesia</th>                
                      <th scope="col" class="color titulo" >Estado</th>
                      <th scope="col" class="color titulo" >Acci&oacute;n</th> 
                  </tr>
                </thead>
                <tbody>
                  @foreach ($agendas_pac_procedimientos as $pac)
                    
                    <?php $agenda_last= DB::table('agenda as a')
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
                    ?>

                    <tr>
                    <td class="color">{{$pac->id}}</td>
                    <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
                    <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
                    <td class="color">{{$pac->fecha_nacimiento}}</td>
                    <td class="color">
                      @if(!is_null($agenda_last)) 
                        {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}}
                      @endif
                    </td>
                    <td class="color">{{$pac->dnombre1}} {{$pac->dapellido1}}</td>
                    <td class="color">
                      {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}
                    </td>
                    <td class="color">
                      @if($pac->proc_consul=='0')
                        CONSULTA 
                      @elseif($pac->proc_consul=='1') 
                        @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}} 
                        @else 
                          PROCEDIMIENTO 
                        @endif 
                      @endif
                    </td>
                    <td>
                      <b>
                        @if($pac->cortesia=='SI')
                         <span style="color: red"> 
                          SI
                         </span> 
                        @else
                         <span class="color"> 
                          NO
                         </span> 
                        @endif
                      </b>
                    </td>
                    @php
                      $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $pac->id_agenda)->first();
                      $nueva_agenda = \Sis_medico\Agenda::find($pac->id_agenda)->first();
                    @endphp
                    <td class="color">@if(!is_null($verificar)) Admisionado @else @if($nueva_agenda->estado_cita == 0) Por Confirmar @elseif($nueva_agenda->estado_cita == 1) Confirmada @elseif($nueva_agenda->estado_cita == 2) Reagendado @endif @endif</td>

                    <td>@if(!is_null($verificar))
                      <a class="btn btn-info btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                      Ver Detalle Completo</a>@endif
                    </td>  

                  </tr> 
                  @endforeach
                </tbody>
                @endif
              </table>
            </div> 
          </div>
        </div>  
      </div> 
      @endif
    </div>
  </div>
</div>

<script type="text/javascript">
    $('#agenda_semana').click(function(){
      
      $.ajax({
          type: 'get',
          url:"{{route('hc4.calendario_fullcalendar')}}",
          success: function(data){
            //console.log(data);
            //alert("ok");
            $("#modificar").html(data);
            //$("#agenda_semana").addClass('oculto');
            
          },
          error: function(data){
            console.log(data);
          }
      });
      
    });

      $('#example2{{rand()}}').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
    
</script>




