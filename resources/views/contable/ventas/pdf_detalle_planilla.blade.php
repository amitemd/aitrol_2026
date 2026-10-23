<!DOCTYPE html>
<html lang="en">

<head>

  <title>Procedimiento</title>
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

<body>
  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}"  style="width:250px;height: 100px">
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
            <span class="h3" style="padding:20px">DETALLE DE PLANILLA</span>
            <p style="padding-left: 10px;font-size: 20px;">
              Id: @if(!is_null($planilla)){{isset($planilla->id)?$planilla->id:''}}@endif<br/>
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
    <div class="modal-content">
      @php  
        $acumt = 0;  $cabeceras = array(1=>'HONORARIOS MEDICOS', 2=>'MEDICINAS VALOR AL ORIGEN', 3=>'INSUMOS VALOR AL ORIGEN', 4=>'IMAGEN (*)', 5=>'SERVICIOS INSTITUCIONALES',  0=>'OTROS'); $acum_honorarios = 0;
      @endphp

      @if ($detalles!='[]') 
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
                       @if($key == '1')
                        @foreach($detalle_pdf as $dpdf)
                          @php $acum_honorarios = $acum_honorarios + $dpdf->valor; @endphp
                          <tr>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{substr($dpdf->created_at,0,10)}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$dpdf->descripcion}}</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">1</td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;"> {{ $dpdf->valor }} </td>
                            <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;"> {{ $dpdf->valor }} </td>
                          </tr> 
                         @endforeach
                          
                        @endif 
                      @if ($detalles!='[]') 
                        @foreach ($detalles as $item) 
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
                      @if($key == '1')
                     <tr>
  	                  <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL {{$row}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{ number_format($acum_honorarios, 2, '.', ' ') }}</td>
                     </tr>
                     @else
                     <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL {{$row}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{ number_format($acumtotal, 2, '.', ' ') }}</td>
                     </tr>
                     @endif
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
                @if ($equipos!='[]') 
                  @foreach ($equipos as $item_equipos) 
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
               <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align:right" colspan="6">TOTAL PLANILLA </td>
               <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align:right">{{number_format(($acumt + $acum_honorarios), 2, '.', ' ') }}</td>
              </tr>
             </tfoot>
          </table>
        </div>
      @else 
      <h3>PLANILLA NO APROBADA</h3>
    @endif
    <div class="separator"></div>
  </div>
</body>

</html>