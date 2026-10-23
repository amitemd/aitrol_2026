   <div class="box-header with-border" style="background-color: #004AC1;color: white; font-size: 14px; padding: 8px;">
        <div class="row">
          <div class="col-4" style="padding-top: 8px">
            <span>RESULTADO DE LA B&Uacute;SQUEDA</span>
          </div>
          <div class="col-3">
            <button type="button" id="agenda_semana" class="btn btn-danger" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> Agenda por semanas\mes</button>
          </div>
          <div class="col-2" style="text-align: center;">
            <a class="btn btn-danger" onclick="cargar_nuevopaciente();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;">  Agregar Nuevo Paciente</a>
          </div>
          <div class="col-2" style="text-align: center;">
            <a class="btn btn-danger" onclick="descargar_hc_reporte();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Historia Clinica</a>
          </div>
          
        </div>        
      </div>
      <div class="box-body" style="border: 2px solid #004AC1;padding-left: 0px;padding-right: 0px;" id="modificar">
        <div class="content col-md-12"  style="padding-left: 5px;padding-right: 5px;">
          <div class="table-responsive">
            <h4> </h4>
            <table  id="example2{{rand()}}" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
              @if($pacientes!=[])
              <thead style="">
                <tr style=" ">
                  <th scope="col" class="color titulo" >C&eacute;dula</th>
                  <th scope="col" class="color titulo" >Apellidos</th>
                  <th scope="col" class="color titulo" >Nombres</th>
                  <th scope="col" class="color titulo" >Fecha Nacimiento</th>
                  <th scope="col" class="color titulo" >Seguro/Convenio</th>
                  @if(($nombres!=null)||($apellidos!=null))
                    <th scope="col" class="color titulo" >Última visita</th>
                  @else
                    <th scope="col" class="color titulo" >Doctor</th>
                    <th scope="col" class="color titulo" >Hora</th>
                  @endif
                  <th scope="col" class="color titulo" >Tipo</th>
                  <th scope="col" class="color titulo" >Cortesia</th>
                  <th scope="col" class="color titulo" >Estado</th>
                  <th scope="col" class="color titulo" >Acci&oacute;n</th> 
                </tr>
              </thead>
              <tbody>
                @foreach($pacientes as $paci)

                  <?php $agenda_last= DB::table('agenda as a')
                                     ->where('a.id_paciente',$paci->id)
                                     ->join('historiaclinica as h','h.id_agenda','a.id')
                                     ->where('a.espid','<>','10')
                                     ->orderBy('a.fechaini','desc')
                                     ->join('seguros as s','s.id','h.id_seguro')
                                     ->join('empresa as em','em.id','a.id_empresa')
                                     ->select('h.*','s.nombre','a.fechaini','a.proc_consul','a.id as id_agenda','a.cortesia','em.nombre_corto')
                                     ->first();  
                    //dd($agenda_last->hcid);
                  
                  if(!is_null($agenda_last)){
                        $dia =  Date('N',strtotime($agenda_last->fechaini)); $mes =  Date('n',strtotime($agenda_last->fechaini));    
                  }else{
                    $dia = 0; $mes= 0;  
                  }
                    
                  ?>
                 
                  <tr>
                    <!--Cedula-->
                    <td class="color">{{$paci->id}}</td>
                    <!--Apellidos-->
                    <td class="color">{{$paci->apellido1}} {{$paci->apellido2}}</td>
                    <!--Nombres-->
                    <td class="color">{{$paci->nombre1}} {{$paci->nombre2}}</td>
                    <!--Fecha Nacimiento-->
                    <td class="color">{{$paci->fecha_nacimiento}}</td>
                    <!--Seguro/Convenio-->
                    <td class="color">
                      @if(($nombres!=null)||($apellidos!=null)) 
                        @if(!is_null($agenda_last)) 
                            {{$agenda_last->nombre}}/{{$agenda_last->nombre_corto}} 
                        @endif 
                      @else 
                        {{$paci->nombre}}/{{$paci->nombre_corto}} 
                      @endif
                    </td>
                    <!--Última visita-->
                    @if(($nombres!=null)||($apellidos!=null))
                      <td class="color">
                        @if(!is_null($agenda_last))
                          @if($dia == '1') 
                            Lunes
                          @elseif($dia == '2') 
                            Martes
                          @elseif($dia == '3') 
                            Miércoles 
                          @elseif($dia == '4') 
                             Jueves 
                          @elseif($dia == '5') 
                             Viernes 
                          @elseif($dia == '6') 
                             Sábado 
                          @elseif($dia == '7') 
                             Domingo 
                          @endif
                            {{substr($agenda_last->fechaini,8,2)}} de 
                              @if($mes == '1') 
                                Enero 
                              @elseif($mes == '2') 
                                Febrero 
                              @elseif($mes == '3') 
                                Marzo 
                              @elseif($mes == '4') 
                                Abril 
                              @elseif($mes == '5') 
                                Mayo 
                              @elseif($mes == '6') 
                                Junio 
                              @elseif($mes == '7') 
                                Julio 
                              @elseif($mes == '8') 
                                Agosto 
                              @elseif($mes == '9') 
                                Septiembre 
                              @elseif($mes == '10') 
                                Octubre 
                              @elseif($mes == '11') 
                                Noviembre 
                              @elseif($mes == '12') 
                                Diciembre
                              @endif 
                                del {{substr($agenda_last->fechaini,0,4)}}   
                        @endif
                      </td>
                    @else
                      <td class="color">{{$paci->dnombre1}} {{$paci->dapellido1}}</td>
                      <td class="color" @if($fecha1!=$fecha2) style="font-size: 11px;" @endif>@if($fecha1!=$fecha2) {{substr($paci->fechaini,0,10)}}: @endif {{substr($paci->fechaini,10,10)}} - {{substr($paci->fechafin,10,10)}}
                      </td>
                    @endif

                    @if(($nombres!=null)||($apellidos!=null))
                        <td class="color">
                          @if(!is_null($agenda_last)) 
                            @if($agenda_last->proc_consul=='0')
                              CONSULTA 
                            @elseif($agenda_last->proc_consul=='1')
                              PROCEDIMIENTO 
                            @elseif($agenda_last->proc_consul=='4') 
                              VISITA 
                            @endif 
                          @endif 
                        </td>
                        <!--Hora-->
                        <td class="color">
                          @if(!is_null($agenda_last))
                             @if($agenda_last->cortesia=='SI')
                               <span style="color: red"> SI</span> 
                             @else 
                               NO 
                             @endif 
                          @endif
                        </td>
                        <!--Tipo-->
                        <td class="color">
                           No Atendido
                        </td>
                    @else
                        <!--Cortesia-->
                        <td class="color">
                          @if($paci->proc_consul=='0')
                            CONSULTA 
                          @elseif($paci->proc_consul=='1') 
                            @if(isset($agendas_proc[$paci->id_agenda])) 
                              {{$agendas_proc[$paci->id_agenda]['0']}} 
                            @else 
                              PROCEDIMIENTO 
                            @endif 
                          @endif    
                        </td>
                        <!--Estado-->
                        <td class="color">
                          @if($paci->cortesia=='SI')
                            <span style="color: red">SI</span> 
                          @else 
                            NO 
                          @endif
                        </td>
                        @php
                          $estado = Sis_medico\Hc_Cie10::where('hcid',$paci->hcid)->first();
                        @endphp
                        <td class="color" style="font-size: 11px;">
                          @if(!is_null($estado))
                            Atendido
                          @else 
                            No Atendido
                          @endif     
                        </td>
                    @endif 
                    <td><a class="btn btn-info boton-2" style="color: white; width: 100%; height: 100%; padding-left: 0px; padding-right: 0px" href="{{route('nd.buscador', ['id_paciente' => $paci->id])}}">
                    Ver Detalle Completo</a></td>
                  </tr>
              @endforeach
              </tbody>
              @endif
             </table>
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

  $(function () {
    $('#example2{{rand()}}').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        'order'       : [[ 6, "asc" ]]
      });
  });  
    
</script>