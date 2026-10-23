
<style type="text/css">
.table{
    font-size: 15px;
}
td{
    padding: 0px;
    padding-bottom: -5px;
}

</style>



<div class="container-fluid" style="margin-top: -45px;">
    <div class="row ">
        <div style="float: left;width: 50%;">
            <img width=300 height=100 src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}">
        </div>
        <div style="float: left;width: 50%;">
            <table style="font-size: 14px;border: 1.1px solid black;border-radius: 15px;padding: 5px;">
                <tr>
                    <td style="padding-bottom: 0px;"><b>Fecha:</b></td><td colspan="2" style="border-bottom: 1px solid black;">{{Date('d/n/Y')}}</td><td style="text-align: right;"><b>Edad:</b></td><td style="border-bottom: 1px solid black;">{{$age}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 0px;"><b>Paciente:</b></td><td colspan="4" style="border-bottom: 1px solid black;font-size: 11px;">{{$agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</td>
                </tr>
                
                <tr>
                    <td style="padding-bottom: 0px;"><b>Seguro:</b></td><td colspan="4" style="border-bottom: 1px solid black;font-size: 12px;">{{$agenda->hsnombre}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 0px;"><b>Medico Solicitante:</b></td><td colspan="3" style="border-bottom: 1px solid black;font-size: 12px;">@if(!is_null($orden)){{$agenda->udnombre}} {{$agenda->udapellido}}@endif</td>
                </tr>
                
                
            </table>
        </div> 
        <div style="clear:both;">
            <table>

                <tr>
                    <td style="padding-bottom: 0px;"><b>Motivo Estudio:</b></td><td colspan="1" style="border-bottom: 1px solid black;font-size: 12px;">@if(!is_null($orden)){{substr($orden->motivo,0,100)}} @endif </td>
                </tr>
                
            </table>
        </div> 
        <div style="clear:both;height: 2px;">&nbsp;</div>                      

        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('1')->nombre}}</b>
        </div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('1')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='1')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;" style="color: #007777;" @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('3')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='3')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('5')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='5')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach        
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('2')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='2')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('4')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='4')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>Otros:<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->endoscopia_urgencia}}@endif</span></td>
                    </tr>
                </table>
            </div>
            <div style="clear:both;height: 1px;">&nbsp;</div> 
            
            
            
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div> 
        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('6')->nombre}}</b>
        </div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">  
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden < '7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden >= '7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>Otros:<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->endoscopia_terapeutica}}@endif</span></td>
                    </tr>
                </table>
            </div>
        </div> 
        
        <div style="clear:both;height: 2px;">&nbsp;</div> 
        <div class="col-md-12" style="background: #007777;clear:both;font-size: 14px;">
            <b style="color: white;">{{$tipos->find('7')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;">
                <table class="table table-striped">  
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" style="background: #007777;font-size: 14px;">
            <b style="color: white;">{{$tipos->find('8')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-5" style="padding: 0px;">
                <table class="table table-striped">    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='8')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
        </div> 
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('9')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('9')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='9')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">Eco Doppler de</b></td>
                    </tr>
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->eco_doppler}}@endif</span></td>
                    </tr>
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('10')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='10')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>Otros:<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->ecografia}}@endif</span></td>
                    </tr>
                </table>
            </div>
        </div>  
        
        <div style="clear:both;height: 1px;">&nbsp;</div> 

        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">

            
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('11')->nombre}}</b>
            </div>
            <div class="col-md-12" >    
                <div class="col-md-6" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                    <table class="table table-striped">  
                    @foreach($detalles as $detalle)
                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden < '6')
                        <tr>
                            <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                        </tr>    
                        @endif
                    @endforeach
                    </table>
                </div>
                <div class="col-md-6" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                    <table class="table table-striped">    
                    @foreach($detalles as $detalle)
                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden >= '6')
                        <tr>
                            <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                        </tr>    
                        @endif
                    @endforeach
                        <tr>
                            <td><span style="width: 20px;">&nbsp;</span></td><td>Otros:<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->prueba_func}}@endif</span></td>
                        </tr>
                    </table>
                </div>
            </div> 
            <div style="clear:both;height: 1px;">&nbsp;</div> 

            <!--SEGUNDA HOJA--> 
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">INSTRUCTIVOS PARA LOS PACIENTES Y RECOMENDACIONES</b>
            </div>
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">Fecha del Examen:</b></td><td style="margin-bottom: 1px solid black">@if(!is_null($orden)){{substr($orden->fecha_examen,0,10)}}@endif</td><td><b style="color: #007777;">Hora:</b></td><td style="margin-bottom: 1px solid black">@if(!is_null($orden)){{substr($orden->fecha_examen,10,10)}}@endif</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4"><b style="color: #007777;">Observacion:</b><span>Venir acompañado(a), debe traer la orden médica</span></td></tr>
                    <tr><td style="padding-bottom: 1px;border-bottom:  1px solid black;" colspan="4">@if(!is_null($orden)){{$orden->observacion}}@endif</td></tr>
                </table>    
            </div>
            <div style="clear:both;height: 15px;">&nbsp;</div> 
                
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">ENDOSCOPIA DIGESTIVA ALTA . CPRE . ENDOSCOPIA . LAPAROSCOPIA</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped" style="width: 100%">
                
                    <tr><td style="padding-bottom: 1px;" colspan="2">1. Día anterior del examen: merienda dieta blanda.</td></tr> 
                    <tr><td style="padding-bottom: 1px;" colspan="2">2. Día del examen: venir completamente en ayunas.</td></tr> 
                    <tr><td style="padding-bottom: 1px;" width="10%">3. Puede</td><td width="90%" style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo1}}@endif</td></tr> 
                    <tr><td style="padding-bottom: 1px;" colspan="2">4. Posterior al examen: En caso de premedicación, no debe conducir vehículos algunas horas después del estudio.</td></tr>
                
                </table>    
            </div>
            <div style="clear:both;height: 10px;">&nbsp;</div> 
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">ENDOSCOPIA DIGESTIVA BAJA</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">Anuscopia y Rectoscopia</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">Aplicar <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo2}}@endif </span>, 3 y 2 horas antes del examen.</td></tr>
                </table>
                <div style="height: 1px;">&nbsp;</div> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;" ><b style="color: #007777;">Sigmoidoscopia</b></td></tr>
                    <tr><td style="padding-bottom: 1px;" >1. Merienda dieta líquida, posteriormente tomar <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo3}}@endif</span></td></tr>
                    <tr><td style="padding-bottom: 1px;" >2. <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo4}}@endif</span> aplicar en el recto 3 y 2 horas antes del examen.</td></tr>
                </table>
                <div style="clear:both;height: 1px;">&nbsp;</div> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;" colspan="4"><b style="color: #007777;">Colonoscopia</b></td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">1. Dos días anteriores, no frutas y legumbres (excepto extractos). 1 tableta de DULCOLAX en la noche.</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">2. Dieta líquida el día anterior al examen.</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">3. Tomar una tableta de <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo5}}@endif</span> 30 minutos antes de ingerir el COLAX</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">4. <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo6}}@endif</span> Diluir cada uno en un litro de agua, tomarlos entre las <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo7}}@endif</span></td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4"> el día <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo8}}@endif</span>.(Aproximadamente 1 vaso cada 10 a 15 minutos).</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">5. Tomar 1 frasco de FLEET FOSFODA en un vaso de agua.</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">6. Posterior al examen: En caso de premedicación, no debe conducir vehículos algunas horas después del estudio.</td></tr>
                </table>    
            </div>
            <div style="clear:both;height: 10px;">&nbsp;</div>  
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">ECOGRAFÍAS</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">Ecografía abdominal superior y renal</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">1. Venir al examen completamente en ayunas</td></tr>
                    <tr><td style="padding-bottom: 1px;">2. En las citas por la tarde, no debe ingerir alimentos y líquidos 6 horas antes del estudio.</td></tr>
                </table>
                <div style="clear:both;height: 1px;">&nbsp;</div> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">Ecografía gineco-obstétrica</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">1. Pacientes para eco ginecológica u obstétrica con menor a 14 semanas, no orinar 2 horas antes del examen y tomar 2 botellas de 1/2 litro de agua 1 hora antes del examen.</td></tr>
                    <tr><td style="padding-bottom: 1px;">2. Pacientes con embarazo mayor a 14 semanas, no orinar 1 hora antes del examen.</td></tr>
                </table>
            </div>
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">OTROS ESTUDIOS</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;">Solicitar el instructivo en la secretaría del IECED o en www.ieced.com.ec</td></tr>
                </table>
            </div>
            <div style="clear:both;height: 100px;">&nbsp;</div> 
            <div align="center" class="col-md-12" style="padding: 0px;text-align: center;"> 
                <p style="color: #007777">_________________________________</p>
                <p style="color: #007777"><b>Médico Solicitante</b></p>
            </div>      

        </div>    

                    
                
    </div>
</div>



