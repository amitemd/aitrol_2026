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

    .evolution-panel {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .09);
        overflow: hidden;
    }

    .evolution-panel__header,
    .evolution-item__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 60px;
        padding: 12px 18px;
        background: linear-gradient(105deg, #064b7a 0%, #087db5 60%, #1db8c8 100%) !important;
    }

    .evolution-panel__title,
    .evolution-item__date {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .evolution-item__summary {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px 18px;
        min-width: 0;
    }

    .evolution-item__professional {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 9px;
        background: rgba(0, 39, 70, .38);
        box-shadow: 0 3px 10px rgba(0, 28, 54, .16);
        color: #fff !important;
        line-height: 1.2;
        text-shadow: 0 1px 2px rgba(0, 25, 45, .45);
    }

    .evolution-item__professional i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .16);
        color: #fff !important;
        font-size: 17px;
        opacity: 1;
    }

    #content .evolution-panel .evolution-item__header .evolution-item__doctor {
        display: block;
        color: #fff !important;
        font-size: 13.5px;
        font-weight: 800;
        letter-spacing: .1px;
    }

    #content .evolution-panel .evolution-item__header .evolution-item__specialty {
        display: block;
        margin-top: 2px;
        color: #f0fbff !important;
        font-size: 11.5px;
        font-weight: 600;
    }

    .evolution-panel__count {
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

    .evolution-panel__heading {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
    }

    .evolution-add {
        min-width: 116px;
        border: 0;
        border-radius: 9px;
        box-shadow: 0 6px 15px rgba(22, 199, 132, .25);
        font-weight: 700;
    }

    .evolution-panel__actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-left: auto;
    }

    .evolution-panel__body {
        padding: 18px !important;
    }

    .evolution-empty {
        padding: 36px 20px;
        border: 1px dashed #b8cedb;
        border-radius: 12px;
        color: #637987;
        text-align: center;
    }

    .evolution-item {
        margin-bottom: 14px;
        border: 1px solid #dce8ef;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: none;
    }

    .evolution-item:last-child {
        margin-bottom: 0;
    }

    .evolution-item__header {
        min-height: 52px;
        padding: 9px 14px;
        background: linear-gradient(105deg, #0c5e91, #159dbb) !important;
    }

    #content .evolution-panel .evolution-item__header .evolution-item__date,
    #content .evolution-panel .evolution-item__header .evolution-item__date b {
        color: #fff !important;
    }

    .evolution-toggle {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 0;
        border-radius: 9px;
        background: rgba(0, 75, 142, .68) !important;
    }

    .evolution-item__body {
        min-height: 52px;
        padding: 14px 16px !important;
        background: #fff;
        color: #344b5b;
    }

    .evolution-item__body b {
        color: #0c3858;
    }

    @media (max-width: 575.98px) {
        .evolution-panel__header,
        .evolution-item__header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .evolution-add {
            width: 100%;
        }

        .evolution-panel__actions {
            width: 100%;
            flex-direction: column;
        }

        .evolution-item__summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .evolution-item__professional {
            padding: 7px 10px;
        }
    }
</style>
<div class="card evolution-panel">
    <div class="card-header evolution-panel__header">
        <div class="evolution-panel__title">
            <span class="evolution-panel__count">{{ $evoluciones->count() }}</span>
            <h3 class="evolution-panel__heading">{{trans('hospitalizacion.Evolución')}}</h3>
        </div>
        <div class="evolution-panel__actions">
            <a class="btn btn-danger evolution-add" target="_blank" href="{{ route('hospital.formulario005_pdf', ['id_solicitud' => $solicitud->id]) }}">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                <span>PDF F-005</span>
            </a>
            <button id="btn-agregar-evolucion" type="button" class="btn btn-success evolution-add" onclick="agregar_evolucion(this);">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>{{trans('hospitalizacion.Agregar')}}</span>
            </button>
        </div>
    </div>
    <div class="card-body evolution-panel__body">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">

        @if($evoluciones->isEmpty())
            <div class="evolution-empty">
                <i class="fa fa-file-medical fa-2x mb-1" aria-hidden="true"></i>
                <div><b>Aún no existen evoluciones.</b></div>
                <small>Use el botón Agregar para registrar la primera.</small>
            </div>
        @endif

        @foreach($evoluciones as $evol)
            <div class="card evolution-item">
                <div class="card-header evolution-item__header colorbasic">
                    <div class="evolution-item__summary">
                        <div class="evolution-item__date">
                            @if(!is_null($evol->created_at))
                                @php
                                 $dia =  Date('N',strtotime($evol->created_at));
                                 $mes =  Date('n',strtotime($evol->created_at));
                                @endphp

                                <b class="colorbasic">
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
                        <div class="evolution-item__professional">
                            <i class="fa fa-user-md" aria-hidden="true"></i>
                            <span>
                                <span class="evolution-item__doctor">{{$evol->medico_nombre}}</span>
                                <span class="evolution-item__specialty">{{$evol->medico_especialidad}}</span>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <button id="plus{{$evol->id}}" type="button" class="btn btn-primary evolution-toggle" title="Abrir evolución" aria-label="Abrir evolución {{$evol->id}}" onclick="ver_evolucion('{{$evol->id}}');">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body evolution-item__body" id="evolucion_detalle{{$evol->id}}">
                    <label><b>{{trans('hospitalizacion.CuadroClinico')}}:</b></label>
                    <span>{{ $evol->cuadro_clinico ?: 'Pendiente de completar' }}</span>
                </div>
            </div>
        @endforeach
    </div>


</div>



<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>

<script type="text/javascript">

    function obtenerContenedorEvolucion(){
        return $("#pasos").length ? $("#pasos") : $("#content");
    }

    function mostrarEditorEvolucion(datahtml){
        var $contenedor = obtenerContenedorEvolucion();
        $contenedor.html(datahtml);
        $('.hospitalization-workspace').removeClass('is-editor-open');
        if ($contenedor.attr('id') === 'pasos') {
            $('html, body').animate({scrollTop: $contenedor.offset().top - 110}, 350);
        }
    }

    function ver_evolucion(id){
        var id_solicitud = $('#id_solicitud').val();
    
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/formu_005/evolucion/detalle')}}/"+id,
            data:{
                'id_solicitud': id_solicitud,

            },
            dataType: "html",
            success: function(datahtml){
                mostrarEditorEvolucion(datahtml);
            },
            error:  function(xhr){
                var message = xhr.status === 404
                    ? 'La evolución seleccionada ya no está disponible.'
                    : 'No fue posible abrir esta evolución.';
                Swal.fire({icon: 'error', title: 'No se pudo cargar', text: message});
            }
        });
    }


    function agregar_evolucion(button){
        var $button = $(button || '#btn-agregar-evolucion');
        var originalHtml = $button.html();

        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Abriendo...');

        $.ajax({
            async: true,
            type: "POST",
            url: "{{route('formulario005.crear_evolucion',[ 'id' => $solicitud->id ])}}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {'_token': '{{ csrf_token() }}'},
            dataType: "html",
            success: function(datahtml){
                mostrarEditorEvolucion(datahtml);
            },
            error: function(xhr){
                var responseMessage = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : '';
                var message = xhr.status === 419
                    ? 'La sesión expiró. Actualice la página e intente nuevamente.'
                    : (responseMessage || 'No fue posible abrir una nueva evolución. Intente nuevamente.');

                Swal.fire({icon: 'error', title: 'No se pudo crear la evolución', text: message});
            },
            complete: function(){
                $button.prop('disabled', false).html(originalHtml);
            }
        });

    }


</script>
