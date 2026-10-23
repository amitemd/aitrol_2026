<!DOCTYPE html>
<html>

<head>
    <title>DEPRECIACION ACUMULADA DE ACTIVOS FIJOS</title>
    <style>
        #page_pdf {
            width: 100%;
            margin: 15px auto 10px auto;
        }

        #importaciones_head {
            width: 100%;
            margin-bottom: 10px;
        }


        .info_empresa {
            width: 50%;
            text-align: left;
        }



        table {
            border-collapse: collapse;
            font-size: 12pt;
            font-family: 'sans-serif';
            width: 100%;
        }


        table tr:nth-child(odd) {
            background: #FFF;
        }

        table td {
            text-align: center;
            color: #000000;
            padding: 10px;
        }

        table th {
            text-align: center;
            color: #000000;
            font-size: 14px;
        }

        * {
            font-family: 'sans-serif' !important;
        }

        .table_1 {

            margin-right: 0px;
            margin-top: 0px;
            margin-left: 0px;
            margin-bottom: 0px;
            font-size: 12px;
            width: 100%;

        }

        .table_2 {

            margin-right: 0px;
            margin-top: 0px;
            margin-left: 0px;
            margin-bottom: 0px;
            font-size: 20px;
            width: 100%;
        }

        .table_3 {

            margin: 15px auto;
            margin-top: 20px;
            font-size: 12px;
            width: 20%;
        }


        .titulo_css {

            text-align: center;
            color: black;
        }

        .page_break {
            page-break-before: always;
        }
    </style>

</head>

<body>
    <div class="box-body">

        <div class="col-md-12 ">

            <div style="text-align: center">
                @if(!is_null($empresa->logo))
                <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:150px;height: 80px">
                @endif

            </div>
            <div style="text-align: center; font-size:0.5em">
                {{$empresa->id}}<br />
                {{$empresa->nombrecomercial}}<br />
                @if(!is_null($empresa->direccion)){{$empresa->direccion}} @endif<br />
                <br />
            </div>
            <label class="table_2" for="cliente">DEPRECIACION ACUMULADA DE ACTIVOS FIJOS</label>
        </div>
        <div class="col-md-12">
            &nbsp;
        </div>

        <table border=1 id="example" class="table table-bordered table-responsive" role="grid" aria-describedby="example2_info">

            <tbody id="cuerpo">
                @php
                $tot_activo = 0;
                if(isset($activo->ultima_depreciacion()->saldo)){
                    $saldo = $activo->ultima_depreciacion()->saldo - $activo->depreciacion_acum;
                }else{
                    $saldo = $activo->costo - $activo->depreciacion_acum;

                }
                
                $tot_activo = 0;
                if (!is_null($depreciacion)) {
                    foreach ($depreciacion as $dep) {
                        $tot_activo += $dep->valordepreciacion;
                    }
                    $tot_activo = $tot_activo+$activo->depreciacion_acum;
                }

                @endphp

                <tr class="table_1">
                    <td>ACTIVO FIJO</td>
                    <td colspan="5">{{ $activo->codigo }} {{ $activo->nombre }}</td>
                    <td>TIPO ACTIVO</td>
                    <td>{{ $activo->tipo->nombre }}</td>
                </tr>
                <tr class="table_1">
                    <td>CATEGORIA</td>
                    <td>{{ $activo->sub_tipo->nombre }}</td>
                    <td>FECHA ADQ.</td>
                    <td>{{substr( $activo->fecha_compra,0,11) }}</td>
                    <td>V.ORIGINAL</td>
                    <td> ${{ $activo->costo }}</td>
                    <td>V.SALVAMENTO</td>
                    <td>$0.0000</td>
                </tr>
                <tr class="table_1">
                    <td>DEPRECIACION ACUMULADA</td>
                    <td>${{ $tot_activo }}</td>
                    <td>SALDO ACTUAL</td>
                    <td>{{ round($saldo,2) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>


            </tbody>
        </table>
        <div class="col-md-12">
            &nbsp;
        </div>


        <table border=1 id="example" class="table table-bordered table-responsive" role="grid" aria-describedby="example2_info">

            <thead>
                <tr class="table_1">
                    <th>AÑO FIJO</th>
                    <th>MES</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
            </thead>
            @php
            $tot_depreciado = 0;
            @endphp
            <tbody id="cuerpo">
                @if(isset($depreciacion))
                    @foreach ($depreciacion as $dep)
                    @php
                    $tot_depreciado += $dep->valordepreciacion;
                    @endphp
                    <tr class="table_1">
                        <td>{{substr($dep->fecha, 0, 4)}}</td>
                        <td>{{date("m", strtotime($dep->fecha))}} </td>
                        <td>{{substr($dep->fecha, 0, 10)}}</td>
                        <td>{{$dep->valordepreciacion}}</td>

                    </tr>
                    @endforeach
                @endif
                <tr class="table_1">
                    <td colspan="3">TOTAL DEPRECIADO POR ACTIVO</td>
                    <td>${{$tot_depreciado}} </td>
                </tr>

            </tbody>
        </table>

    </div>
    </div>
</body>

</html>