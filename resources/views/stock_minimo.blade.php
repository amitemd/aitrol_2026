@extends('contable.importaciones.base')
@section('action-content')
<?php
$rolUsuario = Auth::user()->id_tipo_usuario;
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="box">
                <div class="box-body">
                    <!-- {{ rand(1, 4)}} -->
                    @if(in_array($rolUsuario, array(1)) == true)
                        <div class="form-group col-md-12">
                            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        @if($registros != '[]' )
                                            <div class="table-responsive col-md-12">
                                                <h2><b style="color:#3c8dbc">Insumos Medicos con Stock Minimo</b></h2>
                                                <form method="GET" action="{{ url()->current() }}" style="margin-bottom: 15px;">
                                                    <div style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
                                                        <input 
                                                            type="text" 
                                                            name="search" 
                                                            class="form-control" 
                                                            placeholder="Buscar en toda la tabla..."
                                                            value="{{ request('search') }}"
                                                            style="max-width: 420px;"
                                                        >
                                                        <button type="submit" class="btn btn-primary">Buscar</button>
                                                
                                                        @if(request('search'))
                                                            <a href="{{ url()->current() }}" class="btn btn-default">Limpiar</a>
                                                        @endif
                                                
                                                        <a href="{{ route('menu.exportar_stock_minimo_excel', ['search' => request('search')]) }}" class="btn btn-success">
                                                            <i class="fa fa-file-excel-o"></i> Descargar Excel
                                                        </a>
                                                    </div>
                                                </form>
                                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                                    <thead>
                                                        <tr>
                                                            <th>Codigo</th>
                                                            <th>Nombre</th>
                                                            <th>Descripción</th>
                                                            <th>Stock Minimo</th>
                                                            <th>Cantidad Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(count($registros) > 0)
                                                            @foreach ($registros as $value)
                                                                <tr role="row" class="odd">
                                                                    <td class="sorting_1">{{ $value->codigo }}</td>
                                                                    <td class="sorting_1">{{ $value->nombre }}</td>
                                                                    <td class="sorting_1">{{ $value->descripcion }}</td>
                                                                    <td class="sorting_1" style="text-align: right">{{ (int) $value->stock_minimo }}</td>
                                                                    <td class="sorting_1" 
                                                                        @if($value->cantidad_total <= 0)
                                                                            style="background:red; color:#fff; font-weight:bold; text-align: right"
                                                                        @elseif($value->cantidad_total < $value->stock_minimo)
                                                                            style="background:orange; font-weight:bold; text-align: right"
                                                                        @else
                                                                            style="text-align: right"
                                                                        @endif
                                                                    >
                                                                        {{ (int) $value->cantidad_total }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="text-center">No existen registros con stock mínimo</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                                            Mostrando
                                                            {{ $registros->total() > 0 ? (1 + (($registros->currentPage() - 1) * $registros->perPage())) : 0 }}
                                                            /
                                                            {{ (($registros->currentPage() - 1) * $registros->perPage()) + count($registros) }}
                                                            de {{ $registros->total() }} registros
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7 text-right">
                                                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                                            {{ $registros->appends(request()->query())->links() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection