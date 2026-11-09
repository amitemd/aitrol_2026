<style>
    .vital-signs-panel__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        width: 100%;
        min-height: 60px;
        padding: 12px 18px;
    }

    .vital-signs-panel__title {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .vital-signs-panel__add {
        flex: 0 0 auto;
        min-width: 116px;
        padding: 9px 16px;
        border: 0;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
    }

    .vital-signs-panel__body {
        padding: 18px !important;
    }

    .vital-signs-item {
        margin-bottom: 16px;
        overflow: hidden;
        border: 1px solid #d7e7ef;
        border-radius: 12px;
        background: #fff;
    }

    .vital-signs-item:last-child {
        margin-bottom: 0;
    }

    .vital-signs-item__header {
        display: flex;
        min-height: 58px;
        align-items: center;
        gap: 16px;
        width: 100%;
        padding: 10px 18px !important;
    }

    .vital-signs-item__pdf,
    .vital-signs-item__action {
        flex: 0 0 auto;
    }

    .vital-signs-item__pdf .btn {
        min-width: 72px;
        border: 0;
        border-radius: 9px;
        background: #ef5350 !important;
        color: #fff !important;
        font-weight: 700;
    }

    .vital-signs-item__date {
        flex: 1 1 auto;
        min-width: 0;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
    }

    .vital-signs-item__date b {
        color: inherit !important;
    }

    .vital-signs-item__action {
        margin-left: auto;
    }

    .vital-signs-item__toggle {
        display: inline-flex;
        width: 40px;
        height: 40px;
        padding: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        border-radius: 9px;
        background: rgba(0, 74, 145, .65) !important;
        color: #fff !important;
    }

    .vital-signs-item__body {
        padding: 14px 18px !important;
    }

    .vital-signs-item__body label,
    .vital-signs-item__body p {
        margin: 0;
    }

    .vital-signs-item__body p + label {
        margin-top: 10px;
    }

    @media (max-width: 575px) {
        .vital-signs-panel__header {
            padding: 11px 13px;
        }

        .vital-signs-panel__add {
            min-width: auto;
        }

        .vital-signs-item__header {
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px 13px !important;
        }

        .vital-signs-item__date {
            order: 1;
            flex-basis: calc(100% - 98px);
        }

        .vital-signs-item__pdf {
            order: 2;
        }

        .vital-signs-item__action {
            order: 3;
        }
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary vital-signs-panel__header">
        <h4 class="vital-signs-panel__title">{{trans('hospitalizacion.SignosVitales')}}</h4>
        <button class="btn btn-success vital-signs-panel__add" onclick="agregar_evolucion();">
            <i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}
        </button>
    </div>
    <div class="card-body vital-signs-panel__body">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        @php
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes','Sabado', 'Domingo'];
        @endphp


        @foreach($evoluciones as $evol)

        <article class="vital-signs-item">
        <div class="card-header bg bg-primary colorbasic vital-signs-item__header">
            <div class="vital-signs-item__date">
                @if(!is_null($evol->created_at))
                @php
                $dia = Date('N',strtotime($evol->created_at));
                $mes = Date('n',strtotime($evol->created_at));
                $aux = intval($dia)-1;
                $ms = intval($mes)-1;
                @endphp

                <b>{{$dias[$aux]}} {{substr($evol->created_at,8,2)}} de {{$meses[$ms]}} del {{substr($evol->created_at,0,4)}}</b>
                @endif
            </div>
            <div class="vital-signs-item__pdf">
                <a href="{{route('tipoemergencia.verpdf',['id'=>$evol->id])}}" target="_blank" class="btn btn-danger">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
            </div>
            <div class="vital-signs-item__action">
                <button id="plus{{$evol->id}}" type="button" class="btn btn-primary vital-signs-item__toggle" onclick="ver_evolucion('{{$evol->id}}');" aria-label="Mostrar detalle">
                    <i class="fa fa-plus"></i>
                </button>
                <button id="min{{$evol->id}}" type="button" class="btn btn-primary vital-signs-item__toggle" onclick="ocultar_evolucion('{{$evol->id}}');" style="display: none" aria-label="Ocultar detalle">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body vital-signs-item__body" id="evol_detalle_enfermeria{{$evol->id}}">
            <label><b>{{trans('hospitalizacion.FrecuenciaRespiratoria')}}:</b></label>
            <p>{{$evol->frec_respiratoria}}</p>
            <label><b>{{trans('hospitalizacion.PresionArterial')}}:</b></label>
            <p>{{$evol->presion_arterial}}</p>
        </div>
        </article>

        @endforeach

    </div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
    function ver_evolucion(id) {
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/evolucion/enfermeria/detalle')}}/" + id,
            data: {
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml) {

                $("#evol_detalle_enfermeria" + id).html(datahtml);
                $("#plus" + id).hide();
                $("#min" + id).show();
            },
            error: function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo cargar el detalle de signos vitales.';
                alert(message);
            }
        });
    }


    function ocultar_evolucion(id) {

        $("#evol_detalle_enfermeria" + id).html("<br>");
        $("#min" + id).hide();
        $("#plus" + id).show();
    }

    function agregar_evolucion() {

        $.ajax({
            async: true,
            type: "POST",
            url: "{{route('formulario005.crear_evolucion_enfer',[ 'id' => $solicitud->id ])}}",
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
