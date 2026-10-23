{{-- <b>Detalles de valores a dépositar:</b> --}}
<div id="contenedor">

    <div class="form-group col-xs-12 px-1">
        <div class="col-md-12 px-0">
            <label for="id_cuenta_origen" class="label_header">Detalle de valores recibidos a depositar</label>
        </div>
        <div class="col-md-12 px-0">
            {{-- <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" required>
                <option value="">Seleccione...</option>
                @foreach($bancos as $value)
                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                @endforeach
            </select> --}}
        </div>
    </div>
    <div class="form-group col-xs-12 px-1">
        <div class="col-md-4 px-0">
            <label for="id_forma_pago" class="label_header">Fecha desde</label>
            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" onchange="buscar_forma_pago()" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-4 px-0">
            <label for="id_forma_pago" class="label_header">Fecha hasta</label>
            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" onchange="buscar_forma_pago()" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-4 px-0">
            <label for="id_forma_pago" class="label_header">Filtrar por:</label>
            <select class="js-example-basic-multiple" style="width:100%;" name="id_forma_pago[]" multiple="multiple" id="id_forma_pago" onchange="buscar_forma_pago()" @if(isset($detalles)) disabled @endif required>
                <option value="">Seleccione...</option>
                <option value="T">TODO</option>
                <option value="-1">GENERAL</option>

                @foreach($formas_pago as $value)

                    <option value="{{$value->id}}">{{$value->nombre}}</option>

                @endforeach

            </select>
        </div>
    </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_ventas" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class="well-dark">
                <th width="3%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id</th>
                <th width="3%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id Asiento</th><!--Id Comprobante Ingreso-->
                <th width="3%" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo C.</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Factura</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Referencia</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Banco</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                <th width="15%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cliente</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Div</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Importe</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Base</th>
                </tr>
            </thead>
            <tbody id="tbl_detalles" name="tbl_detalles">
                @if(isset($detalles))
                        @php
                            if(Auth::user()->id == '0922729587' ){
                                //dd($detalles);
                            }
                        @endphp
                    @foreach($detalles as $value)
                    @php
                    $id = DB::table('ct_detalle_pago_ingreso')
                        ->leftjoin('ct_comprobante_ingreso as i' ,'i.id','ct_detalle_pago_ingreso.id_comprobante')
                        ->where('ct_detalle_pago_ingreso.id', $value->id_ingreso)->first();
                    @endphp
                        @if(!is_null($id))
                        <tr>
                            <!-- <td></td> -->
                            <td>{{$id->id_comprobante}} </td>
                            <!--<td>{{$value->tipo_c}}</td>-->
                            <td>{{ $id->id_asiento_cabecera }}</td>
                            <td>{{ $value->id_ingreso }}</td>                            
                            <td>@if(isset($value->tipo_pago)){{ $value->tipo_pago->nombre }} @endif</td>
                            <td>{{ date('d/m/Y', strtotime($value->fecha)) }}</td>
                            {{-- @if(isset())
                            @endif --}}
                            <td>{{$value->facturas}}</td>
                            <td>{{ $value->cheque }}</td>
                            @if(isset($value->banc->nombre))
                            <td>{{ $value->banc->nombre }} </td>
                            @else
                            <td></td>
                            @endif
                            
                            <td>{{ $value->cuenta }}</td>
                            <td>{{ $value->girador }}</td>
                            <td>$</td>
                            <td>{{ number_format($value->importe,2,'.','') }}</td>
                            <td>{{ number_format($value->valor,2,'.','') }}</td>
                            <td>{{ number_format($value->valor_base,2,'.','') }}</td>
                        </tr>
                        @else
                        <tr>
                            <td></td>
                        </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
            <tfoot>
            </tfoot>
            </table>
        </div>
        </div>

    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
</script>
