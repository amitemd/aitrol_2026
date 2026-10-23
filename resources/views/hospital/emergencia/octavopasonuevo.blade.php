<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>
<style>
    canvas {
        border: 1px solid #000;
        cursor: crosshair
    }

    .arriba {
        margin-top: 4px;
        text-align: left;
    }

    .bordernone {
        border: 3px solid black;
    }

    .cuadre {
        max-height: 100px !important;
    }
</style>
<div class="card" id="octavopaso">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                
                <div class="col-md-9">
                    <label style="color: white;" class="control_label">{{trans('pasos.ExamenFisicoDeTraumaCritico')}}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_paso7">
            @php
            $form008 = $solicitud->form008->first();
            @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="col-md-12">
                <b>{{trans('pasos.Observacion')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="examen_fisico_obs" id="examen_fisico_obs" value="{{$form008->examen_fisico_obs}}">
            </div>
            <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                <button class="btn btn-primary" onclick="guardar_paso8();" type="button"> <i class="fa fa-save"></i> </button>
            </div>
        </form>

    </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    function guardar_paso8() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.store_octavo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_paso7").serialize(),

            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
            },
            error: function() {
                alert('error al cargar');
            }
        });

    }
</script>