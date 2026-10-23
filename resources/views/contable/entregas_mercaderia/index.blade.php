@extends('contable.ventas.base')

@section('action-content')
<style>
    .deliveries-page {
        --delivery-primary: #1769aa;
        --delivery-primary-dark: #0f4f85;
        --delivery-border: #dfe7ee;
        --delivery-muted: #6b7c8c;
        --delivery-surface: #ffffff;
        padding-top: 18px;
    }

    .deliveries-shell {
        overflow: hidden;
        background: var(--delivery-surface);
        border: 1px solid var(--delivery-border);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(25, 58, 88, .08);
    }

    .deliveries-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        color: #fff;
        background: linear-gradient(115deg, #0f568e 0%, #1976b9 62%, #2794cf 100%);
    }

    .deliveries-title-wrap {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .deliveries-title-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .26);
        border-radius: 12px;
        font-size: 22px;
    }

    .deliveries-title {
        margin: 0 0 4px;
        font-size: 21px;
        font-weight: 600;
        letter-spacing: -.2px;
    }

    .deliveries-subtitle {
        margin: 0;
        color: rgba(255, 255, 255, .82);
        font-size: 13px;
    }

    .deliveries-new-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 9px 16px;
        color: #11517f !important;
        background: #fff !important;
        border: 1px solid rgba(255, 255, 255, .8) !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(7, 45, 75, .16);
        font-weight: 600;
    }

    .deliveries-new-button:hover,
    .deliveries-new-button:focus {
        color: #0b4068 !important;
        background: #f2f8fc !important;
    }

    .deliveries-content {
        padding: 22px 24px 24px;
    }

    .deliveries-alert {
        margin-bottom: 18px;
        border: 0;
        border-left: 4px solid #2ca66f;
        border-radius: 7px;
    }

    .deliveries-filter {
        margin-bottom: 20px;
        padding: 16px;
        background: #f7fafc;
        border: 1px solid #e4ebf1;
        border-radius: 9px;
    }

    .deliveries-filter-title {
        margin: 0 0 12px;
        color: #29465f;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .55px;
        text-transform: uppercase;
    }

    .deliveries-filter-grid {
        display: flex;
        align-items: flex-end;
        gap: 12px;
    }

    .deliveries-field {
        flex: 1 1 auto;
    }

    .deliveries-field-search {
        flex-basis: 48%;
    }

    .deliveries-field-status {
        flex-basis: 29%;
    }

    .deliveries-field label {
        display: block;
        margin-bottom: 6px;
        color: #536779;
        font-size: 12px;
        font-weight: 600;
    }

    .deliveries-input-wrap {
        position: relative;
    }

    .deliveries-input-wrap > i {
        position: absolute;
        top: 50%;
        left: 12px;
        z-index: 2;
        color: #8294a3;
        transform: translateY(-50%);
    }

    .deliveries-input-wrap .form-control {
        padding-left: 35px;
    }

    .deliveries-filter .form-control {
        height: 40px;
        color: #31495d;
        background: #fff;
        border-color: #d6e0e8;
        border-radius: 7px;
        box-shadow: none;
    }

    .deliveries-filter .form-control:focus {
        border-color: #66a9d4;
        box-shadow: 0 0 0 3px rgba(39, 148, 207, .1);
    }

    .deliveries-filter-actions {
        display: flex;
        flex: 0 0 auto;
        gap: 8px;
    }

    .deliveries-search-button,
    .deliveries-clear-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 40px;
        padding: 8px 15px;
        border-radius: 7px !important;
        font-weight: 600;
    }

    .deliveries-search-button {
        color: #fff !important;
        background: var(--delivery-primary) !important;
        border-color: var(--delivery-primary) !important;
    }

    .deliveries-clear-button {
        color: #5d7182 !important;
        background: #fff !important;
        border-color: #d6e0e8 !important;
    }

    .deliveries-table-wrap {
        overflow: hidden;
        border: 1px solid #e1e8ee;
        border-radius: 9px;
    }

    .deliveries-table {
        margin: 0 !important;
    }

    .deliveries-table > thead > tr > th {
        padding: 12px 13px;
        color: #42596d;
        background: #f4f7f9;
        border: 0;
        border-bottom: 1px solid #dfe7ee;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .deliveries-table > tbody > tr > td {
        padding: 13px;
        color: #31495d;
        border-color: #edf1f4;
        vertical-align: middle;
    }

    .deliveries-table > tbody > tr:hover > td {
        background: #f8fbfd;
    }

    .delivery-number {
        color: #145f98;
        font-weight: 700;
    }

    .delivery-client-name {
        display: block;
        color: #263f53;
        font-weight: 600;
    }

    .delivery-secondary {
        display: block;
        margin-top: 3px;
        color: #8493a0;
        font-size: 11px;
    }

    .delivery-document {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #486174;
        font-size: 12px;
        font-weight: 600;
    }

    .delivery-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .delivery-status-success { color: #177248; background: #e6f6ee; }
    .delivery-status-warning { color: #9b6612; background: #fff3d9; }
    .delivery-status-danger { color: #a13c3c; background: #fdeaea; }
    .delivery-status-neutral { color: #52697a; background: #edf2f5; }

    .delivery-view-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 32px;
        padding: 6px 10px;
        color: #1769aa !important;
        background: #eef6fb !important;
        border: 1px solid #cfe2ef !important;
        border-radius: 6px !important;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .delivery-view-button:hover,
    .delivery-view-button:focus {
        background: #deedf7 !important;
        border-color: #afd0e4 !important;
    }

    .deliveries-empty {
        padding: 54px 20px !important;
        text-align: center;
        background: #fff !important;
    }

    .deliveries-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 66px;
        height: 66px;
        margin-bottom: 14px;
        color: #2d83bd;
        background: #eaf4fa;
        border-radius: 50%;
        font-size: 26px;
    }

    .deliveries-empty h4 {
        margin: 0 0 6px;
        color: #2c4559;
        font-size: 16px;
        font-weight: 600;
    }

    .deliveries-empty p {
        margin: 0 0 16px;
        color: var(--delivery-muted);
        font-size: 13px;
    }

    .deliveries-pagination {
        margin-top: 18px;
        text-align: right;
    }

    .deliveries-pagination .pagination {
        margin: 0;
    }

    .deliveries-page .select2-container { width: 100% !important; }

    .deliveries-page .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #cfdce5;
        border-radius: 7px;
    }

    .deliveries-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
        padding-right: 32px;
        color: #3c5668;
        line-height: 36px;
        font-size: 12px;
    }

    .deliveries-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }

    .deliveries-page .select2-container--default.select2-container--focus .select2-selection--single,
    .deliveries-page .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #55a9d8;
        box-shadow: 0 0 0 3px rgba(45, 145, 199, .11);
    }

    @media (max-width: 991px) {
        .deliveries-filter-grid { flex-wrap: wrap; }
        .deliveries-field-search { flex-basis: 60%; }
        .deliveries-field-status { flex-basis: 35%; }
        .deliveries-filter-actions { width: 100%; }
    }

    @media (max-width: 767px) {
        .deliveries-page { padding-top: 10px; }
        .deliveries-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
        .deliveries-new-button { width: 100%; }
        .deliveries-content { padding: 16px; }
        .deliveries-filter-grid { display: block; }
        .deliveries-field { margin-bottom: 12px; }
        .deliveries-filter-actions { display: flex; }
        .deliveries-filter-actions .btn { flex: 1 1 0; }
    }
</style>

<section class="content deliveries-page">
    <div class="deliveries-shell">
        <div class="deliveries-hero">
            <div class="deliveries-title-wrap">
                <span class="deliveries-title-icon"><i class="fa fa-truck" aria-hidden="true"></i></span>
                <div>
                    <h1 class="deliveries-title">Entregas de mercadería</h1>
                    <p class="deliveries-subtitle">Control de salida, guía de remisión, inventario y facturación.</p>
                </div>
            </div>
            <a href="{{ route('entregas_mercaderia.create') }}" class="btn deliveries-new-button">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Nueva entrega
            </a>
        </div>

        <div class="deliveries-content">
            @if (session('success'))
                <div class="alert alert-success deliveries-alert">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('entregas_mercaderia.index') }}" class="deliveries-filter">
                <p class="deliveries-filter-title"><i class="fa fa-filter" aria-hidden="true"></i> Filtros de búsqueda</p>
                <div class="deliveries-filter-grid">
                    <div class="deliveries-field deliveries-field-search">
                        <label for="buscar-entrega">Buscar entrega</label>
                        <div class="deliveries-input-wrap">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="buscar-entrega" class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Número, identificación o nombre del cliente">
                        </div>
                    </div>
                    <div class="deliveries-field deliveries-field-status">
                        <label for="estado-entrega">Estado</label>
                        <select id="estado-entrega" class="form-control deliveries-status-select" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="ENTREGADA" {{ request('estado') == 'ENTREGADA' ? 'selected' : '' }}>Pendiente de factura</option>
                            <option value="FACTURADA" {{ request('estado') == 'FACTURADA' ? 'selected' : '' }}>Facturada</option>
                            <option value="ANULADA" {{ request('estado') == 'ANULADA' ? 'selected' : '' }}>Anulada</option>
                        </select>
                    </div>
                    <div class="deliveries-filter-actions">
                        <button class="btn btn-primary deliveries-search-button" type="submit"><i class="fa fa-search"></i> Buscar</button>
                        @if(request('buscar') || request('estado'))
                            <a href="{{ route('entregas_mercaderia.index') }}" class="btn btn-default deliveries-clear-button"><i class="fa fa-times"></i> Limpiar</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-responsive deliveries-table-wrap">
                <table class="table deliveries-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Guía SRI</th>
                            <th>Inventario</th>
                            <th>Factura</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($entregas as $entrega)
                        <tr>
                            <td><span class="delivery-number">{{ $entrega->numero }}</span></td>
                            <td>
                                <span class="delivery-document"><i class="fa fa-calendar-o"></i> {{ $entrega->fecha_entrega }}</span>
                            </td>
                            <td>
                                <span class="delivery-client-name">{{ $entrega->cliente ? $entrega->cliente->nombre : 'Cliente sin nombre' }}</span>
                                <span class="delivery-secondary">{{ $entrega->id_cliente }}</span>
                            </td>
                            <td>
                                <span class="delivery-document"><i class="fa fa-file-text-o"></i> #{{ $entrega->id_guia_remision }}</span>
                                <span class="delivery-secondary">
                                    @if($entrega->guia && $entrega->guia->estado == 5)
                                        <span class="delivery-status delivery-status-success"><i class="fa fa-check-circle"></i> Autorizada</span>
                                    @elseif($entrega->guia && $entrega->guia->estado == -2)
                                        <span class="delivery-status delivery-status-danger"><i class="fa fa-ban"></i> Anulada</span>
                                    @elseif($entrega->guia && in_array($entrega->guia->estado, [7, 9]))
                                        <span class="delivery-status delivery-status-danger"><i class="fa fa-exclamation-circle"></i> Error SRI</span>
                                    @else
                                        <span class="delivery-status delivery-status-warning"><i class="fa fa-clock-o"></i> En proceso</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="delivery-document"><i class="fa fa-cubes"></i> Movimiento #{{ $entrega->id_movimiento_inventario }}</span>
                                <span class="delivery-secondary">Asiento contable #{{ $entrega->id_asiento_costo }}</span>
                            </td>
                            <td>
                                @if($entrega->id_venta)
                                    <span class="delivery-document"><i class="fa fa-file-text"></i> #{{ $entrega->id_venta }}</span>
                                @else
                                    <span class="delivery-status delivery-status-neutral"><i class="fa fa-hourglass-half"></i> Pendiente</span>
                                @endif
                            </td>
                            <td>
                                @if($entrega->estado == 'ANULADA')
                                    <span class="delivery-status delivery-status-danger"><i class="fa fa-ban"></i> Anulada</span>
                                @elseif($entrega->estado == 'FACTURADA')
                                    <span class="delivery-status delivery-status-success"><i class="fa fa-check"></i> Facturada</span>
                                @else
                                    <span class="delivery-status delivery-status-warning"><i class="fa fa-truck"></i> Entregada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn delivery-view-button" href="{{ route('entregas_mercaderia.show', $entrega->id) }}" title="Ver detalle y trazabilidad">
                                    <i class="fa fa-eye"></i> Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="deliveries-empty">
                                <span class="deliveries-empty-icon"><i class="fa fa-truck" aria-hidden="true"></i></span>
                                @if(request('buscar') || request('estado'))
                                    <h4>No encontramos entregas</h4>
                                    <p>Prueba cambiando o limpiando los filtros de búsqueda.</p>
                                    <a href="{{ route('entregas_mercaderia.index') }}" class="btn deliveries-clear-button"><i class="fa fa-times"></i> Limpiar filtros</a>
                                @else
                                    <h4>Aún no hay entregas registradas</h4>
                                    <p>Registra la primera salida de mercadería para iniciar su trazabilidad.</p>
                                    <a href="{{ route('entregas_mercaderia.create') }}" class="btn btn-primary deliveries-search-button"><i class="fa fa-plus"></i> Crear primera entrega</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($entregas->lastPage() > 1)
                <div class="deliveries-pagination">
                    {{ $entregas->appends(['buscar' => request('buscar'), 'estado' => request('estado')])->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
<script>
$(function () {
    $('#estado-entrega').select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });
});
</script>
@endsection
