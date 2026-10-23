<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 300px;
        width: 25%;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000px;
        font-size: 11px;
        float: left;
        display: none;
        min-width: 160px;
        _width: 140px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
<section class="content">
    <div class="box">
        <!-- /.box-header -->
        <div class="content">
            <form method="POST" id="plantilla_basica">
                {{ csrf_field() }}
                <div class="row">
                    <input type="hidden" value="" id="bodega_planilla" name="id_bodega">
                    <input type="hidden" name="id_plantilla" class="form-control" value="{{$id}}">
                    <input type="hidden" name="p_hcid" value="{{$hcid}}">
                    <div class="col-md-3">
                        <label>Código</label><br>
                        <input type="text" name="codigo" placeholder="Código" readonly class="form-control" value="{{$plantilla->codigo}}">
                    </div>
                    <div class="col-md-4">
                        <label>Nombre</label><br>
                        <input type="text" name="nombre" placeholder="Nombre" readonly class="form-control" value="{{$plantilla->nombre}}">
                    </div>
                    <div class="col-md-2">
                        <label>Estado</label><br>
                        <input type="text" name="estado" placeholder="estado" readonly class="form-control" value="@if($plantilla->estado == 1) Activo @else Inactivo @endif">
                       
                    </div>
                    <br>
                    <div class="col-md-12"><br>
                        <h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
                        <br />

                        <input name='contador_items' id='contador_items' type='hidden' value="0">
                        <div class="col-md-12 table-responsive">
                            <table id="items" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr class='well-darks'>
                                        <th width="10%" tabindex="0">Código</th>
                                        <th width="40%" tabindex="0">Items</th>
                                        <th width="10%" tabindex="0">Cantidad</th>
                                        <th width="10%" tabindex="0">Guardar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plantillas_items as $value)
                                    <tr class="fila-fija" id="fila{{$value->id_producto}}">
                                        <td>
                                            <input type="text" name="codigo[]" id="codigo" class="form-control" style="height:25px;width:75%;" value="{{$value->codigo}}" readonly>
                                        </td>
                                        <td>
                                            <input type="hidden" name="id_item[]" class="codigo" value="{{$value->id_producto}}" /><input readonly name="item_id[]" id="item_id" class=" buscador_items form-control" style="height:25px;width:90%;" value="{{$value->nom_prod}}" name="producto[]" />
                                        </td>
                                        <td>
                                            <input type="number" name="item_cant[]" id="item_cant" class="form-control " style="height:25px;width:75%;" value="{{$value->cantidad}}">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="codigo" id="check{{$value->id_producto}}" checked name="check[]" onclick="eliminar_detalle_plantilla('{{$value->id_producto}}')"></label>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <br>
                    <div class="col-md-6"><br></div>
                    <div id="proced_list"></div>
                </div>

            </form>

        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script>
    
    /*function guardar_plantilla_basica() {
        var unique = arr.filter(onlyUnique);
        var id_hc_procedimientost = $('#id_hc_procedimientos').val();
        //console.log(id_hc_procedimientost);
        var plantilla_id = $("#id_plantilla_2").val();
        var cantidad = $("#item_cant").val();
        $.ajax({
            type: 'get',
            url: "{{route('enfermeria.guardar_plantilla_ok')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': JSON.stringify(unique),
                'id_plantilla_2': plantilla_id,
                'cantidad': cantidad,
                'hc_procedimientos': id_hc_procedimientost,
            },
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });

    }*/

    function eliminar_detalle_plantilla(id){
        $('#fila'+id).remove();
    }
</script>