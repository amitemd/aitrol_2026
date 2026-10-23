<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">REGISTRAR LIMPIEZA</h3>
    </div>
    <form method="post" id="formulario">
        {{csrf_field()}}
        <div style="margin-top: 5px;" class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-1">
                    <label for="sala" class="col-md-4 texto">Area</label>
                </div>
                <div class="form-group col-md-3">
                    <select name="piso" id="piso" class="form-control piso" onchange="nombrePiso();">
                        <option value="">Seleccione</option>
                        @foreach($piso as $val)
                        <option value="{{$val->id}}">{{$val->nombre_hospital}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <label for="sala" class="col-md-4 texto">Sala</label>
                </div>
                <div class="form-group col-md-3">
                    <select name="sala" id="sala" class="form-control piso">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="desinfectante" class="col-md-4 texto">Desinfectante</label>
                </div>
                <div class="form-group col-md-2">
                    <input type="text" id="desinfectante" name="desinfectante" class="form-control">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="guardar()" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
        </div>
    </form>
</div>
<script src="sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        $(".piso").select2();
    });

    function nombrePiso() {
        var select = $("#piso").val();
        $.ajax({
            url: "{{route('riesgo.nombre_piso')}}",
            data: {
                'term': select
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                //console.log(data);
                $("#sala").empty();
                $("#sala").append('<option value="">Seleccione...</option>');
                for (var i = 0; i < data.length; i++) {
                    //console.log(data[i]);
                    $("#sala").append('<option value=' + data[i].id + '>' + data[i].nombre + '</option>');
                }
            },
            error: function(xhr, status) {
                alert('Disculpe, existió un problema');
                //console.log(xhr);
            },

        });
    }
    jQuery(document).ready(function() {
        jQuery('#boton').on('hidden.bs.modal', function(e) {
            jQuery(this).removeData('bs.modal');
            jQuery(this).find('.modal-content').empty();
        })
    })

    function guardar() {
        let desinfectante = document.getElementById("desinfectante").value;

        if (desinfectante == '' || desinfectante == null) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Campos Desinfectante Vacio',
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            $.ajax({
                url: "{{route('mantenimientohorario.guardar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data == 'ok') {
                        location.reload();
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });
        }
    }
</script>
