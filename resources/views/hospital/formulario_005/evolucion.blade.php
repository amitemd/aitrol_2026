<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<style type="text/css">
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <label class="colorbasic sradio" > 2 </label> 
                </div>
                <div class="col-md-9">
                    <label class="colorbasic" style="font-size: 16px" >{{trans('hospitalizacion.Evolución')}}</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_evolucion();"><i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <br>    
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
       
       
        @foreach($evoluciones as $evol)
            <br><br>
            <div class="col-md-12" style="padding: 0;">
                <div class="card-header bg bg-primary colorbasic">

                   
                    <div class="col-md-6">
                        @if(!is_null($evol->created_at))
                            @php
                             $dia =  Date('N',strtotime($evol->created_at));
                             $mes =  Date('n',strtotime($evol->created_at));
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
                             {{substr($evol->created_at,8,2)}} de
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
                            del {{substr($evol->created_at,0,4)}}</b>
                        @endif
                    </div>
                    <div class="col-md-5">
                        <label style='color: red'; class="colorbasic" >No. {{$evol->id}}</label>
                    </div>
                    <div class="col-md-1">
                        <button id="plus{{$evol->id}}" type="button" class="btn btn-primary" onclick="ver_evolucion('{{$evol->id}}');">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button id="min{{$evol->id}}" type="button" class="btn btn-primary" onclick="ocultar_evolucion('{{$evol->id}}');" style="display: none">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                    
                </div>

                <div class="card-body" id="evolucion_detalle{{$evol->id}}" style="padding: 0px;">
                    <label><b>{{trans('hospitalizacion.CuadroClinico')}}:</b></label>
                    <span>{{$evol->cuadro_clinico}}</span>
                </div>
            </div>
            <br>
        @endforeach
        
    </div>   


</div>



<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>

<script type="text/javascript">

    function ver_evolucion(id){
        var id_solicitud = $('#id_solicitud').val();
    
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/formu_005/evolucion/detalle')}}/"+id,
            data:{
                'id_solicitud': id_solicitud,

            },
            datatype: "html",
            success: function(datahtml){

                $("#evolucion_detalle"+id).html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
        $("#plus"+id).hide();
        $("#min"+id).show();    
    }


    function ocultar_evolucion(id){

        $("#evolucion_detalle"+id).html("<br>");
        $("#min"+id).hide();
        $("#plus"+id).show();
    }

    function agregar_evolucion(){

        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('formulario005.crear_evolucion',[ 'id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){
            /*
                $.ajax({
                    type: "get",
                    url: "{{route('formulario005.f5_evolucion',['id' => $solicitud->id])}}",
                    data: {
                    // 'ep': id_orden,
                    },
                    datatype: "html",
                    success: function(datahtml, data) {

                        $("#quirofano").html(datahtml);
                    },
                    error: function() {
                        alert('error al cargar');
                    }
                });*/

                    //evolucion();
                //$("#evolucion_detalle").html(datahtml);
                $("#pasos").html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });

    }


</script>