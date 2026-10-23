

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Nota de d&eacute;bito</title>
  <style>
        .h2 {
            font-family: 'BrixSansBlack';
            font-size: 40px;
            display: block;
            background: #888888;
            color: #FFF;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
            padding: 7px;
            font-size: 1em;
            margin-bottom: 15px;
        }

        #page_pdf {
            width: 100%;
            /*margin: 15px auto 10px auto;*/
            margin: 0 0;
            float: left;

        }

        .info_empresa {
            width: 50%;
            text-align: center;
        }

        .info_factura {
            width: 31%;
        }

        .round {
            border-bottom: dashed;
            overflow: hidden;
            padding-bottom: 15px;
        }

        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            /*margin-bottom: 10px;*/

        }

        #factura_head {
            margin-top: -50px;
        }

        .h3 {
            font-family: 'BrixSansBlack';
            font-size: 10pt;
            display: block;
            color: black;
            text-align: center;
        }

        .titulo {
            background: #eee;
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .t9 {
            font-size: 0.9rem
        }

        .p5 {
            padding: 0.5rem;
        }

        .celda {
            float: left;
            border: 1px solid #ccc;
            margin: -1px;
        }

        .th {
            font-size: 10pt;
            text-align: left;
        }

        * {
            font-size: 18px;
        }

        table {
            border-collapse: collapse;
            font-size: 14pt;
            font-family: 'arial';
            width: 100%;
        }
        .td{
            font-size: 10pt;
            text-align: center;
        }
    </style>
    <script type="text/javascript">
        var numeroALetras = (function() {

    // Código basado en https://gist.github.com/alfchee/e563340276f89b22042a
        function Unidades(num){

            switch(num)
            {
                case 1: return 'UN';
                case 2: return 'DOS';
                case 3: return 'TRES';
                case 4: return 'CUATRO';
                case 5: return 'CINCO';
                case 6: return 'SEIS';
                case 7: return 'SIETE';
                case 8: return 'OCHO';
                case 9: return 'NUEVE';
            }

            return '';
        }//Unidades()

        function Decenas(num){

            let decena = Math.floor(num/10);
            let unidad = num - (decena * 10);

            switch(decena)
            {
                case 1:
                    switch(unidad)
                    {
                        case 0: return 'DIEZ';
                        case 1: return 'ONCE';
                        case 2: return 'DOCE';
                        case 3: return 'TRECE';
                        case 4: return 'CATORCE';
                        case 5: return 'QUINCE';
                        default: return 'DIECI' + Unidades(unidad);
                    }
                case 2:
                    switch(unidad)
                    {
                        case 0: return 'VEINTE';
                        default: return 'VEINTI' + Unidades(unidad);
                    }
                case 3: return DecenasY('TREINTA', unidad);
                case 4: return DecenasY('CUARENTA', unidad);
                case 5: return DecenasY('CINCUENTA', unidad);
                case 6: return DecenasY('SESENTA', unidad);
                case 7: return DecenasY('SETENTA', unidad);
                case 8: return DecenasY('OCHENTA', unidad);
                case 9: return DecenasY('NOVENTA', unidad);
                case 0: return Unidades(unidad);
            }
        }//Unidades()

        function DecenasY(strSin, numUnidades) {
            if (numUnidades > 0)
                return strSin + ' Y ' + Unidades(numUnidades)

            return strSin;
        }//DecenasY()

        function Centenas(num) {
            let centenas = Math.floor(num / 100);
            let decenas = num - (centenas * 100);

            switch(centenas)
            {
                case 1:
                    if (decenas > 0)
                        return 'CIENTO ' + Decenas(decenas);
                    return 'CIEN';
                case 2: return 'DOSCIENTOS ' + Decenas(decenas);
                case 3: return 'TRESCIENTOS ' + Decenas(decenas);
                case 4: return 'CUATROCIENTOS ' + Decenas(decenas);
                case 5: return 'QUINIENTOS ' + Decenas(decenas);
                case 6: return 'SEISCIENTOS ' + Decenas(decenas);
                case 7: return 'SETECIENTOS ' + Decenas(decenas);
                case 8: return 'OCHOCIENTOS ' + Decenas(decenas);
                case 9: return 'NOVECIENTOS ' + Decenas(decenas);
            }

            return Decenas(decenas);
        }//Centenas()

        function Seccion(num, divisor, strSingular, strPlural) {
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)

            let letras = '';

            if (cientos > 0)
                if (cientos > 1)
                    letras = Centenas(cientos) + ' ' + strPlural;
                else
                    letras = strSingular;

            if (resto > 0)
                letras += '';

            return letras;
        }//Seccion()

        function Miles(num) {
            let divisor = 1000;
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)

            let strMiles = Seccion(num, divisor, 'UN MIL', 'MIL');
            let strCentenas = Centenas(resto);

            if(strMiles == '')
                return strCentenas;

            return strMiles + ' ' + strCentenas;
        }//Miles()

        function Millones(num) {
            let divisor = 1000000;
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)

            let strMillones = Seccion(num, divisor, 'UN MILLON DE', 'MILLONES DE');
            let strMiles = Miles(resto);

            if(strMillones == '')
                return strMiles;

            return strMillones + ' ' + strMiles;
        }//Millones()

        return function NumeroALetras(num, currency) {
            currency = currency || {};
            let data = {
                numero: num,
                enteros: Math.floor(num),
                centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
                letrasCentavos: '',
                letrasMonedaPlural: currency.plural || 'PESOS CHILENOS',//'PESOS', 'Dólares', 'Bolívares', 'etcs'
                letrasMonedaSingular: currency.singular || 'PESO CHILENO', //'PESO', 'Dólar', 'Bolivar', 'etc'
                letrasMonedaCentavoPlural: currency.centPlural || 'CHIQUI PESOS CHILENOS',
                letrasMonedaCentavoSingular: currency.centSingular || 'CHIQUI PESO CHILENO'
            };

            if (data.centavos > 0) {
                data.letrasCentavos = 'CON ' + (function () {
                        if (data.centavos == 1)
                            return Millones(data.centavos) + ' ' + data.letrasMonedaCentavoSingular;
                        else
                            return Millones(data.centavos) + ' ' + data.letrasMonedaCentavoPlural;
                    })();
            };

            if(data.enteros == 0)
                return 'CERO ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos;
            if (data.enteros == 1)
                return Millones(data.enteros) + ' ' + data.letrasMonedaSingular + ' ' + data.letrasCentavos;
            else
                return Millones(data.enteros) + ' ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos;
        };

        })();
        var resultado = numeroALetras({{$registro->valor}}, {
        plural: 'DOLARES (US)',
        singular: 'DOLAR (US)',
        centPlural: 'CENTAVOS',
        centSingular: 'CENTAVO'
        });
        function cargar(e){
            var resultado = numeroALetras(e, {
        plural: 'DOLARES (US)',
        singular: 'DOLAR (US)',
        centPlural: 'CENTAVOS',
        centSingular: 'CENTAVO'
        });
        document.getElementById("resultado").innerHTML=resultado;
        }
        </script>
</head>
@php
  $subtotal   = 0;
  $iva    = 0;
  $impuesto   = 0;
  $tl_sniva   = 0;
  $total    = 0;
@endphp

<body >

  <div id="page_pdf">
    <div style="text-align: left;margin-left:80px;">
    </div>
    <table class="table"  width="100%;" onload="cargar({{$registro->valor}})">
        <thead>
            <tr>
                <td >
                    @if(!is_null($registro->empresas->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$registro->empresas->logo}}" style="width:265px;height: 120px;">
                    @endif
                </td>
                <td style="width:50%">
                    <span class="h3" style="padding:2px;font-size:20px !important;"><strong> {{$registro->empresas->razonsocial}}</strong></span>
                    <span class="h3" style="padding:2px;font-size:20px !important;"><strong> {{$registro->empresas->direccion}}</strong></span>
                </td>
                <td style="width:50%;">
                    <span class="h2" style="padding:20px">NOTA DE D&Eacute;BITO No.: {{$registro->id}}</span>
                </td>
            </tr>
        </thead>
    </table>
    <table  class="table" width="100%;" style="border:1px solid black;border-bottom:none !important;">  
         <thead style="text-align:center;"> 
            <tr>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                    Concepto<br>
                    {{$registro->concepto}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                    Tipo <br>
                    {{$registro->tipo}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                    Fecha <br>
                    {{date('d/m/Y', strtotime($registro->fecha))}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                    Asiento <br>
                    {{$registro->id_asiento}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                    Estado <br>
                    @if($registro->estado==1)Activa @else Anulada @endif</br>
                </td>
            </tr>      
        </thead>
    </table>
    <table class="table" width="100%;" style="border:1px solid black;border-bottom:none !important;">
        <thead style="text-align:center;">
        <tr>      
            <td class="td" scope="col" style="border:0.5px solid black !important;">
                     Beneficiario <br>
                     {{$registro->beneficiario}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                     Caja/Banco <br>
                     {{$registro->banco->grupo}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                     Cuenta <br>
                     {{$registro->banco->nombre}}</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                     Divisa <br>
                     DOLARES</br>
                </td>
                <td class="td" scope="col" style="border:0.5px solid black !important;">
                     Valor <br>
                     {{$registro->valor}}</br>
                </td>          
            </tr>
        </thead>
    </table>
    
    <table class="table" width="100%;"  style="border:1px solid back;border-bottom:none !important;">
        <thead  style="text-align:center;">
            <tr>
                <td class="td"  style="text-align:left; border:0.5px solid black !important;">
                    <span class="td" style=" text-align: left; font-weight: bold;"> Valor </span> <br>
                        @include ('contable.nota_debito.conversor')
                        @php
                            $cent = $registro->valor - (int)($registro->valor);
                            $val = $registro->valor - $cent;
                            $cent = number_format($cent, 2);
                            echo convertir($val, $cent);
                        @endphp
                </td>
            </tr>
        </thead>
    </table> 
    <table width="100%;" style="border:1px solid back">
        <tr style="background: #888888;color:white;width:100%">
            <th class="th" style="border:1px solid black;margin:0px;">
                Codigo
            </th>
            <th  class="th" style="border:1px solid black">
                Cuenta
            </th>
            <th class="th" style="border:1px solid black">
                Div
            </th>
            <th class="th" style="border:1px solid black">
                Valor
            </th>
            <th class="th" style="border:1px solid black">
                Debe
            </th>   
            <th class="th" style="border:1px solid black">
                Haber
            </th>
        </tr>
        @foreach($detalle as $value)
        @php
        $ct_asiento_detalle = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera',$registro->id_asiento)->get();
        $total = $total + $value->debe;
        @endphp
            <tr>
                <td class="td" style=" text-align: left;">@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                <td class="td" style=" text-align: left;">@if(!is_null($value->cuenta)){{$value->cuenta}}@endif</td>
          
                <td class="td" style=" text-align: left;">$</td>
                <td class="td" style=" text-align: left;">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
                <td class="td" style=" text-align: left;">@if(!is_null($value->debe)){{number_format($value->debe, 2)}}@endif</td>
                <td class="td" style=" text-align: left;">@if(!is_null($value->haber)){{number_format($value->haber, 2)}}@endif</td>
            </tr>
        @endforeach
            <tr>
                <td class="td" style=" text-align: left;">{{$registro->banco->grupo}}</td>
                <td class="td" style=" text-align: left;">{{$registro->banco->nombre}}</td>
              
                <td class="td" style=" text-align: left;">$</td>
                <td class="td" style=" text-align: left;">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
                <td class="td" style=" text-align: left;">{{number_format(0, 2)}}</td>
                <td class="td" style=" text-align: left;">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
            </tr>
            <tr  style="font-weight: bold;" >
                <td class="td" style=" text-align: left;"></td>
                <td class="td" style=" text-align: left;">Totales</td>
                <td class="td" style=" text-align: left;">$</td>
                <td class="td" style=" text-align: left;"></td>
                <td class="td" style=" text-align: left;">{{number_format($total, 2)}}</td>
                <td class="td" style=" text-align: left;">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
            <tr>
               
    </table>  
    <table width="100%;" style="text-align:left; border:0.5px solid black !important;">
            <tr style="margin-top:60px !important;">
                <th class="th" style="border-right:1px solid black;height:40px!important;">
                    <b style="display:block; margin-top:100px!important; text-align: center;">Notas</b>
                </th>
                 <th class="th" style="border-right:1px solid black;height:40px!important;">
                    <b style="display:block; margin-top:100px!important; text-align: center;">Elaborado</b>
                </th>
                 <th class="th" style="border-right:1px solid black;height:40px!important;">
                    <b style="display:block; margin-top:100px!important; text-align: center;">Aprobado</b>
                </th>
                 <th class="th" style="border-right:1px solid black;height:40px!important;">
                    <b style="display:block; margin-top:100px!important; text-align: center;">Recibido</b>
                </th>
            </tr>

    </table>

  </div>
</body>

</html>
