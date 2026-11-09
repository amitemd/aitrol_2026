@php $selectorId = 'functional-procedure-selector-'.uniqid(); @endphp
<style>
    .hc4-functional-quick-add {
        margin: 14px 10px 18px;
        padding: 18px;
        border: 1px solid #b8dbe8;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(20, 90, 125, .09);
    }

    .hc4-functional-quick-add__form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 16px;
    }

    .hc4-functional-quick-add__field label {
        display: block;
        margin: 0 0 7px;
        color: #30415a;
        font-size: 14px;
        font-weight: 600;
    }

    .hc4-functional-quick-add .select2-container {
        width: 100% !important;
    }

    .hc4-functional-quick-add .select2-selection--multiple {
        min-height: 44px;
        border: 1px solid #ccd9e2 !important;
        border-radius: 8px !important;
    }

    .hc4-functional-quick-add__submit {
        min-width: 210px;
        height: 44px;
        margin: 0;
        white-space: nowrap;
    }

    .hc4-functional-quick-add__error {
        display: none;
        margin: 10px 0 0;
    }

    @media (max-width: 760px) {
        .hc4-functional-quick-add__form {
            grid-template-columns: 1fr;
        }

        .hc4-functional-quick-add__submit {
            width: 100%;
        }
    }
</style>

<div class="hc4-functional-quick-add" id="{{$selectorId}}">
    <form class="hc4-functional-quick-add__form">
        {{ csrf_field() }}
        <div class="hc4-functional-quick-add__field">
            <label for="{{$selectorId}}-select">Procedimientos funcionales</label>
            <select id="{{$selectorId}}-select" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione uno o más procedimientos"></select>
        </div>
        <button type="submit" class="btn btn-info btn_ordenes hc4-functional-quick-add__submit">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <span>Crear procedimiento</span>
        </button>
    </form>
    <div class="alert alert-danger hc4-functional-quick-add__error"></div>
</div>

<script>
(function () {
    var $panel = $('#{{$selectorId}}');
    var $form = $panel.find('form');
    var $select = $('#{{$selectorId}}-select');
    var $submit = $panel.find('.hc4-functional-quick-add__submit');
    var $error = $panel.find('.hc4-functional-quick-add__error');

    $select.select2({
        width: '100%',
        dropdownParent: $panel,
        placeholder: $select.data('placeholder'),
        closeOnSelect: false,
        ajax: {
            url: "{{route('hc4.ordenes.procedimientos.buscar')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { grupo: 18, q: params.term || '' };
            },
            processResults: function (data) {
                return { results: data.results || [] };
            },
            cache: true
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        $error.hide().text('');

        if (!($select.val() || []).length) {
            $error.text('Seleccione al menos un procedimiento funcional.').show();
            return;
        }

        var contenidoOriginal = $submit.html();
        $submit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creando...');

        $.ajax({
            type: 'POST',
            url: "{{route('hc4_orden_proc_funcional', ['tipo' => $tipo, 'paciente' => $paciente->id])}}",
            data: $form.serialize(),
            dataType: 'json',
            success: function (respuesta) {
                $.get(respuesta.historial_url, function (html) {
                    $('#area_trabajo').html(html);
                }).fail(function () {
                    window.location.reload();
                });
            },
            error: function (xhr) {
                var mensaje = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'No se pudo crear la orden funcional.';
                $error.text(mensaje).show();
                $submit.prop('disabled', false).html(contenidoOriginal);
            }
        });
    });
})();
</script>
