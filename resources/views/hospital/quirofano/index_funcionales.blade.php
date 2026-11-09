<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
<script src="{{ asset ("/js/dropzone.js") }}"></script>

<style>
    .surgery-functional-title,
    .surgery-functional-summary,
    .surgery-functional-detail-header {
        background: linear-gradient(110deg, #0b426d 0%, #087eb9 58%, #27b8c5 100%) !important;
        color: #fff !important;
    }

    .surgery-functional-title {
        min-height: 52px;
        padding: 14px 18px !important;
        display: flex;
        align-items: center;
    }

    .surgery-functional-title h4 {
        margin: 0;
        font-size: 18px !important;
        font-weight: 700;
    }

    .surgery-functional-summary {
        padding: 14px 18px !important;
        display: grid;
        grid-template-columns: 1.15fr 1.45fr 1fr 42px;
        gap: 18px;
        align-items: center;
        border-radius: 12px !important;
    }

    .surgery-history-item {
        margin: 0 0 14px;
        padding: 0 !important;
        border: 1px solid #cfe2ea;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 7px 18px rgba(17, 74, 105, .07);
    }

    .surgery-history-toggle {
        width: 38px;
        height: 38px;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, .45) !important;
        border-radius: 9px !important;
        background: rgba(255, 255, 255, .14) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .surgery-history-content {
        padding: 14px !important;
        background: #f7fbfd;
    }

    .surgery-functional-section {
        margin-bottom: 14px;
        border: 1px solid #d5e6ed !important;
        border-radius: 11px !important;
        overflow: hidden;
        background: #fff;
    }

    .surgery-functional-section > .card-header {
        min-height: 48px;
        padding: 10px 14px !important;
        background: linear-gradient(110deg, #0b426d 0%, #087eb9 58%, #27b8c5 100%) !important;
        color: #fff !important;
    }

    .surgery-evolution-header {
        display: grid;
        grid-template-columns: max-content 1fr 42px;
        gap: 12px;
        align-items: center;
    }

    .surgery-evolution-header .section-title {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
    }

    .surgery-evolution-list {
        padding: 12px !important;
    }

    .surgery-evolution-card {
        margin-bottom: 10px;
        border: 1px solid #d8e7ec;
        border-radius: 9px;
        overflow: hidden;
    }

    .surgery-evolution-card:last-child { margin-bottom: 0; }

    .surgery-evolution-card .evolution-card-header {
        padding: 9px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #edf7fa;
        color: #16445d;
    }

    .surgery-evolution-card .evolution-card-header strong { flex: 1; }

    .surgery-evolution-card .evolution-card-body {
        padding: 12px;
        font-size: 12px;
    }

    .surgery-empty-history {
        margin: 0 14px 14px;
        padding: 24px;
        border: 1px dashed #b9d4df;
        border-radius: 10px;
        color: #617c89;
        text-align: center;
        background: #f8fcfd;
    }

    .surgery-functional-summary .summary-label {
        display: block;
        margin-bottom: 4px;
        color: rgba(255, 255, 255, .78);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .surgery-functional-summary .summary-value {
        display: block;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .surgery-functional-detail-header {
        min-height: 54px;
        padding: 10px 16px !important;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .surgery-functional-edit {
        width: 36px;
        height: 36px;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 8px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
    }

    .surgery-functional-detail-header .detail-title {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .surgery-functional-summary {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .surgery-history-toggle { justify-self: end; }

        .surgery-evolution-header {
            grid-template-columns: 1fr 42px;
        }

        .surgery-evolution-header .surgery-add-evolution {
            grid-column: 1 / -1;
        }
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary colorbasic surgery-functional-title">
        <h4 class="colorbasic"><i class="fa fa-history" aria-hidden="true"></i> Historial de cirugías</h4>
    </div>
    <div class="card-body" style="padding: 14px;">
            
                     <!-- DIV DE LOS PROCEDIMIENTOS -->
                    @php
                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
                    @endphp
                    @forelse ($procedimientos1 as $value)
                        @php
                            if($solicitud->paciente->sexo == 1){
                                $sexo = "MASCULINO";
                            }else{
                                $sexo = "FEMENINO";
                            }
                            $seguro =  \Sis_medico\Seguro::find($value->hc_p_id_seguro);
                            $nombre_procedimiento = "";
                            $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
                            $alergias = \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
                            if($alergias == "[]"){
                                $alergia = "No";
                            }else{
                                $alergia  = "";
                                foreach ($alergias as $value_alergia) {
                                    if($alergia == ""){
                                        $alergia = $value_alergia->principio_activo->nombre;
                                    }else{
                                        $alergia = $alergia.", ".$value_alergia->principio_activo->nombre;
                                    }

                                }
                            }

                            $procedimientos = \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
                            //dd($value);
                            if($procedimientos && $procedimientos->id_procedimiento != null && $procedimientos->procedimiento_completo){
                                $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
                            }
                            $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

                            if($adicionales->isNotEmpty()){
                                $mas = true;
                                $nombre_procedimiento = "";

                                foreach($adicionales as $value2)
                                {
                                    if($mas == true){
                                        $nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre;
                                        $mas = false;
                                    }
                                    else{
                                        $nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre;
                                    }
                                }
                            }
                            $edad = Carbon\Carbon::createFromDate(substr($solicitud->paciente->fecha_nacimiento, 0, 4), substr($solicitud->paciente->fecha_nacimiento, 5, 2), substr($solicitud->paciente->fecha_nacimiento, 8, 2))->age;
                            $contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
                            
                        @endphp
                            <div class="col-md-12 surgery-history-item">
                                <div class="card-header bg bg-primary colorbasic surgery-functional-summary">
                                        <div>
                                            <span class="summary-label">Fecha</span>
                                            @if(!is_null($value->f_operacion))
                                                @php
                                                    $dia =  Date('N',strtotime($value->f_operacion));
                                                    $mes =  Date('n',strtotime($value->f_operacion)); 
                                                    $aux = intval($dia)-1;
                                                    $ms  = intval($mes)-1;
                                                @endphp
                                                    <span class="summary-value">{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</span>
                                            @else
                                                @php
                                                    $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                                    $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); 
                                                    $aux = intval($dia)-1;
                                                    $ms  = intval($mes)-1;
                                                @endphp
                                                    <span class="summary-value">{{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="summary-label">Procedimiento</span>
                                            <span class="summary-value">{{$nombre_procedimiento ?: 'No registrado'}}</span>
                                        </div>
                                        <div>
                                            <span class="summary-label">Médico examinador</span>
                                            <span class="summary-value">Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
                                        </div>
                                        <button class="btn btn-sm surgery-history-toggle collapsed" type="button" data-toggle="collapse" data-target="#surgeryHistory{{$value->id_procedimiento}}" aria-expanded="false" aria-controls="surgeryHistory{{$value->id_procedimiento}}" title="Mostrar u ocultar cirugía">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </button>
                                  
                                    
                                    <!-- /.card-tools -->
                                </div>
                                <div class="card-body collapse surgery-history-content" id="surgeryHistory{{$value->id_procedimiento}}">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <br>
                                                <div class="card-header bg bg-primary colorbasic surgery-functional-detail-header">
                                                    <button class="btn btn-warning btn-xs waves-effect waves-float waves-light surgery-functional-edit" type="button" onclick="editar_procedimiento_funcional({{$value->id_procedimiento}}, '{{$solicitud->paciente->id}}');" aria-label="Editar procedimiento">
                                                        <span class="fa fa-pencil-square-o"></span>
                                                    </button>
                                                    <span class="detail-title">Detalles del procedimiento</span>
                                                </div>
                                                <div class="card-body" style="font-size: 11px;" id="procedimiento{{$value->id_procedimiento}}" >
                                                    <div class="row">
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="">Procedimiento</span>
                                                        </div>

                                                        <div class="col-12">
                                                            <span>
                                                                {{$nombre_procedimiento ?: 'No registrado'}}
                                                            </span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        @php
                                                           $hc_seguro = \Sis_medico\Seguro::where('id', $value->hc_p_id_seguro)->first();
                                                        @endphp
                                                        <div class="col-12">
                                                            <span style="">Seguro</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span>
                                                                @if(!is_null($hc_seguro))
                                                                  {{$hc_seguro->nombre}}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="">Hallazgos</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span><?php echo strip_tags($value->hallazgos); ?></span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="">Conclusiones</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-4">
                                                            <span style="">M&eacute;dico Examinador</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <span style="">&nbsp;</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <span style="">&nbsp;</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="card surgery-functional-section">
                                                <div class="card-header surgery-evolution-header">
                                                    <button type="button" class="btn btn-info btn-sm surgery-add-evolution" onclick="agregar_evolucion({{$value->id_procedimiento}});">
                                                        <i class="fa fa-file-medical" aria-hidden="true"></i> Agregar evolución
                                                    </button>
                                                    <span class="section-title">Historial de evoluciones de la cirugía</span>
                                                    <button class="btn btn-sm surgery-history-toggle" type="button" data-toggle="collapse" data-target="#surgeryEvolutionHistory{{$value->id_procedimiento}}" aria-expanded="true" aria-controls="surgeryEvolutionHistory{{$value->id_procedimiento}}" title="Mostrar u ocultar evoluciones">
                                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <div class="card-body collapse show surgery-evolution-list" id="surgeryEvolutionHistory{{$value->id_procedimiento}}">
                                                    <div id="evolucion_agregar{{$value->id_procedimiento}}">
                                                        @php
                                                            $evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)
                                                                ->where('estado', 1)
                                                                ->orderBy('secuencia', 'desc')
                                                                ->get();
                                                        @endphp
                                                        @forelse($evoluciones as $evolucion)
                                                            <div class="surgery-evolution-card">
                                                                <div class="evolution-card-header">
                                                                    <button class="btn btn-warning btn-xs" type="button" onclick="editar_evolucion({{$evolucion->id}}, '{{$solicitud->paciente->id}}');" aria-label="Editar evolución">
                                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                                    </button>
                                                                    <strong>{{trim(strip_tags($evolucion->motivo)) ?: 'Evolución sin motivo registrado'}}</strong>
                                                                    <button class="btn btn-info btn-xs" type="button" data-toggle="collapse" data-target="#surgeryEvolution{{$evolucion->id}}" aria-expanded="false" aria-controls="surgeryEvolution{{$evolucion->id}}" title="Mostrar u ocultar evolución">
                                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="collapse evolution-card-body" id="surgeryEvolution{{$evolucion->id}}">
                                                                    <div id="evolucion{{$evolucion->id}}">
                                                                        <div><strong>Motivo</strong></div>
                                                                        <div>{{$evolucion->motivo}}</div>
                                                                        <div class="mt-1"><strong>Detalle</strong></div>
                                                                        <div>{!! $evolucion->cuadro_clinico !!}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted text-center">Esta cirugía todavía no tiene evoluciones registradas.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12" >
                                            <div class="card" style="border-top-width: 0px; margin-bottom: 0px;">
                                                <center>
                                                <div  class="card-header bg bg-primary colorbasic">
                                                    <div class="col-12">
                                                        <span>IMAGENES DEL PROCEDIMIENTO</span>
                                                    </div>
                                                    
                                                </div>
                                                </center>
                                                <div class="card-body">
                                                <div class=" col-12">
                                                    <table class="table table-bordered  dataTable" >
                                                        <tbody style="font-size: 12px; ">
                                                            @php

                                                            $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $value->id_protocolo)->where('estado', '1')->orderBy('created_at', 'desc')->get();

                                                            @endphp
                                                            <div class="row">
                                                            @foreach($imagenes as $imagen)

                                                            <div class="col-md-3" style='margin: 10px 0;padding: 2px;' >
                                                                @php
                                                                    $explotar = explode( '.', $imagen->nombre);
                                                                    $extension = end($explotar);
                                                                @endphp
                                                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" style='width:100%;'>
                                                                    </a>
                                                                @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                        <img  src="{{asset('imagenes/pdf.png')}}" style='width:100%;'>
                                                                    </a>
                                                                @elseif(($extension == 'mp4'))
                                                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                        <img  src="{{asset('imagenes/video.png')}}" style='width:100%;'>
                                                                    </a>
                                                                @else
                                                                    @php
                                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                                        $d1 = $variable[3];
                                                                        $d2 = $variable[4];
                                                                        $d3 = $variable[5];
                                                                        $ruta = "http%3A%2F%2F186.68.76.210%3A86%2F".$d1."%2Fstorage%2Fapp%2Fhc_ima%2F".$imagen->nombre;
                                                                    @endphp
                                                                    <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                        <img  src="{{asset('imagenes/office.png')}}" width="70%">
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            @endforeach
                                                            </div>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="conclusiones" class="col-md-12 control-label"><b>Ingreso de Imagenes del Procedimiento</b></label>
                                                        <div class="col-12">
                                                        <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="upload1_{{$value->id_protocolo}}">
                                                            <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <div class="fallback">
                                                            </div>
                                                        </form>
                                                    </div>


                                                <script type="text/javascript">
                                                    $("#upload1_{{$value->id_protocolo}}").dropzone({url: "{{route('hc_video.guardado_foto2')}}"});

                                                </script>
                                                    </div>
                                                </div>
                                                

                                                    
                                                
                                            </div>
                                        </div>
                                        <div class="col-12" >
                                            <div class="card" style="border-top-width: 0px; margin-bottom: 0px;">
                                                <center>
                                                <div  class="card-header bg bg-primary colorbasic">
                                                    <div class="col-12">
                                                        <span>GUARDADO DE DOCUMENTOS &amp; ANEXOS</span>
                                                    </div>
                                                    
                                                </div>
                                                </center>
                                                <div class="card-body">
                                                <div class=" col-12">
                                                    <table class="table table-bordered  dataTable" >
                                                        <tbody style="font-size: 12px; ">
                                                            @php

                                                            $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '2')->get();

                                                            @endphp
                                                            <div class="row">
                                                            @foreach($documentos as $imagen)
                                                                <div class="col-md-4 col-sm-6 col-12" style='margin: 10px 0;text-align: center;' >

                                                                    @php
                                                                        $explotar = explode( '.', $imagen->nombre);
                                                                        $extension = end($explotar);
                                                                    @endphp
                                                                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div class="col-12">
                                                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
                                                                                    <div class="col-12">
                                                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(($extension == 'pdf'))
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div class="col-12">
                                                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
                                                                                    <div class="col-12">
                                                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                    </div>
                                                                                 </a>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        @php
                                                                            $variable = explode('/' , asset('/hc_ima/'));
                                                                            $d1 = $variable[3];
                                                                            $d2 = $variable[4];
                                                                            $d3 = $variable[5];

                                                                        @endphp
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div class="col-12">
                                                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" >
                                                                                    <div class="col-12">
                                                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            </div>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @php
                                                    $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
                                                    $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
                                                    $cant_total = $cant_anexo + $cant_estud;
                                                @endphp

                                                    <div class="row">
                                                <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                                                    <div class="row" style="text-align: center;">
                                                        <div class="col-sm-6 col-12"><b>Ingreso de Imagenes del Procedimiento</b></div>
                                                        <div class="col-sm-6 col-12" >
                                                            @if($cant_total > 0)
                                                            <span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_anexo}} ANEXO(S) </b></span>
                                                            @else
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="upload_{{$value->id_protocolo}}">
                                                            <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <div class="fallback">
                                                            </div>
                                                        </form>
                                                    </div>


                                                <script type="text/javascript">
                                                    $("#upload_{{$value->id_protocolo}}").dropzone({url: "{{route('hc_video.guardado_foto2_documento')}}"});

                                                </script>
                                                </div>
                                            </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!--estudios-->
                                        <div class="col-12" >
                                            <div class="card" style="border-top-width: 0px; margin-bottom: 0px;">
                                                <center>
                                                <div  class="card-header bg bg-primary colorbasic" >
                                                    <div class="col-12">
                                                        <span>GUARDADO DE ESTUDIOS</span>
                                                    </div>
                                                    
                                                </div>
                                                </center>
                                                <div class="card-body">
                                                <div class=" col-12">
                                                    <table class="table table-bordered  dataTable" >
                                                        <tbody style="font-size: 12px; ">
                                                            @php

                                                            $documentos = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo',$value->id_protocolo)->orderBy('id', 'desc')->where('estado', '3')->get();

                                                            @endphp
                                                            <div class="row">
                                                            @foreach($documentos as $imagen)
                                                                <div class="col-md-4 col-sm-6 col-12" style='margin: 10px 0;text-align: center;' >

                                                                    @php
                                                                        $explotar = explode( '.', $imagen->nombre);
                                                                        $extension = end($explotar);
                                                                    @endphp
                                                                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div class="col-12">
                                                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
                                                                                <div>
                                                                                    <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(($extension == 'pdf'))
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div>
                                                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
                                                                                    <div class="col-12">
                                                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        @php
                                                                            $variable = explode('/' , asset('/hc_ima/'));
                                                                            $d1 = $variable[3];
                                                                            $d2 = $variable[4];
                                                                            $d3 = $variable[5];

                                                                        @endphp
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                                                    <div class="col-12">
                                                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p style="font-size: 12px">
                                                                                            @if(strlen($imagen->nombre_anterior) >= '20')
                                                                                                {{substr($imagen->nombre_anterior,0,20)}}...
                                                                                                @else
                                                                                                    {{$imagen->nombre_anterior}}
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" ><!-- ruta 0 desde la historia clinica -->
                                                                                    <div class="col-12">
                                                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            </div>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                    @php
                                                        $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','2')->get()->count();
                                                        $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id_protocolo)->where('estado','3')->get()->count();
                                                        $cant_total = $cant_anexo + $cant_estud;
                                                    @endphp
                                                    <div class="row">
                                                        <div class="form-group col-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                                                            <div class="row" style="text-align: center;">
                                                                <div class="col-sm-6 col-12"><b>Ingreso de Estudios del Procedimiento</b></div>
                                                                <div class="col-sm-6 col-12" >
                                                                    @if($cant_total > 0)
                                                                    <span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_estud}} ESTUDIO(S)</b></span>
                                                                    @else
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <form method="POST" action="{{route('hc_video.guardado_foto2')}}" enctype="multipart/form-data" class="dropzone" id="upload2_{{$value->id_protocolo}}" >
                                                                    <input type="hidden" name="id_hc_protocolo" value="{{$value->id_protocolo}}">
                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                    <div class="fallback" >

                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $("#upload2_{{$value->id_protocolo}}").dropzone({
                                                        url: "{{route('hc_video.guardado_foto2_estudios')}}",
                                                        success: function(file, response){
                                                            alert("entra, 22");
                                                            console.log('entra');
                                                        },
                                                        error: function(file, response) {
                                                                  alert(response);
                                                       }
                                                    });

                                                </script>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        
                    @empty
                        <div class="surgery-empty-history">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            No existen cirugías registradas para este paciente.
                        </div>
                    @endforelse
            
        
    </div>  
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/dropzone.js") }}"></script>
<script src="{{asset('hc4/js/grabador/RecordRTC.js')}}"></script>
<script src="{{asset('hc4/js/grabador/gif-recorder.js')}}"></script>
<script src="{{asset('hc4/js/grabador/getScreenId.js')}}"></script>
<!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
<script src="{{asset('plugins/video/gumadapter.js')}}"></script>
<script type="text/javascript">

    $(document).on('shown.bs.collapse hidden.bs.collapse', '.surgery-history-content, .surgery-evolution-list, .evolution-card-body', function(event) {
        var targetId = event.target.id;
        if (!targetId) return;

        var expanded = event.type === 'shown';
        var $toggle = $('[data-target="#' + targetId + '"]');
        $toggle.find('i.fa').toggleClass('fa-plus', !expanded).toggleClass('fa-minus', expanded);
    });

    function editar_evolucion(id, id_paciente){
        var entra = id;
            $.ajax({
            type: "POST",
            url: "{{route('quirofano.editar_evolucion')}}/"+id+'/'+id_paciente,
            data: {'_token': '{{ csrf_token() }}'},
            datatype: "html",
            success: function(datahtml, entra){
                $("#evolucion"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    function agregar_evolucion(id, id_evolucion){
        var entra = id;
        $.ajax({
            type: "GET",
            url: "{{route('quirofano.agregar_evolucion')}}/"+id,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                anterior  = $("#evolucion_agregar"+id).html();
                $("#evolucion_agregar"+id).html(datahtml+anterior);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    Dropzone.options.addimage = {
      acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif, .rtf",
      init: function() {
        this.on("error", function(file, response) {
            alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
            console.log(response);
        });
        this.on("success", function(file, response) {
            console.log(response);
        });
      }
    };

    function editar_procedimiento_funcional(id, id_paciente){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('quirofano.editar_funcional')}}/"+id+'/'+id_paciente,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                $("#procedimiento"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function agregar_procedimiento_funcional(){
        //alert("agregar");
        contador = parseInt($('#contador_funcionales').val());
        hc_endoscopico = parseInt($('#id_endoscopico').val());
        hc_funcional = parseInt($('#id_funcional').val());
        if(contador>0){
            agregar_procedimiento_hc('funcional_id');
        }else if (hc_func+ional >0) {
            url_datos = "{{asset('/public/procedimiento2/selecciona2/')}}/1/{{$solicitud->id_paciente}}/"+hc_funcional;
            agregar_procedimiento_tipo(url_datos);
        }else if (hc_endoscopico >0) {
            url_datos = "{{asset('/procedimiento2/selecciona2/')}}/1/{{$solicitud->id_paciente}}/"+hc_endoscopico;
            agregar_procedimiento_tipo(url_datos);
            //console.log(url_datos);
        }else{
            $.ajax({
                async: true,
                type: "GET",
                url: "{{route('proc_fun.selecciona_procedimiento',['tipo' => '1', 'paciente' => $solicitud->id_paciente ])}}",
                data: "",
                datatype: "html",
                success: function(datahtml){
                    $("#area_trabajo").html(datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }
    }

    function agregar_imagen(array, div){
        var div_anterior =$("#"+div).html();
        for (i = 0; i < array.length; i++) {
          console.log(array[i]);
          $.ajax({
                async: true,
                type: "GET",
                url: "{{route('hc_4.mostar_div')}}/"+array[i],
                data: "",
                datatype: "html",
                success: function(datahtml){
                    //alert(datahtml);
                    //console.log(datahtml);
                    $("#"+div).html(div_anterior+datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }
    }
    function cargar_tabla(id){
        $.ajax({
                url:"{{route('epicrisis.cargar22')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){

                    var table = document.getElementById("tdiagnostico"+id);

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.descripcion;
                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = value.ingreso_egreso;
                        var cell5 = row.insertCell(4);
                        cell5.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });

                }
            })
    }

    function eliminar(id_h, id){
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico"+id).deleteRow(i);

        $.ajax({
          type: 'delete',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          data: {'_token': '{{ csrf_token() }}'},
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });
    }
    
    function guardar_cie10_PRO(id_procedimiento, id_hc){
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_procedimiento).val(), 'pre_def': $("#pre_def"+id_procedimiento).val(),'hcid': id_hc, 'hc_id_procedimiento': id_procedimiento, 'in_eg': $("#ing_egr"+id_procedimiento).val(), 'id_paciente': '{{$solicitud->id_paciente}}'  },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+id_procedimiento);
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = data.in_eg;
                var cell5 = row.insertCell(4);
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

            },
            error: function(data){

                }
        })
    }
</script>
 

