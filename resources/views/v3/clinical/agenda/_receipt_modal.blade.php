@php
    $patientName = trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2);
@endphp
<div class="modal-header da-modal-head rc-modal-head">
    <div>
        <small>AGENDA MÉDICA · V3</small>
        <h5 class="modal-title"><i class="fas fa-receipt mr-2"></i>Recibos de cobro</h5>
        <span>{{$patientName}} · {{$patient->id}}</span>
    </div>
    <div class="rc-modal-head__actions">
        <a class="btn btn-light" href="{{route('v3.agenda-receipts.create',array('id_agenda'=>$appointment->id,'_v3'=>1,'_v3_menu'=>(int)config('data.menu_id',0)))}}"><i class="fas fa-plus mr-1"></i> Crear nuevo</a>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    </div>
</div>
<div class="modal-body rc-modal-body">
    <div class="alert alert-danger rc-modal-error" role="alert" hidden></div>
    <section class="rc-modal-panel">
        <header><div><i class="fas fa-list-ul"></i></div><span><strong>Recibos de esta atención</strong><small>Administra cada cobro sin salir de la agenda.</small></span></header>
        <div class="rc-modal-table-wrap">
            <table class="rc-modal-table">
                <thead><tr><th>Recibo</th><th>Emisión</th><th>Estado</th><th>Detalle</th><th class="text-right">Valor</th><th class="text-right">Acciones</th></tr></thead>
                <tbody>
                @forelse($receipts as $receipt)
                    <tr>
                        <td><span class="rc-number"><i class="fas fa-receipt"></i> #{{$receipt->id}}</span></td>
                        <td><span class="rc-date"><i class="far fa-calendar-alt"></i> {{$receipt->fecha_emision?date('d/m/Y',strtotime($receipt->fecha_emision)):'Sin fecha'}}</span></td>
                        <td>
                            @if($receipt->venta)
                                <span class="rc-state is-issued"><i class="fas fa-file-invoice"></i> Facturado</span>
                            @elseif((int)$receipt->estado===1)
                                <span class="rc-state is-issued"><i class="fas fa-check-circle"></i> Emitido</span>
                            @else
                                <span class="rc-state is-pending"><i class="fas fa-clock"></i> Pendiente</span>
                            @endif
                        </td>
                        <td class="rc-detail">
                            @forelse($receipt->detalles as $detail)
                                <span>{{$detail->nombre_producto}}</span>
                            @empty
                                <em>Sin productos registrados</em>
                            @endforelse
                        </td>
                        <td class="rc-value">$ {{number_format((float)$receipt->total,2,'.',',')}}</td>
                        <td><div class="rc-actions">
                            <a class="rc-action is-edit" href="{{route('v3.agenda-receipts.edit',array('id'=>$receipt->id,'_v3'=>1,'_v3_menu'=>(int)config('data.menu_id',0)))}}" title="{{(int)$receipt->estado===-1?'Editar':'Ver'}} recibo"><i class="fas {{(int)$receipt->estado===-1?'fa-edit':'fa-eye'}}"></i><span>{{(int)$receipt->estado===-1?'Editar':'Ver'}}</span></a>
                            @if((int)$receipt->estado===1)
                                <a class="rc-action is-print" target="_blank" rel="noopener" href="{{route('facturacion.imprimir_ride',array('id_orden'=>$receipt->id))}}" title="Imprimir recibo"><i class="fas fa-print"></i><span>Imprimir</span></a>
                                <a class="rc-action is-proforma" href="{{route('v3.agenda-receipts.invoice-preview',array('receipt'=>$receipt->id))}}" title="{{$receipt->venta?'Ver factura vinculada':'Facturar recibo'}}"><i class="fas fa-file-invoice-dollar"></i><span>{{$receipt->venta?'Factura':'Facturar'}}</span></a>
                            @else
                                <button type="button" class="rc-action is-void js-void-receipt-modal" data-id="{{$receipt->id}}" data-url="{{route('recibo_cobro.anular')}}" title="Anular recibo"><i class="fas fa-ban"></i><span>Anular</span></button>
                            @endif
                            @if($receipt->id_proforma)
                                <a class="rc-action is-proforma" target="_blank" rel="noopener" href="{{route('comercial.proforma.pdf_proforma',array('id_orden'=>$receipt->id_proforma))}}" title="Descargar proforma"><i class="fas fa-file-pdf"></i><span>Proforma</span></a>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr class="rc-empty"><td colspan="6"><i class="fas fa-file-invoice"></i><strong>No hay recibos registrados</strong><span>Use “Crear nuevo” para iniciar el cobro de esta atención.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="modal-footer rc-modal-footer">
    <span><i class="fas fa-shield-alt mr-1"></i> Los recibos emitidos conservan su impresión y trazabilidad.</span>
    <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
</div>
