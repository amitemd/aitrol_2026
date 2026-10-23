<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px">{{trans('hospitalizacion.TraspasoModulo')}}</label>
                </div>

            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0px;">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        <input type="hidden" name="opcion" id="opcion" value="traslado">
        <br><br>
        <div class="row">

            <div class="col-md-3">
                <label style="font-size: 16px;"><b> {{trans('emergencia.Modulos')}}: </b> </label>
            </div>
            <div class="col-md-5">
                <select name="id_paso" id="id_paso" class="form-control">
                    <option value="">{{trans('emergencia.Seleccione')}}</option>
                    @foreach($pasos as $p)
                    <option value="{{$p->id}}">{{$p->nombre_paso}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-info" name="btng" id="btng" type="button" onclick="guardar_traslado();"> {{trans('emergencia.guardar')}}</button>
            </div>


        </div>

    </div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
    function guardar_traslado() {
        var id_solicitud = document.getElementById('id_solicitud').value;
        var opcion = document.getElementById('opcion').value;
        var id_paso = document.getElementById('id_paso').value;

        $.ajax({

            type: 'post',
            url: "{{route('hospital.guardar_traspaso')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_solicitud': id_solicitud,
                'opcion': opcion,
                'id_paso': id_paso,
            },
            success: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
                if (data.respuesta == 'success') {
                    setTimeout(function() {
                        window.location.href = "{{url('hospital/modulos/index')}}/" + data.paso;
                    }, 1500)
                }

            },
            error: function(data) {
                alert('error al cargar');
            }
        })
    }
</script>