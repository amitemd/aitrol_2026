@extends('contable.balance_general.base')

@section('action-content')
<div class="container-fluid">

    <div class="box box-primary">
        <div class="box-header with-border" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 class="box-title">
                <i class="fa fa-shopping-cart"></i>
                Órdenes de Compra
            </h3>
            <a href="{{ route('ordenes_compra.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
        

        <div class="box-body">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fa fa-times"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead style="background:#f4f6f9;">
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th style="width:140px;">Número</th>
                            <th style="width:120px;">Fecha</th>
                            <th>Proveedor</th>
                            <th style="width:140px;" class="text-center">Cantidades</th>
                            <th style="width:140px;" class="text-right">Subtotal</th>
                            <th style="width:240px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ordenes as $orden)
                            <tr>
                                <td>{{ $orden->id }}</td>

                                <td>
                                    <strong>{{ $orden->numero }}</strong>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $orden->proveedores->nombrecomercial ?? '—' }}
                                </td>

                                <td class="text-center">
                                    {{ number_format($orden->total_cantidades, 2) }}
                                </td>

                                <td class="text-right">
                                    $ {{ number_format($orden->subtotal, 2) }}
                                </td>
                                
                                <td class="text-center">

                                    <a href="{{ route('ordenes_compra.edit', $orden->id) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-pencil"></i> Editar
                                    </a>

                                    <a href="{{ route('ordenes_compra.pdf', $orden->id) }}"
                                       target="_blank"
                                       class="btn btn-danger btn-sm">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding:30px;">
                                    <i class="fa fa-info-circle"></i>
                                    No hay órdenes de compra registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="text-center">
                {{ $ordenes->links() }}
            </div>

        </div>
    </div>

</div>
@endsection