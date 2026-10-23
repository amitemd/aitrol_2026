

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Nota de d&eacute;bito</title>
  <style>


    #page_pdf{
      width: 100%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;
      
    }
    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 60%;
    }
    .info_factura2{
      width: 40%;
    }

    .round{
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      /*margin-bottom: 10px;*/
      
    }
    #factura_head{
      margin-top: -50px; 
    }

    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #aaa;
      color: #FFF;
      text-align: left;
      padding: 3px;
      margin-bottom: 5px;
    }
    .titulo{
        background: #eee;
        padding: 5px;
    }
    .center{
        text-align:center;
    }
    .t9{
        font-size:0.9rem
    }
    .p5{
        padding:0.5rem;
    }
  .celda{
      float:left;
      border: 1px solid #ccc;
      margin:-1px;
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
    <table id="factura_head" onload="cargar({{$registro->valor}})">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        
        <td class="info_factura">
          <div class="round">
            <span  class="h3" style="padding:22px"><strong> {{$registro->empresas->razonsocial}}</strong></span>
            <table width="100%">
                <tr>
                    <td width="60%">
                        <b style="font-size: 20px;">{{$registro->empresas->direccion}}</b>
                    </td>
                    <td width="40%" style="text-align:right;font-size: 20px;">
                        <b>Tlf: {{$registro->empresas->telefono1}} </b>  <br/>
                    </td>
                </tr>
            </table>
          </div>
        </td>
        <td class="info_factura2">
          <div class="round">
            <span class="h3" style="padding:22px">NOTA DE D&Eacute;BITO</span>
            <table width="100%">
                <tr>
                    <td width="60%">
                    <b style="font-size: 20px;"> No.:</b> 
                    </td>
                    <td width="40%" style="text-align:right;font-size: 20px;">
                        <strong> {{$registro->id}}</strong><br/>
                    </td>
                </tr>
            </table>    
          </div>
        </td>
      </tr>
    </table>
    
    <table width='100%' style="margin-top:10px;border:1px solid #777;border-collapse:collapse">   
        <tr>
            <td colspan='6'>
                <div class="titulo">Concepto</div>
                <div class="left t9 bold p5">{{$registro->concepto}}</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Tipo</div>
                <div class="center t9 p5">BAN-ND</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Fecha</div>
                <div class="center t9 p5">{{date('d/m/Y', strtotime($registro->fecha))}}</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Asiento</div>   
                <div class="center t9 p5">{{$registro->id_asiento}}</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Estado</div>
                <div class="center t9 p5">@if($registro->estado==1)Activa @else Anulada @endif</div>
            </td>
        </tr>
        <tr>      
            <td colspan="1">
                <div class="titulo center">Caja/Banco</div>
                <div class="left t9 bold p5">{{$registro->banco->grupo}}</div>
            </td>
            <td colspan="7">
                <div class="titulo">Cuenta</div>
                <div class="t9 p5">{{$registro->banco->nombre}}</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Divisa</div>
                <div class="center t9 p5">DOLARES</div>
            </td>
            <td colspan="1">
                <div class="titulo center">Valor</div>
                <div class="center t9 p5">{{$registro->valor}}</div>
            </td>            
        </tr>
        <tr>
            <td colspan="10">
                <div class="titulo">Son</div>
                <div class="t9 p5" id="resultado">
                @include ('contable.nota_debito.conversor')
                @php
                    $cent = $registro->valor - (int)($registro->valor);
                    $val = $registro->valor - $cent;
                    $cent = number_format($cent, 2);
                    echo convertir($val, $cent);
                @endphp
                
            </div>
            </td>
        </tr>
        <tr>
            <td colspan="1">
                <div class="titulo">C&oacute;digo</div>
            </td>
            <td colspan="6">
                <div class="titulo">Cuenta</div>
            </td>
            <td colspan="1">
                <div class="titulo">Divisa</div>
            </td>
            
            <td colspan="1">
                <div class="center titulo">DEBE</div>
            </td>
            <td colspan="1">
                <div class="center titulo">HABER</div>
            </td>
        </tr>
        @foreach($detalle as $value)
        @php
        $total = $total + $value->debe;
        @endphp
        <tr class="well" >
            <td class='t9 p5'>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
            <td class="t9 p5" colspan="6">@if(!is_null($value->cuenta)){{$value->cuenta}}@endif</td>
            <td class="t9 center p5">$</td>
            <td class="t9 center valor p5">@if(!is_null($value->debe)){{number_format($value->debe, 2)}}@endif</td>
            <td class='t9 center p5' >@if(!is_null($value->haber)){{number_format($value->haber, 2)}}@endif</td>
        </tr>
        @endforeach
        <tr>
            <td class="t9 p5">{{$registro->banco->grupo}}</td>
            <td class="t9 p5" colspan="6">{{$registro->banco->nombre}}</td>
            <td class="t9 center p5">$</td>
            <td class="t9 center valor p5" >{{number_format(0, 2)}}</td>
            <td class="t9 center p5">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
        </tr>
        <tr style="font-weight:bold">
            <td class="t9 p5"></td>
            <td class="t9 p5" colspan="6">Totales</td>
            <td class="t9 center p5">$</td>
            <td class="t9 center valor p5" >{{number_format($total, 2)}}</td>
            <td class="t9 center p5">@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
        </tr>
    </table>

    
    

  </div>
</body>

</html>
