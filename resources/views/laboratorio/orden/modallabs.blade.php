@php $id_auth = Auth::user()->id; @endphp

<style>
    .formularios-labs-modal .modal-header {
        position: relative;
        min-height: 58px;
        display: flex;
        align-items: center;
        padding: 16px 20px;
        color: #fff;
        background: #1976a9;
        border: 0;
    }

    .formularios-labs-modal .modal-title {
        width: 100%;
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        text-align: left;
    }

    .formularios-labs-modal .close {
        position: absolute;
        top: 15px;
        right: 18px;
        color: #fff;
        font-size: 24px;
        opacity: .9;
    }

    .formularios-labs-body {
        padding: 20px;
    }

    .formularios-labs-resumen {
        display: grid;
        grid-template-columns: 100px 1fr 1fr 120px;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px;
        background: #f3f7f9;
        border: 1px solid #dce6eb;
        border-radius: 4px;
    }

    .formularios-labs-dato span {
        display: block;
        margin-bottom: 4px;
        color: #738590;
        font-size: 10px;
        text-transform: uppercase;
    }

    .formularios-labs-dato strong {
        color: #294555;
        font-size: 12px;
    }

    .formularios-labs-selector {
        padding: 16px;
        border: 1px solid #d8e3e8;
        border-radius: 4px;
    }

    .formularios-labs-selector h4 {
        margin: 0 0 5px;
        color: #203e50;
        font-size: 14px;
        font-weight: 700;
    }

    .formularios-labs-selector p {
        margin: 0 0 13px;
        color: #758691;
        font-size: 11px;
    }

    .formularios-labs-selector label {
        margin-bottom: 6px;
        color: #405563;
        font-size: 11px;
    }

    .formularios-labs-selector .form-control {
        width: 100%;
        height: 40px;
        border-color: #cbd7de;
        border-radius: 4px;
        box-shadow: none;
    }

    .formularios-labs-modal .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        background: #f4f7f9;
        border-top: 1px solid #dce5ea;
    }

    .formularios-labs-modal .modal-footer .btn {
        min-width: 116px;
        height: 36px;
        margin: 0;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 767px) {
        .formularios-labs-resumen {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="modal-content formularios-labs-modal">
    <div class="modal-header">
        <h3 class="modal-title">Consentimientos informados</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" onclick="cerrarModalYRecargar();">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <form name="formulario" id="formulario" method="get" action="{{ route('modal.labs.pdf') }}">
        <input type="hidden" name="id_orden" value="{{$orden->id}}">

        <div class="formularios-labs-body">
            <div class="formularios-labs-resumen">
                <div class="formularios-labs-dato">
                    <span>N.&ordm; orden</span>
                    <strong>{{$orden->id}}</strong>
                    <input name="orden" type="hidden" value="{{$orden->id}}">
                </div>
                <div class="formularios-labs-dato">
                    <span>Nombres</span>
                    <strong>{{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}</strong>
                    <input name="nombre" type="hidden" value="{{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}">
                </div>
                <div class="formularios-labs-dato">
                    <span>Apellidos</span>
                    <strong>{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}}</strong>
                    <input name="apellido" type="hidden" value="{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}}">
                </div>
                <div class="formularios-labs-dato">
                    <span>C&eacute;dula</span>
                    <strong>{{$orden->paciente->id}}</strong>
                    <input name="cedula" type="hidden" value="{{$orden->paciente->id}}">
                </div>
            </div>

            <div class="formularios-labs-selector">
                <h4>Seleccionar formulario</h4>
                <p>El documento se generar&aacute; con los datos registrados en la orden.</p>
                <label for="nombre_procedimiento">Formato disponible</label>
                <select name="nombre_procedimiento" id="nombre_procedimiento" class="form-control select2">
                    <option value="">Seleccione</option>
                    @foreach($consentimien as $value)
                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                    @endforeach
                    <option value="citogenetico_1564">Formulario 1564 - Orden de Examen Citogenetico</option>
                </select>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModalYRecargar();">
                Cerrar
            </button>
            <button type="button" formtarget="_blank" onclick="descargar();" class="btn btn-primary">
                <i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Generar documento
            </button>
        </div>
    </form>
</div>

<script>
    function cerrarModalYRecargar() {
        location.reload();
    }

    $(document).on('click', function(event) {
        if ($(event.target).closest('.modal-content').length === 0 && $('.modal').is(':visible')) {
            $('#myModal').modal('hide');
            location.reload();
        }
    });

    function descargar() {
        let formulario = document.forms["formulario"];
        if (formulario.nombre_procedimiento.value == '') {
            alert('Seleccione un formato de consentimiento');
        } else {
            $("#formulario").closest('form').attr('target', '_blank').submit();
        }
    }
</script>
