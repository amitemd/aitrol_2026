
<div class="col-md-12">
    @php
		$fecha_r = null;
		$xdoctor = null;
        if(!is_null($ordimag->fecha_orden)){
           $fecha_r =  Date('Y-m-d',strtotime($ordimag->fecha_orden));
        }

        if($ordimag->id_doctor != ""){
            $xdoctor = DB::table('users as us')->where('us.id',$ordimag->id_doctor)->first();
        }
    @endphp
    @php
        $fecha = substr($ordimag->fecha_orden,0,10);
        $invert = explode( '-',$fecha);
        $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
    @endphp
    @php
        $tipos_orden_imagenes = collect();
        if(!is_null($ordimag->id)){
            $tipos_orden_imagenes = \Sis_medico\Orden_Tipo::where('id_orden', $ordimag->id)->pluck('id');
        }

        $texto = "";
        if($tipos_orden_imagenes->isNotEmpty()){
            $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::whereIn('id_orden_tipo', $tipos_orden_imagenes)->get();

            $mas = true;
            foreach($procedimiento_orden_proced as $value2)
            {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if(is_null($nombre_procedimiento)){
                    continue;
                }

                if($mas == true){
                 $texto = $nombre_procedimiento->nombre;
                 $mas = false; 
                }
                else{
                 $texto = $texto.' + '.$nombre_procedimiento->nombre;
                }

            }

        }
    
    @endphp
    <div class="box hc4-image-order-card @if($fecha_r != date('Y-m-d')) collapsed-box @endif">
        <div class="box-header with-border hc4-image-order-summary">
                <div class="hc4-image-order-summary__date">
                    @if(!is_null($ordimag->fecha_orden))
                        @php 
                         $dia =  Date('N',strtotime($ordimag->fecha_orden)); 
                         $mes =  Date('n',strtotime($ordimag->fecha_orden)); 
                        @endphp
                        <b>
                        @if($dia == '1') Lunes 
                         @elseif($dia == '2') Martes
                         @elseif($dia == '3') Miércoles 
                         @elseif($dia == '4') Jueves 
                         @elseif($dia == '5') Viernes 
                         @elseif($dia == '6') Sábado 
                         @elseif($dia == '7') Domingo 
                        @endif
                         {{substr($ordimag->fecha_orden,8,2)}} de
                        @if($mes == '1') Enero 
                             @elseif($mes == '2') Febrero 
                             @elseif($mes == '3') Marzo 
                             @elseif($mes == '4') Abril 
                             @elseif($mes == '5') Mayo 
                             @elseif($mes == '6') Junio 
                             @elseif($mes == '7') Julio 
                             @elseif($mes == '8') Agosto 
                             @elseif($mes == '9') Septiembre 
                             @elseif($mes == '10') Octubre 
                             @elseif($mes == '11') Noviembre 
                             @elseif($mes == '12') Diciembre 
                        @endif
                        del {{substr($ordimag->fecha_orden,0,4)}}</b>  
                    @endif
                </div>
                <div class="hc4-image-order-summary__doctor">
                    <strong>Dr (a):</strong>
                    @if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @else No asignado @endif
                </div>
                <div class="hc4-image-order-summary__right">
                    <div class="hc4-image-order-summary__procedures">
                       <strong>Procedimientos:</strong>
                       <span>{{trim($texto) !== '' ? $texto : 'Sin procedimientos seleccionados'}}</span>
                    </div>
                    <div class="hc4-image-order-summary__tools box-tools">
                        <span class="hc4-image-order-summary__id">Orden #{{$ordimag->id}}</span>
                        <button type="button" class="btn btn-sm hc4-image-order-summary__toggle" data-widget="collapse" title="Mostrar u ocultar orden">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
        </div>
        <div class="box-body" style="background: white;">
            <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
                <div class="box hc4-image-order-detail">
                    <div class="box-header with-border hc4-image-order-detail__header">
                            <div>
                                <button type="button" class="hc4-image-order-detail__button" onclick="editar_orden_proimagenes({{$ordimag->id}},'{{$ordimag->id_paciente}}');">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    <span>Editar</span>
                                </button>
                            </div>
                            <div class="hc4-image-order-detail__title">
                                Detalle de Orden
                            </div>
                            <div class="hc4-image-order-detail__action--right">
                               <button type="button" class="hc4-image-order-detail__button" onclick="descargar_orden_imagenes({{$ordimag->id}});">
                                   <i class="glyphicon glyphicon-download-alt" aria-hidden="true"></i>
                                   <span>Descargar Orden</span>
                               </button>
                            </div>
                    </div>
                    <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden_imag{{$ordimag->id}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

 	editar_orden_proimagenes_new('{{$ordimag->id}}','{{$ordimag->id_paciente}}');

    function editar_orden_proimagenes_new(id_orden,id_paciente){
        $.ajax({
            type: "GET",
            url: "{{route('editar.orden_procedimiento_imagenes')}}/"+id_orden+'/'+id_paciente, 
            data: "",
            datatype: "html",
            success: function(datahtml){
                $("#xorden_imag"+id_orden).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        }); 
    }

</script>

