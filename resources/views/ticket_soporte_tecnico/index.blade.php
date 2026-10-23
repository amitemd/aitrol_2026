@extends('ticket_soporte_tecnico.base')
@section('action-content')
<style>
.gateway-inbox { padding: 22px 20px 0; }
.gateway-inbox .box { border: 1px solid #dce5ed; border-radius: 14px; background: #fff; overflow: hidden; box-shadow: 0 5px 22px rgba(28,61,88,.05); margin-bottom: 0; }
.gateway-inbox .inbox-heading { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 24px; }
.gateway-inbox .box-title { color: #163d60; font-size: 23px; font-weight: 600; }
.gateway-inbox .inbox-heading p { color: #64788b; margin: 6px 0 0; }
.gateway-inbox .btn { border-radius: 8px; padding: 10px 16px; font-weight: 600; }
.gateway-inbox .btn-primary { background: #1968a8; border-color: #1968a8; }
.inbox-filters { display: grid; grid-template-columns: 1fr 1fr 1fr auto; align-items: end; gap: 18px; background: #f7fafc; padding: 20px 24px; border-top: 1px solid #e5ecf2; border-bottom: 1px solid #e5ecf2; }
.inbox-filters label { display: block; font-size: 12px; color: #486075; margin-bottom: 7px; }
.inbox-filters .form-control { height: 42px; border-radius: 8px; border-color: #ccdbe6; }
.inbox-filter-actions { display: flex; gap: 8px; }
.gateway-inbox .box-body { padding: 22px 24px; }
.inbox-table-scroll { overflow-x: auto; border: 1px solid #e2e9ef; border-radius: 10px; }
.gateway-inbox #example { margin: 0; min-width: 960px; }
.gateway-inbox #example th { background: #245c88; color: #fff; border: 0; padding: 13px 14px; font-size: 12px; white-space: nowrap; }
.gateway-inbox #example td { padding: 16px 14px; border-color: #e8edf2; vertical-align: top; color: #314e65; font-size: 13px; }
.gateway-inbox #example tr:hover { background: #f8fbfe; }
.gateway-inbox #example td:first-child { width: 200px; font-size: 11px; color: #728497; overflow-wrap: anywhere; }
.gateway-inbox #example td:nth-child(3) { min-width: 180px; font-weight: 600; }
.gateway-inbox #example td:last-child { min-width: 220px; max-width: 360px; overflow-wrap: anywhere; }
.gateway-state { display: inline-block; border-radius: 20px; padding: 5px 10px; background: #edf1f5; color: #526577; font-size: 12px; font-weight: 600; white-space: nowrap; }
[data-gateway-status="open"] .gateway-state { background: #eaf3ff; color: #215f9f; }
[data-gateway-status="assigned"] .gateway-state { background: #fff4d9; color: #86600b; }
[data-gateway-status="resolved"] .gateway-state { background: #e6f5ed; color: #227447; }
.gateway-inbox .gateway-resolution { margin: 6px 0; }
.gateway-inbox #example small { color: #7a8c9d; font-size: 11px; }
.gateway-inbox #gateway-tracking { border-top: 1px solid #e8edf2; padding-top: 18px; margin-top: 20px !important; font-size: 12px; color: #64788b; }
.gateway-inbox #gateway-tracking:after { content: ''; display: block; clear: both; }
.gateway-inbox #gateway-notice { color: #345e7c; }
.gateway-inbox #gateway-tracking details { margin-top: 18px; }
.gateway-inbox summary { cursor: pointer; }
@media(max-width: 900px) { .inbox-filters { grid-template-columns: 1fr 1fr; } }
@media(max-width: 540px) { .gateway-inbox { padding: 12px 10px 0; } .gateway-inbox .inbox-heading { align-items: flex-start; flex-direction: column; } .inbox-filters { grid-template-columns: 1fr; } .gateway-inbox .box-body { padding: 16px; } }
</style>
<section class="content gateway-inbox">
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="box">
        <div class="box-header inbox-heading">
            <div><h3 class="box-title">Mis tickets</h3><p>Consulte sus solicitudes y las soluciones del equipo de soporte.</p></div>
            <a href="{{ route('gateway_tickets.create') }}" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo ticket</a>
        </div>
        <form action="{{url('ticket_soporte_tecnico/buscador')}}" id="formi" method="get" class="inbox-filters">
            <div><label for="estado">Estado</label><select class="form-control" name="estado" id="estado">
                <option value="">Todos los estados</option>
                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Recibido</option>
                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Asignado</option>
                <option value="2" {{ request('estado') === '2' ? 'selected' : '' }}>Resuelto</option>
            </select></div>
            <div><label for="desde">Desde</label><input type="date" name="desde" id="desde" class="form-control" value="{{ $fecha_desde }}"></div>
            <div><label for="hasta">Hasta</label><input type="date" name="hasta" id="hasta" class="form-control" value="{{ $fecha_hasta }}"></div>
            <div class="inbox-filter-actions"><button type="submit" class="btn btn-primary" id="boton_buscar"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button><a href="{{ route('ticket_soporte_tecnico.index') }}" class="btn btn-default">Ver todos</a></div>
        </form>
        <!-- /.box-header -->
        <div class="box-body">
            <p class="text-muted">Historial de mis tickets, incluidos los resueltos. Las fechas son opcionales.</p>
            <div id="example2_wrapper">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="inbox-table-scroll"><table id="example" class="table table-hover" aria-label="Mis tickets de Gateway">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th>Referencia</th>
                                    <th>Origen</th>
                                    <th>Solicitud</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Creada Por</th>
                                    <th>Seguimiento</th>
                                </tr>
                            </thead>
                            <tbody id="gateway-ticket-list"><tr><td colspan="8">Cargando tickets de Gateway…</td></tr></tbody>
                        </table></div>
                        @include('ticket_soporte_tecnico.gateway_tracking')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection
