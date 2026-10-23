<script>
    const agregarFilaNueva = (data, i) => {
        var codigo = '';
        var cantidad = 0;
        var precio = 0;
        var descuento = 0;
        var precioNeto = 0;
        var nombre = '';
        var id_inv_serie = '';
        var cajaPrecio = `<select name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                <option value="0"></option>
                          </select>
                          <button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp">
                            <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                         </button>`;
        var verificador = 'onchange="verificar(this)"';
        try {
            codigo = data.codigo !== 'undefined' ? data.codigo : '';
            cantidad = data.cantidad !== 'undefined' ? data.cantidad : 0;
            precio = data.precio !== 'undefined' ? data.precio : 0;
            cajaPrecio = '<input type="text" class="form-control pneto" value="' + precio + '" name="precio[]" onblur="this.value=parseFloat(this.value).toFixed(4);" style="width:40%;display:inline;height:20px;"><button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
            descuento = data.descuento !== 'undefined' ? parseFloat(data.descuento).toFixed(2) : 0;
            precioNeto = parseFloat((cantidad * precio) - descuento).toFixed(2);
            nombre = data.nombre !== 'undefined' ? data.nombre : '';
            id_inv_serie = data.id_inv_serie !== 'undefined' ? data.id_inv_serie : '';
            // verificador = '';
        } catch (error) {
            codigo = '';
            cantidad = 0;
            precio = 0;
            cajaPrecio = '<select name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required><option value="0"></option></select><button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
            descuento = 0;
            precioNeto = 0.00;
            nombre = '';
            // verificador = 'onchange="verificar(this)"';
            id_inv_serie = '';
        }
        let contador_filas = document.getElementById('contador').value;
        let fila = `
            <tr class="wello" id="oculto_${contador_filas}"></tr>
            <tr class="wello">
                            <td style="max-width:100px;">
                                <input type="hidden" name="codigo[]" class="codigo_producto producto_${contador_filas}" value="${codigo}" />
                                <input type="hidden" name="id_inv_serie[]" class="inv_serie_${contador_filas}" value="${id_inv_serie}" />
                                <select name="nombre[]" id="nombre${contador_filas}" class="form-control select2_cuentas" style="width:100%" required ${verificador}">
                                    <option> </option>
                                    @foreach ($productos as $value)
                                    <option value="{{ $value->nombre }}" data-name="{{ $value->nombre }}" data-codigo="{{ $value->codigo }}" data-descuento="{{ $value->mod_desc }}" data-precio="{{ $value->mod_precio }}" data-maxdesc="{{ $value->descuento }}" data-iva="{{ $value->iva }}">
                                        {{ $value->codigo }} | {{ $value->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" id="descripcion_${contador_filas}" maxlength="299" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto cantidad_${contador_filas}" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${cantidad}" name="cantidad[]" onchange="verificar_stock(${contador_filas})" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega bodega_${contador_filas}" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(${contador_filas})">
                                    <option> </option>
                                    @foreach ($bodega as $value)
                                    @if (!is_null($value))
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td id="tprecio">
                                ${cajaPrecio}
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(4);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(4);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="${descuento}" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right luffy" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="${precioNeto}" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form iva_${contador_filas}"  type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray btn_delete boton_desh delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                                <a href="javascript:;" id="btnAgregarPublico${contador_filas}" class="btn btn-info btn-gray" title="Sector público" onclick="agregarDatoPublico('${contador_filas}');"><i class="fa fa-university"></i></a>
                            </td>
                        </tr>`;
        document.getElementById('contador').value = parseInt(contador_filas) + 1;
        //$('#nombre').val(data.nombre).trigger('change');
        return [fila, contador_filas, nombre];
    }
    // const verificar_stock2 = id => {
    //       let class_producto = document.querySelector('.class_producto' + id).value;
    //       let class_cantidad = document.querySelector('.class_cantidad' + id).value;
    //       let class_bodega = document.querySelector('.class_bodega' + id).value;

    //       $.ajax({
    //             url: `{{ route('nuevokardex.verificarKardex') }}`,
    //             type: 'get',
    //             data: {
    //                   'id_bodega': class_bodega,
    //                   'cantidad': class_cantidad,
    //                   'id_producto': class_producto
    //             },
    //             datatype: 'json',
    //             success: function(data) {
    //                   if (data.status == 'error') {
    //                         document.querySelector('.class_bodega' + id).value = null;
    //                         swal("Error!", `${data.msj.toUpperCase()}`, "error");
    //                   }
    //             },
    //             error: function(data) {
    //                   swal("Error!", `Ocurrio un error...`, "error");
    //             }
    //       })

    // }
    function agregarDatoPublico(id) {
        var filaPublico = `<td style="max-width:100px;">
        <input type="text" class="form-control" style="width:95%" id="codigo_publico" name="codigo_publico[]" autocomplete="off" autofocus="false" placeholder="Código público" />&nbsp;&nbsp;&nbsp;
        </td>
        <td colspan="7">
        <input type="text" class="form-control" style="width:98%" id="descripcion_publico" name="descripcion_publico[]" maxlength="300" autocomplete="off" autofocus="false" placeholder="Agregar descripción MAX 300 CARACTERES" />
        </td>
        <td><a href="javascript:;" id="btnQuitarPublico${id}" onclick="quitarDatoPublico('${id}')" class="btn btn-danger btn-gray"><i class="fa fa-minus"></i></a></td>`;
        $('#oculto_' + id).html(filaPublico);
        $('#btnAgregarPublico' + id).hide();
    }

    function quitarDatoPublico(id) {
        $('#oculto_' + id).html('');
        $('#btnAgregarPublico' + id).show();
        $('#btnQuitarPublico' + id).hide();
    }
</script>
