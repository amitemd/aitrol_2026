<div class="odont-cotizaciones-lista" style="max-height:140px;overflow:auto;padding-right:3px">
    @forelse($cotizaciones as $cotizacion)
        @php($numero = count($cotizaciones) - $loop->index)
        <div class="odont-cotizacion-item" style="padding:9px 0;border-bottom:1px solid #d9e2ed">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                <div>
                    <strong style="display:block;color:#234f78">Cotización #{{ $numero }}</strong>
                    <span style="color:#5e6d79">{{ $cotizacion['fecha'] }} · Total: $ {{ number_format($cotizacion['total'], 2) }}</span>
                </div>
                <button type="button" class="btn btn-info btn-xs odont-cotizacion-ver"
                    data-url="{{ $cotizacion['panel_url'] }}">Ver</button>
            </div>
        </div>
    @empty
        <p style="margin:5px 0;color:#5e6d79">Este paciente aún no tiene cotizaciones registradas.</p>
    @endforelse
</div>
