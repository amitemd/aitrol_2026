@if(isset($plantilla) and $plantilla==1)
    @if (!isset($serie[0]->id))
        <script>
            Swal.fire("Error: ", "No se encontro existencia en bodega.", "error");
        </script>
    @else
        @foreach($serie as $x)
        <div class="panel panel-default details" id="heading_details">
            <div class="panel-heading">
                <div class="row titulo">
                    @php
                    // if (isset($serie))
                    // $p= \Sis_medico\InvInventario::find($inventario[0]->id_inv_inventario);
                    @endphp

                    <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
                    <div class="col-md-9" style="text-align: left;">
                        <label class="col-md-9"> @if(isset($x->inventario)) {{$x->inventario->producto->codigo}} | {{$x->inventario->producto->nombre}} @else {{$id}} @endif </label>
                    </div>
                    <div class="col-md-2" style="text-align: right;">
                        <button type="button" class="btn btn-danger des">
                            <i class="fa fa-remove"></i>
                        </button>
                    </div>
                    <input type="hidden" class="product" value="{{$x->serie}}">

                </div>
            </div>
            <div class="panel-body" style="padding:0;">
                <div class="col-md-12 table-responsive " style="padding:0 !important;">
                    <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                        <thead>
                            <tr>

                                <th tabindex="0">Cantidad</th>
                                <th tabindex="0">Serie</th>
                                <th tabindex="0">Lote</th>
                                <th tabindex="0">Fecha Vence</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($x as $x) --}}
                            <tr>
                                <input type="hidden" name="cantidad_usos_kardex[]" value="{{$x->cantidad_usos_kardex}}">
                                <td> <input type="hidden" name="id[]" value="{{$x->id_producto}}"> <input type="number" style="width: 80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]" required value="1" onchange="existenciax(this)"> <input type="hidden" name="existencia" class="existencia" value="{{($x->existencia)}}"> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="serie[]" value="{{$x->serie}}" readonly> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="lote[]" value="{{$x->lote}}" readonly onchange="lote(this)"> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="vence[]" value="{{$x->fecha_vence}}" readonly>
                                    <input type="hidden" name="precio[]" value="{{$x->inventario->costo_promedio}}">
                                </td>
                                <td style="text-align: right;"> <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()"> <i class="fa fa-trash"></i></button> </td>
                            </tr>
                            {{-- @endforeach --}}
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

        @endforeach
    @endif
@elseif(@$pedido==1)
    @if ($serie == '[]')
        <script>
            Swal.fire("Error: ", "No se encontron datos para ese pedido.", "error");
        </script>
    @else
        @php $Key = 1; @endphp
        @foreach ($serie as $row)
            @php
                # traslado #
                $traslado = 0;
                $traslado = \Sis_medico\Pedido::cant_traslado($row->serie);
                $pendiente = $row->cantidad - $traslado;
            @endphp
            @if ($pendiente>0)
                <div class="panel panel-default details" id="heading_details">
                    <div class="panel-heading">
                        <div class="row titulo">
                            <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
                            <div class="col-md-9" style="text-align: left;">
                                <label class="col-md-10"> @if(isset($row->producto)) {{$row->producto->codigo}} | {{$row->producto->nombre}} | PEDIDO: {{$data->pedido}} @endif </label>
                            </div>
                            <div class="col-md-2" style="text-align: right;">
                                <button type="button" class="btn btn-danger des">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </div>
                            <input type="hidden" class="product" value="{{$row->serie}}">

                        </div>
                    </div>
                    <div class="panel-body" style="padding:0;">
                        <div class="col-md-12 table-responsive " style="padding:0 !important;">
                            <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                    <tr>
                                        <th tabindex="0">Cantidad</th>
                                        <th tabindex="0">Serie</th>
                                        <th tabindex="0">Lote</th>
                                        <th tabindex="0">Fecha Vence</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($serie as $x) --}}

                                    @php
                                    $inv_serie = \Sis_medico\InvInventarioSerie::where('serie', $row->serie)
                                    ->where('id_bodega', $id_bodega)
                                    ->where('estado',1)
                                    ->where('existencia','!=',0)
                                    ->orderBy('created_at', 'Desc')
                                    ->first();
                                    @endphp
                                    <tr id="del{{$Key}}{{$row->id}}">
                                        @if (isset($inv_serie->id))<input type="hidden" name="cantidad_usos_kardex[]" value="{{$inv_serie->cantidad_usos_kardex}}">@endif
                                        <td> <input type="hidden" name="id[]" value="{{$row->id_producto}}"> <input type="number" style="width: 80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]" required value="{{$pendiente}}" onchange="existenciax(this)">
                                            <input type="hidden" name="existencia" class="existencia" @if (isset($inv_serie->id)) value="{{($inv_serie->existencia)}}"> @else value="0" @endif
                                        </td>
                                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="serie[]" value="{{$row->serie}}" readonly> </td>
                                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="lote[]" value="{{$row->lote}}" onchange="lote(this)" readonly> </td>
                                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="vence[]" value="{{$row->fecha_vencimiento}}" readonly>
                                            <input type="hidden" name="precio[]" value="{{$row->precio}}">
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex;justify-content: space-around;">
                                                <input onclick="disabledTr({{$Key}}{{$row->id}})" type="checkbox" id="check{{$Key}}{{$row->id}}" style="width:55px;" checked>
                                                <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- @endforeach --}}
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                @php $Key++; @endphp
            @endif
        @endforeach
    @endif

@else

    @if (!isset($serie->id))
        <script>
            Swal.fire("Error: ", "No se encontro existencia en bodega.", "error");
        </script>
    @else
        <div class="panel panel-default details" id="heading_details">
            <div class="panel-heading">
                <div class="row titulo">
                    @php
                    // if (isset($serie))
                    // $p= \Sis_medico\InvInventario::find($inventario[0]->id_inv_inventario);
                    @endphp
                    <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
                    <div class="col-md-9" style="text-align: left;">
                        <label class="col-md-10"> @if(isset($serie->inventario)) {{$serie->inventario->producto->codigo}} | {{$serie->inventario->producto->nombre}} @else {{$id}} @endif </label>
                    </div>
                    <div class="col-md-2" style="text-align: right;">
                        <button type="button" class="btn btn-danger des">
                            <i class="fa fa-remove"></i>
                        </button>
                    </div>
                    <input type="hidden" class="product" value="{{$serie->serie}}">

                </div>
            </div>
            <div class="panel-body" style="padding:0;">
                <div class="col-md-12 table-responsive " style="padding:0 !important;">
                    <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                        <thead>
                            <tr>

                                <th tabindex="0">Cantidad</th>
                                <th tabindex="0">Serie</th>
                                <th tabindex="0">Lote</th>
                                <th tabindex="0">Fecha Vence</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($serie as $x) --}}

                            @php
                            $movimiento = \Sis_medico\Movimiento::where('serie', $serie->serie)->orderBy('created_at', 'Desc')->first();
                            //dd($movimiento);
                            if(Auth::user()->iid =="0957258056"){
                            // dd($movimiento);
                            }
                            @endphp
                            <tr>
                                <td> <input type="hidden" name="cantidad_usos_kardex[]" value="{{$serie->cantidad_usos_kardex}}"> <input type="hidden" name="id[]" value="{{$serie->id_producto}}"> <input type="number" style="width: 80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]" required value="1" onchange="existenciax(this)"> <input type="hidden" name="existencia" class="existencia" value="{{($serie->existencia)}}"> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="serie[]" value="{{$serie->serie}}" readonly> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="lote[]" value="{{$serie->lote}}" onchange="lote(this)" readonly> </td>
                                <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="vence[]" value="{{$serie->fecha_vence}}" readonly>
                                    <input type="hidden" name="precio[]" value="{{$serie->inventario->costo_promedio}}">
                                </td>
                                <td style="text-align: right;"> <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()"> <i class="fa fa-trash"></i></button> </td>
                            </tr>
                            {{-- @endforeach --}}
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    @endif

@endif
