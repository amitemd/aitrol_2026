<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-3">
                <label style="color: red;">{{trans('v3/inventario.productos')}}</label>
                <div class="form-group">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 90%;">{{trans('v3/inventario.producto')}}</th>
                                <th align="center" style="width: 50px;"><a href="javascript:;" id="agregarProducto" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody id="productos">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-sm-3">
                <label style="color: red;">{{trans('v3/inventario.equipos')}}</label>
                <div class="form-group">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 90%;">{{trans('v3/inventario.equipo')}}</th>
                                <th align="center" style="width: 50px;"><a href="javascript:;" id="agregarEquipo" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody id="equipos">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label style="color: red;">{{trans('v3/inventario.procedimientos')}}</label>
                <div class="form-group">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 40%;">{{trans('v3/inventario.procedimiento')}}</th>
                                <th>{{trans('v3/inventario.seguro')}}</th>
                                <th>{{trans('v3/inventario.honorarios')}}</th>
                                <th align="center" style="width: 40px;"><a href="javascript:;" id="agregarProcedimientos" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody id="procedimientos">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label style="color: red;">{{trans('v3/inventario.detallePaquete')}}</label>
                <div class="form-group">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>{{trans('v3/inventario.cantidad')}}</th>
                                <th style="width: 50%;">{{trans('v3/inventario.paquete')}}</th>
                                <th>PVP</th>
                                <th align="center" style="width: 40px;"><a href="javascript:;" id="agregarPaquete" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody id="detallePaquetes">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
