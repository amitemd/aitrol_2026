<style>
    .odont-proc-selector { overflow: hidden; margin: 0; border: 1px solid #c9dee8; border-radius: 12px; background: linear-gradient(145deg, #fbfeff, #edf7fa); box-shadow: 0 8px 24px rgba(0,74,117,.08); }
    .odont-proc-selector-header { padding: 12px 18px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; font-size: 16px; font-weight: 600; }
    .odont-proc-selector-body { padding: 18px; }
    .odont-proc-selector .select2-container { width: 100% !important; }
    .odont-proc-selector .select2-selection--multiple { min-height: 44px; border-color: #c5dbe5; border-radius: 8px; background: #fbfdfe; }
</style>

<section class="odont-proc-selector">
    <div class="odont-proc-selector-header">PROCEDIMIENTOS ODONTOLOGICOS</div>
    <div class="odont-proc-selector-body">
        @if(is_null($historia))
            <div class="alert alert-warning">No existe una historia clinica odontologica asociada al paciente.</div>
        @else
            <form id="odont-proc-form-seleccionar">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_paciente" value="{{ $paciente->id }}">
                <input type="hidden" name="hcid" value="{{ $historia->hcid }}">
                <div class="form-group">
                    <label>Seleccione uno o varios procedimientos</label>
                    <select id="odont-proc-seleccion" class="form-control" name="procedimientos[]" multiple data-placeholder="Buscar procedimientos odontologicos">
                        @foreach($procedimientos as $procedimiento)
                            <option value="{{ $procedimiento->id }}">{{ $procedimiento->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-primary" id="odont-proc-crear" onclick="odontologiaProcedimientoCrear();">
                        <i class="fa fa-save"></i> CREAR PROCEDIMIENTO
                    </button>
                    <button type="button" class="btn btn-default" onclick="cargarProcedimientosOdontologicos();">CANCELAR</button>
                </div>
            </form>
        @endif
    </div>
</section>

<script>
    $('#odont-proc-seleccion').select2({
        width: '100%',
        placeholder: 'Buscar procedimientos odontologicos',
        closeOnSelect: false,
        dropdownParent: $('#area_trabajo')
    });

    function odontologiaProcedimientoCrear() {
        var seleccionados = $('#odont-proc-seleccion').val() || [];
        if (!seleccionados.length) {
            Swal.fire('Seleccione un procedimiento', 'Puede escoger uno o varios procedimientos odontologicos.', 'warning');
            return;
        }

        var boton = $('#odont-proc-crear');
        boton.prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: "{{ route((isset($prefijoModulo) ? $prefijoModulo : 'odontologia').'.procedimientos.guardar') }}",
            data: $('#odont-proc-form-seleccionar').serialize(),
            dataType: 'json',
            success: function (respuesta) {
                cargarProcedimientosOdontologicos(respuesta.id_abrir);
            },
            error: function (xhr) {
                var respuesta = xhr.responseJSON || {};
                Swal.fire('Error', respuesta.mensaje || 'No se pudo crear el procedimiento odontologico.', 'error');
                boton.prop('disabled', false);
            }
        });
    }
</script>
