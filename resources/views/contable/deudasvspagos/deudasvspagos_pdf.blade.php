
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Informe Deudas Vs Pagos</title>
    <style>
      .new_exl_cabecera {
                border: 1px solid black;
                text-align: center;
                padding: 8px;
                font-weight: bold;
                font-size: 24px;
                color: black;
                background-color: #D1F2EB;
      }

      .table {
          border-collapse: collapse;
          padding: 1px;

      }

      .new_exl_cuerpo {
                border: 1px solid black;
                text-align: center;
                padding: 8px;
                /*font-weight: bold;*/
                font-size: 16px;
                color: black;
          }
    </style>
 
 </head>
 <body>
  <div class="box" style=" background-color: white;">
       

      @if(count($deudas)>0)
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">      
              <div class="box-body">
                <div class="col-md-4">
                  <dl>
                    @if(!is_null($empresa->logo))
                    <dd><img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  alt="Logo Image"  style="width:200px;height:80px;" id="logo_empresa" ></dd>
                    @endif
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl>
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h2 style="text-align: center; font-size: 22;">DEUDAS VS PAGOS</h2>
                  @if(($fecha_desde!=null))
                  <h5 style="text-align: center;">Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                  @else
                  <h5 style="text-align: center;">Al - {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                  @endif
                </div>
                <div class="col-md-12">
                  <label style=" color:black; font-weight: bold;" class="label label-info">Ref: Es el ID de la Factura</label>
                </div>          
                @php
                  $cont=0;
                @endphp
                  <table id="example2" style="font-size: 12px; border:none !important; width: 100%" class="table">
                    <thead>
                      <tr>
                        <th style="width: 40%;"  class="new_exl_cabecera" >Detalles</th>
                        <th style="width: 10%;" class="new_exl_cabecera" >Fecha</th>
                        <th style="width: 10%;" class="new_exl_cabecera">Ref</th>
                        <th style="width: 10%;" class="new_exl_cabecera">Valor</th>
                        <th style="width: 10%;" class="new_exl_cabecera">Debe</th>
                        <th style="width: 10%;" class="new_exl_cabecera">Haber</th>
                        <th style="width: 10%;" class="new_exl_cabecera">Saldo</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- u can change this query with simple query date 1/12/2020 -->
                    @php 
                      $debet=0;
                      $habert=0;
                      $valor_contable=0;     
                    @endphp
                    @foreach($deudas as $value)
                      @if(($value)!=null)
                        @if($value->estado>0)
                          @php 
                            $debet+= $value->total_final;
                            $fec=new DateTime($value->f_autorizacion);
                            $fec2=new DateTime(date('Y-m-d'));
                            $fultima = date('Y-m-d');
                          @endphp
                          <tr>
                            <td class="new_exl_cuerpo">
                              @if(($value)!=null) @if(($value->numero)!=null && (($value->numero)!=null)) {{$value->proveedorf->nombrecomercial}} Fact: # {{$value->numero}} Ref:  {{$value->id}} @endif  @endif
                              <label id="co{{$value->id}}"> </label>
                              @if($value->valor_contable > 0)
                                @php        
                                  $diff = $fec->diff($fec2);
                                  $daysf=$diff->format("%r%a");                                 
                                  $valor_contable+= $value->valor_contable;
                                @endphp
                                <label style="color: red;" class="label @if($daysf>10 && $daysf<20) label-warning @elseif($daysf>=20) label-danger @else label-primary @endif">{{$daysf}} Dias</label>
                              @else
                                @php
                                  $fultima = '0012-07-31';
                                @endphp
                              @endif
                            </td>
                            <td class="new_exl_cuerpo" >@if(($value->f_autorizacion)!=null) {{date("d-m-Y", strtotime($value->f_autorizacion))}} @endif</td>
                            <td class="new_exl_cuerpo" >COM-FA @if(($value->numero)!=null) {{$value->numero}} @endif</td>
                            <td class="new_exl_cuerpo" >@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                            <td class="new_exl_cuerpo" >@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                            <td class="new_exl_cuerpo" >0.00</td>
                            <td class="new_exl_cuerpo" > {{number_format($value->valor_contable,2,'.',',')}} </td>
                          </tr>
                          @if(($value->egresos)!=null && (($value->egresos)!='[]'))
                            @foreach($value->egresos as $v)
                              @if($v->comp_egreso->estado==1)
                                @php 
                                  $habert+= $v->abono; 
                                  $pagos_tipo_pago = "";
                                  if(isset($v->comp_egreso)){
                                    if(isset($v->comp_egreso->bancoa)){
                                      if(!is_null($v->comp_egreso->bancoa->nombre)){
                                        $pagos_tipo_pago = $v->comp_egreso->bancoa->nombre . " / " ;
                                      }
                                    }
                                  }
                                  if(isset($v->comp_egreso)){
                                    if(isset($v->comp_egreso->tipo_pago)){
                                      $pagos_tipo_pago = $pagos_tipo_pago . "" . $v->comp_egreso->tipo_pago->nombre;
                                    }
                                  }
                                @endphp
                                @if($v->comp_egreso->id_empresa==$empresa->id)
                                  <tr>
                                    @if($value->valor_contable == 0)
                                      @php
                                        if($fultima < $v->comp_egreso->fecha_comprobante){
                                          $fultima = $v->comp_egreso->fecha_comprobante;
                                        }
                                        $habert+= $value->valor_contable;
                                      @endphp
                                    @endif
                                    <td class="new_exl_cuerpo" >
                                      @if(($v->comp_egreso)!='[]') 
                                        @if(($v->comp_egreso->descripcion)!=null)
                                        <p style="margin-bottom: 0px!important; color:blue;" > {{$value->proveedorf->nombrecomercial}} 
                                          # {{$v->comp_egreso->descripcion}} Ref: {{$value->numero}}  -- Asiento: {{$v->comp_egreso->id_asiento_cabecera}} 
                                        </p>
                                        <label style="font-size: 11px; color:blue;"> 
                                          #egreso {{$v->id_comprobante}}  @if($pagos_tipo_pago!= "") --Método de pago: {{$pagos_tipo_pago}}  @endif
                                        </label>
                                        @endif 
                                      @endif
                                    </td>
                                    <td class="new_exl_cuerpo" >@if(($v->comp_egreso)!='[]') @if(($v->comp_egreso->fecha_comprobante)!=null) {{date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante))}} @endif @endif</td>
                                    <td class="new_exl_cuerpo" >ACR-EG  @if(($v->comp_egreso)!='[]')  @if(($v->comp_egreso->secuencia)!=null) {{$v->comp_egreso->secuencia}} @endif @endif</td>
                                    <td class="new_exl_cuerpo" >@if(($v->abono)!=null)  {{number_format($v->abono,2,'.',',')}}  @endif</td>
                                    <td class="new_exl_cuerpo" >0.00</td>
                                    <td class="new_exl_cuerpo" >@if(($v->abono)!=null) {{number_format($v->abono,2,'.',',')}} @endif</td>
                                    <td class="new_exl_cuerpo" >0.00</td>
                                  </tr>
                                @endif
                              @endif
                            @endforeach
                          @endif
                          @if(($value->bndebito)!=null && (($value->bndebito)!='[]'))
                            @foreach($value->bndebito as $v)
                              @php 
                                $meto_pago = "";
                                if(isset($v->cabecera)){
                                  if(!is_null($v->cabecera->nombre)){
                                    $meto_pago = "-- Método de pago". $v->cabecera->nombre; 
                                  }
                                }
                              @endphp
                              @if($v->cabecera->estado==1)
                                @php 
                                  $habert+= $v->abono;
                                @endphp
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $v->cabecera->fecha){
                                        $fultima = $v->cabecera->fecha;
                                      }
                                      $habert+= $value->valor_contable;    
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo">
                                    @if(($v->cabecera)!='[]')
                                      @if(($v->cabecera->concepto)!=null)
                                        <p style="margin-bottom: 0px!important; font-weight: bold" > {{$value->proveedorf->nombrecomercial}}  # {{$v->cabecera->concepto}} Ref: {{$value->numero}} -- Asiento: {{$v->cabecera->id_asiento}}</p>  
                                        <label for="" class="label label-success" style="font-size: 11px;"> # Debito: {{$v->id_debito}} {{$meto_pago}}</label>
                                      @endif
                                    @endif
                                  </td>
                                  <td class="new_exl_cuerpo" > @if(($v->cabecera)!='[]') @if(($v->cabecera->fecha)!=null) {{date("d-m-Y", strtotime($v->cabecera->fecha))}} @endif @endif</td>
                                  <td class="new_exl_cuerpo" >BAN-ND  @if(($v->cabecera)!='[]')  @if(($v->cabecera->secuencia)!=null) {{$v->cabecera->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo" >@if(($v->saldo)!=null)  {{number_format($v->saldo,2,'.',',')}}  @endif</td>
                                  <td class="new_exl_cuerpo" >0.00</td>
                                  <td class="new_exl_cuerpo" >@if(($v->abono)!=null) {{number_format($v->abono,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo" >0.00</td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @if(($value->masivos)!=null && (($value->masivos)!='[]'))
                            @foreach($value->masivos as $v)
                              @php 
                                $eg_mas_pago="";
                                if(isset($v->comp_egreso)){
                                  if(isset($v->comp_egreso->bancoa)){
                                    if(!is_null($v->comp_egreso->bancoa->nombre)){
                                      $eg_mas_pago = $v->comp_egreso->bancoa->nombre ." / " ;
                                    }
                                  }
                                }
                                if(isset($v->comp_egreso)){
                                  if(isset($v->comp_egreso->tipo_pago)){
                                    if(!is_null($v->comp_egreso->tipo_pago->nombre)){
                                      $eg_mas_pago =  $eg_mas_pago ."". $v->comp_egreso->tipo_pago->nombre;
                                    }          
                                  }
                                }
                              @endphp
                              @if($v->comp_egreso->estado==1)
                                @php 
                                  $habert+= $v->abono;
                                @endphp
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $v->comp_egreso->fecha_comprobante){
                                        $fultima = $v->comp_egreso->fecha_comprobante;
                                      }
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo" >
                                    @if(($v->comp_egreso)!='[]')
                                      @if(($v->comp_egreso->descripcion)!=null)
                                        <p style="margin-bottom: 0px!important; font-weight: bold" > {{$value->proveedorf->nombrecomercial}}  # {{$v->comp_egreso->descripcion}} Ref: {{$value->numero}} #Egreso Masivo: {{$v->id}} -- Asiento: {{$v->comp_egreso->id_asiento_cabecera}}</p>
                                        <label style="font-size: 11px; color: blue;" for="" class="label label-success"> #Egreso Masivo: {{$v->id}} @if($eg_mas_pago!="") -- Método de pago:  {{$eg_mas_pago}} @endif</label>
                                      @endif
                                    @endif
                                  </td>
                                  <td class="new_exl_cuerpo" >@if(($v->comp_egreso)!='[]') @if(($v->comp_egreso->fecha_comprobante)!=null) {{date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante))}} @endif @endif</td>
                                  <td class="new_exl_cuerpo" >ACR-M  @if(($v->comp_egreso)!='[]')  @if(($v->comp_egreso->secuencia)!=null) {{$v->comp_egreso->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo" >@if(($v->saldo_base)!=null)  {{number_format($v->saldo_base,2,'.',',')}} @else 0.00  @endif</td>
                                  <td class="new_exl_cuerpo" >0.00</td>
                                  <td class="new_exl_cuerpo" >@if(($v->abono)!=null) {{number_format($v->abono,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo" >0.00</td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @if(($value->cruce)!=null && (($value->cruce)!='[]'))
                            @foreach($value->cruce as $x) 
                              @if(($x->cabecera->estado)==1)
                                @php      
                                  $habert+= $x->total;
                                @endphp
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $x->cabecera->fecha_pago){
                                        $fultima = $x->cabecera->fecha_pago;
                                      }
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo">@if(($x->cabecera)!='[]') @if(($x->cabecera->detalle)!=null) <p class="margin-bottom: 0px!important; font-weight: bold"> {{$value->proveedorf->nombrecomercial}}  # {{$x->cabecera->detalle}} Ref: {{$value->numero}}  #cruce: {{$x->id_comprobante}} -- Asiento: {{$x->cabecera->id_asiento_cabecera}}</p>  @endif  @endif</td>
                                  <td class="new_exl_cuerpo">@if(($x->cabecera)!='[]') @if(($x->cabecera->fecha_pago)!=null) {{date("d-m-Y", strtotime($x->cabecera->fecha_pago))}} @endif @endif</td>
                                  <td class="new_exl_cuerpo">ACR-CR-AF  @if(($x->cabecera)!='[]')  @if(($x->cabecera->secuencia)!=null) {{$x->cabecera->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo">@if(($x->total)!=null)  {{number_format($x->total,2,'.',',')}}  @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                  <td class="new_exl_cuerpo">@if(($x->total)!=null) {{number_format($x->total,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @if(($value->retenciones)!=null && (($value->retenciones)!='[]'))
                            @foreach($value->retenciones as $xrete)
                              @if(($xrete->estado)==1)
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $xrete->fecha){
                                        $fultima = $xrete->fecha;
                                      }
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo">@if(($xrete)!='[]') @if(($xrete->secuencia)!=null) <p class="margin-bottom: 0px!important; font-weight: bold"> {{$value->proveedorf->nombrecomercial}}   # {{$xrete->descripcion}} Ref: {{$value->numero}} # retencion: {{$xrete->id}} -- Asiento: {{$xrete->id_asiento_cabecera}} </p>  @endif  @endif</td>
                                  <td class="new_exl_cuerpo">@if(($xrete)!='[]') {{date("d-m-Y", strtotime($xrete->fecha))}} @endif</td>
                                  <td class="new_exl_cuerpo">ACR-RE @if(($xrete)!='[]') @if(($xrete->secuencia)!=null) {{$xrete->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo">@if(($xrete)!='[]') @php $total= ($xrete->valor_fuente)+($xrete->valor_iva); @endphp {{number_format($total,2,'.','')}}  @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                  <td class="new_exl_cuerpo">@if(($xrete)!='[]') {{number_format($total,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                  @php 
                                    $habert+= $total;
                                  @endphp
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @php 
                            $fechas =date("Y-m-d");
                            $fechas = date("Y-m-d", strtotime("09-10-2021"));
                          @endphp
                          @if(($value->debitoacreedor)!=null && (($value->debitoacreedor)!='[]'))
                            @if(!is_null($value->debitoacreedor))
                              @foreach($value->debitoacreedor as $xs)
                                @if($xs->cabecera->estado==1)
                                  @php 
                                    $habert+= $xs->cabecera->valor_contable;
                                  @endphp
                                  @if(!is_null($xs->cabecera))
                                    <tr>
                                      @if($value->valor_contable == 0)
                                        @php
                                          if($fultima < $xs->cabecera->fecha){
                                            $fultima = $xs->cabecera->fecha;
                                          }
                                        @endphp
                                      @endif
                                      @php 
                                        if(($xs->cabecera)!='[]'){
                                          if(!is_null($xs->cabecera->fecha)){
                                            $fechas = $value->fecha;
                                          }else{
                                            $fechas = $xs->cabecera->fecha;
                                          }
                                        }
                                      @endphp
                                      <td class="new_exl_cuerpo">
                                        @if(($xs->cabecera)!='[]') 
                                            <p class="margin-bottom: 0px!important; font-weight: bold"> {{$value->proveedorf->nombrecomercial}}  # 
                                              {{$xs->cabecera->concepto}} Ref: {{$value->numero}} # Debito {{$xs->id_debito_acreedores}}  
                                              -- Asiento: {{$xs->id_asiento_cabecera}}
                                            </p>  
                                        @endif
                                      </td>
                                      <td class="new_exl_cuerpo"> {{$fechas}}</td>
                                      <td class="new_exl_cuerpo">ACR-DB @if(($xs->cabecera)!='[]') @if(($xs->cabecera->secuencia)!=null) {{$xs->cabecera->secuencia}} @endif @endif</td>
                                      <td class="new_exl_cuerpo">@if(($xs->cabecera)!='[]')  {{$xs->total}}  @endif</td>
                                      <td class="new_exl_cuerpo">0.00</td>
                                      <td class="new_exl_cuerpo">@if(($xs->cabecera)!='[]') {{number_format($xs->total,2,'.',',')}} @endif</td>
                                      <td class="new_exl_cuerpo">0.00</td>
                                    </tr>
                                  @endif
                                @endif
                              @endforeach
                            @endif
                          @endif
                          @if(($value->credito_acreedor)!=null && (($value->credito_acreedor)!='[]'))
                            @foreach($value->credito_acreedor as $credito)
                              @if(($credito->estado)==1)
                                @php 
                                  $habert+= $credito->subtotal;
                                @endphp
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $credito->fecha){
                                        $fultima = $credito->fecha;
                                      }
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo">@if(($credito)!='[]') @if(($credito->secuencia)!=null) <p class="margin-bottom: 0px!important; font-weight: bold"> {{$value->proveedorf->nombrecomercial}}   # {{$credito->concepto}} Ref: {{$value->numero}} #credito:  {{$credito->id}} -- Asiento: {{$credito->id_asiento_cabecera}} </p>  @endif  @endif</td>
                                  <td class="new_exl_cuerpo">@if(($credito)!='[]') {{date("d-m-Y", strtotime($credito->fecha))}} @endif</td>
                                  <td class="new_exl_cuerpo">ACR-NC @if(($credito)!='[]') @if(($credito->secuencia)!=null) {{$credito->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo">@if(($credito)!='[]')  {{$credito->valor_contable}}  @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                  <td class="new_exl_cuerpo">@if(($credito)!='[]') {{number_format($credito->valor_contable,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @if(($value->cruce_cuentas)!=null && (($value->cruce_cuentas)!='[]'))
                            @foreach($value->cruce_cuentas as $cruce_cuentas)
                              @if(($cruce_cuentas->estado)==1)
                                @php 
                                  $habert+= $cruce_cuentas->total;
                                @endphp
                                <tr>
                                  @if($value->valor_contable == 0)
                                    @php
                                      if($fultima < $cruce_cuentas->fecha){
                                        $fultima = $cruce_cuentas->fecha;
                                      }
                                    @endphp
                                  @endif
                                  <td class="new_exl_cuerpo">@if(($cruce_cuentas)!='[]') @if(($cruce_cuentas->secuencia)!=null) <p class="margin-bottom: 0px!important; font-weight: bold"> {{$value->proveedorf->nombrecomercial}}   # {{$cruce_cuentas->concepto}} Ref: {{$value->numero}} #cruce_cuentas:  {{$cruce_cuentas->id}} -- Asiento: {{$cruce_cuentas->id_asiento_cabecera}} </p>  @endif  @endif</td>
                                  <td class="new_exl_cuerpo">@if(($cruce_cuentas)!='[]') {{date("d-m-Y", strtotime($cruce_cuentas->fecha))}} @endif</td>
                                  <td class="new_exl_cuerpo">CRUCE-CUENTAS @if(($cruce_cuentas)!='[]') @if(($cruce_cuentas->secuencia)!=null) {{$cruce_cuentas->secuencia}} @endif @endif</td>
                                  <td class="new_exl_cuerpo">@if(($cruce_cuentas)!='[]')  {{$cruce_cuentas->total}}  @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                  <td class="new_exl_cuerpo">@if(($cruce_cuentas)!='[]') {{number_format($cruce_cuentas->total,2,'.',',')}} @endif</td>
                                  <td class="new_exl_cuerpo">0.00</td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                          @if($value->valor_contable == 0)
                            @php
                              $fec=new DateTime($value->f_autorizacion);
                              $fec2=new DateTime($fultima);
                              $fultima = date('Y-m-d');
                              $diff = $fec->diff($fec2);
                              $daysf=$diff->format("%r%a");
                            @endphp
                          @endif
                        @endif
                      @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td class="new_exl_cuerpo"><label>TOTAL :</label></td>
                        <td class="new_exl_cuerpo"></td>
                        <td class="new_exl_cuerpo"></td>
                        <td class="new_exl_cuerpo"></td>
                        <td class="new_exl_cuerpo">{{number_format($debet,2,'.',',')}}</td>
                        <td class="new_exl_cuerpo">{{number_format($habert,2,'.',',')}}</td>
                        <td class="new_exl_cuerpo">{{number_format($valor_contable,2,'.',',')}}</td>
                      </tr>
                    </tfoot>
                  </table>                             
              </div>             
            </div>           
          </div>
        </div>
      </div>
      @endif
    </div>
  </body>

</html>