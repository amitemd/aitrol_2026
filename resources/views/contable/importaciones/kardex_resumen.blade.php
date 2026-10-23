@extends('contable.importaciones.base')
@section('action-content')

<style>
    th {
        font-size: 12px;
        text-align: center;

    }

    td {
        text-align: center;
    }

    .gastos {
        background-color: #aaa;
        font-weight: bold;
        color: white;

    }

    .tot {
        background-color: white;
        color: black;

    }

    .borde {
        border: 1px solid #aaa;

    }

    .nuevo {
        background-color: #aaa;
        font-weight: bold;
        color: white;
    }

    .nuevo1 {
        background-color: #aaa;
        font-weight: bold;
        color: white;
    }


    .bord {
        border: 1px solid #aaa;
    }

    .estilo1 {
        background-color: white;
        color: black;
    }

    .info_empresa {
        width: 50%;
        text-align: left;

    }

    .borde-celda td {
        border: 1px solid #aaa;
    }
</style>

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">RESUMEN KARDEX</h3>
                </div>
            </div>
        </div>
        <div class="box-body">
            <table id="example" class="table" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr>
                        <th>COD. CONTABLE</th>
                        <th>NOMBRE </th>
                        <th>COD. COMPRAS</th>
                        <th>NOMBRE</th>
                        <th>CANTIDAD</th>
                        <th>VAL. UNITARIO</th>
                        <th>TOTAL IMP.</th>
                        <th>BODEGA COMPRAS</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($compras->detalles))
                    @foreach ($compras->detalles as $det)
                    @php
                    $producto = $det->producto;
                    $producto_insumo = Sis_medico\Ct_productos_insumos::where('id_producto',$producto->id)->first();
                    @endphp
                    <tr>
                        <td>{{$producto->codigo}} </td>
                        <td>{{$producto->nombre}}</td>
                        <td>@if(isset($producto_insumo->producto_compra)) {{$producto_insumo->producto_compra->codigo}} @else <label class="label label-danger">Falta ligar producto</label> @endif</td>
                        <td>@if(isset($producto_insumo->producto_compra)) {{$producto_insumo->producto_compra->nombre}} @else <label class="label label-danger">Falta ligar producto</label> @endif</td>
                        <td>{{$det->cantidad}}</td>
                        <td>{{$det->costo_unitario}}</td>
                        <td>{{$det->costo_total}}</td>
                        <td>@if(isset($det->union_bodega))
                            @if(isset($det->union_bodega->bodega_compras)) {{$det->union_bodega->bodega_compras->nombre}} @endif @else <label class="label label-danger">Falta ligar Bodega</label> @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection