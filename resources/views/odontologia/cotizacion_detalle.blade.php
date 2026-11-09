<style>
    .odont-cotizacion-exportar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 18px;
        border: 1px solid rgba(255, 255, 255, .34) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        box-shadow: 0 5px 14px rgba(0, 74, 117, .2);
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        text-decoration: none !important;
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .odont-cotizacion-exportar:hover,
    .odont-cotizacion-exportar:focus {
        color: #fff !important;
        filter: brightness(1.06);
        box-shadow: 0 7px 17px rgba(0, 74, 117, .26);
        outline: none;
        transform: translateY(-1px);
    }
    .odont-cotizacion-exportar:active {
        box-shadow: 0 3px 9px rgba(0, 74, 117, .2);
        transform: translateY(0);
    }
</style>
<div style="padding:10px;background:#f6f9fc;border:1px solid #cbd9e7;border-radius:4px">
    <div class="table-responsive">
        <table class="table table-condensed table-bordered" style="margin-bottom:8px;background:#fff">
            <thead>
                <tr><th>#</th><th>Fecha</th><th>Pieza</th><th>Caras</th><th>Diagnóstico</th><th>Procedimiento</th><th>Precio</th></tr>
            </thead>
            <tbody>
                @forelse($filas as $fila)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ isset($fila['fecha']) ? $fila['fecha'] : '' }}</td>
                        <td>{{ isset($fila['pieza']) ? $fila['pieza'] : '' }}</td>
                        <td>{{ isset($fila['caras']) ? $fila['caras'] : '' }}</td>
                        <td>{{ isset($fila['diagnosticos']) ? $fila['diagnosticos'] : '' }}</td>
                        <td>{{ isset($fila['procedimiento']) ? $fila['procedimiento'] : '' }}</td>
                        <td style="white-space:nowrap">{{ isset($fila['precio']) && $fila['precio'] !== null && $fila['precio'] !== '' ? '$ '.number_format($fila['precio'], 2) : 'Sin precio' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">Esta cotización no tiene procedimientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
        <strong>Total: $ {{ number_format($q->total, 2) }}</strong>
        <a class="btn odont-cotizacion-exportar" target="_blank" rel="noopener" href="{{ route(isset($rutaPdf) ? $rutaPdf : 'odontologia.cotizaciones.pdf', array('hcid' => $q->hcid, 'id' => $q->id)) }}">
            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
            <span>Exportar PDF</span>
        </a>
    </div>
</div>
