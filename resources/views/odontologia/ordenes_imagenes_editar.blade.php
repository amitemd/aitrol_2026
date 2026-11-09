@include('hc4.ordenes.orden_procedimiento_imagenes.editar_orden')

<script>
    (function () {
        var permitidos = {!! json_encode($px->pluck('id')->map(function ($id) { return (string) $id; })->values()->all()) !!};
        var mapaPermitidos = {};
        $.each(permitidos, function (_, id) {
            mapaPermitidos[id] = true;
        });

        var select = $('#id_procedimiento_imag');
        if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }
        select.find('option').each(function () {
            if (!mapaPermitidos[String($(this).val())]) {
                $(this).remove();
            }
        });
        select.find('option').prop('disabled', false);
        select.select2({
            tags: false,
            width: '100%',
            placeholder: 'Seleccione procedimientos odontologicos'
        });

        $('#edit_orden_imag button[onclick^="actualiza_orden_imagenes"]')
            .removeAttr('onclick')
            .off('click.odontologia')
            .on('click.odontologia', function () {
                guardarOrdenImagenOdontologia({{ $data_orden_imag->id }});
            });
    })();

    function guardarOrdenImagenOdontologia(idOrden) {
        var procedimientos = $('#id_procedimiento_imag').val();
        if (!procedimientos || !procedimientos.length) {
            $('#select_proc').fadeIn(1000).fadeOut(3000);
            return;
        }

        // TinyMCE es inline: sincronizamos su contenido antes de serializar el
        // formulario para que diagnostico e historia no lleguen vacios.
        if (typeof tinyMCE !== 'undefined') {
            $('#edit_orden_imag input[name="imag_historia_clinica"]').each(function () {
                var editorId = this.id.replace('imag_historia_clinica', 'imag_resumen_orden');
                var editor = tinyMCE.get(editorId);
                if (editor) {
                    $(this).val(editor.getContent());
                }
            });
            $('#edit_orden_imag input[name="imag_des_diagnostico"]').each(function () {
                var editorId = this.id.replace('imag_des_diagnostico', 'imag_diagnostico');
                var editor = tinyMCE.get(editorId);
                if (editor) {
                    $(this).val(editor.getContent());
                }
            });
        }

        var botonGuardar = $('#edit_orden_imag .hc4-image-edit-actions__button').first();
        botonGuardar.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: "{{ route('actualiza.ordenhc4_procedimagenes') }}",
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
            data: $('#edit_orden_imag').serialize(),
            success: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Orden guardada',
                        text: 'La orden de imagen odontologica se guardo correctamente.',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
                if (typeof carga_ordenes_imagenes === 'function') {
                    carga_ordenes_imagenes();
                }
            },
            error: function (xhr) {
                var mensaje = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo guardar la orden de imagen.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', mensaje, 'error');
                } else {
                    alert(mensaje);
                }
            },
            complete: function () {
                botonGuardar.prop('disabled', false);
            }
        });
    }

    function im_actualiza_select() {
        $('#id_procedimiento_imag option').prop('disabled', false);
    }

    function im_quita_select() {
        $('#id_procedimiento_imag option').prop('disabled', false);
    }
</script>
