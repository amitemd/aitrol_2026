@extends('hc_admision.orden_proc.base')

@section('action-content')

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 

</style>



<div  class="modal fade fullscreen-modal" id="Crea_Actualiza" tabindex="-1" role="dialog" aria-labelledby="Crea_ActualizaLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-10" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Nombres</b></td><td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</td>
                                    <td><b>Apellidos</b></td><td>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td><b>Identificación</b></td><td>{{$agenda->id_paciente}}</td>
                                    <td><b>Edad:</b></td><td><span id="edad"></span></td>
                                    <td style="text-align: right;background: #e6ffff;"><b>@if($agenda->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($agenda->espid)->nombre}} @elseif($agenda->proc_consul=='1')PROCEDIMIENTO @endif</b></td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a type="button" href="{{ URL::previous() }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Regresar</span>
            </a>
        </div>
        
        <div class="col-md-12" style="padding-right: 6px;">
            
            <div class="box box-primary " style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-6">
                        <h3 class="box-title"><b>Generar Orden de Procedimiento</b></h3>
                    </div>
                    <div class="col-md-6">
                        <a href="{{route('auditoria_orden_proc.imprimir_orden',['hcid' => $historiaclinica->hcid])}}">
                            <button class="btn btn-success btn-sm">Descargar</button>
                        </a>
                    </div>
                </div>
                <div class="box-body" style="padding: 5px;">
                    <form id="frm">
                        <input type="hidden" name="hcid" value="{{$historiaclinica->hcid}}">
                        <div class="col-md-12" style="padding-left: 1px;padding-right: 1px;">
                            <div class="col-md-2 {{ $errors->has('id_seguro') ? ' has-error' : '' }}" style="padding: 1px;">
                                <label for="id_seguro" class="control-label">Seguro</label>
                                <select id="id_seguro" name="id_seguro" class="form-control input-sm" required onchange="guardar();">
                                @foreach($seguros as $seguro)    
                                    <option @if(!is_null($orden))@if($orden->id_seguro==$seguro->id) selected @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                                </select>
                            </div>

                            <div class="col-md-2" style="padding: 1px;">
                                <label for="fecha_examen" class="control-label">Fecha Examen</label>
                                <input id="fecha_examen" type="datetime-local" onchange="guardar();" name="fecha_examen" value="@if(!is_null($orden)){{substr($orden->fecha_examen,0,10)}}T{{substr($orden->fecha_examen,11,8)}}@endif" class="form-control input-sm" >
                            </div>
                            
                            <div class="col-md-4" style="padding: 1px;">
                                <label for="motivo" class="control-label">Motivo</label>
                                <input id="motivo" type="text" onchange="guardar();" name="motivo" value="@if(!is_null($orden)){{$orden->motivo}}@endif" class="form-control input-sm" >
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="observacion" class="control-label">Observación</label>
                                <input id="observacion" type="text" onchange="guardar();" maxlength="100" name="observacion" value="@if(!is_null($orden)){{$orden->observacion}}@endif" class="form-control input-sm" >
                            </div>
                        </div>
                        <div class="col-md-12">&nbsp;</div>            
                
                        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">
                        

                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">{{$tipos->find('1')->nombre}}</b>
                            </div>
                            <div class="col-md-12" >    
                                <div class="col-md-7" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('1')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='1')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-5" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('2')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='2')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-7" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('3')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='3')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-5" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('4')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='4')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                        <tr>
                                            <td></td><td>Otros: <input type="text" maxlength="25" class="input-sm" name="endoscopia_urgencia" value="@if(!is_null($orden)){{$orden->endoscopia_urgencia}} @endif" onchange="guardar();"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-8" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('5')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='5')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">{{$tipos->find('6')->nombre}}</b>
                            </div>
                            <div class="col-md-12" >    
                                <div class="col-md-7" style="padding: 0px;">
                                    <table class="table table-striped">  
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden < '7')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-5" style="padding: 0px;">
                                    <table class="table table-striped">    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden >= '7')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                        <tr>
                                            <td></td><td>Otros: <input type="text" maxlength="25" class="input-sm" name="endoscopia_terapeutica" onchange="guardar();" value="@if(!is_null($orden)){{$orden->endoscopia_terapeutica}}@endif"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div> 
                            <div class="col-md-12" style="background: #00b3b3;">
                                <div class="col-md-7"><b style="color: white;">{{$tipos->find('7')->nombre}}</b></div>
                                <div class="col-md-5"><b style="color: white;">{{$tipos->find('8')->nombre}}</b></div>
                            </div>
                            <div class="col-md-12" >    
                                <div class="col-md-7" style="padding: 0px;">
                                    <table class="table table-striped">  
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='7')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-5" style="padding: 0px;">
                                    <table class="table table-striped">    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='8')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                            </div> 
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">{{$tipos->find('9')->nombre}}</b>
                            </div>
                            <div class="col-md-12" >    
                                <div class="col-md-7" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('9')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='9')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-5" style="padding: 0px;">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">Eco Doppler de</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><input type="text" name="eco_doppler" maxlength="25" class="input-sm" onchange="guardar();" value="@if(!is_null($orden)){{$orden->eco_doppler}}@endif"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b style="color: #00b3b3;">{{$tipos->find('10')->ubicacion}}</b></td>
                                        </tr>    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='10')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                        <tr>
                                            <td></td><td>Otros: <input type="text" class="input-sm" name="ecografia" maxlength="25" onchange="guardar();" value="@if(!is_null($orden)){{$orden->ecografia}}@endif"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>  

                        </div>

                        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">

                            
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">{{$tipos->find('11')->nombre}}</b>
                            </div>
                            <div class="col-md-12" >    
                                <div class="col-md-6" style="padding: 0px;">
                                    <table class="table table-striped">  
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden < '6')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                    </table>
                                </div>
                                <div class="col-md-6" style="padding: 0px;">
                                    <table class="table table-striped">    
                                    @foreach($detalles as $detalle)
                                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden >= '6')
                                        <tr>
                                            <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                                        </tr>    
                                        @endif
                                    @endforeach
                                        <tr>
                                            <td></td><td>Otros: <input type="text" class="input-sm" name="prueba_func" onchange="guardar();" maxlength="25" value="@if(!is_null($orden)){{$orden->prueba_func}}@endif"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>  
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">ENDOSCOPIA DIGESTIVA ALTA . CPRE . ENDOSCOPIA . LAPAROSCOPIA</b>
                            </div> 
                            <div class="col-md-12" style="padding: 0px;"> 
                                <table class="table table-striped">
                                
                                    <tr><td colspan="2">1. Día anterior del examen: merienda dieta blanda.</td></tr>
                                    <tr><td colspan="2">2. Día del examen: venir completamente en ayunas.</td></tr>
                                    <tr><td width="10%">3. Puede</td><td width="90%"><input style="width: 100%" type="text" onchange="guardar();" class="input-sm" name="campo1" maxlength="75" value="@if(!is_null($orden)){{$orden->campo1}}@endif"></td></tr>
                                    <tr><td colspan="2">4. Posterior al examen: En caso de premedicación, no debe conducir vehículos algunas horas después del estudio.</td></tr>
                                
                                </table>    
                            </div>
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">ENDOSCOPIA DIGESTIVA BAJA</b>
                            </div> 
                            <div class="col-md-12" style="padding: 0px;"> 
                                <table class="table table-striped">
                                    <tr><td colspan="3"><b style="color: #00b3b3;">Anuscopia y Rectoscopia</b></td></tr>
                                    <tr><td>Aplicar </td><td><input style="width: 100%" type="text" maxlength="50" class="input-sm" name="campo2" onchange="guardar();" value="@if(!is_null($orden)){{$orden->campo2}}@endif"></td><td>, 3 y 2 horas antes del examen.</td></tr>
                                </table>
                                <table class="table table-striped">
                                    <tr><td colspan="3"><b style="color: #00b3b3;">Sigmoidoscopia</b></td></tr>
                                    <tr><td colspan="2">1. Merienda dieta líquida, posteriormente tomar</td><td><input style="width: 100%" type="text" class="input-sm" name="campo3" onchange="guardar();" maxlength="50" value="@if(!is_null($orden)){{$orden->campo3}}@endif"></td></tr>
                                    <tr><td>2. </td><td><input style="width: 100%" type="text" class="input-sm" onchange="guardar();" maxlength="30" name="campo4" value="@if(!is_null($orden)){{$orden->campo4}}@endif"></td><td> aplicar en el recto 3 y 2 horas antes del examen.</td></tr>
                                </table>
                                <table class="table table-striped">
                                    <tr><td colspan="4"><b style="color: #00b3b3;">Colonoscopia</b></td></tr>
                                    <tr><td colspan="4">1. Dos días anteriores, no frutas y legumbres (excepto extractos). 1 tableta de DULCOLAX en la noche.</td></tr>
                                    <tr><td colspan="4">2. Dieta líquida el día anterior al examen.</td></tr>
                                    <tr><td colspan="4">3. Tomar una tableta de <input type="text" class="input-sm" name="campo5" maxlength="30" onchange="guardar();" value="@if(!is_null($orden)){{$orden->campo5}}@endif"> 30 minutos antes de ingerir el COLAX</td></tr>
                                    <tr><td colspan="4">4. <input type="text" class="input-sm" name="campo6" onchange="guardar();" maxlength="30" value="@if(!is_null($orden)){{$orden->campo6}}@endif"> Diluir cada uno en un litro de agua, tomarlos entre las </td></tr>
                                    <tr><td><input type="text" class="input-sm" name="campo7" maxlength="30" onchange="guardar();" value="@if(!is_null($orden)){{$orden->campo7}}@endif"> el día <input type="text" class="input-sm" name="campo8" onchange="guardar();" maxlength="30" value="@if(!is_null($orden)){{$orden->campo8}}@endif">.(Aproximadamente 1 vaso cada 10 a 15 minutos).</td></tr>
                                    <tr><td colspan="4">5. Tomar 1 frasco de FLEET FOSFODA en un vaso de agua.</td></tr>
                                    <tr><td colspan="4">6. Posterior al examen: En caso de premedicación, no debe conducir vehículos algunas horas después del estudio.</td></tr>
                                </table>    
                            </div> 
                            <div class="col-md-12" style="background: #00b3b3;"><b style="color: white;">ECOGRAFÍAS</b>
                            </div> 
                            <div class="col-md-12" style="padding: 0px;"> 
                                <table class="table table-striped">
                                    <tr><td><b style="color: #00b3b3;">Ecografía abdominal superior y renal</b></td></tr>
                                    <tr><td>1. Venir al examen completamente en ayunas</td></tr>
                                    <tr><td>2. En las citas por la tarde, no debe ingerir alimentos y líquidos 6 horas antes del estudio.</td></tr>
                                </table>
                                <table class="table table-striped">
                                    <tr><td><b style="color: #00b3b3;">Ecografía gineco-obstétrica</b></td></tr>
                                    <tr><td>1. Pacientes para eco ginecológica u obstétrica con menor a 14 semanas, no orinar 2 horas antes del examen y tomar 2 botellas de 1/2 litro de agua 1 hora antes del examen.</td></tr>
                                    <tr><td>2. Pacientes con embarazo mayor a 14 semanas, no orinar 1 hora antes del examen.</td></tr>
                                </table>
                            </div>      

                        </div>    

                    </form>
                </div>
            </div>
        </div>  
    </div>
</div>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>


<script>
 
    $(document).ready(function() {

        $('input[type="checkbox"].flat-green').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass   : 'iradio_flat-green'
        })  

        var edad;
        edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>');
        //alert(edad);
        $('#edad').text( edad );

       
        
        $(".breadcrumb").append('<li class="active">Atención</li>');    

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
                
        

        
        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });

        @foreach($detalles as $detalle)

            $('input[type="checkbox"]#ch{{$detalle->id}}').on('ifChecked', function(){

                $.ajax({
                  type: 'get',
                  url:'{{route('auditoria_orden_proc.existe',['hcid' => $historiaclinica->hcid, 'id_doc' => $detalle->id])}}',
                  success: function(data){
                  //alert(data);
                    if(data=='0'){
                        crea_detalle({{$historiaclinica->hcid}},{{$detalle->id}}); 
                    } 

                  }
                });
    
            });

            $('input[type="checkbox"]#ch{{$detalle->id}}').on('ifUnchecked', function(){

                $.ajax({
                  type: 'get',
                  url:'{{route('auditoria_orden_proc.eliminar',['hcid' => $historiaclinica->hcid, 'id_doc' => $detalle->id])}}',
                  success: function(data){
                  //alert(data);
                    
                  }
                });            
        
            });

        @endforeach




    });

    function guardar(){
        //alert("guardar");
        $.ajax({
          type: 'post',
          url:"{{route('auditoria_orden_proc.guardar',['hcid' => $historiaclinica->hcid])}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
            //alert(data);
            //var edad;
            //edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>');
            //alert(edad);
            //$('#edad').text( edad );

          },
          error: function(data){

            //console.log(data.responseJSON);
             
          }
        });
    }

    function crea_detalle(hcid,id){

      $.ajax({
        type: 'get',
        url:'{{url("auditoria_orden_proc/auditoria_imprimir_orden/crear_detalle_orden_procedimiento")}}'+'/'+hcid+'/'+id, //controldoc.crea_doc
        success: function(data){
            
        }
      })
    }  

    


    

</script>

@include('sweet::alert')
@endsection

