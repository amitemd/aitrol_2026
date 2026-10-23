<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .vtdobra {
        background-color: #eafcff;

        padding: 0;
    }
    .vtdobra2 {
        background-color: #eafcff;

        padding: 0;
    }
    .select2 {
        width: 100% !important;
        background-color: #eafcff !important;
    }
    .vt_middle{
        vertical-align: middle;
    }
    .borde {
      border-radius: 6px;
    }
    .letra_tabla{
        font-size: 14px;
    }

/* boton agregar*/

.boton {
  background-color: #D3EEFF;
  color: black;
  border: #8DCCF5 0.2em solid;
  border-radius: 11px;
  /*text-align: right;*/
  /*font-family: sans-serif;*/
}


.text {
  margin: 0 1.5em
}

<?php
    $porcentajeIva = $porIva->iva * 100;
?>

</style>
<input type="hidden" name="id_nota" id="id_nota" value="{{ $nota_entrega->id }}">
<div class="panel panel-default" >
    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
        <thead >
            <tr class='well' style="color: black; background-color: #D3EEFF;">
                <th style="text-align: center;" width="64%" tabindex="0">Articulo</th>
                <th style="text-align: center;" width="10%" tabindex="0">Cantidad Recetada</th>
                <th style="text-align: center;" width="10%" tabindex="0">Cantidad a Entregar</th>
                <th style="text-align: center;" width="5%" tabindex="0">Precio</th>
                <th style="text-align: center;" width="5%" tabindex="0">Valor Total</th>
                <th style="text-align: center;" width="5%" tabindex="0">IVA</th>
                <th style="text-align: center;" width="5%" tabindex="0">Accion</th>
            </tr>
        </thead>
        <tbody >
            @foreach($detalles as $detalle)
                <tr>
                    <td>
                        <div class="form-group" style="margin-bottom: 1px;">
                            <label>{{ $detalle->nombre_medicina }}</label><br>
                            
                                    @php
                                    $id_empresa = Session::get('id_empresa');
                                    $ct_productos = null;
                                    $producto = null;
                                    $inv_serie = [];
                                    $cantidad = $detalle->cantidad;
                                    $id_agenda = $nota_entrega->historia->agenda->id;
                                    $id_hc_procedimiento = Sis_medico\hc_procedimientos::where('id_hc', $nota_entrega->id_hc)->first()->id;

                                    

                                    //dd($planilla_det);

                                    $cantidad_uso = 1;
                                    $id_movimiento_paciente = null;
                                    $medicina_tarifario = Sis_medico\Medicina_tarifario::where('id_medicina', $detalle->id_medicina)->first();
                                    $ap_procedimiento = $medicina_tarifario->ap_procedimiento;
                                    @endphp

                                    @if(!is_null($ap_procedimiento))
                                      @php $ct_productos = Sis_medico\Ct_productos::find($ap_procedimiento->id_producto); @endphp
                                    @endif

                                    @if(!is_null($ct_productos))
                                       @php $producto = Sis_medico\Producto::where('codigo', $ct_productos->codigo)->first(); @endphp
                                    @endif

                                    @if(!is_null($producto))
                                       @php $inv_serie = Sis_medico\InvInventarioSerie::where('id_producto', $producto->id)
                                       ->join('producto as p', 'inv_inventario_serie.id_producto', 'p.id')
                                       ->join('bodega as b', 'inv_inventario_serie.id_bodega', 'b.id')
                                        ->where('inv_inventario_serie.id_bodega', $bodegas->id)
                                        ->where('inv_inventario_serie.id_empresa', $id_empresa)
                                        ->where('inv_inventario_serie.estado', 1)
                                        ->where('inv_inventario_serie.existencia', '!=', 0)
                                        ->orderBy('inv_inventario_serie.created_at', 'asc')
                                        ->select('inv_inventario_serie.*', 'p.nombre as nombre_medicina', 'b.nombre as nombre_bodega','p.codigo', 'b.id as id_bodega')
                                        ->get(); @endphp
                                    @endif

                                    @if($inv_serie != [])
                                        <table class='table' >
                                            <thead>
                                             <!-- <tr style='background-color: #637FFF; color: black;'>
                                                <th colspan='8' style='text-align: center; font-size: 16px;' >Existencias en Inventario</th>
                                             </tr> -->
                                             <tr>
                                                <th class="letra_tabla">Codigo</th>
                                                <th class="letra_tabla">Nombre</th>
                                                <th class="letra_tabla">Bodega</th>
                                                <th class="letra_tabla">Lote</th>
                                                <th class="letra_tabla">Serie</th>
                                                <th class="letra_tabla">Fecha Vencimiento</th>
                                                <th class="letra_tabla">Existencia</th>
                                                <th class="letra_tabla">Cantidad</th>
                                                <!-- <th class="letra_tabla">Accion</th> -->
                                             </tr>
                                            </thead>
                                            @php 
                                                $planilla_cab = Sis_medico\Planilla_Cabecera::where('id_agenda', $id_agenda)->where('id_hc_procedimiento', $id_hc_procedimiento)->where('nota_entrega', 1)->where('estado', 1)->first();
                                            @endphp
                                            @foreach($inv_serie as $inv)
                                                @if(!is_null($planilla_cab))
                                                    @php 
                                                        $planilla_det = Sis_medico\Planilla_Detalle::where('id_planilla_cabecera', $planilla_cab->id)->where('codigo', $inv->codigo)->where('id_bodega', $inv->id_bodega)->where('serie', $inv->serie)->where('lote', $inv->lote)->where('check', 1)->where('estado', 1)->first(); 
                                                    @endphp
                                                @endif
                                                <tbody>
                                                    <input type="hidden" name="codigo{{$inv->id}}" id="codigo{{$inv->id}}" value="{{$inv->codigo}}">
                                                    <input type="hidden" name="id_bodega{{$inv->id}}" id="id_bodega{{$inv->id}}" value="{{$inv->id_bodega}}">
                                                    <input type="hidden" name="lote{{$inv->id}}" id="lote{{$inv->id}}" value="{{$inv->lote}}">
                                                    <input type="hidden" name="serie{{$inv->id}}" id="serie{{$inv->id}}" value="{{$inv->serie}}">
                                                    <input type="hidden" name="fecha_vence{{$inv->id}}" id="fecha_vence{{$inv->id}}" value="{{$inv->fecha_vence}}">
                                                    <input type="hidden" name="existencia{{$inv->id}}" id="existencia{{$inv->id}}" value="{{$inv->existencia}}">

                                                 <tr>
                                                    <td class="letra_tabla">{{$inv->codigo}}</td>
                                                    <td class="letra_tabla">{{$inv->nombre_medicina}}</td>
                                                    <td class="letra_tabla">{{$inv->nombre_bodega}}</td>
                                                    <td class="letra_tabla">{{$inv->lote}}</td>
                                                    <td class="letra_tabla">{{$inv->serie}}</td>
                                                    <td class="letra_tabla">{{$inv->fecha_vence}}</td>
                                                    <td class="letra_tabla">{{$inv->existencia}}</td>
                                                    <td style="width: 1%">
                                                     <input  type='text' class='borde letra_tabla' id='cantidad_inv{{$inv->id}}' @if(!is_null($planilla_det)) value='{{$planilla_det->cantidad}}' @else value='0' @endif onkeypress="return soloNumeros(this);" placeholder="Cantidad a descontar" onchange = "agregarDetalle('{{$inv->id}}', '{{$nota_entrega->id}}')" @if($nota_entrega->estado == '1') readonly @endif> 
                                                    </td>
                                                 </tr>
                                                </tbody>
                                            @endforeach
                                        </table>
                                        <center>
                                           @if($nota_entrega->estado == '-1')
                                            <a style="padding: 0.5%; border-bottom-width: 5px ; " class="boton " onclick='guardarDetalle("{{$detalle->id}}", "{{$planilla_cab->id}}", "{{$inv->codigo}}", "{{$detalle->cantidad}}")' ><span class="glyphicon glyphicon-arrow-right" style="font-size: 15px"> Agregar</span> </a>
                                            @endif
                                        </center> 
                                    @else
                                        <label style="color: red">No existe medicina en Inventario</label>
                                    @endif
                            
                            <input class="id_producto" type="hidden" name="id_producto" id="id_producto{{$detalle->id}}" value="{{ $detalle->id_medicina}}">
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="form-group">
                            <input readonly type="text" class="form-control vtdobra" name="cantidad_receta{{$detalle->id}}" id="cantidad_receta{{$detalle->id}}" value="{{$detalle->cantidad_receta}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="form-group">
                            <input readonly type="text" class="form-control vtdobra" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="form-group">
                            <input readonly type="text" class="form-control vtdobra" name="precio{{$detalle->id}}" id="precio{{$detalle->id}}" value="{{number_format($detalle->precio, 4, '.', ' ')}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                        </div>
                    </td>

                    <td style="vertical-align: middle; text-align: right;">
                        <span id="valor_neto{{$detalle->id}}">${{number_format($detalle->total, 4, ',', ' ')}}</span>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        <input type="hidden" name="iva{{$detalle->id}}" id="iva{{$detalle->id}}" value="{{$detalle->iva}}" >
                        <span id="valor_iva{{$detalle->id}}">${{number_format($detalle->iva, 4, ',', ' ')}}</span>
                    </td>
                    <td style="vertical-align: middle; text-align: center;">
                        @if($nota_entrega->estado != 1)<button type="button" class="btn btn-danger" onclick="eliminar_detalle('{{ $detalle->id }}')"><i class="fa fa-trash"></i></button>@endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td>
                    @if($nota_entrega->estado != 1)
                    <div class="form-group" style="display: none;">
                        <select id="producto_nuevo" name="producto_nuevo" class="form-control select2_productos" required placeholder="Seleccione una medicina..." onchange="seleccionar_producto();">
                        </select>
                    </div>
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
        <thead>
            <tr style="display: none;">
                <th width="35%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="10%" tabindex="0"></th>
                <th width="5%" tabindex="0"></th>
                <th width="10%" tabindex="0">
                </th>
            </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot class='well'>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-right">Subtotal {{$porcentajeIva}}%</td>
                <td id="subtotal_12" class="text-right px-1">${{ number_format($nota_entrega->subtotal_12, 4, ',', ' ') }}</td>
                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-right">Subtotal 0%</td>
                <td id="subtotal_0" class="text-right px-1">${{ number_format($nota_entrega->subtotal_0, 4, ',', ' ') }}</td>
                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-right">Subtotal sin Impuesto</td>
                <td id="subtotal_0" class="text-right px-1">${{ number_format($nota_entrega->subtotal_0, 4, ',', ' ') }}</td>
                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-right">Tarifa Iva {{$porcentajeIva}}%</td>
                <td id="tarifa_iva" class="text-right px-1">${{ number_format($nota_entrega->iva, 4, ',', ' ') }}</td>
                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-right"><strong>Total</strong></td>
                <td id="total" class="text-right px-1">${{ number_format($nota_entrega->total, 4, ',', ' ') }}</td>
                <input type="hidden" name="total1" id="total1" class="hidden">
            </tr>
        </tfoot>
    </table>
</div>
<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script type="text/javascript">
    $('.select2_productos').select2({
        placeholder: "Seleccione una medicina...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("nota_entrega.buscarMedicinas")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });

    function agregarDetalle(id, id_nota_entrega) 
    {
        var codigo = $('#codigo'+ id).val();
        var id_bodega = $('#id_bodega'+ id).val();
        var fecha_vence = $('#fecha_vence'+ id).val();
        var lote = $('#lote'+ id).val();
        var serie = $('#serie'+ id).val();
        var existencia = $('#existencia'+ id).val();
        var cantidad_inv = $('#cantidad_inv'+ id).val();
        if (cantidad_inv == "") {
            $('#cantidad_inv'+id).val(0);       
        }
        var cantidad_inv = parseInt($('#cantidad_inv'+ id).val());
        if(cantidad_inv > existencia){
            Swal.fire({
                title: "Advertencia!",
                icon: 'info',
                html: 'La cantidad no debe ser mayor a la existencia'
            });
            $('#cantidad_inv'+id).val(0);
        }else{
            $.ajax({
                url: `{{ route('nota_entrega.guardar_planilla_detalle') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': `{{ csrf_token() }}`
                },
                data: {
                    codigo,
                    id_bodega,
                    lote,
                    serie,
                    cantidad_inv,
                    id_nota_entrega,
                    fecha_vence 
                },
                success: function(data) { 
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }
    }

    function guardarDetalle(id, id_planilla, codigo, cantidad)
    {
        $.ajax({
            url: `{{ route('nota_entrega.agregar_stock_medicina') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            data: {
                codigo,
                id_planilla,
                id,
                cantidad
            },
            success: function(data) {
                if(data.status === 'error'){
                    alertas(data.status, `${data.status.toUpperCase()}...`, data.message);
                }else{
                    $('#cantidad'+id).val(data);
                    Swal.fire({
                        title: "Correcto",
                        icon: 'success',
                        html: '!Cantidad agregada!'
                    });
                    actualizar_valor(id);
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
</script>
