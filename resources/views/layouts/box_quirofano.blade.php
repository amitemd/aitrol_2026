
<style>
    .lds-ring {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 64px;
        height: 64px;
        margin: 8px;
        border: 8px solid #004AC1 ;
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #004AC1  transparent transparent transparent;
    }

    .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
    }

    .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
    }

    .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
    }

    @keyframes lds-ring {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .recuadro {
        height: 200px;
        width: 100px;
    }

    .sradio {
        border: 1px solid white;
        border-radius: 20px;
        font-size: 20px;
        width: 40px;
        text-align: center;
        color: white;
    }

    .colorbasic {
        color: white !important;
    }

    b {
        padding-top: 11px !important;
    }

    .burbuja {
        padding-left: 4px;
        height: 200px;
    }

    .card-title {
        font-size: 14px !important;
    }

    .card-header {

        max-height: 100px;
    }



    *::-webkit-scrollbar {
        -webkit-appearance: none;
    }

    *::-webkit-scrollbar:vertical {
        width: 10px;
    }

    *::-webkit-scrollbar-button:increment,
    *::-webkit-scrollbar-button {
        display: none;
    }

    *::-webkit-scrollbar:horizontal {
        height: 10px;
    }

    *::-webkit-scrollbar-thumb {
        background-color: #797979;
        border-radius: 20px;
        border: 2px solid #f1f2f3;
    }

    *::-webkit-scrollbar-track {
        border-radius: 10px;
    }

    .div_limitado{
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2; /* Número máximo de líneas que mostrará el contenedor */
        height: 50px; /* Altura máxima del contenedor */
    }
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>

<div class="modal fade" id="examen_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="examen_div"  >
        </div>
    </div>
</div>
<div class="modal fade" id="consulta_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="consulta_div"  >
        </div>
    </div>
</div>
<div class="modal fade" id="estudio_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content "id="estudio_div"  >
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="row">
        @if($tipo == 1)
        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Cirugia')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="primer_paso();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b>{{trans('transquirofano.Fecha')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    <div class="col-md-12">
                        <b>{{trans('transquirofano.MédicoExaminador')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!is_null($pro_final)) {{$pro_final->apellido1}} {{$pro_final->nombre1}} @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        @php
                        $adicionales = [];
                        if(!is_null($pro_final)){
                        $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $pro_final->id_procedimiento)->get();
                        }

                        $mas = true;
                        $texto = "";
                        foreach($adicionales as $value2)
                        {
                        if($mas == true){
                        $texto = $texto.$value2->procedimiento->nombre ;
                        $mas = false;
                        }
                        else{
                        $texto = $texto.' + '. $value2->procedimiento->nombre ;
                        }
                        }
                        @endphp
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>
        @endif


        @if($tipo == 0)
        @php

        $adicionales = [];
        if(!empty($pro_final)){
        $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $pro_final->id_procedimiento)->get();
        }
        $mas = true;
        $texto = "";
        foreach($adicionales as $value2)
        {
        if($mas == true){
        $texto = $texto.$value2->procedimiento->nombre ;
        $mas = false;
        }
        else{
        $texto = $texto.' + '. $value2->procedimiento->nombre ;
        }
        }
        @endphp

        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.Imagenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="ecografia();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($historia)) {{$historia->created_at}} @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.MédicoExaminador')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($pro_final)) {{$pro_final->apellido1}} {{$pro_final->nombre1}} @endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>
        @endif

        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Evolución')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="evolucion();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($evolucion)){{$evolucion->created_at}}@endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Motivo')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($evolucion)){{$evolucion->motivo}}@endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Detalle')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($evolucion)){{$evolucion->cuadro_clinico}}@endif</span>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Medicamentos')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="medicamentos();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    @php 
                        $agenda = $log->agenda; 
                        $historia = $agenda->historia_clinica;
                        $receta = $historia->recetas->last();
                    @endphp
                    <br>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Fecha')}}</b>
                        </div>
                        <div class="col-md-12">
                            <span>{{$historia->created_at}} </span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Medicamentos')}}</b>
                        </div>
                        <div class="col-md-12">
                        
                            @foreach($receta->detalles as $detalle)
                                <span>* {{$detalle->nombre}}: cantidad {{$detalle->cantidad}} - {{substr($detalle->dosis, 0, 10)}}...</span><br>   
                            @endforeach 
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.SignosVitales')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="evolucion_enfermeria();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Fecha')}}</b>
                        </div>
                        <div class="col-md-12">
                            @if(!is_null($evolucion_enfermeria)) <span> {{$evolucion_enfermeria->created_at}}  </span> @endif
                            <span></span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Observacion')}}</b>
                        </div>
                        <div class="col-md-12">
                            @if(!is_null($evolucion_enfermeria)) <span> <b> {{trans('hospitalizacion.FrecuenciaRespiratoria')}}: </b> {{$evolucion_enfermeria->frec_respiratoria}} </span> <span><b>{{trans('hospitalizacion.PresionArterial')}}:</b> {{$evolucion_enfermeria->presion_arterial}} </span> @endif
                        </div>

                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> 
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.NotasEvolucionEnfermeria')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="notas_enfermeria();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Fecha')}}</b>
                        </div>
                        <div class="col-md-12">
                            @if(!is_null($notas)) <span> {{$notas->created_at}}  </span> @endif
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Observacion')}}</b>
                            @if(!is_null($notas)) <span> {{$notas->descripcion}} </span> @endif
                        </div>
                        <div class="col-md-12">
                            
                        </div>

                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> 
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.DescargoEnfermeria')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="descargo_enfermeria()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">

                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Fecha')}}</b>
                        </div>
                        <div class="col-md-12">
                            <span>{{$historia->created_at}} </span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Medicinas')}}</b>
                        </div>
                        <div class="col-md-12">
                            @foreach($receta->detalles as $detalle)
                            <span>* {{$detalle->nombre}}: cantidad {{$detalle->cantidad}} - {{substr($detalle->dosis, 0, 10)}}...</span><br>
                            @endforeach
                        </div>

                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>


        <!-- Card de crear imagenes -->
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.subir_imagenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="subirImagenes()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px; overflow-y: scroll;">
                    <div id="image_container" class="row col-md-12">
                        <div style="text-align: center;display: grid;place-items: center;width: 100%;height: 200px;">
                            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                        </div>
                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>



        <!-- FIN DE CREAR IMAGENES -->

        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.ArmarEstudios')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="armar_estudio();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($historia)) {{$historia->created_at}} @endif</span>
                    </div>

                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Pre-VisualizarEstudios')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="ver_estudio();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!empty($historia)) {{$historia->created_at}} @endif</span>
                    </div>

                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.ResultadodeExamenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="mostrarExamenes()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    @php
                    $examenes = [];
                    $examen = [];
                    if($solicitud != null){
                    $examenes= \Sis_medico\Ho_Solicitud::where('id',$solicitud->id)->first();
                    $examen= \Sis_medico\Examen_Orden::where('id_paciente',$examenes->id_paciente)->orderBy('created_at', 'DESC')->limit(1)->get();
                    }

                    @endphp
                    <div class="row">

                        <!--div class="col-md-12">
                            <table id="example2" class="table ">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">{{trans('transquirofano.Fecha')}}</th>
                                        <th style="text-align: center;">{{trans('transquirofano.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($examen as $value)
                                    <tr>
                                        <td>@if($value == null) @else {{$value->created_at}} @endif</td>
                                        <td> <a class="btn btn-primary btn-xs" href="{{route('hospitalizacion.imprimir',['id' => $value->id])}}" target="_blank">{{trans('hospitalizacion.DescargarExamen')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div-->
                        <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.fecha')}} </b>
                        </div>
                        <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.tipo')}} </b>
                        </div>
                        <div class="col-md-4">
                            <b>  {{trans('hospitalizacion.ver')}} </b>
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[0])) @else {{$arr_listado[0][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[0])) @else {{$arr_listado[0][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[0]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_laboratorio('{{$arr_listado[0][2]}}')">{{trans('hospitalizacion.ver')}}</button> 
                            <!--a  class="btn btn-primary btn-xs"  href="{{route('hospitalizacion.imprimir',['id' =>$arr_listado[0][2]])}}" target="_blank">{{trans('hospitalizacion.ver')}} </a-->@endif
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[1])) @else {{$arr_listado[1][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[1])) @else {{$arr_listado[1][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[1]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_estudio_vt('{{$arr_listado[1][2]}}')">{{trans('hospitalizacion.ver')}}</button>@endif
                        </div>
                        <div class="col-md-12" style="text-align: center;"> <br> </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[2])) @else {{$arr_listado[2][0]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;"> 
                            @if(empty($arr_listado[2])) @else {{trans('hospitalizacion.Interconsulta')}} <br> {{$arr_listado[2][1]}} @endif
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @if(empty($arr_listado[2]))   @else  <button class="btn btn-primary btn-xs" onclick="ver_consulta('{{$arr_listado[2][2]}}')">{{trans('hospitalizacion.ver')}}  </button> @endif
                        </div>
                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Epicrisis')}} 
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="epicrisis(0);"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    @if(!is_null($epicrisis))
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span> {{$epicrisis->created_at}} </span>
                    </div>

                    <div class="col-md-12">
                        <b> Cuadro Clinico </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$epicrisis->cuadro_clinico}}</span>
                    </div>
                    <div class="col-md-12">
                        <b> Hallazgos </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$epicrisis->hallazgo}}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.Traspaso')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="trapaso_modulo();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">


                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.SubirArchivos')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="subir_archivos();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">


                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center">
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            Formulario 013
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="cargar_formulario_013('{{$pro_final->id_procedimiento}}')"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">
                        @php    
                            $formulario013 = Sis_medico\HcFormulario013::where('id_hc_procedimiento',$pro_final->id_procedimiento)->first();
                        @endphp
                        @if(!is_null($formulario013))
                            <div class="col-md-12"><b>Resumen Clínico</b></div>
                            <br>
                            <div class="col-md-12">
                                <div class="div_limitado">
                                <?php echo ($formulario013->resumen_clinico) ?>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12"><b>Descripcion</b></div>
                            <br>
                            <div class="col-md-12">
                                <div class="div_limitado">
                                {{ $formulario013->descripcion_estudio }}
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12"><b>Tratamiento</b></div>
                            <br>
                            <div class="col-md-12">
                                <div class="div_limitado">
                                {{ $formulario013->tratamiento }}
                                </div>
                            </div>
                            
                        @else
                            Sin Biopsia Ingresada
                        @endif
                    </div>
                </div>
                <!---->
                <!---->
            </div>
        </div>


    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {
        primer_paso()
    });

    function primer_paso() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('hospital.primerpaso',['id' => $solicitud->id ])}}",
            data: {
                //'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }
    $(document).ready(function() {});

    function mostrarExamenes() {
        $.ajax({
            type: "get",
            url: "{{route('hospitalizacion.cargar_examenes',['id' => $solicitud != null ?  $solicitud->id : ''])}}",
            datatype: "html",
            success: function(datahtml, data) {
                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function evolucion() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_evolucion',['id' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }


    function medicamentos() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_medicamentos',['id' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }


    function ordenes_funcionales() {
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.index_funcionales',['id_solicitud' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function armar_estudio() {
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.armar_estudio',['id_solicitud' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function ver_estudio() {
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.ver_estudio',['id_solicitud' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function ecografia() {
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.ecografia',['id_solicitud' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function epicrisis(var_id) {
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.epicrisis',['id_solicitud' => $solicitud != null ? $solicitud->id : ''])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
                if(var_id != 0){
                    ver_epicrisis(var_id);
                }
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function descargo_enfermeria() {

        $.ajax({
            type: "get",
            url: "{{route('hospitalizacion.descargo_medicina',['id_solic' => $solicitud->id])}}",
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#pasos").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });

    }

    function trapaso_modulo() {
        $.ajax({
            type: "get",
            url: "{{route('hospital.traspaso_modulo',['id' => $solicitud->id])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function subir_archivos() {
        $.ajax({
            type: "get",
            url: "{{route('hospital.subir_formularios',['id' => $solicitud->id])}}",
            data: {
                // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function evolucion_enfermeria(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.evolucion_enfermeria',['id' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
                
            }, error: function() {
            
            }
        });
    }

    function subirImagenes() {
        $.ajax({
            type: "get",
            url: ` {{ route('hospital.viewImagenes', ['id' => $solicitud->id])}} `,
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").empty().html(datahtml);
                $('html, body').animate({
                    // scrollTop: $("#quirofano").offset().top
                }, 1000)
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function notas_enfermeria(){
        $.ajax({
            type: "get",
            url: "{{route('formulario005.notas_enfermeria',['id' => $solicitud->id])}}",
            
            datatype: "html",
            success: function(datahtml, data) {

                $("#pasos").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });

    }

    var remoto_href = '';
    jQuery('body').on('click', '[data-toggle="modal"]', function() {
        if (remoto_href != jQuery(this).data('remote')) {
            remoto_href = jQuery(this).data('remote');
            jQuery(jQuery(this).data('target')).removeData('bs.modal');

            jQuery(jQuery(this).data('target')).find('.modal-body').empty();
            jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
        }
    });
    // }

    $('#foto').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    const fileChange = () => {
        let imgNew = document.getElementById('img')
        let input = document.getElementById('file-1')
        // input.onchange = (e) => {
        if (input.files[0]) {
            imgNew.src = URL.createObjectURL(input.files[0])
        }
        // }
    }

    function cargarImagenesHospital() {
        $.ajax({
            type: "get",
            url: `{{ route('hospital.imagenes.box.verFotos')}}`,
            datatype: "json",
            data: {
                'id_solicitud': '{{$solicitud->id}}',
            },
            beforeSend:function(){
                $("#image_container").empty().append(`
                        <div style="text-align: center;display: grid;place-items: center;width: 100%; height: 200px;">
                            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                        </div>`)
            },
            success: function(data) {
                let html = ``;
                if(data.status == "success"){
                    if (data.imagenes.length > 0) {
                        data.imagenes.map((imagen) => {
                            html += `<div class="col-md-6" style="margin-top: 20px;">
                                        <a data-toggle="modal" data-target="#foto" data-remote="{{ url('historiaclinica/video/mostar_foto/eliminar2/${imagen.id}') }}">
                                            <img src="{{asset('public')}}/${imagen.nombre}" style='width:100%;' alt="">
                                        </a>
                                    </div>`
                        })
                    }
                }

                if (html == '') {
                    $("#image_container").empty().append(`<div class="col-md-12" style="margin-top: 20px;">
                                    <h4>No se ha cargado imagenes</h4>
                                </div>`)
                    return;
                }
                $("#image_container").empty().append(html);

            },
            error: function(data) {

            }
        });


    }
    window.onload = function() {
        cargarImagenesHospital()
    }
    function ver_laboratorio(id){
    $.ajax({
        type: "get",
        url: "{{url('hospital/vtlaboratorio/modal')}}/"+id,
        data: {
        // 'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            $("#examen_div").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
    $('#examen_modal').modal('show');
}
function ver_consulta(id){
    $.ajax({
        type: "get",
        url: "{{url('hospital/consultas_listado/modal/')}}/"+id,
        data: {
        // 'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            $("#consulta_div").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
    $('#consulta_modal').modal('show');
}
    function ver_estudio_vt(id){
        $.ajax({
            type: "get",
            url: "{{url('hospital/vtimagen/modal')}}/"+id,
            data: {
            // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {
                $("#examen_div").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
        $('#examen_modal').modal('show');
    }

    function cargar_formulario_013(id_procedimiento){
        $.ajax({
            type: "GET",
            url:"{{url('vthospital/proc_endoscopicos/formulario013/cargar')}}/" + id_procedimiento,
            data: "",
            datatype: "html",
            success: function(datahtml){
                
                $("#pasos").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#pasos").offset().top
                }, 1000);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
</script>