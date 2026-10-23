<style type="text/css">
    input {
        width: 100% !important;
    }
</style>
<form method="POST" id="store_reporte_master">
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
                <input type="date" name="fecha_asiento" id="fecha_asiento" onblur="validarCierre();" class="form-control validar" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{$fin_dia}}">
                <input type="hidden" id="fecha_hoy" value="{{$fechahoy}}">

            </div>
        </div>

        <div class="form-group col-md-8 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="det_asiento" class=" texto col-sm-2 control-label">Detalle:</label>
            <div class="col-md-10 px-0">
                <input type="text" name="det_asiento" id="det_asiento" class="form-control validar">
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-3">
            <a class="btn btn-info" type="button" href="{{route('activosfijos.depreciacion.index_depreciaciones')}}" target="_blank">Ver Depreciaciones</a>
        </div>
        <div class="col-md-3">
            <a class="btn btn-primary" type="button" href="{{route('depreciacion.excel_masivo_depreciacion')}}" target="_blank"> <i class="fa fa-download"></i> Listado General Dep.</a>
        </div>
    </div>
    <br> <br>

    <b>Activos a depreciar</b>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
            <div class="table-responsive col-md-12">
                <table id="tbl_depreciacion" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class="well-dark">
                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()"></th>
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
                        <?php
                        $fhasta = new DateTime($fecha_hasta);
                        $cont1 = 0;
                        $count2 = 0;
                        $count3 = 0;
                        $count4 = 0;
                        $count5 = 0;
                        //dd($fhasta);
                        foreach ($activos as $value) {
                            $sumDepreciacion = Sis_medico\AfDepreciacionCabecera::where('id_empresa', Session::get('id_empresa'))
                                ->where('af_depreciacion_cabecera.estado', 1)
                                ->join('af_depreciacion_detalle as add', 'af_depreciacion_cabecera.id', 'add.depreciacion_cabecera_id')->where('add.activo_id', $value->id)->sum('add.valordepreciacion');
                            // dd($sumDepreciacion);

                            // if (isset($value->ultima_depreciacion()->totaldepreciado)) {
                            //     $count2 += $value->ultima_depreciacion()->totaldepreciado + $value->depreciacion_acum;
                            // } else {
                            //     $count2 += $value->depreciacion_acum;
                            // }

                            if (!is_null($sumDepreciacion)) {
                                $count2 += $sumDepreciacion + $value->depreciacion_acum;
                            } else {
                                $sumDepreciacion = 0;
                                $count2 += $value->depreciacion_acum;
                            }

                            $new_saldo = $value->costo - $sumDepreciacion;

                            $cont1 += $value->costo;
                            $saldo = 0;
                            $valor_depreciar = 0;
                            $fecha_compra = new DateTime($value->fecha_compra);
                            $diff = $fecha_compra->diff($fhasta);
                            $days = $diff->days;
                            if ($days >= 30) {
                                $days = 30;
                            }
                            if ($value->tasa == null || !is_numeric($value->tasa)) {
                                $value->tasa = 0;
                                //dd($value->tasa);
                            }
                            //if($value->tasa==null || is_nan($value->tasa)){
                            //$value->tasa = 0;
                            //dd($value->tasa);
                            // }


                            // if (isset($value->ultima_depreciacion()->saldo)) {
                            //     $saldo = $value->ultima_depreciacion()->saldo - $value->depreciacion_acum;
                            // } else {
                            //     //$saldo = $value->costo - ((($value->costo * $value->tasa )/100));
                            //     $saldo = $value->costo - $value->depreciacion_acum;
                            // }

                            if ($new_saldo != 0) {
                                $saldo = $new_saldo - $value->depreciacion_acum;
                            } else {
                                //$saldo = $value->costo - ((($value->costo * $value->tasa )/100));
                                $saldo = $value->costo - $value->depreciacion_acum;
                            }

                            /*if(isset($value->ultima_depreciacion()->totaldepreciado)) {
                            $valor_depreciar = (($value->ultima_depreciacion()->saldo * $value->tasa)/100);
                            }
                            else{
                            $valor_depreciar = (($value->costo * $value->tasa )/100);
                            }*/
                            $valor_depreciar = ((($value->costo * ($value->tasa / 100)) / 360) * $days);
                            $count3 += round($saldo, 2);
                            $count4 += round($valor_depreciar, 2);
                            $count5 += round(($saldo - $valor_depreciar), 2);
                        ?>
                            <tr class="well">
                                <td>
                                    <input type="checkbox" onchange="cambiarEstado({{$value->id}})" id="id_activo{{ $value->id }}" class="form-check-input" name="id_activo[]" value="{{ $value->id }}">
                                    <input class="cdays" type="hidden" name="days[]" disabled id="days{{ $value->id }}" value="{{$days}}">
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
                                <td style="text-align: right">@if($value->tasa > 0.0) {{ $value->tasa }} % @else <span style="color: red;font-weight:bold">No tiene tasa</span> @endif </td>
                                <td style="text-align: right">@if($value->costo > 0.0) {{ number_format($value->costo,2,'.',',') }} @else <span style="color: red;font-weight:bold">No tiene costo</span> @endif</td>
                                <td style="text-align: right">
                                    @if($sumDepreciacion != 0) {{ number_format($sumDepreciacion + $value->depreciacion_acum,2,'.',',') }} @else {{$value->depreciacion_acum}} @endif
                                </td>
                                <td style="text-align: right">{{ number_format($saldo,2,'.',',') }}</td>
                                <td style="text-align: right">{{ number_format($valor_depreciar,2,'.',',') }}</td>
                                <td style="text-align: right">{{ number_format(($saldo - $valor_depreciar),2,'.',',') }}
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
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
                <!-- <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true,'tbl_detalles')" class="btn btn-default">
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
                    </button> -->
            </div>
            <div class="col-xs-3">
                <!-- <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false,'tbl_detalles')" class="btn btn-default">
                        <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
                    </button> -->
            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-primary" id="btn_guardar" onclick="guardarDepreciacion(event)">
                    <span class="glyphicon glyphicon-save" aria-hidden="true"></span>&nbsp; Guardar
                </button>
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
        //console.log(fecha_ultima)
    }

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_asiento").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            console.log(fechaInicial+'----'+fechaFinal);
            if (fechaFinal <= fechaInicial) {
                alertas("error", "Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.')
                // esto para ir al ultimo dia del mes
                fechaAc.setMonth(fechaAc.getMonth() + 1);
                fechaAc.setDate(0);
                //
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_asiento").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    const cambiarEstado = id => {
        let dias = document.getElementById("id_activo" + id);


        if (dias.checked) {
            document.getElementById("days" + id).disabled = false;

        } else {
            document.getElementById("days" + id).disabled = true;
        }
    }


    function guardarDepreciacion(e) {
        e.preventDefault();
        if (!validarDatos()) {

            $.ajax({
                type: 'post',
                url: "{{route('activosfijos.depreciacion.store_depreciacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#store_reporte_master').serialize(),
                success: function(data) {
                    if (data.respuesta == 'success') {
                        alertas(data.respuesta, data.respuesta, data.msj)
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        alertas(data.respuesta, data.respuesta, data.msj)
                    }

                },
                error: function(data) {

                }
            });
        } else {
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }

    }
</script>