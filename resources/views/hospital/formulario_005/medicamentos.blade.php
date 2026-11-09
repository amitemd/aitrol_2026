<style>
    .medication-panel {
        overflow: hidden;
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .09);
    }

    .medication-panel__header,
    .medication-item__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: nowrap;
        gap: 16px;
        min-height: 60px;
        padding: 12px 18px;
        background: linear-gradient(105deg, #064b7a 0%, #087db5 60%, #1db8c8 100%) !important;
    }

    .medication-panel__identity {
        display: flex;
        align-items: center;
        min-width: 0;
        gap: 12px;
    }

    .medication-panel__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 26px;
        padding: 0 9px;
        border: 1px solid rgba(255, 255, 255, .75);
        border-radius: 999px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }

    .medication-panel__title {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
    }

    .medication-panel__add {
        flex: 0 0 auto;
        min-width: 116px;
        margin-left: auto;
        border: 0;
        border-radius: 9px;
        box-shadow: 0 6px 15px rgba(22, 199, 132, .25);
        font-weight: 700;
    }

    .medication-panel__body {
        padding: 18px !important;
    }

    .medication-item {
        margin-bottom: 14px;
        overflow: hidden;
        border: 1px solid #dce8ef;
        border-radius: 12px;
    }

    .medication-item:last-child {
        margin-bottom: 0;
    }

    .medication-item__header {
        min-height: 52px;
        padding: 9px 14px;
        background: linear-gradient(105deg, #0c5e91, #159dbb) !important;
    }

    .medication-item__date {
        min-width: 0;
        margin: 0;
        color: #fff !important;
        font-weight: 700;
    }

    .medication-item__actions {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        margin-left: auto;
    }

    .medication-item__toggle {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 0;
        border-radius: 9px;
        background: rgba(0, 75, 142, .68) !important;
    }

    .medication-item__body {
        padding: 0 !important;
        background: #fff;
    }

    @media (max-width: 575.98px) {
        .medication-panel__header {
            align-items: stretch;
            flex-direction: column;
        }

        .medication-panel__add {
            width: 100%;
            margin-left: 0;
        }
    }
</style>

<div class="card medication-panel">
    <div class="card-header medication-panel__header">
        <div class="medication-panel__identity">
            <span class="medication-panel__count">3</span>
            <h3 class="medication-panel__title">{{trans('hospitalizacion.Medicamentos-Receta')}}</h3>
        </div>
        <button type="button" class="btn btn-success medication-panel__add" onclick="agregar_receta();">
            <i class="fa fa-plus" aria-hidden="true"></i>
            {{trans('hospitalizacion.Agregar')}}
        </button>
    </div>

    <div class="card-body medication-panel__body">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        @foreach($recetas as $receta)
            <section class="medication-item">
                <div class="card-header medication-item__header">
                    <div class="medication-item__date">
                        @if(!is_null($receta->created_at))
                            @php
                                $dia = Date('N', strtotime($receta->created_at));
                                $mes = Date('n', strtotime($receta->created_at));
                            @endphp
                            {{Sis_medico\Utilidades::getDia($dia)}}
                            {{substr($receta->created_at, 8, 2)}} de
                            {{Sis_medico\Utilidades::getMes($mes)}}
                            del {{substr($receta->created_at, 0, 4)}}
                        @endif
                    </div>
                    <div class="medication-item__actions">
                        <button id="plus{{$receta->id}}" type="button" class="btn btn-primary medication-item__toggle" title="Abrir receta" aria-label="Abrir receta {{$receta->id}}" onclick="ver_receta('{{$receta->id}}');">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                        <button id="min{{$receta->id}}" type="button" class="btn btn-primary medication-item__toggle" title="Cerrar receta" aria-label="Cerrar receta {{$receta->id}}" onclick="ocultar_receta('{{$receta->id}}');" style="display: none">
                            <i class="fa fa-minus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body medication-item__body" id="receta_detalle{{$receta->id}}"></div>
            </section>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    function ver_receta(id){
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/receta/detalle')}}/"+id,
            data:{
                'id_solicitud': id_solicitud,
            },
            dataType: "html",
            success: function(datahtml){

                $("#receta_detalle"+id).html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
        $("#plus"+id).hide();
        $("#min"+id).show();    
    }


    function ocultar_receta(id){

        $("#receta_detalle"+id).html("<br>");
        $("#min"+id).hide();
        $("#plus"+id).show();
    }

    function agregar_receta(){

        $.ajax({
            async: true,
            type: "POST",
            url: "{{route('formulario005.crear_receta',[ 'id' => $solicitud->id ])}}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {'_token': '{{ csrf_token() }}'},
            dataType: "html",
            success: function(datahtml){

                $("#pasos").html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });

    }



    </script>
