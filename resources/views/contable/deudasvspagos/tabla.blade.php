
              <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr >
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" ></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" ></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                  </tr>
                </thead>
                  <tbody>
                      @foreach($deudas as $values)
                          
                            @php
                              $suma= $values->retencion_totalr+$values->retenciontotaliv;
                            @endphp
                          <tr>
                              <td style="color: black; font-weight: bold; text-size: 30px;">@if(($values->compra_detalle)!=null) {{$values->compra_detalle}} @endif</td>
                              <td>@if(($values->compra_fecha)!=null) {{$values->compra_fecha}} @endif</td>
                              <td>COM-FA</td>
                              <td>@if(($values->compra_secuencia)!=null) {{$values->compra_secuencia}} @endif</td>
                              <td>$</td>
                              <td>@if(($values->compra_valor)!=null) {{$values->compra_valor}} @endif</td>
                              <td>@if(($values->compra_valor)!=null){{$values->compra_valor}}  @endif</td>
                              <td>0.00</td>
                              <td>@if(($values->compra_valor_f)!=null){{$values->compra_valor_f}}  @endif</td>
                          </tr>
                          <tr>
                              <td class="secundario">@if(($values->egreso_descripcion)!=null) {{$values->egreso_descripcion}} @endif</td>
                              <td >@if(($values->fecha_comprobante)!=null) {{$values->fecha_comprobante}} @endif</td>
                              <td>ACR-EG</td>
                              <td>@if(($values->egreso_secuencia)!=null) {{$values->egreso_secuencia}} @endif</td>
                              <td>$</td>
                              <td>@if(($values->egreso_valor)!=null) {{$values->egreso_valor}} @endif</td>
                              <td>0.00</td>
                              <td>@if(($values->egreso_valor)!=null) {{$values->egreso_valor}}  @endif</td>
                              <td>0.00</td>

                          </tr>
                          <tr>
                              <td class="secundario">@if(($values->retencion_descripcion)!=null) {{$values->retencion_descripcion}} @endif</td>
                              <td>@if(($values->fecha_retenciones)!=null) {{$values->fecha_retenciones}} @endif</td>
                              <td>ACR-EG</td>
                              <td>@if(($values->retenciones_secuencia)!=null) {{$values->retenciones_secuencia}} @endif</td>
                              <td>$</td>
                              <td>@if(($suma)!=null) {{$suma}} @endif</td>
                              <td>0.00</td>
                              <td>@if(($suma)!=null) {{$suma}}  @endif</td>
                              <td>0.00</td>
                          </tr>
                        
                      @endforeach

                  </tbody>
              </table>