<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">{{trans('v3/new_menu.registrarTransportista')}}</h3>
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <div class="box-body">
            <form id="frm_modaltransportista">
                {{ csrf_field() }}
                <input type="hidden" id="action"
                    value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveTranportista" />
                <input type="hidden" id="d" name="d" />
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="cedula_ruc">{{trans('v3/new_menu.ci_Ruc')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" maxlength="13" minlength="10" class="form-control" id="cedula_ruc"
                            name="cedula_ruc" oninput="this.value = this.value.replace(/\D+/g, '')"
                            onblur="validarCampo('cedula',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="razon_social">{{trans('v3/new_menu.razonSocial')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                    </div>

                </div>

                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="email">{{trans('v3/new_menu.email')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="email" class="form-control" id="email" name="email"
                            onblur="validarCampo('email',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="telefono1">{{trans('v3/new_menu.telefono')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="number" class="form-control" id="telefono" name="telefono"
                            onblur="validarCampo('telefono',this)">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="placaM">{{trans('v3/new_menu.placa')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="placaM" name="placaM"
                            onblur="validarCampo('placa',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="tipo_documento">{{trans('v3/new_menu.tipodocumento')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <select name="tipo_documento" id="tipo_documento" class="form-control">
                            <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                            <?php
                            if (config('data.documentoGuia') != '' && count(config('data.documentoGuia')) > 0) {
                                foreach (config('data.documentoGuia') as $value) {
                                    echo '<option value="' . $value->valor . '" >' . $value->catalogo . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label for="rise">{{trans('v3/new_tabla.elevar')}}</label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="rise" name="rise">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="direccion">{{trans('v3/new_tabla.direccion')}}</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
                <div class="row form-group" style="justify-content: center;align-items:center">
                    <a href="javascript:;" id="btntransportistaNuevo" class="btn btn-primary">{{trans('v3/new_tabla.guardar')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
