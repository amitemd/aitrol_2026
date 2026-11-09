@include('v3.clinical.operations._styles')
@php
  $receiptCount = count($receipts);
  $issuedCount = 0;
  $pendingCount = 0;
  $receiptTotal = 0;
  foreach ($receipts as $receiptSummary) {
      $receiptTotal += (float) $receiptSummary->total;
      if ((int) $receiptSummary->estado === 1) {
          $issuedCount++;
      } else {
          $pendingCount++;
      }
  }
  $patientName = trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2);
@endphp

<div class="co-page receipt-list-page">
  <section class="co-hero receipt-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas fa-receipt" aria-hidden="true"></i></div>
      <div>
        <div class="co-eyebrow">AGENDA MÉDICA · V3</div>
        <h1>Recibos de cobro</h1>
        <p>{{$patientName}} · {{$patient->id}}</p>
      </div>
    </div>
    <a class="btn btn-light receipt-back" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctorId,'fecha'=>date('Y-m-d',strtotime($appointment->fechaini))))}}">
      <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver a la agenda
    </a>
  </section>

  <section class="receipt-overview" aria-label="Resumen de recibos">
    <article class="receipt-metric">
      <span class="receipt-metric__icon is-blue"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span>
      <div><small>Recibos registrados</small><strong id="receipt-total-count">{{$receiptCount}}</strong></div>
    </article>
    <article class="receipt-metric">
      <span class="receipt-metric__icon is-amber"><i class="fas fa-clock" aria-hidden="true"></i></span>
      <div><small>Pendientes de emitir</small><strong id="receipt-pending-count">{{$pendingCount}}</strong></div>
    </article>
    <article class="receipt-metric">
      <span class="receipt-metric__icon is-green"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
      <div><small>Recibos emitidos</small><strong>{{$issuedCount}}</strong></div>
    </article>
    <article class="receipt-metric receipt-metric--amount">
      <span class="receipt-metric__icon is-teal"><i class="fas fa-dollar-sign" aria-hidden="true"></i></span>
      <div><small>Valor acumulado</small><strong>$ <span id="receipt-total-value">{{number_format($receiptTotal,2,'.',',')}}</span></strong></div>
    </article>
  </section>

  <section class="co-card receipt-list-card">
    <header class="co-head receipt-list-head">
      <div class="co-heading">
        <i class="fas fa-list-ul" aria-hidden="true"></i>
        <div>
          <h2>Recibos registrados</h2>
          <p>Consulta el estado, detalle y valor de los cobros asociados a esta atención.</p>
        </div>
      </div>
      <a class="btn btn-primary js-new-receipt" hidden href="{{route('v3.agenda-receipts.create',array('id_agenda'=>$appointment->id,'_v3'=>1,'_v3_menu'=>(int)config('data.menu_id',0)))}}">
        <i class="fas fa-plus" aria-hidden="true"></i> Crear nuevo recibo
      </a>
    </header>

    <div class="alert alert-danger js-receipt-error" role="alert" hidden></div>

    <div class="receipt-table-wrap">
      <table class="receipt-table">
        <thead>
          <tr>
            <th scope="col">Recibo</th>
            <th scope="col">Fecha de emisión</th>
            <th scope="col">Estado</th>
            <th scope="col">Detalle</th>
            <th scope="col" class="text-right">Valor</th>
            <th scope="col" class="text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($receipts as $receipt)
            <tr data-receipt-total="{{(float)$receipt->total}}" data-receipt-status="{{(int)$receipt->estado}}">
              <td><span class="receipt-number"><i class="fas fa-receipt" aria-hidden="true"></i> #{{$receipt->id}}</span></td>
              <td><span class="receipt-date"><i class="far fa-calendar-alt" aria-hidden="true"></i> {{date('d/m/Y',strtotime($receipt->fecha_emision))}}</span></td>
              <td>
                @if($receipt->venta)
                  <span class="receipt-status is-issued"><i class="fas fa-file-invoice" aria-hidden="true"></i> Facturado</span>
                @elseif((int)$receipt->estado===1)
                  <span class="receipt-status is-issued"><i class="fas fa-check-circle" aria-hidden="true"></i> Emitido</span>
                @else
                  <span class="receipt-status is-pending"><i class="fas fa-clock" aria-hidden="true"></i> Pendiente de emitir</span>
                @endif
              </td>
              <td class="receipt-detail">
                @forelse($receipt->detalles as $detail)
                  <span class="receipt-detail__item">{{$detail->nombre_producto}}</span>
                @empty
                  <span class="receipt-empty-detail">Sin detalle registrado</span>
                @endforelse
              </td>
              <td class="receipt-value">$ {{number_format((float)$receipt->total,2,'.',',')}}</td>
              <td>
                <div class="receipt-actions">
                  <a class="receipt-action is-edit" href="{{route('v3.agenda-receipts.edit',array('id'=>$receipt->id,'_v3'=>1,'_v3_menu'=>(int)config('data.menu_id',0)))}}"><i class="fas {{(int)$receipt->estado===-1?'fa-edit':'fa-eye'}}" aria-hidden="true"></i> {{(int)$receipt->estado===-1?'Editar':'Ver'}}</a>
                  @if((int)$receipt->estado===1)
                    <a class="receipt-action is-print" target="_blank" rel="noopener" href="{{route('facturacion.imprimir_ride',array('id_orden'=>$receipt->id))}}"><i class="fas fa-print" aria-hidden="true"></i> Imprimir</a>
                    <a class="receipt-action is-proforma" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$receipt->id))}}"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i> {{$receipt->venta?'Factura':'Facturar'}}</a>
                  @else
                    <button type="button" class="receipt-action is-void js-void-receipt" data-id="{{$receipt->id}}"><i class="fas fa-ban" aria-hidden="true"></i> Anular</button>
                  @endif
                  @if($receipt->id_proforma)
                    <a class="receipt-action is-proforma" target="_blank" rel="noopener" href="{{route('comercial.proforma.pdf_proforma',array('id_orden'=>$receipt->id_proforma))}}"><i class="fas fa-file-pdf" aria-hidden="true"></i> Proforma</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr class="receipt-empty-row">
              <td colspan="6"><i class="fas fa-file-invoice" aria-hidden="true"></i><strong>No hay recibos registrados</strong><span>Crea un recibo para esta atención médica.</span></td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <footer class="receipt-list-footer">
      <span><i class="fas fa-shield-alt" aria-hidden="true"></i> Los recibos emitidos conservan su respaldo e impresión.</span>
      <strong id="receipt-footer-count">{{$receiptCount}} {{$receiptCount===1?'registro':'registros'}}</strong>
    </footer>
  </section>
</div>

<style>
.receipt-list-page{max-width:1540px;margin:0 auto;color:#173b50}
.receipt-hero{margin-bottom:13px}.receipt-back{display:inline-flex;align-items:center;gap:7px;white-space:nowrap}
.receipt-overview{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:13px}
.receipt-metric{display:flex;align-items:center;gap:12px;min-width:0;padding:13px 15px;border:1px solid #d4e5ed;border-radius:11px;background:#fff;box-shadow:0 5px 16px rgba(29,76,99,.045)}
.receipt-metric__icon{display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border-radius:10px;font-size:15px}.receipt-metric__icon.is-blue{color:#087da4;background:#e2f3fa}.receipt-metric__icon.is-amber{color:#9b6a0c;background:#fff2d7}.receipt-metric__icon.is-green{color:#167454;background:#e1f4eb}.receipt-metric__icon.is-teal{color:#087f82;background:#ddf5f2}
.receipt-metric small,.receipt-metric strong{display:block}.receipt-metric small{margin-bottom:2px;color:#718998;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.35px}.receipt-metric strong{color:#123f5d;font-size:19px;line-height:1.15}.receipt-metric--amount strong{color:#087f82}
.receipt-list-card{width:100%;overflow:hidden}.receipt-list-head{min-height:62px}.receipt-list-head .btn{border-radius:8px;font-size:11px;font-weight:750;padding:8px 12px;background:#078ba2;border-color:#078ba2}.receipt-list-card .js-receipt-error{margin:12px 14px 0;border-radius:8px;font-size:12px}
.receipt-table-wrap{width:100%;overflow-x:auto;padding:8px 14px 4px}.receipt-table{width:100%;min-width:940px;border-collapse:separate;border-spacing:0}
.receipt-table thead th{padding:10px 12px;border-bottom:1px solid #dbe7ec;background:#f5f9fb;color:#587486;font-size:9.5px;font-weight:800;letter-spacing:.45px;text-transform:uppercase;white-space:nowrap}.receipt-table thead th:first-child{border-radius:8px 0 0 8px}.receipt-table thead th:last-child{border-radius:0 8px 8px 0}
.receipt-table tbody td{padding:13px 12px;border-bottom:1px solid #e5eef2;color:#294c61;font-size:11.5px;vertical-align:middle}.receipt-table tbody tr:last-child td{border-bottom:0}.receipt-table tbody tr:hover td{background:#fbfdfe}
.receipt-number{display:inline-flex;align-items:center;gap:7px;color:#075a7c;font-weight:800;white-space:nowrap}.receipt-number i{color:#11a1a8}.receipt-date{display:inline-flex;align-items:center;gap:7px;white-space:nowrap}.receipt-date i{color:#7993a2}
.receipt-status{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:18px;font-size:10px;font-weight:800;white-space:nowrap}.receipt-status.is-issued{color:#19734f;background:#e2f4ec}.receipt-status.is-pending{color:#8a6016;background:#fff2d8}
.receipt-detail{min-width:210px;max-width:420px}.receipt-detail__item{display:block;line-height:1.45;overflow-wrap:anywhere}.receipt-detail__item+.receipt-detail__item{margin-top:3px}.receipt-empty-detail{color:#8aa0ad;font-style:italic}.receipt-value{color:#0b526e!important;font-size:13px!important;font-weight:850;white-space:nowrap;text-align:right}
.receipt-actions{display:flex;justify-content:flex-end;gap:6px;white-space:nowrap}.receipt-action{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-height:30px;padding:6px 9px;border:1px solid transparent;border-radius:7px;background:#fff;font:inherit;font-size:10px;font-weight:800;line-height:1.2;cursor:pointer;text-decoration:none!important;transition:background .15s ease,border-color .15s ease,color .15s ease}.receipt-action.is-edit{color:#087c9b;border-color:#a9d4df}.receipt-action.is-edit:hover{color:#07647c;background:#eaf7fa}.receipt-action.is-print{color:#187151;border-color:#afdbc8}.receipt-action.is-print:hover{color:#125c41;background:#ecf8f2}.receipt-action.is-void{color:#c8444b;border-color:#efb7bb}.receipt-action.is-void:hover{color:#a82e35;background:#fff0f1}.receipt-action.is-proforma{color:#657584;border-color:#cdd8de}.receipt-action.is-proforma:hover{color:#435767;background:#f2f6f8}.receipt-action:disabled{opacity:.55;cursor:wait}
.receipt-list-footer{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:10px 16px;border-top:1px solid #e0eaee;background:#fbfdfe;color:#718998;font-size:10.5px}.receipt-list-footer i{margin-right:5px;color:#1495a1}.receipt-list-footer strong{color:#47687b}
.receipt-empty-row td{height:210px!important;text-align:center!important;color:#78909f!important}.receipt-empty-row i,.receipt-empty-row strong,.receipt-empty-row span{display:block}.receipt-empty-row i{margin-bottom:10px;color:#b7ced8;font-size:31px}.receipt-empty-row strong{margin-bottom:4px;color:#365d72;font-size:14px}.receipt-empty-row span{font-size:11px}
body.dark-mode .receipt-metric,body.dark-mode .receipt-list-card{border-color:#395363;background:#203846}body.dark-mode .receipt-metric strong,body.dark-mode .receipt-list-card .co-head h2{color:#e2edf2}body.dark-mode .receipt-table thead th,body.dark-mode .receipt-list-footer{border-color:#395363;background:#192f3c;color:#a2bac7}body.dark-mode .receipt-table tbody td{border-color:#395363;color:#d8e5eb}body.dark-mode .receipt-table tbody tr:hover td{background:#25404f}body.dark-mode .receipt-value{color:#7fd3d1!important}
@media(max-width:1000px){.receipt-overview{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:650px){.receipt-overview{grid-template-columns:1fr 1fr}.receipt-metric{padding:11px}.receipt-list-head{align-items:flex-start;flex-direction:column}.receipt-list-head .btn{width:100%}.receipt-list-footer{align-items:flex-start;flex-direction:column}.receipt-hero .receipt-back{width:100%;justify-content:center}}@media(max-width:430px){.receipt-overview{grid-template-columns:1fr}}
</style>

<script>
(function() {
  var tableBody = document.querySelector('.receipt-table tbody');
  var totalCount = document.getElementById('receipt-total-count');
  var pendingCount = document.getElementById('receipt-pending-count');
  var totalValue = document.getElementById('receipt-total-value');
  var footerCount = document.getElementById('receipt-footer-count');
  function refreshSummary(row) {
    var currentTotal = parseFloat(String(totalValue.textContent).replace(/,/g, '')) || 0;
    var removedTotal = parseFloat(row.getAttribute('data-receipt-total')) || 0;
    totalCount.textContent = Math.max(0, (parseInt(totalCount.textContent, 10) || 0) - 1);
    pendingCount.textContent = Math.max(0, (parseInt(pendingCount.textContent, 10) || 0) - 1);
    totalValue.textContent = Math.max(0, currentTotal - removedTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    footerCount.textContent = totalCount.textContent + (totalCount.textContent === '1' ? ' registro' : ' registros');
  }
  document.querySelectorAll('.js-void-receipt').forEach(function(button) {
    button.addEventListener('click', function() {
      if (!window.confirm('¿Anular este recibo pendiente?')) return;
      var error = document.querySelector('.js-receipt-error');
      var row = button.closest('tr');
      error.hidden = true;
      button.disabled = true;
      fetch("{{route('recibo_cobro.anular')}}", {
        method: 'POST', credentials: 'same-origin',
        headers: {'X-CSRF-TOKEN': '{{csrf_token()}}', 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_orden=' + encodeURIComponent(button.getAttribute('data-id'))
      }).then(function(response) {
        if (!response.ok || response.redirected || (response.headers.get('content-type') || '').indexOf('text/html') !== -1 && response.headers.get('content-length') !== '0') {
          return response.text().then(function(body) { if (!response.ok || response.redirected || body.trim()) throw new Error('No se pudo anular el recibo.'); });
        }
      }).then(function() {
        refreshSummary(row);
        row.remove();
        if (!tableBody.querySelector('tr[data-receipt-total]')) {
          tableBody.innerHTML = '<tr class="receipt-empty-row"><td colspan="6"><i class="fas fa-file-invoice" aria-hidden="true"></i><strong>No hay recibos registrados</strong><span>Crea un recibo para esta atención médica.</span></td></tr>';
          document.querySelector('.js-new-receipt').hidden = false;
        }
      }).catch(function(err) {
        error.textContent = err.message;
        error.hidden = false;
        button.disabled = false;
      });
    });
  });
})();
</script>
