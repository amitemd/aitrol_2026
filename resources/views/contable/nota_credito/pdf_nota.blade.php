

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Nota de Cr&eacute;dito</title>
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
      width: 31%;
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
            <span  class="h3" style="padding:20px"><strong> {{$registro->empresas->razonsocial}}</strong></span>
            <table width="100%">
                <tr>
                    <td width="70%">
                        {{$registro->empresas->direccion}}
                    </td>
                    <td width="20%" style="text-align:right;padding-right:10px">
                        Tlf: {{$registro->empresas->telefono1}}<br/>
                    </td>
                </tr>
            </table>
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">NOTA DE CR&Eacute;DITO</span>
            <table width="100%">
                <tr>
                    <td width="10%">
                        No.: 
                    </td>
                    <td width="90%" style="text-align:right;padding-right:10px">
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
                <div class="center t9 p5">BAN-NC</div>
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
        $total = $total + $value->haber;
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
            <td class="t9 center valor p5" >@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
            <td class="t9 center p5">{{number_format(0, 2)}}</td>
        </tr>
        <tr style="font-weight:bold">
            <td class="t9 p5"></td>
            <td class="t9 p5" colspan="6">Totales</td>
            <td class="t9 center p5">$</td>
            <td class="t9 center valor p5" >@if(!is_null($registro->valor)){{number_format($registro->valor, 2)}}@endif</td>
            <td class="t9 center p5">{{number_format($total, 2)}}</td>
        </tr>
    </table>

    
    

  </div>
</body>

</html>
