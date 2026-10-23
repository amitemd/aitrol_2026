<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8" />
    <title> Traslados de Insumos  </title>
    <style>
        h3 {
            color: black;
            text-align: center;
        }

        table {
            width: 100%;
            border: 1px solid;
            size: 8px;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }

        thead {}

        caption {
            padding: 0.3em;
            color: #fff;
            background: #000;
        }

        div {
            text-align: left;
        }

        p.thick {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <table id="factura_head" class="bordes_padding">
        <tr>
            <td class="info_empresa">
                <div style="text-align: center">
                    @if(!is_null($empresa->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:300px;height: 100px">
                    @endif

                </div>
                <div style="text-align: center; font-size:0.8em">
                    {{$empresa->id}}<br />
                    {{$empresa->nombrecomercial}}<br />
                    @if(!is_null($empresa->direccion)){{$empresa->direccion}} @endif<br />
                    <br />
                </div>
            </td>
        </tr>
    </table>
    <table id="factura_head" class="bordes_padding">
        <tr>
            <td>
                <label for="numeroDocumento"><b>Numero de documento: </b>{{$cab_movimiento->numero_documento}}</label>
            </td>
            <td>
                <label for="fecha"><b>Fecha: </b>{{$cab_movimiento->fecha}}</label>
            </td>
        </tr>
        <tr>
            
            <td>
                <label for="bodega_envia"><b>Bodega Origen: </b>{{$bodegaOrigen->nombre}}</label>
            </td>
            <td>
                <label for="bodega_recibe"><b>Bodega Destino: </b>{{$bodegaDestino->nombre}}</label>
            </td>
            
        </tr>
        
        <tr>
            <td>
                <label for="observaciones" class="col-md-2 control-label"><b>Observaciones:</b>{{$cab_movimiento->observacion}}</label>
            </td>
        </tr>
    </table>

    <div class="col-md-12">
        &nbsp;
    </div>
    <div class="box-body">
        <div class="panel panel-default">
            <div class="box-body">
                <div class="table-responsive col-md-12">
                    
                    <table>
                        <thead style="text-align:center;font-size:small">
                            <tr role="row">
                                <th>Serie</th>
                                <th>Nombre</th>
                                <th>Lote del producto</th>
                                <th>Cantidad</th>
                                <th>Número de usos</th>

                         
                                @if($x == null)
                                 <th>Total </th>
                                @endif
                                
                                
                            </tr>
                        </thead>
                        <tbody id="crear" style="text-align:center; font-size:x-small">
                        @foreach ($detalle as $value)
                        <tr>
                            <td>
                                {{$value->serie}}
                            </td>
                            <td>
                                {{$value->nombre}}
                            </td>
                            <td>
                                {{$value->lote}}
                            </td>
                            <td>
                                {{$value->cantidad}}
                            </td>
                            <td>
                                {{$value->cant_uso}}
                            </td>
                            
                            @if($x == null)
                                <td>
                                    {{$value->total}}
                                </td>
                            @endif

                            
                        </tr>

                        @endforeach
                        </tbody>
                       
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>