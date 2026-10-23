<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.codigo')}}</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="codigo" name="codigo" class="form-control" placeholder="{{trans('v3/inventario.codigoProducto')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/new_varios.nombre')}}</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="{{trans('v3/inventario.nombreProducto')}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>{{trans('v3/new_varios.modelo')}}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="modelo" name="modelo" class="form-control" placeholder="{{trans('v3/inventario.modeloProducto')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.codigoBarras')}}</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        </div>
                        <input type="text" id="codigo_barra" name="codigo_barra" class="form-control" placeholder="{{trans('v3/inventario.codigoBarraProducto')}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.grupo')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="grupo" name="grupo" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <?php
                            if (config('data.grupo') != null && count(config('data.grupo')) > 0) {
                                foreach (config('data.grupo') as $row) {
                                    echo '<option value="' . $row->id_catalogo . '">' . $row->catalogo . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.marca')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="marca" name="marca" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <?php
                            if (config('data.marcas') != null && count(config('data.marcas')) > 0) {
                                foreach (config('data.marcas') as $row) {
                                    echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.stockMin')}}</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" placeholder="{{trans('v3/inventario.stockMinProducto')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.fechaExpiracion')}}</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                        <input type="date" id="fecha_expiracion" name="fecha_expiracion" class="form-control" placeholder="{{trans('v3/inventario.fechaExpiracionProducto')}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.proveedor')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2_provi" style="width: 100%;" id="proveedor" name="proveedor">
                            <option value="">--Seleccione--</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.tipoProducto')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="tipo_producto" name="tipo_producto" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <option value="1">{{trans('v3/new_varios.servicio')}}</option>
                            <option value="2">{{trans('v3/new_varios.producto')}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" style="display: flex;text-align: center;">
                <div class="col-sm-3">
                    <label class="font-normal">Registro de No. de series &nbsp;<input type="checkbox" id="reg_serie" name="reg_serie"></label>
                </div>
                <div class="col-sm-3">
                    <label class="font-normal">Permitir Modificar Descuento &nbsp;<input type="checkbox" id="mod_desc" name="mod_desc"></label>
                </div>
                <div class="col-sm-3">
                    <label class="font-normal">Permitir Modificar Precio &nbsp;<input type="checkbox" id="mod_precio" name="mod_precio"></label>
                </div>
                <div class="col-sm-3">
                    <label class="font-normal">Aplica IVA &nbsp;<input type="checkbox" id="iva" name="iva"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label>{{trans('v3/new_varios.descripcion')}}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-align-justify"></i></span>
                        </div>
                        <textarea rows="3" id="descripcion" name="descripcion" class="form-control" placeholder="{{trans('v3/inventario.descripcionProducto')}}"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label></label>
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="cheestado">
                        <label class="custom-control-label" for="cheestado">{{trans('v3/new_varios.estado')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
