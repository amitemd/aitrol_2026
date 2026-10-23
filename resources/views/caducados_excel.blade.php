<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<table border="1">
    <thead>
        <tr style="background-color: #3c8dbc; color: #ffffff;">
            <th colspan="11" style="text-align: center; font-size: 16px;">
                Control de Vigencia de Insumos Médicos
            </th>
        </tr>
        @if(!empty($search))
        <tr>
            <th colspan="11" style="text-align: left;">
                Filtro aplicado: {{ $search }}
            </th>
        </tr>
        @endif
        <tr style="background-color: #d9edf7;">
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Serie</th>
            <th>Pedido</th>
            <th>Bodega</th>
            <th>Proveedor</th>
            <th>Marca</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Fecha Vencimiento</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($caducado as $value)
            <tr>
                <td>{{ $value->codigo }}</td>
                <td>{{ $value->nombre }}</td>
                <td>{{ $value->serie }}</td>
                <td>{{ $value->pedido_detalle }}</td>
                <td>{{ $value->bodega ? $value->bodega : '-' }}</td>
                <td>{{ $value->proveedor ? $value->proveedor : '-' }}</td>
                <td>{{ $value->marca }}</td>
                <td>{{ $value->tipo }}</td>
                <td>{{ $value->existencia }}</td>
                <td>{{ $value->fecha_vence }}</td>
                <td>{{ $value->prioridad_uso }}</td>
            </tr>
        @endforeach
    </tbody>
</table>