@php
    $nombrePaciente = trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2);
    $dias = array(1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 7 => 'Domingo');
    $meses = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
@endphp

<style>
    .odont-consultas { overflow: hidden; background: linear-gradient(145deg, #fbfeff, #edf7fa); border: 1px solid #c9dee8; border-radius: 12px; color: #213f51; min-height: 260px; box-shadow: 0 8px 24px rgba(0,74,117,.08); }
    .odont-consultas__header { align-items: center; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; display: flex; justify-content: space-between; padding: 14px 22px; }
    .odont-consultas__header h3 { color: #fff; font-size: 18px; margin: 0; }
    .odont-consultas__paciente { font-size: 15px; font-weight: 600; text-align: right; }
    .odont-consultas__body { overflow: visible; padding: 14px 18px; }
    .odont-consulta { overflow: hidden; background: #fff; border: 1px solid #c9dee8; border-radius: 10px; margin-bottom: 12px; box-shadow: 0 5px 14px rgba(0,74,117,.08); }
    .odont-consulta__resumen { align-items: center; display: grid; gap: 14px; grid-template-columns: 1.15fr 1fr 1.4fr 42px; padding: 12px 16px; }
    .odont-consulta__dato { font-size: 13px; min-width: 0; }
    .odont-consulta__dato strong { display: block; font-size: 11px; text-transform: uppercase; }
    .odont-consulta__fecha { font-size: 15px; text-align: center; }
    .odont-consulta__toggle { background: linear-gradient(135deg, #087eae, #16aaa5); border: 1px solid rgba(255,255,255,.34); border-radius: 9px; color: #fff; height: 34px; width: 34px; box-shadow: 0 4px 11px rgba(0,94,129,.2); }
    .odont-consulta__toggle[disabled] { cursor: wait; opacity: .65; }
    .odont-consulta__detalle { border-top: 1px solid #cbd8e4; display: none; padding: 10px; }
    .odont-consultas__vacio, .odont-consultas__error, .odont-consultas__cargando { background: #fff; color: #4b5563; padding: 24px; text-align: center; }
    .odontologia-033-embebida.content-wrapper { background: transparent !important; margin-left: 0 !important; min-height: 0 !important; }
    .odontologia-033-embebida .odontologia-033 { padding: 0; }
    .odontologia-033-embebida .odo-context, .odontologia-033-embebida .odo-page { max-width: none; }
    .odontologia-033-embebida .odo-footer-actions { position: static; }
    .odontologia-033-embebida .odo-context-grid, .odontologia-033-embebida .odo-c3, .odontologia-033-embebida .odo-c4, .odontologia-033-embebida .odo-c5, .odontologia-033-embebida .odo-c6 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    @media (max-width: 767px) {
        .odont-consultas__header { align-items: flex-start; flex-direction: column; gap: 6px; }
        .odont-consultas__paciente { text-align: left; }
        .odont-consulta__resumen { grid-template-columns: 1fr 42px; }
        .odont-consulta__fecha { grid-column: 1 / -1; text-align: left; }
        .odontologia-033-embebida .odo-context-grid, .odontologia-033-embebida .odo-c2, .odontologia-033-embebida .odo-c3, .odontologia-033-embebida .odo-c4, .odontologia-033-embebida .odo-c5, .odontologia-033-embebida .odo-c6, .odontologia-033-embebida .odo-two-col { grid-template-columns: 1fr; }
    }
</style>

<div class="odont-consultas">
    <div class="odont-consultas__header">
        <h3><i class="fa fa-stethoscope"></i> CONSULTAS ODONTOLOGICAS</h3>
        <div class="odont-consultas__paciente">Paciente: {{ $nombrePaciente }}</div>
    </div>
    <div class="odont-consultas__body">
        @forelse($consultas as $consulta)
            @php
                $fechaConsulta = \Carbon\Carbon::parse($consulta->fechaini);
                $doctor = trim($consulta->doctor_nombre1.' '.$consulta->doctor_nombre2.' '.$consulta->doctor_apellido1.' '.$consulta->doctor_apellido2);
                $esPeriodontologia = stripos((string) $consulta->especialidad_nombre, 'periodon') !== false;
                $urlFormulario = $esPeriodontologia
                    ? route('periodontologia.formulario033.agenda', array('id_agenda' => $consulta->id_agenda))
                    : route('odontologia.formulario033.agenda', array('id_agenda' => $consulta->id_agenda));
            @endphp
            <article class="odont-consulta" data-odont-consulta="{{ $consulta->id_agenda }}">
                <div class="odont-consulta__resumen">
                    <div class="odont-consulta__fecha">
                        {{ $dias[(int) $fechaConsulta->format('N')] }} {{ $fechaConsulta->format('d') }} de {{ $meses[(int) $fechaConsulta->format('n')] }} del {{ $fechaConsulta->format('Y') }}
                    </div>
                    <div class="odont-consulta__dato"><strong>Especialidad</strong>{{ $consulta->especialidad_nombre ?: 'ODONTOLOGIA' }}</div>
                    <div class="odont-consulta__dato"><strong>Doctor(a)</strong>{{ $doctor }}</div>
                    <button type="button" class="odont-consulta__toggle" title="Abrir Formulario 033" aria-expanded="false" data-formulario-url="{{ $urlFormulario }}" onclick="cargarFormulario033Odontologia({{ $consulta->id_agenda }}, this)">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div id="odont-consulta-detalle-{{ $consulta->id_agenda }}" class="odont-consulta__detalle"></div>
            </article>
        @empty
            <div class="odont-consultas__vacio">No existen consultas odontologicas para este paciente.</div>
        @endforelse
    </div>
</div>

<script>
    function cerrarFormularios033Odontologia(excepto) {
        $('.odont-consulta__detalle').each(function () {
            if (this.id === excepto) {
                return;
            }
            $(this).hide().empty();
            $(this).closest('.odont-consulta').find('.odont-consulta__toggle')
                .attr('aria-expanded', 'false')
                .find('i').removeClass('fa-minus').addClass('fa-plus');
        });
    }

    function cargarFormulario033Odontologia(idAgenda, boton) {
        var detalleId = 'odont-consulta-detalle-' + idAgenda;
        var $detalle = $('#' + detalleId);
        var $boton = $(boton);

        if ($detalle.is(':visible')) {
            $detalle.hide().empty();
            $boton.attr('aria-expanded', 'false').find('i').removeClass('fa-minus').addClass('fa-plus');
            return;
        }

        cerrarFormularios033Odontologia(detalleId);
        $boton.prop('disabled', true);
        $detalle.show().html('<div class="odont-consultas__cargando"><i class="fa fa-spinner fa-spin"></i> Cargando Formulario 033...</div>');

        $.ajax({
            type: 'GET',
            url: $boton.attr('data-formulario-url'),
            data: { embebido: 1 },
            dataType: 'html',
            success: function (html) {
                $detalle.html(html);
                $boton.attr('aria-expanded', 'true').find('i').removeClass('fa-plus').addClass('fa-minus');
                $('html, body').animate({ scrollTop: $detalle.offset().top - 80 }, 250);
            },
            error: function (xhr) {
                var mensaje = xhr.status === 404
                    ? 'La consulta no tiene una agenda odontologica valida.'
                    : 'No se pudo cargar el Formulario 033. Error ' + xhr.status + '.';
                $detalle.html('<div class="odont-consultas__error">' + mensaje + '</div>');
                $boton.attr('aria-expanded', 'false').find('i').removeClass('fa-minus').addClass('fa-plus');
            },
            complete: function () {
                $boton.prop('disabled', false);
            }
        });
    }
</script>
