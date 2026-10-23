@extends('insumos.asiento_costo_venta.base')
@section('action-content')
<form method="GET" action="{{ url('contable/asientoventa/insumos') }}">

    <div class="bg-white p-4 mb-4 rounded-lg">
        <div class="grid sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">

            <div>
                <label for="id_venta" class="block mb-2 text-sm font-medium text-gray-900 ">ID Venta</label>
                <input type="text" id="id_venta" name="id_venta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="ingresar id" @if (isset($parametro['id_venta'])) value="{{ $parametro['id_venta'] }}" @endif>
            </div>
            <div>
                <label for="asiento_venta" class="block mb-2 text-sm font-medium text-gray-900 ">Asiento venta</label>
                <input type="text" id="asiento_venta" name="asiento_venta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="ingresar asiento de venta" @if (isset($parametro['asiento_venta'])) value="{{ $parametro['asiento_venta'] }}" @endif>
            </div>
            <div>
                <label for="asiento_costo" class="block mb-2 text-sm font-medium text-gray-900 ">Asiento costo</label>
                <input type="text" id="asiento_costo" name="asiento_costo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="ingresar asiento de costo" @if (isset($parametro['asiento_costo'])) value="{{ $parametro['asiento_costo'] }}" @endif>
            </div>
            <div>
                <label for="nro_factura" class="block mb-2 text-sm font-medium text-gray-900 ">Nro. factura</label>
                <input type="text" id="nro_factura" name="nro_factura" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="ingresar nro. de factura" @if (isset($parametro['nro_factura'])) value="{{ $parametro['nro_factura'] }}" @endif>
            </div>
            <div>
                <label for="nro_factura" class="block mb-2 text-sm font-medium text-gray-900 ">Asiento costo anulado</label>
                <input type="text" id="asiento_costo_anulado" name="asiento_costo_anulado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="ingresar asiento costo anulado" @if (isset($parametro['asiento_costo_anulado'])) value="{{ $parametro['asiento_costo_anulado'] }}" @endif>
            </div>
            <div>
                <label for="estado" class="block mb-2 text-sm font-medium text-gray-900 ">Estado factura</label>
                <select id="estado_factura" name="estado_factura" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5 ">
                    <option value="">seleccione</option>
                    <option @if (isset($parametro['estado_factura']) and $parametro['estado_factura']==1) selected @endif value="1">Activo</option>
                    <option @if (isset($parametro['estado_factura']) and $parametro['estado_factura']==0) selected @endif value="0">Inactivo</option>
                </select>
            </div>

            <div>
                <label for="nc" class="block mb-2 text-sm font-medium text-gray-900 ">NC</label>
                <select id="nc" name="nc" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5 ">
                    <option value="">seleccione</option>
                    <option @if (isset($parametro['nc']) and $parametro['nc']==1) selected @endif value="1">SI</option>
                    <option @if (isset($parametro['nc']) and $parametro['nc']==0) selected @endif value="0">NO</option>
                </select>
            </div>

            <div>
                <label for="fechad" class="block mb-2 text-sm font-medium text-gray-900 ">Fecha desde</label>
                <input type="date" id="fecha_desde" name="fecha_desde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="Seleccione fecha" @if (isset($parametro['fecha_desde'])) value="{{ $parametro['fecha_desde'] }}" @endif>
            </div>

            <div>
                <label for="fechah" class="block mb-2 text-sm font-medium text-gray-900 ">Fecha hasta</label>
                <input type="date" id="fecha_hasta" name="fecha_hasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#3B82F6] focus:border-[#3B82F6] block w-full p-2.5" placeholder="Seleccione fecha" @if (isset($parametro['fecha_hasta'])) value="{{ $parametro['fecha_hasta'] }}" @endif>
            </div>
        </div>
        <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none ">Buscar</button>
    </div>
</form>


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Id
                </th>
                <th scope="col" class="px-6 py-3">
                    Fecha
                </th>
                <th scope="col" class="px-6 py-3">
                    Nro. factura
                </th>
                <th scope="col" class="px-6 py-3">
                    Observación
                </th>
                <th scope="col" class="px-6 py-3">
                    Estado factura
                </th>
                <th scope="col" class="px-6 py-3">
                    N/C
                </th>
                <th scope="col" class="px-6 py-3">
                    Asiento venta
                </th>
                <th scope="col" class="px-6 py-3">
                    Asiento costo
                </th>
                <th scope="col" class="px-6 py-3">
                    Asiento costo anulado
                </th>

            </tr>
        </thead>
        <tbody>

            @if (count($consulta) > 0)
            @foreach ($consulta as $value)
            <tr class="bg-white border-b  hover:bg-gray-50 ">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $value->venta->id }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $value->venta->fecha }}
                </th>
                <td class="px-6 py-4">
                    {{ $value->venta->nro_comprobante }}
                </td>
                <td class="px-6 py-4">
                    {{ $value->venta->concepto }}
                </td>
                <td class="px-6 py-4">
                    <span class="@if ($value->venta->estado == 0) bg-red-500 text-red-800 @else bg-[#78DA9C] text-green-800 @endif text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">
                        @if ($value->venta->estado == 0)
                        inactivo
                        @else
                        activo
                        @endif
                    </span>
                </td>
                <td class="px-6 py-4">

                    @if(isset($value->venta->rnota_credito))
                    <span class="bg-[#3B82F6] text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">
                        Si
                        <div class="hidden">{{ $value->venta->rnota_credito->id }}</div>
                    </span>
                    @else
                    <span class="bg-red-500 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">
                        No
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a target="_blank" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $value->venta->id_asiento }}">
                        <span class="bg-[#3B82F6] text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">{{ $value->venta->id_asiento }}</span>

                    </a>
                </td>
                <td class="px-6 py-4">
                    @if ($value->inv_cab_movimiento->id_asiento != null)
                    <a target="_blank" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $value->inv_cab_movimiento->id_asiento }}">
                        <span class="bg-[#78DA9C] text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">{{ $value->inv_cab_movimiento->id_asiento }}</span>
                    </a>
                    @php
                    $asiento = Sis_medico\LogAsiento::buscarAsientoAnulado($value->inv_cab_movimiento->id_asiento);
                    @endphp
                    @else
                    @if (isset($value->asiento_costo))
                    <a target="_blank" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $value->asiento_costo->id_asiento }}">
                        <span class="bg-[#78DA9C] text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">{{ $value->asiento_costo->id_asiento }}</span>
                    </a>
                    @php
                    $asiento = Sis_medico\LogAsiento::buscarAsientoAnulado($value->asiento_costo->id_asiento);
                    @endphp
                    @endif
                    @endif
                </td>

                <td class="px-6 py-4">
                    @if (!is_null($asiento))
                    <a target="_blank" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $asiento->id_referencia }}">
                        <span class="bg-red-500 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">{{ $asiento->id_referencia }}</span>
                        @else
                        <span class="bg-red-500 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded text-white">no
                            esta anulado</span>
                        @endif
                    </a>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@if (count($consulta) > 0)
<div class="col-sm-12">
    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
        {{ $consulta->appends(Request::only(['id_venta', 'asiento_costo', 'asiento_venta', 'asiento_costo_anulado', 'nro_factura', 'estado_factura','fecha_desde','fecha_hasta', 'nc']))->links() }}
    </div>
</div>
@endif
</div>
@endsection