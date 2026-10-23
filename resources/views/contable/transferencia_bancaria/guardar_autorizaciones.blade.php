<div class="modal-header">
    <div class="col-md-10" style="text-align: center;">
        <h3>Guardar Autorizaciones</h3>
    </div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
        </button>
    </div>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6" style="padding: 5px;">
            <label class="col-md-12">Proveedor</label>
            <select class="form-control select2" name="id_proveedor_aut" id="id_proveedor_aut" style="width: 100%;" required>
                <option value="">Seleccione...</option>
                @foreach ($proveedores as $x)
                <option value="{{ $x->id }}">{{ $x->razonsocial }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6" style="padding: 5px;">
            <label class="col-md-12">Autorización</label>
            <input onkeypress="return isNumberKey(event)" minlength="49" maxlength="49" id="autorizacion_aut" type="text" class="form-control " name="autorizacion_aut" autofocus required>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">Base Retencion Fuente</label>
            <input id="base_ret_fuente_aut" type="text" class="form-control" style="text-align:right;" value="0.00" name="base_ret_fuente_aut" autofocus>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">Porcentaje Fuente</label>
            <div class="col-md-12 px-0">
                <select class="form-control" name="id_base_ret_fuente" id="id_base_ret_fuente">
                    <option value="">{{ trans('agenda.seleccione') }}...</option>
                    <option value="0" data-value="0">(Ninguno)</option>
                    @foreach ($retenciones as $value)
                    <option value="{{ $value->cuenta_deudora }}|{{$value->valor}}" data-value="{{ $value->valor }}">
                        {{ $value->valor }} % | {{$value->nombre}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">Fuente retenido</label>
            <input id="fuenteRetenido" type="text" class="form-control" style="text-align:right;" value="0.00" name="fuenteRetenido" readonly>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">Base I.V.A</label>
            <input id="baseiva_aut" type="text" class="form-control" style="text-align:right;" value="0.00" name="baseiva_aut" autofocus>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">Porcentaje Iva</label>
            <div class="col-md-12 px-0">
                <select class="form-control" name="id_base_iva" id="id_base_iva">
                    <option value="">{{ trans('agenda.seleccione') }}...</option>
                    <option value="0" data-value="0">(Ninguno)</option>
                    @foreach ($retencionesR as $value)
                    <option value="{{ $value->cuenta_deudora }}|{{$value->valor}}" data-value="{{ $value->valor }}">
                        {{ $value->valor }} % | {{$value->nombre}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4" style="padding: 5px;">
            <label class="col-md-12">IVA retenido</label>
            <input id="ivaRetenido" type="text" class="form-control" style="text-align:right;" value="0.00" name="ivaRetenido" readonly>
        </div>
        <div class="col-md-12" style="text-align: center; padding-top: 20px;">
            <button onclick="guardar();" class="btn btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Guardar</button>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove-sign"></i> Cerrar</button>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    $('.select2').select2({
        tags: false
    });
    $(document).ready(function() {
        $('#id_base_iva').change(function() {
            if ($('#baseiva_aut').val() == '') {
                Swal.fire({
                    title: 'Alerta',
                    text: "Ingrese el valor de la base de iva para continuar",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6'
                })
                return;
            } else {
                if ($('#baseiva_aut').val() > 0) {
                    var porIva = $('#id_base_iva').val().split('|');
                    var valIva = $('#baseiva_aut').val() * (porIva[1] / 100);
                    $('#ivaRetenido').val(valIva);
                }
            }
        });
        $('#id_base_ret_fuente').change(function() {
            if ($('#base_ret_fuente_aut').val() == '') {
                Swal.fire({
                    title: 'Alerta',
                    text: "Ingrese el valor de la base de iva para continuar",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6'
                })
                return;
            } else {
                if ($('#base_ret_fuente_aut').val() > 0) {
                    var porIva = $('#id_base_ret_fuente').val().split('|');
                    var valIva = $('#base_ret_fuente_aut').val() * (porIva[1] / 100);
                    $('#fuenteRetenido').val(valIva);
                }
            }
        });
    });

    function guardar() {
        var proveedor = $("#id_proveedor_aut").val();
        var autorizacion = $("#autorizacion_aut").val();
        var baseiva = $("#baseiva_aut").val();
        var id_base_iva = $("#id_base_iva").val();
        var base_ret_fuente_aut = $("#base_ret_fuente_aut").val();
        var id_base_ret_fuente = $("#id_base_ret_fuente").val();
        var msj = "";
        if (proveedor == "") {
            msj = msj + "Por favor, Seleccione el Proveedor. <br>";
        }
        if (autorizacion == "") {
            msj = msj + "Ingrese el No. de Autorizacion. <br>";
        }
        if (autorizacion.length != 49) {
            msj = msj + "El No. de autorización debe contener 49 digitos. <br>";
        }
        /*if (baseiva == '0.00') {
            msj = msj + "Ingrese la base del iva. <br>";
        }
        if (id_base_iva == "") {
            msj = msj + "Ingrese el porcentaje de la base iva. <br>";
        }*/
        if (base_ret_fuente_aut == '0.00') {
            msj = msj + "Ingrese la base retención de la fuente. <br>";
        }
        if (id_base_ret_fuente == '') {
            msj = msj + "Ingrese el porcentaje de la fuente. <br>";
        }
        if (msj != "") {
            Swal.fire("Oops...", msj, "error");
            return false;
        }
        let array = {
            proveedor: $("#id_proveedor_aut").val(),
            autorizacion: $("#autorizacion_aut").val(),
            base_autorizacion: $("#baseiva_aut").val(),
            iva_porcentaje: $("#id_base_iva").val(),
            base_porcentaje: $("#base_ret_fuente_aut").val(),
            porcentaje_base: $("#id_base_ret_fuente").val(),
        };
        Swal.fire({
            title: 'Alerta',
            text: "¿ Esta seguro que desea realizar esta accion ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('transferenciabancaria.save_autorizacion') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: array,
                    success: function(data) {
                        if (data.status) {
                            Swal.fire("!! Correcto !!", "Guardado Correctamente", "success");
                            $("#retenciones").modal("hide");
                        } else {
                            Swal.fire("!! Incorrecto !!", data.msj, "error");
                        }
                    },
                    error: function(data) {}
                });
            }
        });
    }
</script>
