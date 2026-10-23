<div class="panel panel-default">
    <div class="col-md-12" style="margin-top: 10px;">
        <div class="row">
            <div class="col-md-10">
            <span style="font-family: Helvetica; font-weight: bold; font-style: italic; padding-top:10px; margin-left: 4px; color: #534f4f;">RECIBO DE COBRO # {{$id}}   -  @if(isset($recibo->agenda->historia_clinica->hc_procedimiento))  {{$recibo->agenda->historia_clinica->hc_procedimiento->hc_procedimiento_final->procedimiento->nombre}} @endif</span>
            </div>
            <div class="col-md-2" style="text-align: right;">
                <button class="btn btn-danger" type="button" onclick="eliminar_recibo(this)" > <i class="fa fa-trash"></i> </button>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        &nbsp;
        <input type="hidden" name="recibo[]" value="{{$recibo->id}}">
        <input type="hidden" name="cliente[]" class="clientes" data-id="{{$recibo->identificacion}}" data-nombre="{{$recibo->cliente->nombre}}" data-ciudad="{{$recibo->ciudad}}" data-email="{{$recibo->email}}" data-telefono="{{$recibo->telefono}}" data-direccion="{{$recibo->direccion}}" data-ciudad="{{$recibo->ciudad}}" value="{{$recibo->id}}" >
    </div>
    <div class="panel-body" style="padding:0;">

        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class='well-dark'>
                    <th width="35%" tabindex="0">Descripci&oacute;n del Producto</th>
                    <th width="10%" tabindex="0">Cantidad</th>
                    <th width="10%" tabindex="0">Precio</th>
                    <th width="10%" tabindex="0">Cobrar Seguro</th>
                    <th width="10%" tabindex="0">% Desc</th>
                    <th width="10%" tabindex="0">Descuento</th>
                    <th width="10%" tabindex="0">Precio Neto</th>
                    <th width="5%" tabindex="0">IVA</th>
                    <th>Accion</th>
                </tr>
            </thead>

            <tbody class="agregar_cuentas">
                @php
                $contadore=1;
                //dd($recibo);
                @endphp
                @foreach($recibo->detalles as $z)
                @php 
                    //dd($z);
                @endphp
                <tr class="well">
                    
                    <td style="max-width:100px;">
                             @php
                            $prod = DB::table('ct_productos')->where("codigo", $z->cod_prod)->where("id_empresa", $empresa->id)->first();
                            
                            @endphp
                        <input type="hidden" name="codigo[]" class="codigo_producto" @if($prod!=null) value="{{$prod->codigo}}" @else value="{{$z->cod_prod}}" @endif />
                        <select name="nombre[]" class="form-control select" style="width:100%" required onchange="verificar(this)">
                            <option> </option>
                          
                            @if(!is_null($prod))
                            <option value="{{$prod->nombre}}" selected="selected" data-name="{{$prod->nombre}}" data-codigo="{{$prod->codigo}}" data-descuento="{{$prod->mod_desc}}" data-precio="{{$prod->mod_precio}}" data-maxdesc="{{$prod->descuento}}" data-iva="{{$prod->iva}}">{{$prod->codigo}} | {{$prod->descripcion}}</option>
                            @else 
                             @foreach($productos as $p)
                             <option value="{{$p->nombre}}" selected="selected" data-name="{{$p->nombre}}" data-codigo="{{$p->codigo}}" data-descuento="{{$p->mod_desc}}" data-precio="{{$p->mod_precio}}" data-maxdesc="{{$p->descuento}}" data-iva="{{$p->iva}}">{{$p->codigo}} | {{$p->descripcion}}</option>
                             @endforeach
                            @endif


                        </select>
                        <textarea rows="3" wrap="hard" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                        <input type="hidden" name="iva[]" class="iva" />
                    </td>
                    <td>
                        <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$z->cantidad}}" name="cantidad[]" required>
                    </td>
                    <td id="tprecio">
                       <!--  <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                            <option value="0"> </option>
                        </select>
                        <button type="button" class="btn btn-info btn-gray btn-xs cp">
                            <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                        </button> -->
                        <input type="text" class="form-control pneto" name="precio[]" style="width:60%;height:20px;display:inline;"  value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" >
                    </td>
                    <td>
                        <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                    </td>
                    <td>
                        <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                        <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                    </td>
                    <td>
                        <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                    </td>
                    <td>

                        <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                    </td>
                    <td>
                        <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-gray delete">
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
                @php
                $contadore++;
                @endphp
                @endforeach


            </tbody>

        </table>

    </div>
</div>