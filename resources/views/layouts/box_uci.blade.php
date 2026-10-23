<style>
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
</style>

@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
<div class="col-md-12">
    <input type="hidden" id="solicitudGeneral" value="{{$solicitud->id}}">

    <div class="row">
        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <!---->
                <!---->
                
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">1</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.Admision')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="primer_paso()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('hospitalizacion.ApellidosyNombres')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('hospitalizacion.CeduladeCuidadania')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->id_paciente }} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('hospitalizacion.Ciudad')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->ciudad }} </span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('hospitalizacion.Telefono')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{ $solicitud->paciente->telefono1 }} </span>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-80">
                <!---->
                <!---->
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">2</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.Evolución')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="evolucion();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                        <b> {{trans('hospitalizacion.Fecha')}}</b>
                        </div>
                        <div class="col-md-12">
                            <span> {{$evolucion->created_at}} </span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Motivo')}}</b>
                        </div>
                        <div class="col-md-12">
                            <span> {{$evolucion->motivo}}</span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.ExamenFisico')}}</b>
                        </div>
                        <div class="col-md-12">
                            <span> {{$child_pugh->examen_fisico}}</span>
                        </div>
                    </div>
                    
                    


                </div>
                <!---->
                <!---->
            </div>
        </div>

        <!-- <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">3</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.Medicamentos')}}
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
        </div> -->
      
      
        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">3</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.SolicituddeExámenes')}}
                        </h4>
                    </div>

                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick=""> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    
                    <div class="row">
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_laboratorio()">

                                <span style="font-size: 10px;">{{trans('hospitalizacion.LABORATORIO.nhddd')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_procedimientos()">
                                <span style="font-size: 10px;">{{trans('hospitalizacion.QUIRURGICOS')}}</span>
                            </button></center>
                        </div>
                        
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_imagenes()">
                                <span style="font-size: 10px;">{{trans('hospitalizacion.IMAGENES')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_interconsultas()">
                                <span style="font-size: 10px;">{{trans('hospitalizacion.INTERCONSULTAS')}}</span>
                            </button></center>
                        </div> 
                        
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_endoscopicos()">
                                <span style="font-size: 10px;">{{trans('hospitalizacion.ENDOSCOPICOS')}}</span>
                            </button></center>
                        </div>
                        <div class="col-md-6" style="padding: 5px;"><center>
                            <button class="btn btn-primary btn-xs" style="color: white; width: 100%;" onClick="carga_ordenes_funcionales()">
                                <span style="font-size: 10px;">{{trans('hospitalizacion.FUNCIONALES')}}</span>
                            </button></center>
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
                    <div class="d-flex align-items-center"> <span class="sradio">4</span>
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

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">5</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('hospitalizacion.ResultadodeExamenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="mostrarExamenes()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br>
                    <div class="row">
                        
                        <div class="col-md-6" style="text-align: center;">
                                Fecha :<br> @if(empty($examenes)) @else {{$examenes->created_at}} @endif
                        </div>
                        <div class="col-md-6" style="text-align: center;">
                                Acci&oacuten :<br>
                                @if(empty($examenes))   @else  <a  class="btn btn-primary btn-xs"  href="{{route('hospitalizacion.imprimir',['id' => $examenes->id])}}" target="_blank">{{trans('hospitalizacion.DescargarExamen')}} @endif</a>
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
                    <div class="d-flex align-items-center"> <span class="sradio">6</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('Evolución Enfermeria')}}
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
                            <span></span>
                        </div>
                        <div class="col-md-12">
                            <b> {{trans('hospitalizacion.Medicinas')}}</b>
                        </div>
                        <div class="col-md-12">
                            
                        </div>

                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>
        

    </div>
</div>
<script>
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

    function evolucion() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_evolucion',['id' => $solicitud->id])}}",
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


    function diagnostico(){
        var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_diagnostico')}}",
            data: {
                'ep': id_orden,
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

    function medidas_generales(){
        var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_medidas_generales')}}",
            data: {
                'ep': id_orden,
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

    function tratamiento(){
        var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_tratamiento')}}",
            data: {
                'ep': id_orden,
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

   

    function medicamentos(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_medicamentos',['id' => $solicitud->id])}}",
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

    function examenes(){
        var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_examenes')}}",
            data: {
                'ep': id_orden,
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

    function traspaso_salas(){
        var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_salas')}}",
            data: {
                'ep': id_orden,
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

    function descargo_enfermeria(){

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

    function mostrarExamenes(){
        $.ajax({
            type: "get",
            url: "{{route('hospitalizacion.cargar_examenes',['id' => $solicitud->id])}}",
            datatype: "html",
            success: function(datahtml, data) {
                console.log(data);
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

    function carga_interconsultas(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('decimo.interconsulta',['id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#pasos").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#pasos").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function carga_ordenes_funcionales(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 1 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_imagenes(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 2 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }
    function carga_ordenes_endoscopicos(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 0 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function carga_ordenes_procedimientos(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_procedimiento',['id' => $solicitud->id, 'tipo' => 3 ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#content").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#content").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
    }

    function carga_ordenes_laboratorio(){
        @if($rolUsuario==3)
        $.ajax({
            type: "GET",
            url: "{{route('hospital.decimo_laboratorio',['id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#pasos").html(datahtml);
                $('html, body').animate({
                    scrollTop: $("#pasos").offset().top
                }, 1000)
            },
            error:  function(){
                alert('error al cargar');
            }
        });
        @else
        alert("No tiene permiso de usuario para modificar");
        @endif
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
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }



</script>