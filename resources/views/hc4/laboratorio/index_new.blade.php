<style type="text/css">
.parent {
    overflow-y: scroll;
    height: 462px;
}

.parent::-webkit-scrollbar {
    width: 8px;
}

/* this targets the default scrollbar (compulsory) */
.parent::-webkit-scrollbar-thumb {
    background: #004AC1;
    border-radius: 10px;
}

.parent::-webkit-scrollbar-track {
    width: 10px;
    background-color: #004AC1;
    box-shadow: inset 0px 0px 0px 3px #56ABE3;
}

.boton-lab {
    font-size: 14px;
    width: 90%;
    background-color: #004AC1;
    color: white;
    text-align: center;
    margin-bottom: 10px;
}
</style>
<!-- Etiqueta container-fluid contenedora para poder ocupar todo el ancho-->
<div id="lab_index" class="container-fluid" style="padding-left: 8px">
    <div class="row">
        <div class="col-12"
            style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;">
            <!--Agrupamos la fila en 12 columnas-->
            <div class="col-12"
                style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#56ABE3;">
                <!--Titulo-->
                <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;">
                    <img style="width: 35px; margin-left: 15px; margin-bottom: 5px"
                        src="{{asset('/')}}hc4/img/iconos/lab.png">
                    <b>LABORATORIO</b>
                    @if(!is_null($paciente))
                    <center>
                        <div class="col-12" style="padding-bottom: 20px;">
                            <h1
                                style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px">
                                <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                                    {{$paciente->nombre1}} {{$paciente->nombre2}}
                                </b>
                            </h1>
                        </div>
                    </center>
                    @endif
                </h1>
                <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px">
                    <!--Hacemos el llamado al estilo parent-->
                    <div class="parent" style="background-color: #56ABE3">
                        <div style=" margin-right: 30px;">
                            <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                            @foreach($ordenes as $value) 
                            <div class="box collapsed-box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                <!--Cabecera-->
                                <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">

                                    @if(!is_null($value))
                                      @php
                                        $fecha_orden = $value->fecha_orden;
                                        $dia = Date('N',strtotime($value->fecha_orden));
                                        $mes = Date('n',strtotime($value->fecha_orden));
                                        $txt_mes = ['','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                        $txt_dia = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo' ];
                                      @endphp

                                     
                                    @endif
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span>
                                          <b style="font-family: 'Helvetica';" class="box-title">Fecha: {{$txt_dia[$dia]}} {{substr($value->fecha_orden,8,2)}} de  {{$txt_mes[$mes]}} del {{substr($value->fecha_orden,0,4)}}</b>
                                        </span>
                                      </div>
                                      <div class="col-md-12"><br></div>
                                      <div class="col-md-6">
                                        <span>
                                          <b style="font-family: 'Helvetica';" class="box-title">Medico: {{ $value->dapellido1 }} {{ $value->dnombre1 }}</b>
                                        </span>
                                      </div>
                                      <div class="col-md-6">
                                        <span>
                                          <b style="font-family: 'Helvetica';" class="box-title">Seguro: {{$value->snombre}}</b>
                                        </span>
                                      </div>
                                    </div>

                                    <div class="pull-right box-tools ">
                                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse"
                                            title="" data-original-title="Collapse" id="fili" onclick="cargar_load('{{$value->id}}')">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--Cuerpo-->
                                <div class="box-body">
                                    <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                                        <div class="col-12">
                                            <div class="row ">
                                                <!--div class="col-md-8 col-12"
                                                    style="border: 2px solid #004AC1;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 100%; margin-bottom: 10px">
                                                    <div class="col-12"
                                                        style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1; text-align: center ">
                                                        <label class="box-title"
                                                            style="background-color: #004AC1;  font-size: 16px;">
                                                            Detalle de la Orden de Laboratorio
                                                        </label>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        
                                                        <div class="form-group col-12" style="padding-right: 0px">
                                                            <label style="font-family: 'Helvetica general';"
                                                                for="id_doctor_ieced" class="col-12">Médico</label>
                                                            <div class="col-12">
                                                                @if($value->id_doctor_ieced != null)
                                                                {{ $value->dapellido1 }} {{ $value->dnombre1 }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-12" style="padding-right: 0px">
                                                            <label style="font-family: 'Helvetica general';"
                                                                for="id_doctor_ieced" class="col-12">Seguro</label>
                                                            <div class="col-12">
                                                                @if(!is_null($value->snombre))
                                                                {{$value->snombre}}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div-->

                                                <div class="col-md-6 col-12">
                                                    <span>Progreso:</span>
                                                    <br>
                                                    <div class="progress progress">
                                                        <div class="progress-bar progress-bar-striped active"
                                                            role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                                            aria-valuemax="100" id="td{{$value->id}}">
                                                            <span id="sp{{$value->id}}" style="color: ##00c0ef;"></span>
                                                        </div>
                                                    </div>
                                                       
                                                   
                                                </div>

                                                <div class="col-md-6 col-8">
                                                    <a target="_blank" onclick="descargar({{$value->id}});"
                                                        style="width: 100%; height: 100%; color: white"
                                                        class="btn btn-info boton-lab">
                                                        <span style="color: white"
                                                            class="fa fa-download"></span> Resultados
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!--Contenedor Listado de Examenes de Laboratorio-->
                                    <div class="col-12" style="border-radius: 0px" id="div_res_orden{{$value->id}}">
                                      <img src="https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif" id="loadingGif{{$value->id}}" /> 
                                      <!--embed id="miIFrame" src="{{route('hc4resultados.imprimir',['id' => $value->id])}}" width="100%" height= "600px" type="application/pdf" -->  
                                      
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function descargar(id_or) {
    //location.href = '{{url('inicio/laboratorio/resultados/imprimir')}}/'+id_or;
    var cert = $('#sp' + id_or).text();

    if (cert == '0%') {
        alert("Sin Exámenes Ingresados");
    } else {

        window.open('{{url('inicio/laboratorio/hc4resultados/imprimir')}}/' + id_or, '_blank');

    }

}

function cargar_load(id){
  //console.log("cargando");
  //document.getElementById('loadingGif'+id).style.display = 'block';
  /*setTimeout(function() {
    // Esconder el GIF de carga
    document.getElementById('loadingGif'+id).style.display = 'none';
    //console.log("fin");
    
  }, 1000); */
  $.ajax({
        type: 'get',
        url: "{{ url('inicio/laboratorio/hc4resultados/imprimir/por_orden') }}/" + id,
        success: function(data) {
            $('#div_res_orden'+id).empty().html(data);
        },

        error: function(data) {}

    });

}

$(document).ready(function($) {
    @foreach($ordenes as $value)
    $.ajax({
        type: 'get',
        url: "{{ route('valida.puede_imprimir',['id' => $value->id]) }}",
        success: function(data) {
            console.log(data);
            if (data.cant_par == 0) {
                var pct = 0;
                //console.log(pct);  
            } else {
                var pct = data.certificados / data.cant_par * 100;
                //console.log(pct); 
            }
            //alert(pct);
            //$('#st').val(pct);
            if (pct <= 0) {
                document.getElementById("{{$value->id}}").style.display = "none";
            }
            $('#td{{$value->id}}').css("width", Math.round(pct) + "%");
            $('#sp{{$value->id}}').text(Math.round(pct) + "%");
            if (pct < 10) {
                $('#td{{$value->id}}').addClass("progress-bar-danger");
            } else if (pct >= 10 && pct < 90) {
                $('#td{{$value->id}}').addClass("progress-bar-warning");
            } else {
                $('#td{{$value->id}}').addClass("progress-bar-success");
            }
        },

        error: function(data) {}

    });

    @endforeach
});
</script>