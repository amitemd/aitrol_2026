<div style="padding:10px;background:#f6f9fc;border:1px solid #cbd9e7;border-radius:4px">
    <div class="table-responsive">
        <table class="table table-condensed table-bordered" style="margin-bottom:8px;background:#fff">
            <thead><tr><th>#</th><th>Procedimiento / concepto</th><th>Cantidad</th><th>Precio unitario</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($detalles as $detalle)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detalle->nombre_producto ?: $detalle->descripcion }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>$ {{ number_format($detalle->precio, 2) }}</td>
                        <td>$ {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Esta cotización no tiene detalles.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <strong>Total: $ {{ number_format($q->total_final, 2) }}</strong>
</div>
