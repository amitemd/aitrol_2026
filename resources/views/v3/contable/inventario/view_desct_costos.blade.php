<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-4">
                <label style="color: red;">{{trans('v3/inventario.desctMaximos')}}</label>
                <table>
                    <tr>
                        <td>
                            <label>% {{trans('v3/inventario.descuento')}}</label>
                        </td>
                        <td>
                            <input type="number" id="descuento" name="descuento" class="form-control" placeholder="% {{trans('v3/inventario.descuento')}}">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>% {{trans('v3/inventario.financiero')}}</label>
                        </td>
                        <td>
                            <input type="number" id="financiero" name="financiero" class="form-control" placeholder="% {{trans('v3/inventario.financiero')}}">
                        </td>
                    </tr>
                </table>
            </div> 
            <div class="col-12 col-sm-4">
                <label style="color: red;">{{trans('v3/inventario.costos')}}</label>
                <div class="form-group">
                    <table>
                        <tr>
                            <td>
                                <label>{{trans('v3/inventario.promedio')}}</label>
                            </td>
                            <td>
                                <input type="number" id="promedio" name="promedio" class="form-control" placeholder="{{trans('v3/inventario.promedio')}}">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>{{trans('v3/inventario.reposicion')}}</label>
                            </td>
                            <td>
                                <input type="number" id="reposicion" name="reposicion" class="form-control" placeholder="{{trans('v3/inventario.reposicion')}}">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>{{trans('v3/inventario.lista')}}</label>
                            </td>
                            <td>
                                <input type="number" id="lista" name="lista" class="form-control" placeholder="{{trans('v3/inventario.lista')}}">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>{{trans('v3/inventario.ultima_compra')}}</label>
                            </td>
                            <td>
                                <input type="number" id="ultima_compra" name="ultima_compra" class="form-control" placeholder="{{trans('v3/inventario.ultima_compra')}}">
                            </td>
                        </tr>
                    </table>
                </div>
            </div> 
            <div class="col-12 col-sm-4">
                <label style="color: red;">{{trans('v3/inventario.detalle_precios')}}</label>
                <div class="form-group">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 45%;">PVP/{{trans('v3/inventario.nivel')}}</th>
                                <th style="width: 45%;">{{trans('v3/inventario.precio')}}</th>
                                <th><a href="javascript:;" id="agregarPrecio" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody id="detalle_precio">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
