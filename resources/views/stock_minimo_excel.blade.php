<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<table border="1">
    <thead>
        <tr style="background-color: #3c8dbc; color: #ffffff;">
            <th colspan="5" style="text-align: center; font-size: 16px;">
                Insumos Medicos con Stock Minimo
            </th>
        </tr>

        @if(!empty($search))
        <tr>
            <th colspan="5" style="text-align: left;">
                Filtro aplicado: {{ $search }}
            </th>
        </tr>
        @endif

        <tr style="background-color: #d9edf7;">
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
                <tr>
                    <td>{{ $value->codigo }}</td>
                    <td>{{ $value->nombre }}</td>
                    <td>{{ $value->descripcion }}</td>
                    <td>{{ (int) $value->stock_minimo }}</td>
                    <td>{{ (int) $value->cantidad_total }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" style="text-align: center;">No existen registros con stock mínimo</td>
            </tr>
        @endif
    </tbody>
</table>