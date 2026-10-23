<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>País</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2 pais" id="pais" name="pais" style="width: 100%;">
                            <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                            <?php
                            if (config('data.paises') != null && count(config('data.paises')) > 0) {
                                foreach (config('data.paises') as $row) {
                                    echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Provincia</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2 provincia" name="provincia" id="provincia" style="width: 100%;">
                            <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Ciudad</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 100%;">
                            <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Urbanizaci&oacute;n</label>
                    <div class="input-group">
                        <div class="input-group-prepend" style="cursor: pointer">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                        </div>
                        <input type="text" id="urbanizacion" name="urbanizacion" class="form-control urbanizacion" placeholder="Urbanización">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Etapa</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="etapa" name="etapa" class="form-control etapa" placeholder="Etapa">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Manzana</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                        </div>
                        <input type="text" id="manzana" name="manzana" class="form-control manzana" placeholder="Manzana">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Villa</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                        </div>
                        <input type="text" id="villa" name="villa" class="form-control villa" placeholder="Villa">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Direcci&oacute;n</label>
                    <div class="input-group">
                        <div onclick="verMapa()" class="input-group-prepend" style="cursor: pointer">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        </div>
                        <input type="text" id="direccion" name="direccion" class="form-control direccion" placeholder="Dirección">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label>Referencia</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-pencil"></i></span>
                        </div>
                        <textarea id="referencia" name="referencia" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>