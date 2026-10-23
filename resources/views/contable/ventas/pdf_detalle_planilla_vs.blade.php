<!-- VENTANA VS PARTICULAR -->
<!DOCTYPE html>
<html lang="en">

<head>

  <title>PROCEDIMIENTO VS PARTICULAR</title>
  <style>
    #page_pdf {
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      margin-bottom: 10px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    #detalle_totales span {
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura {
      width: 25%;
    }

    .info_empresa {
      width: 50%;
      text-align: center;
    }

    .info_factura {
      width: 31%;
    }

    .info_cliente {
      width: 69%;
    }

    .textright {
      padding-left: 3;
    }
 
    .h3 {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 5px;
      padding-bottom: 5px;
      margin-bottom: 5px;
      margin-top: 5px;
    }

    .round {
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    table {
      border-collapse: collapse;
      font-size: 12pt;
      font-family: 'arial';
      width: 100%;
    }


    table tr:nth-child(odd) {
      background: #FFF;
    }

    table td {
      padding: 4px; 
    }

    table th {
      text-align: left;
      color: #3d7ba8;
      font-size: 15px;
    }

    .totales {
      color:#0000;
      font-size: 20px;
      font-weight: bold;
    }

    .datos_cliente {
      font-size: 0.8em;
    }

    .datos_cliente label {
      width: 75px;
      display: inline-block;
    }

    .lab {
      font-size: 18px;
      font-family: 'arial';
    }

    * {
      font-family: 'Arial' !important;
    }

    .mLabel {
      width: 20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left: 15px;
      font-size: 0.9em;

    }

    .mValue {
      width: 79%;
      display: inline-block;
      vertical-align: top;
      padding-left: 7px;
      font-size: 0.9em;
    }

    .totals_wrapper {
      width: 100%;
    }

    .totals_label {
      display: inline-block;
      vertical-align: top;
      width: 85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }

    .totals_value {
      display: inline-block;
      vertical-align: top;
      width: 14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }

    .totals_separator {
      width: 100%;
      height: 1px;
      clear: both;
    }

    .separator {
      width: 100%;
      height: 60px;
      clear: both;
    }

    .details_title_border_left {
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-left: 10px;
    }

    .details_title_border_right {
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-right: 3px;
    }

    .details_title {
      background: #3d7ba8;
      color: #FFF;
      padding: 10px;
    }

    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }
  </style>

</head>
@php $tcosto = 0; $tventa = 0; @endphp
<body>
  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  style="width:250px;height: 100px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$empresa->id}}<br/>
            Nombre Comercial: {{$empresa->nombrecomercial}}<br/>
            Teléfono: {{$empresa->telefono1}}<br/>
            Dir.Matriz: {{$empresa->direccion}}<br/>
            <br/>
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">DATOS DEL PACIENTE</span>
            <p style="padding-left: 10px;font-size: 20px;">
              Id: @if(!is_null($planilla)){{$planilla->id}}@endif<br/>
              Cedula: @if(!is_null($paciente)){{$paciente->id}}@endif<br/>
              Paciente :<strong> @if(!is_null($paciente)){{$paciente->nombre1}} {{$paciente->apellido1}} {{$paciente->apellido2}}@endif</strong><br />
              {{-- Seguro:<strong></strong><br /> --}}
              <!--Nivel:<strong></strong><br />-->
              Procedimiento:@if(!is_null($hcp) and isset($hcp->hc_procedimiento_final->procedimiento)) {{$hcp->hc_procedimiento_final->procedimiento->nombre}} @endif<br />
              Fecha:@if(!is_null($hc)){{date('d/m/Y', strtotime($hc->created_at))}}@endif<br />
            </p>
          </div>
        </td>
      </tr>
    </table>
    <div class="h3">DETALLES DE LA VENTA</div>                         
    <div class="modal-content">
        @php  $acumt = 0;  $cabeceras = array(1=>'HONORARIOS MEDICOS', 2=>'MEDICINAS VALOR AL ORIGEN', 3=>'INSUMOS VALOR AL ORIGEN', 4=>'IMAGEN (*)', 5=>'SERVICIOS INSTITUCIONALES', 6=>'EQUIPOS ESPECIALES',0=>'OTROS'); @endphp
        @if (!is_null($detallesv)&&$detallesv!='[]') 
          @foreach ($detallesv as $d) 
                  <div class="table-responsive col-md-12">
                  <table style="border: 1px solid;max-width:800px;!important"> 
                    <thead>
                    <tr>
                      <th width="10%">C&oacute;digo</th>
                      <th width="50%">Descripci&oacute;n</th>
                      <th width="10%">Cantidad</th>
                      <th width="10%">Costo Uni.</th> 
                      <th width="10%">Subtotal</th> 
                      <th width="10%">Iva</th> 
                      <th width="10%">TOTAL</th>
                    </tr>
                    </thead> 
            <tbody>@php $vtacum = 0 @endphp
            @foreach($d as $item) 
                          <tr> 
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px; font-weight: bold;">{{$item->codigo}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px; font-weight: bold;">{{$item->nombre}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;text-align: right;">{{$item->cantidad}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($item->precio, 2, '.', '')}}</td>
                @php $piva = 0; $ptotal = 0; $psubtotal = 0; $iva = 0;
                  if ($item->check_iva) { $iva = floatval($item->porcentaje); }  
                            $psubtotal = $item->cantidad * $item->precio;
                  $piva = $psubtotal * $iva;
                  $ptotal = $psubtotal + $piva;
                 
                
                            @endphp
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($psubtotal, 2, '.', '')}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($piva, 2, '.', '')}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($ptotal, 2, '.', '')}}</td>
                @php $vtacum += $ptotal; @endphp
                          </tr> 
                          @if ($item->ident_paquete==1)
                            @php $paquete = \Sis_medico\Ct_productos_paquete::where('id_producto', $item->id_producto)->get();  @endphp 
                            @foreach ($paquete as $det)
                              @php 
                              $subtotal = 0; $subtotal = $det->cantidad * $det->precio; 
                              $iva = 0;  $iva = $det->iva;
                              $total = 0;  $total = $subtotal + $iva;
                              @endphp
                              <tr> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">- {{$det->nombre}}</td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{$det->cantidad}}</td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($det->precio, 2, '.', '')}}</td> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($subtotal, 2, '.', '')}}</td> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($iva, 2, '.', '')}}</td> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($total, 2, '.', '')}}</td>
                              </tr> 
                            @endforeach
                              <tr> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px; font-weight: bold;">EQUIPOS</td> 
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                              </tr> 
                              @php $equipos = \Sis_medico\Ct_productos_equipos::where('id_producto', $item->id_producto)->get();  @endphp 
                              @foreach ($equipos as $det)
                                @php 
                                $subtotal = 0; 
                                $iva = 0;  
                                $total = 0;  
                                @endphp
                                <tr> 
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">- {{$det->codigo_producto}}</td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">1</td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format(0, 2, '.', '')}}</td> 
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($subtotal, 2, '.', '')}}</td> 
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($iva, 2, '.', '')}}</td> 
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px; text-align: right;">{{number_format($total, 2, '.', '')}}</td>
                                </tr> 
                              @endforeach 
                          @endif
            @endforeach  
                    </tbody> 
                    <tfoot>
                      <tr>
                         <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="6">TOTAL </td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{number_format($vtacum, 2, '.', '')}}</td>
                @php $acumt += $vtacum;  @endphp
                      </tr>
                     </tfoot>
                   </table>
                  </div> 
          @endforeach
      <br>
      <table style="border: 1px solid; width: 100%;">
        <tfoot>
          <tr>
           <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align:right" colspan="6">TOTAL VENTA </td>
           <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align:right">{{$acumt}}</td>
           @php $tventa = $acumt; @endphp
          </tr>
         </tfoot>
       </table>
    </div>
    @else 
    <h3>PLANILLA VENTA NO APROBADA</h3>
    @endif
    <br>

    <div class="h3">DETALLES DE COSTOS</div>

      <div class="modal-content">
        @php  $acumt = 0;  $cabeceras = array(1=>'HONORARIOS MEDICOS', 2=>'MEDICINAS VALOR AL ORIGEN', 3=>'INSUMOS VALOR AL ORIGEN', 4=>'IMAGEN (*)', 5=>'SERVICIOS INSTITUCIONALES',0=>'OTROS'); @endphp
        @if ($detallesc!='[]') 
          @foreach ($cabeceras as $key => $row)
            <div class="h3">{{$row}}</div>
            <div class="table-responsive col-md-12">
                  <table style="border: 1px solid;max-width:800px;!important">
                      {{-- <caption><b>{{$row}}</b></caption> --}}
                      <thead>
                      <tr>
                        <th width="10%">Fecha</th>
                        <th width="10%">C&oacute;digo</th>
                        <th width="50%">Descripci&oacute;n</th>
                        <th width="10%">Cantidad</th>
                        <th width="10%">Costo Uni.</th>
                        {{-- <th width="10%">Subtotal</th> --}}
                        {{-- <th width="10%">10%</th>
                        <th width="10%">IVA</th> --}}
                        <th width="10%">TOTAL</th>
                      </tr>
                      </thead> 
                      @php $acumtotal = 0; @endphp

                      <tbody> 
                        @if ($detallesc!='[]') 
                          @foreach ($detallesc as $item) 
                          @php if ($item->tipo_plantilla==null) $item->tipo_plantilla=0; @endphp
                            @if (isset($item->producto)  and $item->producto->tipo == $key)
                              @if ($item->check==1)
                                <tr>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{date('d/m/Y', strtotime($item->created_at))}}</td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item->codigo}}</td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;"> 
                                    @php 
                                    
                                    if(isset($item->producto)){
                                      $producto_contable = $item->producto->producto_contable(); 
                                    } else {
                                      $producto_contable ='[]';
                                    }

                                    @endphp
                                    @if($producto_contable!='[]' or $producto_contable!=null or count($producto_contable) > 0)
                                      @if (isset($producto_contable->nombre))
                                        {{($producto_contable->nombre)}}
                                      @else 
                                        @if(isset($item->producto))
                                        [{{$item->producto->nombre}}]
                                        @endif
                                      @endif
                                    @endif
                                    
                                  </td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$item->cantidad}}</td>
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($item->precio, 2, '.', ' ')}}</td>
                                  @php $subt = $item->cantidad * $item->precio; @endphp 
                                  {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($subt, 2, '.', ' ')}}</td> --}}
                                  {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">@php $porcent = ((($item->cantidad * $item->precio)*10)/100); @endphp {{number_format($porcent, 2, '.', ' ')}}</td> --}}
                                  @php  $imp = 0; $iva = 0; $porcent =0; /*$iva = ($subt*$imp);*/ 
                                        // if (isset($item->producto->iva) && $item->producto->iva==1) {
                                        //     $conf = \Sis_medico\Ct_Configuraciones::find(3);
                                        //     $iva  = ($subt+$porcent) * $conf->iva;
                                        // }
                                        $total = ($subt+$porcent+$iva); 
                                  @endphp
                               
                                  {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($iva, 2, '.', ' ')}}</td> --}}
                                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($total, 2, '.', ' ')}}</td>
                                </tr>  
                                @php $acumtotal += $total;  $acumt += $total; @endphp
                              @endif
                             
                            @endif
                          @endforeach
                        @endif
                      </tbody>

                      <tfoot>
                       <tr>
    	                  <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL {{$row}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{ number_format($acumtotal, 2, '.', ' ') }}</td>
                       </tr>
                      </tfoot>
                  </table>
            </div>  
          @endforeach

          <div class="h3">EQUIPOS ESPECIALES</div>
            <div class="table-responsive col-md-12">
              <table style="border: 1px solid;max-width:800px;!important">
                  {{-- <caption><b>EQUIPOS ESPECIALES</b></caption> --}}
                  <thead>
                  <tr>
                    <th width="10%">Fecha</th>
                    <th width="10%">C&oacute;digo</th>
                    <th width="50%">Descripci&oacute;n</th>
                    <th width="10%">Cantidad</th>
                    <th width="10%">Costo Uni.</th>
                    {{-- <th width="10%">Subtotal</th> --}}
                    {{-- <th width="10%">10%</th>
                    <th width="10%">IVA</th> --}}
                    <th width="10%">TOTAL</th>
                  </tr>
                  </thead> 
                  
                  <tbody> 
                    @if ($equipos2!='[]') 
                      @foreach ($equipos2 as $item_equipos) 
                        @if (isset($item_equipos))  
                            <tr>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{date('d/m/Y', strtotime($item_equipos->created_at))}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item_equipos->serie}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$item_equipos->nombre}} </td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{1}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
                              
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
                            </tr>  
                        @endif
                      @endforeach
                    @endif
                  </tbody>

                  <tfoot>
                   <tr>
                    <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL EQUIPOS ESPECIALES</td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
                   </tr>
                  </tfoot>
              </table>
            </div>  

        <br>
        <table style="border: 1px solid; width: 100%;">
          <tfoot>
            <tr>
             <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align:right" colspan="6">TOTAL COSTO </td>
             <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align:right">{{number_format($acumt, 2, '.', ' ') }}</td>
             @php $tcosto = $acumt; @endphp
            </tr>
           </tfoot>
         </table>
      </div>
    @else
    <h3>PLANILLA DE COSTO NO APROBADA </h3>
    @endif
    <div class="separator"></div>
    
    <div class="h3">RESUMEN</div>
    <table style="border: 1px solid;max-width:800px;!important">  
        <tbody>  
            <tr>  
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">TOTAL VENTAS (v)</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;font-weight: bold;">{{number_format($tventa, 2, '.', '')}}</td>  
            </tr>  
            <tr> 
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">TOTAL COSTOS (c)</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;text-align: right;">{{number_format($tcosto, 2, '.', '')}}</td> 
            </tr>
            <tr>  
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">TOTAL (v) - (c)</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;font-weight: bold;">{{number_format(($tventa-$tcosto), 2, '.', '')}}</td> 
            </tr>    
        </tbody>  
    </table>

  </div>
</body>

</html>