<div class="card card-default">
    <div class="card-body">
        <div class="row">

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Nombre</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="nombre_representante" name="nombre_representante" class="form-control"
                            placeholder="Nombre del Representante">
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>C&eacute;dula</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="cedula" name="cedula" class="form-control text-right"
                            onkeypress="return soloNumeros(event);" placeholder="Número de cédula">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Pais</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-list"></i></span>
                        </div>
                        <select class="form-control select2 pais_representante" id="pais_representante"
                            name="pais_representante" style="width: 88%;">
                            <option value="">--Seleccione--</option>
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
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-globe-americas"></i></span>
                        </div>
                        <select class="form-control select2 provincia_representante" id="provincia_representante"
                            name="provincia_representante" style="width: 88%;">
                            <option value="">--Seleccione--</option>
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
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-globe-americas"></i></span>
                        </div>
                        <select class="form-control select2 ciudad_representante" id="ciudad_representante"
                            name="ciudad_representante" style="width: 88%;">
                            <option value="">--Seleccione--</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
