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
                            <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($retenciones as $value)
                                <tr>
                                    <td>{{$value->created_at}}</td>
                                    <td>@if(($value->secuencia)!=null) {{$value->secuencia}} @endif</td>
                                    <td>@if(($value->nro_comprobante)!=null) {{$value->nro_comprobante}} @endif</td>
                                    <td>ACR-RT</td>
                                    <td>@if(($value->proveedor)!=null) {{$value->proveedor->nombrecomercial}} @endif</td>
                                    <td>@if(($value->proveedor)!=null) {{$value->proveedor->id}} @endif</td>
                                    <td>@if(($value->descripcion)!=null){{$value->descripcion}}  @endif</td>
                                    <td>@if(($value->valor_fuente)!=null) {{$value->valor_fuente}}  @endif</td>
                                    <td>@if(($value->valor_iva)!=null) {{$value->valor_iva}}  @endif</td>
                                    <td>@if(($value->estado)==1) ACTIVO @else ANULADA @endif</td>
                                    <td>@if(($value->usuario)!=null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif</td>
                                    <td>@if(($value->estado)==0) {{$value->usuario->nombre1}} {{$value->usaurio->apellido1}} @else  @endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>