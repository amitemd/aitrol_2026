
  <style>
    p.s1 {
      margin-left: 10px;
      font-size: 14px;
      font-weight: bold;
    }
  
    p.s2 {
      margin-left: 20px;
      font-size: 12px;
      font-weight: bold;
    }
  
    p.s3 {
      margin-left: 30px;
      font-size: 10px;
      font-weight: bold;
    }
  
    p.s4 {
      margin-left: 60px;
      font-size: 10px;
    }
  
    p.t1 {
      font-size: 14px;
      font-weight: bold;
    }
  
    p.t2 {
      font-size: 12px;
      font-weight: bold;
    }
  
    p.t3 {
      font-size: 10px;
    }
  
    .td_center {
      text-align: center;
    }
  
    .td_der {
      text-align: right;
    }
  
    .table-condensed>thead>tr>th>td,
    .table-condensed>tbody>tr>th>td,
    .table-condensed>tfoot>tr>th>td,
    .table-condensed>thead>tr>td,
    .table-condensed>tbody>tr>td,
    .table-condensed>tfoot>tr>td {
  
      padding: 0;
      font-size: 14px !important;
      line-height: 1;
    }
  
    .t_det {
      font-size: 10px; 
    }
    .tt_det {
      font-size: 10px; 
      font-weight: bold;
    }
    .t_det_r {
      text-align: right;
    }
    
    .t_det_c {
      text-align: center;
    }
  </style>
    @foreach ($skardex as $r) 
    <div class="box-body">
      <div class="box">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                  <div class="table-responsive col-md-12">
                      <table id="tbl_kardex" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                          <thead>
                              <tr> 
                                  <th width="20%">Codigo</th>
                                  <th width="40%">Nombre</th>
                                  <th width="40%">Descripcion</th>
                              </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td bgcolor="00cefc"> {{$r['codigo']}}</td>
                              <td bgcolor="00cefc">{{trim($r['nombre'])}}</td> 
                              <td bgcolor="00cefc">{{trim($r['descripcion'])}}</td> 
                            </tr> 
                            <tr>
                              <table class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                  <tr>
                                    <th class="tt_det" colspan="5">Detalles</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Entradas</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Salidas</th>
                                    <th class="tt_det" colspan="3" style="text-align:center">Saldos</th> 
                                  </tr>
                                    <tr> 
                                        <th class="tt_det" width="10%">Fecha</th>
                                        <th class="tt_det" width="15%">Descripción</th> 
                                        <th class="tt_det" width="10%">Referencia</th>
                                        <th class="tt_det" width="10%">Tipo</th>
                                        <th class="tt_det" width="10%">Bodega</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                        <th class="tt_det t_det_r" width="5%">Cantidad</th>
                                        <th class="tt_det t_det_r" width="5%">Valor Unitario</th>
                                        <th class="tt_det t_det_r" width="5%">Total</th>
                                    </tr>
                                </thead> 
                                <tbody>
                                  <tr>
                                    <th colspan="4"></th>
                                    <th class="tt_det" width="5%">Saldo Anterior</th>
                                    <th class="tt_det t_det_r" width="5%">{{$r['tcantidad']}}</th>
                                    <th class="tt_det t_det_r" width="5%">{{number_format($r['tvaluni'],2,'.','')}}</th>
                                    <th class="tt_det t_det_r" width="5%">{{number_format($r['ttotal'],2,'.','')}}</th>
                                    <th colspan="6"></th>
                                  </tr>
                                  @php 
                                  $acum_cant = $r['tcantidad'];
                                  $acum_tvaluni = $r['tvaluni'];
                                  $acum_ttotal = $r['ttotal'];
                                  @endphp
                                  @foreach ($r['detales'] as $it)
                                  @php
                                      if($it['tipo']=='I'){
                                        $acum_cant += $it['cantidad'];
                                        $acum_tvaluni = $it['valor_unitario'];
                                        $acum_ttotal = $acum_cant*$acum_tvaluni;
                                      } else {
                                        $acum_cant -= $it['cantidad'];
                                        $acum_tvaluni = $it['valor_unitario'];
                                        $acum_ttotal += $acum_cant*$acum_tvaluni;
                                      }
                                      if ($acum_cant<0) {$acum_cant=0;}
                                  @endphp
                                      <tr>
                                        <td class="t_det">{{date('d/m/Y', strtotime($it['fecha']))}}</td>
                                        <td class="t_det">{{$it['descripcion']}}</td>
                                        <td class="t_det">{!!$it['referencia']!!}</td>
                                        <td class="t_det">@if($it['tipo']=='I') INGRESO @else EGRESO @endif</td>
                                        <td class="t_det">{{$it['bodega']}}</td>
                                        @if($it['tipo']=='I')
                                          <td class="t_det t_det_r">{{$it['cantidad']}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['valor_unitario'],2,'.','')}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['total'],2,'.','')}}</td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                        @else 
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r"> - </td>
                                          <td class="t_det t_det_r">{{$it['cantidad']}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['valor_unitario'],2,'.','')}}</td>
                                          <td class="t_det t_det_r">{{number_format($it['total'],2,'.','')}}</td>
                                        @endif
                                        <td class="t_det t_det_r">{{$acum_cant}}</td>
                                        <td class="t_det t_det_r">{{number_format($acum_tvaluni,2,'.','')}}</td>
                                        <td class="t_det t_det_r">{{number_format($acum_ttotal,2,'.','')}}</td>
                                      </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    </div>
    @endforeach