<!DOCTYPE html>
<html>

<head>
    <title>Gastos Depreciacion</title></title>
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
            
            font-size: 10px;
        }

        table th {
            text-align: center;
            color: #000000;
            font-size: 10px;
        }

        * {
            font-family: 'sans-serif' !important;
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
            <br/>
            </div>
                <label class="table_2" for="cliente">GASTOS DEPRECIACIÓN MENSUAL</label>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>

            <table border=1 id="example" class="table table-bordered table-responsive" role="grid" aria-describedby="example2_info">
                 <thead>

                    <tr class="table_1">
                        <th>FECHA INS REG PROP</th>
                        <th>CODIGO</th>
                        <th>NOMBRE</th>
                        <th>DESCRIPCION</th>
                        <th>MARCA</th>
                        <th>MODELO</th>
                        <th>SERIE</th>
                        <th>COLOR</th>
                        <th>COSTO</th>
                        <th>20%</th>
                        <th>DEP DIARIO</th>
                        <th>DIAS DE DEPRECIACIÓN</th>
                        <th>GTO. DEP. MENSUAL</th>
                        <th>DEPRECIACIÓN</th>
                        <th>SALDO ACTUAL</th>
                    </tr>
                </thead>
                    @php
                    $cont1 = 0;
                    $cont2 = 0;
                    $cont3 = 0;
                    $cont4 = 0;
                    $cont6=  0;
                    $cont4_nuevo = 0;

               
                @endphp
                <tbody id="cuerpo">
                @foreach ($activos as $activo) 
                @php
                $fhasta = new DateTime($activo->hasta);    
                $depreciacion =Sis_medico\AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $activo->id)->get();
                    $tot_activo = 0;
                    if (!is_null($depreciacion)) {
                        foreach ($depreciacion as $dep) {
                            $tot_activo += $dep->valordepreciacion;
                        }
                        $tot_activo = $tot_activo+$activo->depreciacion_acum;
                    }

                    $new_saldo = $activo->costo - $tot_activo;

                    
                    /*if(isset($activo->ultima_depreciacion()->saldo)){
                        $saldo = $activo->ultima_depreciacion()->saldo - $activo->depreciacion_acum;
                    }else{
                   
                        $saldo = $activo->costo - $activo->depreciacion_acum;
                    }*/

                    if ($new_saldo != 0) {
                        $saldo = $new_saldo - $activo->depreciacion_acum;
                    } else {
                        $saldo = $activo->costo - $activo->depreciacion_acum;
                    }


                 
                    $cont1 += round($activo->costo, 2);
          
                    $cont2 += round(($activo->costo * 0.2), 2);
                  
                    $cont3 += round((($activo->costo * 0.2) / 360),2);
                    $cont6 += $tot_activo;
            
                    $fecha_compra = new DateTime($activo->fecha_compra);

                    $diff = $fecha_compra->diff($fhasta);

                    $days= $diff->days;

                    if($days >= 30){
                        $days = 30;
                    }
                    //$cont4 += round(((($activo->costo *($activo->tasa/100)) / 360) * $days),2);
                    if(!is_null($activo->tasa) && $activo->tasa > 0){
                    $cont4 += round(((($activo->costo *($activo->tasa/100)) / 360) * $days),2);
                        $cont4_nuevo = round(((($activo->costo *($activo->tasa/100)) / 360) * $days),2);
                    }else{
                        $cont4_nuevo = 0;
                    }
                     
                @endphp
                    <tr class="table_1">
                        <td>{{substr($activo->fecha_compra, 0, 10)}}</td>
                        <td>{{$activo->codigo}}</td>
                        <td>{{$activo->nombre}}</td>
                        <td>{{$activo->descripcion}} </td>
                        <td>{{$activo->marca}}</td>
                        <td>{{$activo->modelo}}</td>
                        <td>{{$activo->serie}}</td>
                        <td>{{$activo->color}} </td>
                        <td>${{number_format($activo->costo, 2, '.', ',')}}</td>
                        <td>{{round(($activo->costo * 0.2), 2)}}%</td>
                        <td>{{round((($activo->costo * 0.2) / 360),2)}}</td>
                        <td>{{$days}} </td>
                        <td>{{$cont4_nuevo}}</td>
                        <td>${{number_format($tot_activo, 2, '.', ',')}}</td>
                        <td>{{number_format($saldo,2,'.',',')}}</td>
                    </tr>
                @endforeach 
                <tr class="table_1">
                        <td colspan="8">TOTAL</td>
                        <td>${{$cont1}} </td>
                        <td>{{$cont2}}</td>
                        <td>{{$cont3}}</td>
                        <td> </td>
                        <td>{{$cont4}}</td>
                        <td>${{$cont6}}</td>
                      <td style=" border: inset 0pt"></td>
                    </tr>  
                </tbody>
            </table>
            <div class="col-md-12">
                &nbsp;
            </div>

        </div>
    </div>
</body>

</html>