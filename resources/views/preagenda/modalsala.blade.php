<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">BLOQUEAR SALA</h3>
    </div>
    <form method="post" id="formulario" action="#">
        <div style="margin-top: 5px;" class="col-md-12">
            <div class="form-row">
                <div style="margin-top: 2px;" class="col-md-12">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="salas" class="col-form-label-sm">Salas</label>
                            <select class="form-control" name="salas" id="salas">
                                <option value="">Seleccione</option>
                                @foreach($salas as $val)
                                <option value="{{$val->id}}">{{$val->nombre_sala}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="salas" class="col-form-label-sm">Fecha Desde</label>
                            <input type="datetime-local" class="form-control" id="desde" onchange="cambio()" name="desde" style="text-align: center;line-height:10px;">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="salas" class="col-form-label-sm">Fecha Hasta</label>
                            <input type="datetime-local" class="form-control" onchange="cambio()" id="hasta" name="hasta" style="text-align: center;line-height:10px;">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nombre_reunion" class="col-form-label-sm">Nombre Reunion</label>
                            <input type="text" class="form-control" id="nombre_reunion" name="nombre_reunion">
                            <span id="vacio" style="color:red;display:none;">Llene el campo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal-footer">
        <button type="submit" onclick="guardar()" class="btn btn-danger" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    document.getElementById("hasta").disabled = true;
    var start = document.querySelector('input[type="datetime-local"]#desde'),
        end = document.querySelector('input[type="datetime-local"]#hasta');

    function guardar() {
        var datos = $('#nombre_reunion').val();
        if (datos == "") {
            $('#vacio').show();
        } else {
            $.ajax({
                url: "{{route('guardar_sala')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'get',
                dataType: 'json',
                success: function(data) {

                    swal("Correcto!", "Ocupado", "success");
                    var url = '{{url("agenda_procedimiento/pentax_procedimiento")}}';
                    window.location = url;
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });

        }
    }

    function cambio() {
        var desde = document.getElementById("desde").value;
        var hasta = document.getElementById("hasta").value;
        if (desde != '') {
            document.getElementById("hasta").disabled = false;
            if (desde != '' && hasta != '') {
                $.ajax({
                    url: "{{route('validar_hora')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: $('#formulario').serialize(),
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        if (data == "ok") {}
                        if (data == "no") {
                            alert("Fecha Menor al Actual !!!");
                        }
                    },
                    error: function(xhr, status) {
                        alert('Existió un problema');
                    },
                });
            }
        }
    }
</script>