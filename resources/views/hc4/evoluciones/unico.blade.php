<div id="" >  
    <table style="margin-left: 20px; margin-top: 10px; margin-bottom: 10px; margin-right: 30px;" >
        <tr style="height: 40px" >
            <td style="width: 300px;" ><span style="font-family: 'Helvetica general';">Fecha</span></td>
            <td style="width: 200px"><span style="font-family: 'Helvetica general';">Hora</span></td>
        </tr>
        <tr>
            <td>
                @if(!is_null($evoluciones))
             @php $dia =  Date('N',strtotime($evoluciones->fechaini)); $mes =  Date('n',strtotime($evoluciones->fechaini)); @endphp
            <b>
                @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($evoluciones->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($evoluciones->fechaini,0,4)}}</b> 
             @endif
            </td>
            <td> @if(!is_null($evoluciones)) {{substr($evoluciones->fechaini,10,10)}} @endif</td>
        </tr>

        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">Procedimiento</span></td>
        </tr>
        @php
            $procedimiento = \Sis_medico\hc_procedimientos::find($evoluciones->hc_id_procedimiento);
            $nprocedimiento = null;
            $texto = null;
            if(!is_null($procedimiento->id_procedimiento_completo)){
                $nprocedimiento = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                $texto =  $nprocedimiento->nombre_general;
            }else{
                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
                $mas = true; 

                foreach($adicionales as $value2)
                {
                    if($mas == true){
                     $texto = $texto.$value2->procedimiento->nombre  ;
                     $mas = false; 
                     }
                    else{
                     $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                     }                                          
                }
            }
        @endphp
        <tr style="">
            <td style="" colspan="2">{{$texto}}</td>
        </tr>


        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">Motivo</span></td>
        </tr>
        <tr>
            <td style="" colspan="2">@if(!is_null($evoluciones)){{strip_tags($evoluciones->motivo)}}@endif</td>
        </tr>
        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span></td>
            <td><span style="font-family: 'Helvetica general';">Seguro</span></td>
        </tr>
        <tr>
            @php $procedimiento_3333 = null; @endphp
            @if(!is_null($evoluciones))
            @php
                $procedimiento_3333 = Sis_medico\hc_procedimientos::find($evoluciones->hc_id_procedimiento);
            @endphp
            @endif
            <td>
                <b>@if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_doctor_examinador))Dr. {{$procedimiento_3333->doctor->nombre1}} {{$procedimiento_3333->doctor->apellido1}}@else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif @else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif</b>
            </td>
            <td>
                @if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_seguro)){{$procedimiento_3333->seguro->nombre}}@else{{$agenda->snombre}}@endif @else{{$agenda->snombre}}@endif
            </td>
            
        </tr>
        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">Observaci&oacute;n</span></td>
        </tr>
        <tr>
            <td colspan="2"> 

            @if(!is_null($procedimiento_3333)){{strip_tags($procedimiento_3333->observaciones)}} @endif
            </td>
        </tr>
        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">Evoluci&oacute;n</span></td>
        </tr>
        <tr>
            <td colspan="2">
             @if(!is_null($evoluciones))
                   
            <p> <?php echo  strip_tags($evoluciones->cuadro_clinico); ?></p>
            @endif 
            </td>
        </tr>


        <tr style="height: 40px">
            <td colspan="2"><span style="font-family: 'Helvetica general';">Resultados de Exámenes y Procedimientos Diagnósticos</span></td>
        </tr>
        <tr>
            <td colspan="2">
             @if(!is_null($evoluciones))
                   
            <p> <?php echo  strip_tags($evoluciones->resultado); ?></p>
            @endif 
            </td>
        </tr>


        <tr style="height: 40px">
            <td><span style="font-family: 'Helvetica general';">Diagn&oacute;stico</span></td>
        </tr>
        <tr>
            <td colspan="2">

            @php $hc_cie10 = null ;  @endphp
            @if(!is_null($evoluciones))
            @php 
                $hc_cie10 = DB::table('hc_cie10')->where('hc_id_procedimiento',$evoluciones->hc_id_procedimiento)->get();
            @endphp
            @endif
            
                @if(!is_null($hc_cie10))
                @foreach($hc_cie10 as $cie10)
                @php $c10 = DB::table('cie_10_3')->where('id',$cie10->cie10)->first(); @endphp
                @if(!is_null($c10))
                <tr><td colspan="4">
                {{$c10->descripcion}}
                </td></tr>
                @endif 
                @php $c10 = DB::table('cie_10_4')->where('id',$cie10->cie10)->first(); @endphp
                @if(!is_null($c10))
                <tr><td colspan="4">
                {{$c10->descripcion}}
                </td></tr>
                @endif 
                @endforeach 
                @endif
            </td>
        </tr>
    </table>
</div>