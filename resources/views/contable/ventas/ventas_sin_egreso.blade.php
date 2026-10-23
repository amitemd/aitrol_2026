@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .ventas-sin-egreso {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .ventas-sin-egreso {
            background: #f4f6f9;
            padding: 1rem 0;
        }

        .ventas-sin-egreso .page-header {
            background: #ffffff;
            color: #acacac;
            padding: 1.4rem 1.5rem;
            border-radius: 0.85rem;
            border: 1px solid #dfe6ee;
            box-shadow: 0 10px 20px rgba(16, 40, 86, 0.08);
            margin-bottom: 1.5rem;
        }

        .ventas-sin-egreso .page-header h4 {
            margin-bottom: 0.25rem;
            font-size: 1.45rem;
            letter-spacing: 0.02em;
        }

        .ventas-sin-egreso .page-header small {
            color: #5f6f8a;
        }

        .ventas-sin-egreso .page-header .page-actions .btn {
            min-width: 210px;
        }

        .ventas-sin-egreso .filter-panel {
            background: #ffffff;
            border-radius: 0.85rem;
            border: 1px solid #e5ebf2;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 18px rgba(15, 40, 75, 0.05);
        }

        .ventas-sin-egreso .filter-panel .form-group label {
            font-weight: 600;
            color: #3c4a66;
        }

        .ventas-sin-egreso .filter-panel .btn-primary {
            background: #0e5487;
            border-color: #0e5487;
        }

        .ventas-sin-egreso .summary-card {
            background: #f7f9fc;
            color: #1b2a4f;
            border-radius: 0.85rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 8px 24px rgba(14, 56, 113, 0.08);
            margin-bottom: 1.25rem;
        }

        .ventas-sin-egreso .summary-card strong {
            display: block;
            font-size: 0.88rem;
            color: #5f6f8a;
        }

        .ventas-sin-egreso .summary-card .value {
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #0e5487;
        }

        .ventas-sin-egreso .table thead th {
            background: #0e5487;
            color: #fff;
            border-color: rgba(255, 255, 255, 0.08);
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: .78rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.12);
        }

        .ventas-sin-egreso .table tbody tr {
            border-bottom: 1px solid #e8ecf5;
        }

        .ventas-sin-egreso .table tbody tr:hover {
            background: #e5ebf2;
        }

        .ventas-sin-egreso .table td {
            color: #2c3e58;
            vertical-align: middle;
        }

        .ventas-sin-egreso .table .badge {
            font-size: .75rem;
            padding: .45em .7em;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .ventas-sin-egreso .table td .btn {
            min-width: 36px;
        }

        .ventas-sin-egreso .sticky-footer {
            background: #ffffff;
            border-top: 1px solid #e5ebf2;
            padding: 0.85rem 1rem;
            border-radius: 0 0 0.85rem 0.85rem;
        }
    </style>

    <div class="row ventas-sin-egreso">
        <div class="col-md-12">
            <div class="page-header d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center">
                <div>
                    <h4>Ventas sin Egreso Asignado</h4>
                    <small>Total: {{ $ventas_sin_egreso->total() }} registros</small>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <button type="button" class="btn btn-light font-weight-bold shadow-sm" onclick="asignarEgresosMasivo()">
                        <i class="fa fa-cog mr-1"></i> Asignar Egresos Masivamente
                    </button>
                </div>
            </div>

            <form id="filtrosVentas" class="filter-panel row" method="GET" action="{{ url()->current() }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde', '') }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta', '') }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cliente">Cliente</label>
                        <input type="text" id="cliente" name="cliente" class="form-control" placeholder="Buscar por nombre" value="{{ request('cliente', '') }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block mt-4">Filtrar</button>
                </div>
            </form>

            <div class="summary-card d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <strong>Total Ventas sin egreso</strong>
                    <div class="value">{{ $ventas_sin_egreso->total() }}</div>
                </div>
                <div>
                    <strong>Fecha de consulta</strong>
                    <div class="value">{{ date('d/m/Y H:i') }}</div>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                <table class="table table-hover table-sm table-bordered mb-0 align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>Nro Comprobante</th>
                            <th style="width: 140px;">Fecha</th>
                            <th>Cliente</th>
                            <th class="text-right" style="width: 130px;">Total</th>
                            <th style="width: 120px;">Estado</th>
                            <th style="width: 170px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas_sin_egreso as $venta)
                        <tr>
                            <td class="text-center">{{ $venta->id }}</td>
                            <td class="text-truncate" style="max-width: 170px;">{{ $venta->nro_comprobante }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $venta->nombre_cliente ?? 'N/A' }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($venta->total_final, 2) }}</td>
                            <td>
                                @if($venta->estado == 1)
                                <span class="badge badge-success">Activo</span>
                                @elseif($venta->estado == 0)
                                <span class="badge badge-secondary">Anulada</span>
                                @else
                                <span class="badge badge-warning">{{ $venta->estado }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.asignar_egreso', $venta->id) }}" class="btn btn-sm btn-outline-warning mr-1" title="Asignar Egreso Manual">
                                    <i class="fa fa-truck"></i>
                                </a>
                                <a href="{{ route('ventas.sin_egreso') }}?id={{ $venta->id }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="fa fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay ventas sin egreso que coincidan con los filtros.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Página {{ $ventas_sin_egreso->currentPage() }} de {{ $ventas_sin_egreso->lastPage() }}</small>
                {{ $ventas_sin_egreso->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal Asignacion Masiva -->
<div class="modal fade" id="modalAsignarMasivo" tabindex="-1" role="dialog" aria-labelledby="modalAsignarMasivoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAsignarMasivoLabel">Asignar Egresos Masivamente</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Esta acción asignará de forma automática el egreso más cercano por fecha a cada venta sin egreso.</p>
                <div class="form-group">
                    <label for="mostrarResumen">Mostrar resumen</label>
                    <select id="mostrarResumen" class="form-control">
                        <option value="1">Sí (recomendado)</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="alert alert-warning small">Esta operación no se puede deshacer. Antes de continuar, asegúrate de que las ventas y egresos están correctamente procesados.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="asignarEgresosMasivo()">Confirmar Asignación</button>
            </div>
        </div>
    </div>
</div>

<script>
    function asignarEgresosMasivo() {
        if (!confirm('¿Desea ejecutar la asignación masiva de egresos ahora?')) {
            return;
        }
        $.ajax({
            url: '{{ route("ventas.asignar_egresos_masivo") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#modalAsignarMasivo').modal('hide');
                $('button').prop('disabled', true);
            },
            success: function(response) {
                $('button').prop('disabled', false);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Asignación completada',
                        text: response.msj,
                        confirmButtonColor: '#3085d6'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.msj, 'error');
                }
            },
            error: function(xhr) {
                $('button').prop('disabled', false);
                Swal.fire('Error', 'Ocurrió un error al procesar el servicio.', 'error');
            }
        });
    }
</script>
@endsection