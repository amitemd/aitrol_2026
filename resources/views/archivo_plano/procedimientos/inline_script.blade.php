<script>
(function ($) {
    'use strict';
    $(function () {
        var $root = $('#items-inline');
        var $table = $root.find('#frmpro');
        if (!$root.length) { return; }

        function initSelects() {
            $root.find('select').each(function () {
                var $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) { return; }
                var manyOptions = $select.find('option').filter(function () {
                    return this.value !== '' && !this.disabled;
                }).length > 20;
                $select.select2({
                    width: $select.hasClass('items-filter') ? '150px' : '100%',
                    placeholder: $select.data('field') === 'id_seguro' ? ' ' : undefined,
                    allowClear: $select.data('field') === 'id_seguro',
                    minimumInputLength: manyOptions ? 1 : 0,
                    minimumResultsForSearch: 0,
                    language: {
                        inputTooShort: function () { return 'Escribe para buscar…'; },
                        noResults: function () { return 'No se encontraron resultados'; },
                        searching: function () { return 'Buscando…'; }
                    }
                });
                var label = $select.attr('aria-label');
                if (!label && this.id) {
                    label = $root.find('label').filter(function () {
                        return this.htmlFor === $select.attr('id');
                    }).text();
                }
                if (label) {
                    $select.next('.select2').find('.select2-selection')
                        .removeAttr('aria-labelledby').attr('aria-label', label);
                }
            });
        }
        if ($.fn.select2) {
            initSelects();
        } else {
            $.getScript("{{asset('v3/plugins/select2/js/select2.full.min.js')}}")
                .done(initSelects)
                .fail(function () {
                    $root.find('.items-search').after(
                        '<p class="text-danger" role="alert">No se pudo cargar Select2. Recarga la página para volver a intentarlo.</p>'
                    );
                });
        }

        function fields($row) {
            var values = {};
            $row.find('.item-field').each(function () {
                values[$(this).data('field')] = String($(this).val());
            });
            return values;
        }
        function changed($row) {
            var values = fields($row), dirty = {}, saved = $row.data('inlineState').saved;
            $.each(values, function (key, value) {
                if (value !== saved[key]) { dirty[key] = value; }
            });
            return dirty;
        }
        function status($row, message, kind) {
            $row.find('.item-status').removeClass('text-muted text-success text-danger text-info')
                .addClass('text-' + (kind || 'muted')).text(message);
        }
        function busy($row, active) {
            $row.toggleClass('item-saving', active);
            $row.find('.item-save, .item-delete').prop('disabled', active);
        }
        function errorMessage(xhr) {
            if (xhr.status === 401 || xhr.status === 419 || (xhr.status === 200 && !xhr.responseJSON)) {
                return 'La sesión venció. Inicia sesión y vuelve a guardar.';
            }
            return xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message : 'No se pudo guardar. Revisa la conexión y pulsa Guardar.';
        }
        function save($row, all) {
            var state = $row.data('inlineState');
            if (state.deleting) { return; }
            if (state.saving) {
                state.queued = true;
                state.saveAll = state.saveAll || all;
                return;
            }
            var payload = all ? fields($row) : changed($row);
            if ($.isEmptyObject(payload)) { return; }
            var valid = true;
            $row.find('.item-field').each(function () {
                if (Object.prototype.hasOwnProperty.call(payload, $(this).data('field')) && !this.checkValidity()) {
                    $(this).addClass('item-invalid').attr('aria-invalid', 'true');
                    valid = false;
                }
            });
            if (!valid) {
                status($row, 'Revisa los campos marcados. No se guardaron los cambios.', 'danger');
                return;
            }
            state.saving = true;
            state.queued = false;
            state.saveAll = false;
            busy($row, true);
            status($row, 'Guardando…', 'info');
            $.ajax({
                url: $row.attr('data-save-url'),
                method: 'POST',
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $root.attr('data-csrf'), 'Accept': 'application/json'},
                data: payload
            }).done(function (response) {
                if (!response || !response.item) {
                    status($row, 'Respuesta inválida. Vuelve a guardar.', 'danger');
                    return;
                }
                $.each(payload, function (key, sent) {
                    var $field = $row.find('[data-field="' + key + '"]');
                    var value = String(response.item[key]);
                    state.saved[key] = value;
                    // No reemplazar texto que el usuario escribió mientras se guardaba.
                    if (String($field.val()) === sent) {
                        $field.val(value).removeClass('item-invalid').removeAttr('aria-invalid');
                        // Refrescar Select2 sin disparar otro guardado automático.
                        if ($field.hasClass('select2-hidden-accessible')) { $field.trigger('change.select2'); }
                    }
                });
                var dirty = !$.isEmptyObject(changed($row));
                $row.toggleClass('item-dirty', dirty);
                status($row, dirty ? 'Cambios pendientes' : 'Guardado', dirty ? 'muted' : 'success');
            }).fail(function (xhr) {
                var messages = [];
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, errors) {
                        $row.find('[data-field="' + key + '"]').addClass('item-invalid').attr('aria-invalid', 'true');
                        messages.push(errors[0]);
                    });
                }
                status($row, messages.length ? messages.join(' ') : errorMessage(xhr), 'danger');
                $row.addClass('item-dirty');
            }).always(function () {
                state.saving = false;
                busy($row, false);
                if (state.queued) { save($row, state.saveAll); }
            });
        }

        $table.find('tr[data-item-id]').each(function () {
            var $row = $(this);
            $row.data('inlineState', {saved: fields($row), saving: false, deleting: false, queued: false, saveAll: false});
        });
        $table.on('input', '.item-field', function () {
            var $row = $(this).closest('tr');
            $(this).removeClass('item-invalid').removeAttr('aria-invalid');
            $row.toggleClass('item-dirty', !$.isEmptyObject(changed($row)));
            if (!$row.data('inlineState').saving) { status($row, 'Cambios pendientes'); }
        });
        $table.on('change', '.item-field', function () { save($(this).closest('tr'), false); });
        $table.on('click', '.item-save', function () { save($(this).closest('tr'), true); });
        $table.on('keydown', 'input.item-field', function (event) {
            if (event.key === 'Enter') { event.preventDefault(); save($(this).closest('tr'), true); }
        });
        $table.on('click', '.item-step', function () {
            var $input = $(this).siblings('input'), value = parseFloat($input.val());
            if (!isFinite(value)) { value = 1; }
            value = Math.max(1, Math.min(1000000, value + Number($(this).data('step'))));
            $input.val(value).trigger('input').trigger('change');
        });
        $table.on('click', '.item-delete', function () {
            var $row = $(this).closest('tr'), state = $row.data('inlineState');
            if (state.saving || state.deleting) { return; }
            var code = $row.find('[data-field="codigo"]').val();
            if (!window.confirm('¿Eliminar el ítem ' + code + '? Se desactivará del catálogo; las planillas existentes se conservarán.')) { return; }
            state.deleting = true;
            busy($row, true);
            $row.find('.item-field, .item-step').prop('disabled', true);
            status($row, 'Eliminando…', 'info');
            $.ajax({
                url: $row.attr('data-delete-url'),
                method: 'POST',
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $root.attr('data-csrf'), 'Accept': 'application/json'}
            }).done(function (response) {
                if (!response || String(response.id) !== $row.attr('data-item-id')) {
                    status($row, 'No se pudo confirmar la eliminación.', 'danger');
                    return;
                }
                $row.find('select.select2-hidden-accessible').each(function () { $(this).select2('destroy'); });
                $row.remove();
                var $count = $('#items-count'), total = Math.max(0, Number($count.attr('data-total')) - 1);
                var count = $table.find('tr[data-item-id]').length;
                $count.attr('data-total', total).text(count + ' ítems en esta página · ' + total + ' resultados');
                if (!count) {
                    $table.find('tbody').append('<tr><td colspan="8" class="text-center text-muted">No hay ítems en esta página. Usa Buscar para actualizar la lista.</td></tr>');
                }
            }).fail(function (xhr) {
                status($row, errorMessage(xhr), 'danger');
            }).always(function () {
                state.deleting = false;
                busy($row, false);
                $row.find('.item-field, .item-step').prop('disabled', false);
            });
        });
        $(window).on('beforeunload.itemsInline', function (event) {
            var pending = false;
            $table.find('tr[data-item-id]').each(function () {
                var $row = $(this), state = $row.data('inlineState');
                pending = pending || state.saving || state.deleting || !$.isEmptyObject(changed($row));
            });
            if (pending) {
                event.preventDefault();
                event.originalEvent.returnValue = '';
                return '';
            }
        });
    });
})(jQuery);
</script>
