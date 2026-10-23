<!DOCTYPE html>
<html lang="en">

<head>

  <title>Factura</title>
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
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
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
      font-size: 1em;
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
  </style>


</head>
@php
$subtotal = 0;
$iva = 0;
$impuesto = 0;
$tl_sniva = 0;
$total = 0;
@endphp

<body>

  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        @if($emp->id == '0992704152001')

        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}" style="width:350px;height: 150px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$emp->id}}<br />
            Nombre Comercial: {{$emp->nombrecomercial}}<br />
            Teléfono: {{$emp->telefono1}}<br />
            Dir.Matriz: {{$emp->direccion}}<br />
            <br />
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">FACTURA</span>
            <p style="padding-left:20px;padding-right:20px;padding-top:0px;padding-bottom:0px">
              No. Factura:<strong> {{$fact_venta->nro_comprobante}}</strong><br />
              Fecha: {{$fact_venta->fecha}}<br />
              Recaudador: @if(isset($recaud)){{$recaud->nombre1}} {{$recaud->apellido1}} @endif<br />
            </p>
          </div>
        </td>
        @else
        <td class="info_empresa">
          <div style="text-align: center">
            @if(!is_null($emp->logo))
            <img src="{{base_path().'/storage/app/logo/'.$emp->logo}}" style="width:350px;height: 150px">
            @else

            @endif
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$emp->id}}<br />
            Nombre Comercial: {{$emp->nombrecomercial}}<br />
            Teléfono: {{$emp->telefono1}}<br />
            Dir.Matriz: {{$emp->direccion}}<br />
            <br />
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">FACTURA</span>
            <p style="padding-left:20px;padding-right:20px;padding-top:0px;padding-bottom:0px">
              No. Factura:<strong> {{$fact_venta->nro_comprobante}}</strong><br />
              Fecha: {{$fact_venta->fecha}}<br />
              Recaudador: @if(isset($recaud)){{$recaud->nombre1}} {{$recaud->apellido1}} @endif<br />
            </p>
          </div>
        </td>
        @endif
      </tr>
    </table>
    @php
    if(!is_null($fact_venta)){
    $seguro = Sis_medico\Seguro::find($fact_venta->seguro_paciente);
    }
    @endphp
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="round">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td width="15%">
                    <div class="mLabel">
                      CLIENTE:
                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">
                      {{trim($cliente->nombre)}}
                    </div>
                  </td>
                  <td width="15%">
                    @if(!is_null($pacient))
                    <div class="mLabel">
                      PACIENTE:
                    </div>
                    @endif
                  </td>
                  <td width="35%">
                    @if(!is_null($pacient))
                    <div class="mValue">
                      {{$pacient->apellido1}} @if($pacient->apellido2!='(N/A)'){{$pacient->apellido2}}@endif {{$pacient->nombre1}} @if($pacient->nombre2!='(N/A)'){{$pacient->nombre2}}@endif
                    </div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td width="15%">
                    <div class="mLabel">
                      DIRECCION:
                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">
                      {{$cliente->direccion_representante}}
                    </div>
                  </td>
                  <td width="15%">
                    @if(!is_null($pacient))
                    <div class="mLabel">
                      SEGURO:
                    </div>
                    @endif
                  </td>
                  <td width="35%">
                    @if(!is_null($pacient))
                    <div class="mValue">
                      @if(isset($seguro))
                      @if(!is_null($seguro))
                      {{$seguro->nombre}}
                      @endif
                      @endif
                    </div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td width="15%">
                    <div class="mLabel">
                      R.U.C./C.I.:
                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">
                      {{$cliente->identificacion}}
                    </div>
                  </td>
                  <td width="15%">
                    @if(!is_null($pacient))
                    <div class="mLabel">
                      PROCED:
                    </div>
                    @endif
                  </td>
                  <td width="35%">
                    @if(!is_null($pacient))
                    <div class="mValue">
                      {{$fact_venta->procedimientos}}
                    </div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td width="15%">
                    <div class="mLabel">
                      TELÉFONO:
                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">
                      {{$cliente->telefono1_representante}}
                    </div>
                  </td>
                  <td width="15%">
                    @if(!is_null($pacient))
                    <div class="mLabel">
                      FECHA PROCED:
                    </div>
                    @endif
                  </td>
                  <td width="35%">
                    @if(!is_null($pacient))
                    <div class="mValue">
                      {{$fact_venta->fecha_procedimiento}}
                    </div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td width="15%">
                    <div class="mLabel">
                      FECHA:
                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">
                      {{$fact_venta->fecha}}
                    </div>
                  </td>
                  <td width="15%">
                    <div class="mLabel">

                    </div>
                  </td>
                  <td width="35%">
                    <div class="mValue">

                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px;">
            <div class="details_title_border_left">CÓDIGO</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">DESCRIPCIÓN</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">CANTIDAD</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">PRECIO</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title_border_right">P.NETO</div>
          </th>
        </tr>
      </thead>
      <tbody id="detalle_productos">
        @if($fact_venta->tipo=='VENFA-CO')
        @foreach($detalle_venfaco as $x)
        <tr class="round">

          <td style="font-size: 16px">
            @if(!is_null($x->id_ct_productos))
            {{$x->id_ct_productos}}
            @endif
          </td>
          @php
          if(!is_null($x->id_ct_productos)){
          $ct_prod = DB::table('ct_productos')->where('codigo',$x->id_ct_productos)->first();
          }
          @endphp
          <td style="font-size: 16px">
            @if(!is_null($ct_prod))
            <label>{{$ct_prod->descripcion}}</label>
            @endif

          </td>
          <td style="font-size: 16px;">
            1
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($x->precio))

            @if(isset($x->descuento))
            {{number_format($x->descuento,2,'.',',')}}
            @else
            {{number_format($x->precio,2,'.',',')}}
            @endif
            @endif
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($x->precio))
            {{number_format($x->precio,2,'.',',')}}
            @endif
          </td>
        </tr>
        @endforeach
        @elseif($fact_venta->tipo=='VEN-CONVENIO')
        @php
        $obs = $deta_vent[0]->codigo;
        $cont= sizeof($deta_vent);
        $last_key = end(($deta_vent));
        @endphp
        @foreach ($deta_vent as $value)

        <tr class="round">
          <td style="font-size: 16px">
            @if(!is_null($value->id_ct_productos))
            {{$value->id_ct_productos}}
            @endif
          </td>
          @php
          if(!is_null($value->id_ct_productos)){
          $ct_prod = DB::table('ap_agrupado')->where('cod_proceso',$value->id_ct_productos)->first();
          }
          @endphp
          <td style="font-size: 16px">

            <label> Mes plano {{$ct_prod->mes_plano}}</label>


            <span style="font-size: 15px;
                    font-stretch: semi-condensed;
                    white-space: pre-line;">
              @if(!is_null($value->detalle))
              {{$value->detalle}}
              @endif
            </span>
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->cantidad))
            {{$value->cantidad}}
            @endif
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->precio))

            @if(!is_null($value->descuento_porcentaje))
            {{number_format((($value->extendido)/ ($value->cantidad)),2)}}
            @else
            {{$value->precio}}
            @endif
            @endif
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->extendido))
            {{$value->extendido}}
            @endif
          </td>
        </tr>
        @if($cont>1)
        @if ($value->codigo != $last_key[1]->codigo)
        <tr>
          <td style="font-size: 16px;" colspan="5">
            {{$value->codigo}}
          </td>
        </tr>
        @endif
        @else
        @if ($value->codigo !="")
        <tr>
          <td style="font-size: 16px;" colspan="5">
            {{$value->codigo}}
          </td>
        </tr>
        @endif
        @endif
        @endforeach
        @if($cont>1)

        <tr>
          <td style="font-size: 16px;" colspan="8">
            {{$deta_vent[$cont-1]->codigo}}
          </td>
        </tr>
        @endif
        @else
        @php
        $obs = $deta_vent[0]->codigo;
        $cont= sizeof($deta_vent);
        $last_key = end(($deta_vent));
        @endphp
        @foreach ($deta_vent as $value)

        <tr class="round">
          <td style="font-size: 16px">
            @if(!is_null($value->id_ct_productos))
            {{$value->id_ct_productos}}
            @endif
          </td>
          @php
          if(!is_null($value->id_ct_productos)){
          $ct_prod = DB::table('ct_productos')->where('codigo',$value->id_ct_productos)->first();
          }
          @endphp
          <td style="font-size: 16px">
            @if(!is_null($ct_prod->descripcion))
            <label>{{$ct_prod->descripcion}}</label>
            @endif

            <span style="font-size: 15px;
                    font-stretch: semi-condensed;
                    white-space: pre-line;">
              @if(!is_null($value->detalle))
              {{$value->detalle}}
              @endif
            </span>
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->cantidad))
            {{$value->cantidad}}
            @endif
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->precio))

            @if(!is_null($value->descuento_porcentaje))
            {{number_format((($value->extendido)/ ($value->cantidad)),2)}}
            @else
            {{$value->precio}}
            @endif
            @endif
          </td>
          <td style="font-size: 16px;">
            @if(!is_null($value->extendido))
            {{$value->extendido}}
            @endif
          </td>
        </tr>
        @if($cont>1)
        @if ($value->codigo != $last_key[1]->codigo)
        <tr>
          <td style="font-size: 16px;" colspan="5">
            {{$value->codigo}}
          </td>
        </tr>
        @endif
        @else
        @if ($value->codigo !="")
        <tr>
          <td style="font-size: 16px;" colspan="5">
            {{$value->codigo}}
          </td>
        </tr>
        @endif
        @endif
        @endforeach
        @if($cont>1)

        <tr>
          <td style="font-size: 16px;" colspan="8">
            {{$deta_vent[$cont-1]->codigo}}
          </td>
        </tr>
        @endif
        @endif
      </tbody>
    </table>
    <div class="separator"></div>
    <div class="totals_wrapper">
      <div class="totals_label">
        SUBTOTAL 0%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_0))
        {{$fact_venta->subtotal_0}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        SUBTOTAL 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_12))
        {{$fact_venta->subtotal_12}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        DESCUENTO
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->descuento))
        {{$fact_venta->descuento}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        BASE IMPONIBLE:
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->base_imponible))
        {{$fact_venta->base_imponible}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        TARIFA 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->impuesto))
        {{$fact_venta->impuesto}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        TOTAL
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->total_final))
        {{$fact_venta->total_final}}
        @endif
      </div>
    </div>
    <div class="separator"></div>
    <div>
      <!--FormaS de Pago-->
      @if($ct_for_pag !='[]')
      <span class="h3">FORMAS DE PAGO</span>
      <table id="form_pag" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 15px">
              <div class="details_title_border_left">TIPO</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title_border_right">VALOR</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_pago">
          @foreach ($ct_for_pag as $value)
          <tr class="round">
            <td style="font-size: 16px">
              @php
              if(!is_null($value->tipo)){
              $tipo_nomb = Sis_medico\Ct_Tipo_Pago::where('id',$value->tipo)->first();
              }
              @endphp
              @if(!is_null($tipo_nomb))
              {{$tipo_nomb->nombre}}
              @endif
            </td>
            <td style="font-size: 16px">
              @if(!is_null($value->valor))
              {{$value->valor}}
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
    <div class="separator"></div>
    <div>
      <!--Valor de Retenciones echa a la Factura de Venta-->
      @if(!is_null($ct_val_ret))
      @if((!is_null($ct_val_ret->total_rfiva))||(!is_null($ct_val_ret->total_rfir)))
      <span class="h3">RETENCIONES</span>
      @php
      $valor_tot_ret = $ct_val_ret->total_rfiva+$ct_val_ret->total_rfir;
      @endphp
      <table id="form_ret" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 15px">
              <div class="details_title_border_left">RFIVA</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title">TIPO R.IVA</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title">RFIR</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title">TIPO R.FUENTE</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title_border_right">TOTAL RETENIDO</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_retencion">
          <td style="font-size: 16px">
            @if(!is_null($ct_val_ret->total_rfiva))
            {{$ct_val_ret->total_rfiva}}
            @endif
          </td>
          @php
          if(!is_null($ct_val_ret->tipo_rfiva)){
          $tip_ret_iva = Sis_medico\Ct_Porcentajes_Retencion_Iva::where('id',$ct_val_ret->tipo_rfiva)->first();
          }
          @endphp
          <td style="font-size: 16px">
            @if(!is_null($tip_ret_iva->nombre))
            {{$tip_ret_iva->nombre}}
            @endif
          </td>
          <td style="font-size: 16px">
            @if(!is_null($ct_val_ret->total_rfir))
            {{$ct_val_ret->total_rfir}}
            @endif
          </td>
          @php
          if(!is_null($ct_val_ret->tipo_rfir)){
          $tip_ret_fuent = Sis_medico\Ct_Porcentajes_Retencion_Fuente::where('id',$ct_val_ret->tipo_rfir)->first();
          }
          @endphp
          <td style="font-size: 16px">
            @if(!is_null($tip_ret_fuent))
            {{$tip_ret_fuent->nombre}}
            @endif
          </td>
          <td style="font-size: 16px">
            {{$valor_tot_ret}}
          </td>
        </tbody>
      </table>
      @endif
      @endif
    </div>
  </div>

</body>

</html>