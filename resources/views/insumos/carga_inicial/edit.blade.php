@extends('insumos.carga_inicial.base')
@section('action-content')
<style>
    .border {
        border: 3px solid red;
        border-radius: 5px;
    }

    .warning {
        text-align: center;
        text-transform: uppercase;
        text-decoration: underline;
        color: red;
    }

    .border-sucess {
        border: 3px solid green;
        border-radius: 5px;
    }

    .success {
        text-align: center;
        text-transform: uppercase;
        text-decoration: underline;
        color: green;
    }
</style>
<div style="display:flex; padding: 15px;">
    <h3>Visualizar Pedido</h3>
</div>

<div class="col-md-12" style="text-align: end;">
    <!-- <button class="btn btn-success">Procesar <i class="fa fa-check-circle-o" aria-hidden="true"></i></button> -->
</div>
@if(count($detalle) > 0)
<div style="padding: 20px;margin-top: 21px;">
    <div class="border-sucess" id="mov_suave">
        <div class="success">
            <h4>Insumos</h4>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Descripción</th>
                    <th>Codigo</th>
                    <th>Cantidad</th>
                    <th>Serie</th>
                    <th>Lote</th>
                    <th>Precio</th>
                    <th>Bodega</th>
                    <th>Fecha Expiración</th>
                    <th>Observación</th>

                </tr>
            </thead>
            <tbody>

                @foreach($detalle as $pro)

                <tr>
                    <td>{{$pro->marca}}</td>
                    <td>{{$pro->descripcion}}</td>
                    <td>{{$pro->codigo}}</td>
                    <td>{{$pro->cantidad}}</td>
                    <td>{{$pro->serie}}</td>
                    <td>{{$pro->lote}}</td>
                    <td>{{$pro->precio}}</td>
                    <td>{{ isset($pro->bodega) ? $pro->bodega->nombre : '' }}</td>
                    <td>{{$pro->fecha_exp}}</td>
                    <td>@php echo $pro->observacion; @endphp</td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>


<div style="display: flex; justify-content: center;">
    <div style="width: 20%;">
        <table class="table table-bordered" style="text-align: center;"> 
            <thead>
                <tr>
                    <th style="text-align: center">Marca</th>
                    <th style="text-align: center">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($marca as $mar)
                <tr>
                    <td>{{$mar->marca}}</td>
                    <td>{{$mar->contador}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endif

@if(count($problema) > 0)
<div style="padding: 20px;margin-top: 21px;">
    <div class="border">
        <div class="warning">
            <h4>Problemas Insumos</h4>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Descripción</th>
                    <th>Codigo</th>
                    <th>Cantidad</th>
                    <th>Serie</th>
                    <th>Lote</th>
                    <th>Bodega</th>
                    <th>Fecha Expiración</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($problema as $pro)
                <tr>
                    <td>{{$pro->marca}}</td>
                    <td>{{$pro->descripcion}}</td>
                    <td>{{$pro->codigo}}</td>
                    <td>{{$pro->cantidad}}</td>
                    <td>{{$pro->serie}}</td>
                    <td>{{$pro->lote}}</td>
                    <td>{{ isset($pro->bodega) ? $pro->bodega->nombre : '' }}</td>
                    <td>{{$pro->fecha_exp}}</td>
                    <td>@php echo $pro->observacion; @endphp</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>


@endif



@endsection