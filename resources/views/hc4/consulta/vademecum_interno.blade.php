<style>
    .v3-medicine-table-wrap { max-height: 58vh; overflow: auto; background: #fff; }
    .v3-medicine-table { width: 100%; margin: 0; border: 0; border-collapse: separate; border-spacing: 0; color: #34495a; font-size: 12px; }
    .v3-medicine-table th { position: sticky; top: 0; z-index: 2; padding: 11px 10px; border-right: 1px solid rgba(255,255,255,.18); border-bottom: 0; background: #075f86; color: #fff; font-size: 11px; font-weight: 700; line-height: 1.25; text-align: left; vertical-align: middle; white-space: normal; }
    .v3-medicine-table th:last-child { border-right: 0; }
    .v3-medicine-table td { padding: 11px 10px; border-right: 1px solid #e3edf1; border-bottom: 1px solid #e3edf1; line-height: 1.4; vertical-align: top; overflow-wrap: anywhere; }
    .v3-medicine-table td:last-child { border-right: 0; }
    .v3-medicine-table tbody tr:nth-child(even) { background: #f3f9fb; }
    .v3-medicine-table tbody tr:hover { background: #e5f6f7; }
    .v3-medicine-name { color: #075f86; font-weight: 700; }
    .v3-medicine-lab { display: inline-block; padding: 3px 7px; border-radius: 999px; background: #dff4f3; color: #087c82; font-size: 10px; font-weight: 700; }
    .v3-medicine-empty { padding: 38px 20px !important; color: #6f8290; text-align: center; }
    .v3-medicine-pager { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 13px 18px; border-top: 1px solid #d7e7ee; background: #f5fafc; }
    .v3-medicine-pager__summary { color: #607888; font-size: 12px; }
    .v3-medicine-pager__buttons { display: flex; align-items: center; gap: 5px; }
    .v3-medicine-page { min-width: 34px; height: 34px; padding: 0 9px; border: 1px solid #bdd6df; border-radius: 8px; background: #fff; color: #075f86; font-size: 12px; font-weight: 700; }
    .v3-medicine-page:hover:not(:disabled), .v3-medicine-page.is-active { border-color: #0b94aa; background: #0b94aa; color: #fff; }
    .v3-medicine-page:disabled { cursor: default; opacity: .45; }
    @media (max-width: 760px) {
        .v3-medicine-table-wrap { max-height: 54vh; }
        .v3-medicine-table { min-width: 1050px; }
        .v3-medicine-pager { align-items: flex-start; flex-direction: column; }
    }
</style>

<div class="v3-medicine-table-wrap">
    <table class="v3-medicine-table">
        <thead>
            <tr>
                <th>Nombre</th><th>Presentación</th><th>Dosis recomendada</th><th>Dosis pediátrica</th><th>Laboratorio</th><th>Indicaciones</th><th>Contraindicaciones</th><th>P. unitario</th><th>P. total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nombre as $value)
                <tr>
                    <td class="v3-medicine-name">{{$value->nombre ?: 'Sin nombre'}}</td>
                    <td>{{$value->presentacion ?: '—'}}</td>
                    <td>{{$value->dosis ?: '—'}}</td>
                    <td>{{$value->dosis_pediatrica ?: '—'}}</td>
                    <td>@if($value->laboratorio)<span class="v3-medicine-lab">{{$value->laboratorio}}</span>@else — @endif</td>
                    <td>{{$value->indicaciones ?: '—'}}</td>
                    <td>{{$value->contraindicaciones ?: '—'}}</td>
                    <td>{{is_null($value->precio_unitario) ? '—' : number_format((float) $value->precio_unitario, 2)}}</td>
                    <td>{{is_null($value->precio_total) ? '—' : number_format((float) $value->precio_total, 2)}}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="v3-medicine-empty"><i class="fa fa-search"></i> No se encontraron medicinas con ese criterio.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@php
    $paginaActual = $nombre->currentPage();
    $ultimaPagina = $nombre->lastPage();
    $paginaInicial = max(1, $paginaActual - 2);
    $paginaFinal = min($ultimaPagina, $paginaActual + 2);
@endphp

<footer class="v3-medicine-pager">
    <div class="v3-medicine-pager__summary">Mostrando {{$nombre->firstItem() ?: 0}}–{{$nombre->lastItem() ?: 0}} de {{$nombre->total()}} medicinas</div>
    @if($ultimaPagina > 1)
        <div class="v3-medicine-pager__buttons" aria-label="Paginación del catálogo">
            <button type="button" class="v3-medicine-page js-vademecum-page" data-page="{{$paginaActual - 1}}" @if($paginaActual <= 1) disabled @endif aria-label="Página anterior"><i class="fa fa-angle-left"></i></button>
            @for($pagina = $paginaInicial; $pagina <= $paginaFinal; $pagina++)
                <button type="button" class="v3-medicine-page js-vademecum-page @if($pagina === $paginaActual) is-active @endif" data-page="{{$pagina}}" @if($pagina === $paginaActual) disabled @endif>{{$pagina}}</button>
            @endfor
            <button type="button" class="v3-medicine-page js-vademecum-page" data-page="{{$paginaActual + 1}}" @if($paginaActual >= $ultimaPagina) disabled @endif aria-label="Página siguiente"><i class="fa fa-angle-right"></i></button>
        </div>
    @endif
</footer>
