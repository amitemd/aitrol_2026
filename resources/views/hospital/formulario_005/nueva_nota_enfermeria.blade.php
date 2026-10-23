<div class="card">
    <div class="card-body">
        <br>
        <form id="form_nota_enfermeria" method="POST">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-12">
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">
                    <h4>{{trans('hospitalizacion.Observacion')}}</h4>
                    <textarea name="observacion" id="observacion" class="form-control input-sm" rows="3">@if(!is_null($nota_enfermeria)){{$nota_enfermeria->descripcion}}@endif</textarea>
                </div>
                

            </div>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-6">
                    <button class="btn btn-primary" type="button" id="guardar_diagnostico" onclick="guardar_nota();"> <span class="fa fa-save"> {{trans('hospitalizacion.Guardar')}}</span> </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    //CANVAS
    function guardar_nota() {

        $.ajax({
            type: 'post',
            url:"{{route('formulario005.guardar_nota_enfermeria',['id' => $nota_enfermeria->id])}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#form_nota_enfermeria").serialize(),
            success: function(data){

                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`); 
                
            },
            error: function(data){
            }
        })
    }
</script>