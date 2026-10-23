<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
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
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px">{{trans('Epicrisis')}}</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_epicrisis();"><i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <br>
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">


        @foreach($epicrisis as $epi)
        <br><br>
        <div class="col-md-12" style="padding: 0;">
            <div class="card-header bg bg-primary colorbasic">


                <div class="col-md-6">
                    @if(!is_null($epi->created_at))
                    @php
                    $dia = Date('N',strtotime($epi->created_at));
                    $mes = Date('n',strtotime($epi->created_at));
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
                        {{substr($epi->created_at,8,2)}} de
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
                        del {{substr($epi->created_at,0,4)}}</b>
                    @endif
                </div>
                <div class="col-md-4">
                    <label style='color: red' ; class="colorbasic">No. {{$epi->id}}</label>
                </div>
                <div class="col-md-1">
                    <a id="pdf{{$epi->id}}" class="btn btn-success" href="{{ route('formulario.006',['id' => $epi->id]) }}" target="_blank">
                        formulario-006
                    </a>
                   
                </div>
                <div class="col-md-1">
                    <button id="plus{{$epi->id}}" type="button" class="btn btn-primary" onclick="ver_epicrisis('{{$epi->id}}');">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button id="min{{$epi->id}}" type="button" class="btn btn-primary" onclick="ocultar_epicrisis('{{$epi->id}}');" style="display: none">
                        <i class="fa fa-minus"></i>
                    </button>
                    
                </div>

            </div>

            <div class="card-body" id="epicrisis_detalle{{$epi->id}}" style="padding: 0px;">

            </div>
        </div>
        <br>

        @endforeach
    </div>
</div>


<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>

<script type="text/javascript">

    function ver_epicrisis(id) {
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/quirofano/epicrisis/detalle')}}/" + id,
            data: {
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml) {

                $("#epicrisis_detalle" + id).html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });
        $("#plus" + id).hide();
        $("#min" + id).show();
    }


    function ocultar_epicrisis(id) {

        $("#epicrisis_detalle" + id).html("<br>");
        $("#min" + id).hide();
        $("#plus" + id).show();
    }

    function agregar_epicrisis() {

        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('quirofano.crear_epicrisis',[ 'id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml) {

                /*$.ajax({
                    type: "get",
                    url: "{{route('quirofano.epicrisis',['id' => $solicitud->id])}}",
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
                epicrisis(datahtml.id);

            },
            error: function() {
                alert('error al cargar');
            }
        });

    }
</script>