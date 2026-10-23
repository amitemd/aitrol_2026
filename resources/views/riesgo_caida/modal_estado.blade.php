<div class="modal-content modal-lg modal-dialog">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Cambio Estado</h3>
    </div>
    <form method="post" id="formulario" action="#">
        {{csrf_field()}}
        <input type="hidden" name="id_camilla" value="{{$edit->camilla}}">
        <div style="margin-top: 5px;" class="col-md-12">
            <div class="form-row">
                <div class="col-md-4">
                    <label for="camilla" class="col-form-label-sm" style="text-align: center;">Estado</label>
                    @if(($edit->estado_uso ) == 3 )
                    <select name="estado" id="estado" class="form-control select2" style="width:100%;">
                        <option value="">Seleccione</option>
                        <option {{ $edit->estado_uso == 0 ? 'selected' : ''}} value="1">Libre</option>
                    </select>
                    @elseif(($edit->estado_uso )== 4)
                    <select name="estado" id="estado" class="form-control select2" style="width:100%;">
                        <option value="">Seleccione</option>
                        <option {{ $edit->estado_uso == 3 ? 'selected' : ''}} value="3">Preparacion</option>
                    </select>
                    @endif
                </div>
                @if(($edit->estado_uso )== 3)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="check" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Paciente Movilizado
                        </label>
                    </div>
                </div>
                @endif
                <div style="margin-top: 2px;" class="col-md-12">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal-footer">
        <button type="submit" id="gard" onclick="guardar()" class="btn btn-danger" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
    </div>
</div>
<script src="sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            tags: false
        });
    });

    function guardar() {
        let campo = $("#estado").val();
        if (campo == '') {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Hay campos vacios',
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            document.getElementById("gard").disabled = true;
            $.ajax({
                url: "{{route('cambio_estado_uno')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Guardado con Exito',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    var url = '{{url("gestion/camilla/index")}}';
                    window.location = url;
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }
</script>