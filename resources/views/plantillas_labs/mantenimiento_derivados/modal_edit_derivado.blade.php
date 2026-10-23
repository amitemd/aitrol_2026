<div class="modal-header">
    <div class="col-md-10" style="padding: 2px;">
        <h3 style="margin:0;">{{trans('prod_tar.agregarvalor')}} - {{$examen_derivado->examen->nombre}}</h3>
    </div>
    <div class="col-md-2" style="padding: 2px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
        </button>
    </div>
</div>
<div class="modal-body">

    <form method="post" id="form_nivel" action="">
        {{ csrf_field() }}
        <div class="row">
            <div class="form-group col-md-6">
                <input type="hidden" class="form-control" name="id_exam_der" value="{{$id}}">
                <label for="id_seguro" class="col-md-4 control-label">Derivado:</label>
                <div class="col-md-8">
                    <label>{{$examen_derivado->tipo_derivado->nombre}}</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div id="div_precio" style="margin-bottom: 0px;" class="form-group col-md-6">
                <label class="col-md-4 control-label"> {{trans('prod_tar.precio')}}: </label>
                <div class="col-md-8">
                    <input type="number" class="form-control input-sm validar" name="precio" required value="{{$examen_derivado->valor}}">
                </div>
            </div>
        </div>


    </form>

</div>

<div class="modal-footer">

    <button type="button" class="btn btn-primary pull-right" id="btn_guardar" onclick="guardar(event)"><i class="glyphicon glyphicon-floppy-disk"> {{trans('prod_tar.actualizar')}}</i></button>


</div>

<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;
        //  console.log(campo)
        for (let i = 0; i < campo.length; i++) {
            //console.log(`${campo[i].name}: ${campo[i].value}`);
            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }


    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function guardar(e) {
        e.preventDefault();
        $('#btn_guardar').prop("disabled", true);
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{ route('examderivados.update_derivado') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_nivel").serialize(),
                success: function(data) {
                    //console.log(data);
                    alertas(data.respuesta, data.titulos, data.msj)
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function(data) {
                    //console.log(data);
                }
            });
        } else {
            $('#btn_guardar').prop("disabled", false);
            alertas('error', 'ERROR', 'Existen campos vacios')
        }


    }
</script>