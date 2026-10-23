<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">REGISTRAR OBSERVACIONES</h3>
    </div>
    <form method="post" id="formulario">
        {{csrf_field()}}
        <input type="hidden" id="id" value="{{$id}}">
        <div style="margin-top: 5px;" class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="obs" class="col-md-4 texto">Observaciones</label>
                </div>
                <div class="form-group col-md-4">
                    <input name="obs" id="obs" class="form-control" required />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="guardar()" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>EDITAR</button>
        </div>
    </form>
</div>
<script src="sweetalert2.all.min.js"></script>
<script>
    function guardar() {
        var id = $("#id").val();
        var obs = $("#obs").val();

        if (obs == '' || obs == null) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Campos Observaciones Vacios',
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            $.ajax({
                url: "{{route('mantenimientohorario.agragsobs')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id': id,
                    'obs': obs,
                },
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