<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <input type="hidden">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">REGISTRAR TRANSPORTISTA</h3>
    </div>
    <div class="modal-body">
        <div class="box-body">
            <form method="post" id="formulario">
                {{ csrf_field() }}
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="cedula_ruc"> Cédula o Ruc</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" maxlength="13" minlength="10" class="form-control" id="cedula_ruc" name="cedula_ruc" oninput="this.value = this.value.replace(/\D+/g, '')" onchange="funtionCall(this)" onblur="validarCampo('cedula',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="razon_social"> Razón Social</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                    </div>

                </div>

                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="email">Email</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="email" class="form-control" id="email" name="email" onblur="validarCampo('email',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="telefono1">Telefono</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="telefono" name="telefono" onblur="validarCampo('telefono',this)">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="placaM">Placa</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="email" class="form-control" id="placaM" name="placaM" onblur="validarCampo('placa',this)">
                    </div>
                    <div class="col-sm-2">
                        <label for="tipo_documento">Tipo Documento</label>
                    </div>
                    <div class="col-sm-3">
                        <select name="tipo_documento" id="tipo_documento" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="05">Cedula</option>
                            <option value="06">Pasaporte</option>
                            <option value="04">Ruc</option>
                            <option value="08">Cédula extranjera</option>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label for="rise">Rise</label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" id="rise" name="rise">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2">
                        <label for="direccion">Direccion</label>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
                <div class="row form-group text-center">
                    <button type="button" id="boton_guardar" class="btn btn-primary" onclick="guardart(event)">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const validarCampo = (e, valor) => {
        $.ajax({
            type: 'get',
            url: "{{route('validar_campos_transportista_datos_guia')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                e: e,
                value: valor.value
            },
            success: function(data) {
                console.log(data);
                if (data.status) {
                    valor.value = '';
                    alertas("error", "Error", [data.data]);
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }


    const validarFormulario = () => {

        let formulario = document.forms["formulario"];
        let array = [];
        if (formulario.cedula_ruc.value == '') {
            array.push('cedula');
        }
        if (formulario.razon_social.value == '') {
            array.push('razon_social');
        }
        if (formulario.placaM.value == '') {
            array.push('placa');
        }
        if (formulario.telefono.value == '') {
            array.push('telefono');
        }
        if (formulario.email.value == '') {
            array.push('email');
        }
        if (formulario.tipo_documento.value == '') {
            array.push('tipo_documento');
        }
        return array;
    }



    const guardart = (e) => {
        e.preventDefault();
        document.getElementById('boton_guardar').disabled = true;
        let valForm = validarFormulario();
        if (valForm.length == 0) {
            $.ajax({
                type: 'post',
                url: "{{route('save_transportista_datos_guia')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    form: $('#formulario').serialize(),
                    check: $('#rise').is(":checked"),
                },
                success: function(data) {
                    if (data.status) {
                        alertas('error', 'Error', [data.msj]);
                    } else {
                        console.log(data.data.placa);
                        $("#email_transportista").val(data.data.email);
                        $("#placa").val(data.data.placa);
                        var option = new Option(data.data.razon_social, data.data.id, true, true);
                        $("#js-data-nombre-ruc").append(option).trigger('change');
                        alertas('success', 'Correcto', [data.msj]);
                        $("#button").click();
                    }
                },
                error: function(data) {
                    document.getElementById('boton_guardar').disabled = false;
                }
            });
        } else {
            alertas("error", "Porfavor llenes los siguientes campos", valForm);
            document.getElementById('boton_guardar').disabled = false;
        }
    }
</script>