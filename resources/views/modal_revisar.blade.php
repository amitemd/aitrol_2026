<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Editar Fecha de Vencimiento</h3>
    </div>


    <div class="modal-body">
        <div class="box-body">
        @if(is_null($fecha))
        <div style="text-align:center">No se encuentra el item</div>
        @else
            <input type="hidden" id="serie" value="{{$fecha->serie}}">
            <form id="formulario" action="" class="form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label for="">Actualizar Fecha</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="fecha_vence" name="fecha_vence" value="{{$fecha->fecha_vence}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-12" style="margin-top: 2%;">
                        <div class="text-center">
                            <button type="button" id='editar' class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>EDITAR</button>
                        </div>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
    <div class="modal-footer">

    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    document.getElementById("editar").addEventListener('click', function() {

        $.ajax({
            url: "{{route('dashboard.consulta')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'serie': document.getElementById("serie").value,
                'fecha': document.getElementById("fecha_vence").value
            },
            type: 'post',
            dataType: 'json',
            success: function(data) {

                var url = "{{route('dashboard')}}"
                if (data == 'ok') {

                    swal("Editado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });


    });
</script>