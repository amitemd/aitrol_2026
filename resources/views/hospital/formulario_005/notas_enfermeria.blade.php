<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px">{{trans('hospitalizacion.NotasEvolucionEnfermeria')}}</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_nota();"><i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0px;">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        @php
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
        @endphp


        @foreach($notas_enfermeria as $nota)

        <br> <br>
        <div class="card-header bg bg-primary colorbasic">
            <div class="col-md-1" style="margin-left: 10px;">
                <!-- <a href="{{route('tipoemergencia.verpdf',['id'=>$nota->id])}}" target="_blank" class="btn btn-warning">PDF</a> -->
            </div>
            <div class="col-md-4">
                @if(!is_null($nota->created_at))
                @php
                $dia = Date('N',strtotime($nota->created_at));
                $mes = Date('n',strtotime($nota->created_at));
                $aux = intval($dia)-1;
                $ms = intval($mes)-1;
                @endphp

                <b>{{$dias[$aux]}} {{substr($nota->created_at,8,2)}} de {{$meses[$ms]}} del {{substr($nota->created_at,0,4)}}</b>
                @endif
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-2">
                <button id="plus{{$nota->id}}" type="button" class="btn btn-primary" onclick="ver_nota('{{$nota->id}}');">
                    <i class="fa fa-plus"></i>
                </button>
                <button id="min{{$nota->id}}" type="button" class="btn btn-primary" onclick="ocultar_nota('{{$nota->id}}');" style="display: none">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="nota_detalle_enfermeria{{$nota->id}}" style="padding: 0px;">
                <label><b>{{trans('hospitalizacion.Observacion')}}:</b></label>
                <span>{{$nota->descripcion}}</span>

        </div>

        <br>

        @endforeach

    </div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
    function ver_nota(id) {
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/nota/enfermeria/detalle')}}/" + id,
            data: {
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml) {

                $("#nota_detalle_enfermeria" + id).html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });
        $("#plus" + id).hide();
        $("#min" + id).show();
    }


    function ocultar_nota(id) {

        $("#nota_detalle_enfermeria" + id).html("<br>");
        $("#min" + id).hide();
        $("#plus" + id).show();
    }

    function agregar_nota() {

        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('formulario005.crear_nota_enfermeria',[ 'id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml) {

                $("#pasos").html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });

    }
</script>