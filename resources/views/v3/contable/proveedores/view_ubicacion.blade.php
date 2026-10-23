<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.pais')}}</label>
            <div class="input-group mb-3">
                <select class="form-control select2 pais" id="pais" style="width: 100%;">
                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
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
            <label>{{trans('v3/new_varios.provincia')}}</label>
            <div class="input-group mb-3">
                <select class="form-control select2 provincia" id="provincia" style="width: 100%;">
                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.ciudad')}}</label>
            <div class="input-group mb-3">
                <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 100%;">
                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_tabla.direccion')}}</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                </div>
                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="{{trans('v3/new_varios.direccionProveedor')}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.urbanizacion')}}</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                </div>
                <input type="text" id="urbanizacion" name="urbanizacion" class="form-control" placeholder="{{trans('v3/new_varios.urbanizacionProveedor')}}">
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.etapa')}}</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                </div>
                <input type="text" id="etapa" name="etapa" class="form-control" placeholder="{{trans('v3/new_varios.etapaProveedor')}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.manzana')}}</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-map"></i></span>
                </div>
                <input type="text" id="manzana" name="manzana" class="form-control" placeholder="{{trans('v3/new_varios.manzanaProveedor')}}">
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.villa')}}</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-home"></i></span>
                </div>
                <input type="text" id="villa" name="villa" class="form-control" placeholder="{{trans('v3/new_varios.villaProveedor')}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="form-group">
            <label>{{trans('v3/new_varios.referencia')}}</label>
            <div class="input-group mb-6">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-map"></i></span>
                </div>
                <textarea rows="3" id="referencia" name="referencia" class="form-control" placeholder="{{trans('v3/new_varios.referenciaProveedor')}}"></textarea>
            </div>
        </div>
    </div>
</div>