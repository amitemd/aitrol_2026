@php
    $nombrePaciente = trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2);
    $dias = array(1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo');
    $meses = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
@endphp

<style>
    .odont-cotizaciones-panel { overflow:hidden; background:linear-gradient(145deg,#fbfeff,#edf7fa); border:1px solid #c9dee8; border-radius:12px; color:#213f51; min-height:260px; box-shadow:0 8px 24px rgba(0,74,117,.08); }
    .odont-cotizaciones-panel__header { align-items:center; background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%); color:#fff; display:flex; justify-content:space-between; padding:14px 22px; }
    .odont-cotizaciones-panel__header h3 { color:#fff; font-size:18px; margin:0; }
    .odont-cotizaciones-panel__paciente { font-size:15px; font-weight:600; text-align:right; }
    .odont-cotizaciones-panel__body { overflow:visible; padding:14px 18px; }
    .odont-cotizacion-panel-item { overflow:hidden; background:#fff; border:1px solid #c9dee8; border-radius:10px; margin-bottom:12px; box-shadow:0 5px 14px rgba(0,74,117,.08); }
    .odont-cotizacion-panel-item__resumen { align-items:center; display:grid; gap:14px; grid-template-columns:1.25fr .8fr 1fr 42px; padding:12px 16px; }
    .odont-cotizacion-panel-item__fecha { font-size:15px; text-align:center; }
    .odont-cotizacion-panel-item__dato { font-size:14px; min-width:0; }
    .odont-cotizacion-panel-item__dato strong { display:block; font-size:11px; text-transform:uppercase; }
    .odont-cotizacion-panel-item__toggle { background:linear-gradient(135deg,#087eae,#16aaa5); border:1px solid rgba(255,255,255,.34); border-radius:9px; color:#fff; height:34px; width:34px; box-shadow:0 4px 11px rgba(0,94,129,.2); }
    .odont-cotizacion-panel-item__toggle[disabled] { cursor:wait; opacity:.65; }
    .odont-cotizacion-panel-item__detalle { border-top:1px solid #cbd8e4; display:none; padding:10px; }
    .odont-cotizaciones-panel__vacio, .odont-cotizaciones-panel__error, .odont-cotizaciones-panel__cargando { background:#fff; color:#4b5563; padding:24px; text-align:center; }
    @media (max-width:767px) {
        .odont-cotizaciones-panel__header { align-items:flex-start; flex-direction:column; gap:6px; }
        .odont-cotizaciones-panel__paciente { text-align:left; }
        .odont-cotizacion-panel-item__resumen { grid-template-columns:1fr 42px; }
        .odont-cotizacion-panel-item__fecha { grid-column:1 / -1; text-align:left; }
    }
</style>

<div class="odont-cotizaciones-panel">
    <div class="odont-cotizaciones-panel__header">
        <h3><i class="fa fa-file-text-o"></i> COTIZACIONES</h3>
        <div class="odont-cotizaciones-panel__paciente">Paciente: {{ $nombrePaciente }}</div>
    </div>
    <div class="odont-cotizaciones-panel__body">
        @forelse($cotizaciones as $cotizacion)
            @php
                $numero = count($cotizaciones) - $loop->index;
                $fechaCotizacion = \Carbon\Carbon::parse($cotizacion['fecha']);
            @endphp
            <article class="odont-cotizacion-panel-item" data-cotizacion="{{ $cotizacion['clave'] }}">
                <div class="odont-cotizacion-panel-item__resumen">
                    <div class="odont-cotizacion-panel-item__fecha">
                        {{ $dias[(int) $fechaCotizacion->format('N')] }} {{ $fechaCotizacion->format('d') }} de {{ $meses[(int) $fechaCotizacion->format('n')] }} del {{ $fechaCotizacion->format('Y') }}
                    </div>
                    <div class="odont-cotizacion-panel-item__dato"><strong>Cotización</strong>#{{ $numero }}</div>
                    <div class="odont-cotizacion-panel-item__dato"><strong>Total</strong>$ {{ number_format($cotizacion['total'], 2) }}</div>
                    <button type="button" class="odont-cotizacion-panel-item__toggle" title="Ver cotización" aria-expanded="false"
                        data-url="{{ $cotizacion['detalle_url'] }}" onclick="cargarDetalleCotizacionOdontologica('{{ $cotizacion['clave'] }}', this)">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div id="odont-cotizacion-detalle-{{ $cotizacion['clave'] }}" class="odont-cotizacion-panel-item__detalle"></div>
            </article>
        @empty
            <div class="odont-cotizaciones-panel__vacio">No existen cotizaciones para este paciente.</div>
        @endforelse
    </div>
</div>

<script>
    function cerrarDetallesCotizacionesOdontologicas(excepto) {
        $('.odont-cotizacion-panel-item__detalle').each(function () {
            if (this.id === excepto) { return; }
            $(this).hide().empty();
            $(this).closest('.odont-cotizacion-panel-item').find('.odont-cotizacion-panel-item__toggle')
                .attr('aria-expanded', 'false').find('i').removeClass('fa-minus').addClass('fa-plus');
        });
    }

    function cargarDetalleCotizacionOdontologica(clave, boton) {
        var detalleId = 'odont-cotizacion-detalle-' + clave;
        var $detalle = $('#' + detalleId);
        var $boton = $(boton);
        if ($detalle.is(':visible')) {
            $detalle.hide().empty();
            $boton.attr('aria-expanded', 'false').find('i').removeClass('fa-minus').addClass('fa-plus');
            return;
        }
        cerrarDetallesCotizacionesOdontologicas(detalleId);
        $boton.prop('disabled', true);
        $detalle.show().html('<div class="odont-cotizaciones-panel__cargando"><i class="fa fa-spinner fa-spin"></i> Cargando cotización...</div>');
        $.ajax({
            type: 'GET',
            url: $boton.data('url'),
            dataType: 'html',
            success: function (html) {
                $detalle.html(html);
                $boton.attr('aria-expanded', 'true').find('i').removeClass('fa-plus').addClass('fa-minus');
                $('html, body').animate({scrollTop:$detalle.offset().top - 80}, 250);
            },
            error: function () {
                $detalle.html('<div class="odont-cotizaciones-panel__error">No se pudo cargar esta cotización.</div>');
                $boton.attr('aria-expanded', 'false').find('i').removeClass('fa-minus').addClass('fa-plus');
            },
            complete: function () { $boton.prop('disabled', false); }
        });
    }

    (function () {
        var abrir = {!! json_encode($abrir) !!};
        if (!abrir) { return; }
        var $item = $('.odont-cotizacion-panel-item').filter(function () { return $(this).data('cotizacion') === abrir; }).first();
        var boton = $item.find('.odont-cotizacion-panel-item__toggle').get(0);
        if (boton) { cargarDetalleCotizacionOdontologica(abrir, boton); }
    }());
</script>
