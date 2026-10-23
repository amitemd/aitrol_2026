<style type="text/css">
    input{
        width: 100% !important; 
    }
</style>
<form method="POST" id="store_reporte_master" action="{{ route('afDepreciacion.store') }}">
    {{ csrf_field() }}
    
    <div class="box-body">
        @php 
            $fechahoy = date('Y-m-d'); 
        @endphp
        <div class="form-group col-md-4 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_asiento" class="texto col-md-4 control-label">Fecha Asiento:</label>
          <div class="col-md-8 px-0">
              @php
                   $fecha = date("d-m-Y H:i:s");
                   $fecha = explode(" ",$fecha);
                   $L = new DateTime($fecha[0]); 
                   $fin_dia = $L->format( 'Y-m-t' );
              @endphp
            <input type="date" name="fecha_asiento" id="fecha_asiento" class="form-control" value="{{$fin_dia}}">
          </div>
        </div>

        <div class="form-group col-md-8 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="det_asiento" class=" texto col-sm-2 control-label">Detalle:</label>
          <div class="col-md-10 px-0" >
            <input type="text" name="det_asiento" id="det_asiento" class="form-control" >
          </div>
        </div>
    </div>
    
    <br> <br>

    <b>Activos a depreciar</b>

    <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="row">
                <div class="table-responsive col-md-12">
                    <table id="tbl_depreciacion" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr class="well-dark">
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"></th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Compra</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Activo</th>
                                <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Descripción</th>
                                <th width="20%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                                <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Factura / Saldo</th>
                                <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Dias Compra</th>
                                <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Última depreciación</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vida Util</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tasa / Porcentaje</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Costo</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Depreciacion Acumulada</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo Actual</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor a Depreciar</th>
                                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo después de depreciar</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_detalles" name="tbl_detalles">
                            @php
                            $fhasta = new DateTime($fecha_hasta);
                            $cont1 = 0;
                            $count2 = 0;
                            $count3 = 0;
                            $count4 = 0; 
                            $count5 = 0; 
                            //dd($fhasta);
                            @endphp
                            @foreach ($activos as $value)
                            @php
                            


                            if(isset($value->ultima_depreciacion()->totaldepreciado)) {
                                $count2 += $value->ultima_depreciacion()->totaldepreciado + $value->depreciacion_acum;
                                

                            }else {
                                $count2 += $value->depreciacion_acum;
                            } 

                            


                            $cont1 += $value->costo;
                            $saldo = 0;
                            $valor_depreciar = 0;
                            $fecha_compra = new DateTime($value->fecha_compra);

                            $diff = $fecha_compra->diff($fhasta);

                            $days= $diff->days;

                            if($days >= 30){
                            $days = 30;
                            }

                            if($value->tasa==null){
                            $value->tasa = 0;
                            }

                            if(isset($value->ultima_depreciacion()->saldo)){
                                $saldo = $value->ultima_depreciacion()->saldo - $value->depreciacion_acum;
                            }else{
                            //$saldo = $value->costo - ((($value->costo * $value->tasa )/100));
                                $saldo = $value->costo - $value->depreciacion_acum;
                            }

                            /*if(isset($value->ultima_depreciacion()->totaldepreciado)) {
                            $valor_depreciar = (($value->ultima_depreciacion()->saldo * $value->tasa)/100);
                            }
                            else{
                            $valor_depreciar = (($value->costo * $value->tasa )/100);
                            }*/

                            $valor_depreciar = ((($value->costo * ($value->tasa/100))/360)*$days);

                            $count3 += round($saldo,2);

                            $count4 += round($valor_depreciar,2);
                            $count5 += round(($saldo - $valor_depreciar),2);
                            @endphp
                            <tr class="well">
                                <td>
                                    <input type="checkbox" id="id_activo{{ $value->id }}" class="form-check-input" name="id_activo[]" value="{{ $value->id }}" @if(@$value['validado']=="1" ) checked @endif>
                                    <input type="hidden" name="days[]" id="days{{ $value->id }}" value="{{$days}}">

                                </td>
                                <td>{{substr($value->fecha_compra,0,10)}}</td>
                                <td>{{ $value->codigo }}</td>
                                <td>{{ $value->nombre }}</td>
                                <td>{{ $value->descripcion }}</td>
                                <td>{{ $value->tipo->nombre }}</td>
                                <td style="text-align: center">@if($value->factura != null) FC @else SI @endif</td>
                                <td>{{$days}}</td>
                                <td>@if(isset($value->ultima_depreciacion()->created_at)) {{ date('d/m/Y', strtotime($value->ultima_depreciacion()->created_at)) }} @endif</td>
                                <td style="text-align: center">@if(!is_null($value->vida_util)) {{ $value->vida_util }} @else <span style="color: red;font-weight:bold">No tiene vida util</span> @endif </td>
                                <td style="text-align: right">@if($value->tasa > 0.0) {{ $value->tasa }} %  @else <span style="color: red;font-weight:bold">No tiene tasa</span> @endif </td>
                                <td style="text-align: right">@if($value->costo > 0.0) {{ number_format($value->costo,2,'.',',') }} @else <span style="color: red;font-weight:bold">No tiene costo</span> @endif</td>
                                <td style="text-align: right">
                                @if(isset($value->ultima_depreciacion()->totaldepreciado)) {{ number_format($value->ultima_depreciacion()->totaldepreciado + $value->depreciacion_acum,2,'.',',') }} 
                                    @else {{$value->depreciacion_acum}} @endif
                                </td>
                                <td style="text-align: right">{{ number_format($saldo,2,'.',',') }}</td>
                                <td style="text-align: right">{{ number_format($valor_depreciar,2,'.',',') }}</td>
                                <td style="text-align: right">{{ number_format(($saldo - $valor_depreciar),2,'.',',') }}

                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right">{{number_format($cont1,2,'.',',')}}</td>
                                <td style="text-align: right">{{number_format($count2,2,'.',',')}}</td>
                                <td style="text-align: right">{{number_format($count3,2,'.',',')}}</td>
                                <td style="text-align: right">{{number_format($count4,2,'.',',')}}</td>
                                <td style="text-align: right">{{number_format($count5,2,'.',',')}}</td>
                            </tr>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-2">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros {{count($activos)}} </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true,'tbl_detalles')" class="btn btn-default">
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                    </button>
                </div>
                <div class="col-xs-2">
                    <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false,'tbl_detalles')" class="btn btn-default">
                        <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                    </button>
                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-primary" id="btn_guardar">
                        <span class="glyphicon glyphicon-save" aria-hidden="true"></span>&nbsp; Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
 window.onload = function() {
    var date = new Date();
    var primerDia = new Date(date.getFullYear(), date.getMonth(), 1);
    var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    //let fecha_ultima = ultimoDia.getDate() +"/"+ (parseInt(date.getMonth())+1) +"/" +date.getFullYear();
    let fecha_ultima = `${date.getFullYear()}-${(parseInt(date.getMonth())+1)}-${ultimoDia.getDate()}`

    //document.getElementById('fecha_asiento').value = new Date(fecha_ultima);
    console.log(fecha_ultima)
 }


 
</script>