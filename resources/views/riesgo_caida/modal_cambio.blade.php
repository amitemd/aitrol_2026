<div class="modal-content modal-lg modal-dialog">
                <div class="modal-header" style="background: #3c8dbc;">
                    <input type="hidden" name="empresacheck">
                    <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Cambio Estado</h3>
                </div>
                <form method="post" id="formulario" action="#">
                    {{csrf_field()}}
                    <div style="margin-top: 5px;" class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-4">
                                <label for="camilla" class="col-form-label-sm">Paciente</label>
                                <input type="text" class="form-control" disabled value="{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}" />
                                <input type="hidden" id="id_paciente" name="id_paciente" value="{{$paciente->id}}">
                                <input type="hidden" id="id_camilla" name="id_camilla" value="{{$id}}">
                                <input type="hidden" id="id_agenda" name="id_agenda" value="{{$edit->id_agenda}}">
                            </div>
                            <div class="col-md-4">
                                <label for="camilla" class="col-form-label-sm">Estado</label>
                                <select name="estado" id="estado" class="form-control select2" style="width:100%;">
                                    <option {{ $edit->estado_uso == 2 ? 'selected' : ''}} value="2">Ocupado</option>
                                    <option {{ $edit->estado_uso == 4 ? 'selected' : ''}} value="4">Desinfección</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="observacion">Observación</label>
                                <input type="text" name="observacion" id="obs" class="form-control">
                            </div>
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
                    <button type="submit" id="gard" onclick="guardar()" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
                </div>
            </div>
            <script src="sweetalert2.all.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.select2').select2({
                        tags: false
                    });
                    $("#oss").hide();
                });

                function guardar() {
                    var observacion = document.getElementById('obs').value;
                    var estado = document.getElementById('estado').value;
                    if (observacion == '' || estado == '') {
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
                            url: "{{route('riesgo.cambio_estado')}}",
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
                            },
                        });
                    }
                }
            </script>