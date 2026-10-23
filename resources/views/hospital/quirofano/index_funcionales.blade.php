<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
<script src="{{ asset ("/js/dropzone.js") }}"></script>

<div class="card">
    <div class="card-header bg bg-primary colorbasic">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <!--label class="colorbasic sradio" > 3</label--> 
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px" ><b>Cirugia</b>  </label>
                </div>
                <div class="col-md-2">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <br>
            
                     <!-- DIV DE LOS PROCEDIMIENTOS -->
                    @php
                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
                    @endphp
                    @foreach ($procedimientos1 as $value)
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
                            if($procedimientos->id_procedimiento != null){
                                $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
                            }
                            $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

                            $mas = true;
                            $nombre_procedimiento = "";

                            foreach($adicionales as $value2)
                            {
                                if($mas == true){
                                    $nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre  ;
                                    $mas = false;
                                 }
                                else{
                                    $nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre  ;
                                 }
                            }
                            $edad = Carbon\Carbon::createFromDate(substr($solicitud->paciente->fecha_nacimiento, 0, 4), substr($solicitud->paciente->fecha_nacimiento, 5, 2), substr($solicitud->paciente->fecha_nacimiento, 8, 2))->age;
                            $contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
                            
                        @endphp
                            <div class="col-md-12" style="padding: 0;">
                                <div  class="card-header bg bg-primary colorbasic">
                                        <div class="col-md-4">
                                            @if(!is_null($value->f_operacion))
                                                @php
                                                    $dia =  Date('N',strtotime($value->f_operacion));
                                                    $mes =  Date('n',strtotime($value->f_operacion)); 
                                                    $aux = intval($dia)-1;
                                                    $ms  = intval($mes)-1;
                                                @endphp
                                                    <b>{{$dias[$aux]}} {{substr($value->f_operacion,8,2)}} de {{$meses[$ms]}} del {{substr($value->f_operacion,0,4)}}</b>
                                            @else
                                                @php
                                                    $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                                    $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); 
                                                    $aux = intval($dia)-1;
                                                    $ms  = intval($mes)-1;
                                                @endphp
                                                    <b> {{$dias[$aux]}} {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de {{$meses[$ms]}} del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                                            @endif
                                        </div>
                                        <div class="col-md-5">
                                            <div>
                                                <span style=" font-size: 12px">Procedimiento:</span>
                                                <span style="font-size: 12px"> {{$value->nombre}} </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <span style=" font-size: 12px">Dr (a) </span>
                                                <span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
                                            </div>
                                        </div>
                                  
                                    
                                    <!-- /.card-tools -->
                                </div>
                                <div class="card-body" style="padding: 0px;">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <br>
                                                <div  class="card-header bg bg-primary colorbasic">
                                                    <div class="col-3" >
                                                        <a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editar_procedimiento_funcional({{$value->id_procedimiento}}, '{{$solicitud->paciente->id}}');" ><span class="fa fa-pencil-square-o"></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-9">
                                                      <span>Detalles del Procedimientos</span>
                                                    </div>
                                                </div>
                                                <div class="card-body" style="font-size: 11px;" id="procedimiento{{$value->id_procedimiento}}" >
                                                    <div class="row">
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="">Procedimiento</span>
                                                        </div>

                                                        <div class="col-12">
                                                            <span>
                                                                {{$value->nombre}}
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

                                        <div class="col-12" >
                                            <div class="card">
                                                <div  class="card-header bg bg-primary colorbasic">
                                                    
                                                    <div class="col-md-3 col-sm-3 col-4">
                                                        <a class="btn btn-info btn-block" style="color: white; width: 100%; height: 100%; padding-left: 0px;padding-right: 0px; border: 2px solid white;" onClick="agregar_evolucion({{$value->id_procedimiento}});" >
                                                            <div class="row" style="margin-left: 0px; margin-right: 0px; ">
                                                                <div class="col-12" style="padding-left: 5px;padding-right: 5px;">
                                                                    <img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
                                                                    <label style="font-size: 10px; ">AGREGAR EVOLUCION</label>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-7" style="padding-left: 5px">
                                                        <span> <b>Historial de Evoluciones del Procedimiento</b></span>
                                                    </div>

                                                </div>
                                                <div class="card-body" style="padding: 0px;" id="evolucion_agregar{{$value->id_procedimiento}}">
                                                    @php
                                                        $evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
                                                    @endphp
                                                    @foreach($evoluciones as $evolucion)
                                                    <br>
                                                    <div class="card">
                                                        <div  class="card-header bg bg-primary colorbasic">
                                                            
                                                            <div class="col-2" style="margin-right: 10px" >
                                                                
                                                                <a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editar_evolucion({{$evolucion->id}}, '{{$solicitud->paciente->id}}');"><span class="fa fa-pencil-square-o"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-9" style="text-align: center;">
                                                                <span style="padding-top: 5px;">Detalles de la Evolucion</span>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="card-body" style="font-size: 11px;" id="evolucion{{$evolucion->id}}">
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <span style="">Motivo</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span>{{$evolucion->motivo}}</span>
                                                                </div>
                                                                <div class="col-12">&nbsp;</div>
                                                                <div class="col-12">
                                                                    <span style="">Detalle</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span><?php echo $evolucion->cuadro_clinico; ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" style="height: 5px;"></div>
                                                    @endforeach
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
                                                                        <img  src="{{asset('imagenes/descarga/thumb')}}/{{$imagen->nombre}}" style='width:100%;'>
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
                        
                    @endforeach
            
        
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

    function editar_evolucion(id, id_paciente){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('quirofano.editar_evolucion')}}/"+id+'/'+id_paciente,
            data: "",
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
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
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
 

