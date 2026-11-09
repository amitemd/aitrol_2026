<style>
    .nursing-notes__header,
    .nursing-notes__item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        width: 100%;
    }

    .nursing-notes__header {
        min-height: 60px;
        padding: 12px 18px;
    }

    .nursing-notes__title {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .nursing-notes__add {
        flex: 0 0 auto;
        min-width: 116px;
        padding: 9px 16px;
        border: 0;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
    }

    .nursing-notes__item-header {
        min-height: 54px;
        padding: 9px 14px;
    }

    .nursing-notes__date {
        margin: 0;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
    }

    .nursing-notes__toggle {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        padding: 0;
        border-radius: 9px;
    }

    .nursing-notes__summary {
        padding: 14px 16px;
        color: #24445d;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .nursing-notes__summary-label {
        display: block;
        margin-bottom: 4px;
        color: #173d59;
        font-weight: 700;
    }

    .nursing-notes__summary-text {
        display: block;
        white-space: pre-wrap;
    }

    @media (max-width: 575px) {
        .nursing-notes__header {
            padding: 11px 13px;
        }

        .nursing-notes__add {
            min-width: auto;
        }
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary nursing-notes__header">
        <h4 class="nursing-notes__title">{{trans('hospitalizacion.NotasEvolucionEnfermeria')}}</h4>
        <button class="btn btn-success nursing-notes__add" onclick="agregar_nota();">
            <i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}
        </button>
    </div>
    <div class="card-body" style="padding: 0px;">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        @php
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
        @endphp


        @foreach($notas_enfermeria as $nota)

        <br> <br>
        <div class="card-header bg bg-primary colorbasic nursing-notes__item-header">
            @if(!is_null($nota->created_at))
            @php
            $dia = Date('N',strtotime($nota->created_at));
            $mes = Date('n',strtotime($nota->created_at));
            $aux = intval($dia)-1;
            $ms = intval($mes)-1;
            @endphp

            <p class="nursing-notes__date">{{$dias[$aux]}} {{substr($nota->created_at,8,2)}} de {{$meses[$ms]}} del {{substr($nota->created_at,0,4)}}</p>
            @endif
            <div>
                <button id="plus{{$nota->id}}" type="button" class="btn btn-primary nursing-notes__toggle" onclick="ver_nota('{{$nota->id}}');">
                    <i class="fa fa-plus"></i>
                </button>
                <button id="min{{$nota->id}}" type="button" class="btn btn-primary nursing-notes__toggle" onclick="ocultar_nota('{{$nota->id}}');" style="display: none">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body nursing-notes__summary" id="nota_detalle_enfermeria{{$nota->id}}">
            <span class="nursing-notes__summary-label">{{trans('hospitalizacion.Observacion')}}:</span>
            <span class="nursing-notes__summary-text">{{$nota->descripcion}}</span>
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
            type: "POST",
            url: "{{url('hospital/nota/enfermeria/detalle')}}/" + id,
            data: {
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml) {
                $("#nota_detalle_enfermeria" + id)
                    .removeClass('nursing-notes__summary')
                    .html(datahtml);
                $("#plus" + id).hide();
                $("#min" + id).show();
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }


    function ocultar_nota(id) {
        var $detalle = $("#nota_detalle_enfermeria" + id);
        var $observacion = $detalle.find('textarea[name="observacion"]');

        if ($observacion.length) {
            var $etiqueta = $('<span>', {
                'class': 'nursing-notes__summary-label',
                text: "{{trans('hospitalizacion.Observacion')}}:"
            });
            var $texto = $('<span>', {
                'class': 'nursing-notes__summary-text',
                text: $observacion.val()
            });

            $detalle
                .empty()
                .addClass('nursing-notes__summary')
                .append($etiqueta, $texto);
        }

        $("#min" + id).hide();
        $("#plus" + id).show();
    }

    function agregar_nota() {

        $.ajax({
            async: true,
            type: "POST",
            url: "{{route('formulario005.crear_nota_enfermeria',[ 'id' => $solicitud->id ])}}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {'_token': '{{ csrf_token() }}'},
            dataType: "html",
            success: function(datahtml) {

                $("#pasos").html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });

    }
</script>
