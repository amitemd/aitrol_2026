<div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.banco')}}</label>
                <div class="input-group mb-3">
                    <select class="form-control select2" id="id_configuracion" name="id_configuracion" style="width: 100%;">
                        <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.numCuenta')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                    </div>
                    <input type="text" id="cuenta" name="cuenta" class="form-control text-right" placeholder="{{trans('v3/new_varios.numCuentaProveedor')}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.beneficiario')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                    </div>
                    <input type="text" id="beneficiario" name="beneficiario" class="form-control" placeholder="{{trans('v3/new_varios.beneficiarioBanco')}}">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.identificacion')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                    </div>
                    <input type="text" id="identificacion" name="identificacion" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.identificacionBeneficiario')}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.tipoCuenta')}}</label>
                <div class="input-group mb-3">
                    <select class="form-control select2" id="tipo_cuenta" name="tipo_cuenta" style="width: 100%;">
                        <option value="">-- {{trans('v3/new_varios.seleccionar')}} --</option>
                        <option value="1">{{trans('v3/new_varios.ahorro')}}</option>
                        <option value="2">{{trans('v3/new_varios.corriente')}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>