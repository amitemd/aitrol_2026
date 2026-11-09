@if(!$imprimir)
<div class="odo-no-print" data-cotizacion-editor style="margin-top:16px">
    <button type="button" id="odo-armar-cotizacion" class="odo-primary" aria-expanded="false" aria-controls="odo-cotizacion">Armar cotizacion</button>
    <section id="odo-cotizacion" hidden style="margin-top:16px;border:1px solid #ccd9e7;padding:16px;border-radius:6px;background:#f8fbff">
        <div class="odo-title"><h2>Cotizacion del tratamiento</h2><span id="odo-cotizacion-numero"></span></div>
        <p id="odo-cotizacion-mensaje" role="status" aria-live="polite"></p>
        <div style="overflow-x:auto"><table class="odo-table">
            <thead><tr><th>#</th><th>Fecha</th><th>Pieza</th><th>Caras</th><th>Diagnostico (CIE-10)</th><th>Procedimiento asociado</th><th>Precio por procedimiento</th></tr></thead>
            <tbody id="odo-cotizacion-filas"></tbody>
        </table></div>
        <p style="text-align:right;font-size:18px;margin-top:14px"><strong id="odo-cotizacion-total"></strong></p>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <button type="button" id="odo-cotizacion-guardar" class="odo-primary">Guardar</button>
            <button type="button" id="odo-cotizacion-pdf" class="odo-secondary">Exportar PDF</button>
            <button type="button" id="odo-cotizacion-emitir" class="odo-secondary">Emitir cotizacion</button>
        </div>
        <p style="margin-top:10px;color:#52657b">El precio se modifica solo en esta cotizacion. Al emitirla se conserva una copia de los procedimientos y precios.</p>
    </section>
</div>
@endif
