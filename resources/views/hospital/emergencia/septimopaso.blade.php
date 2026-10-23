<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.ExamenFisicoyDiagnostico')}}
                    </h4>
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
            <div class="row" style="padding-top: 10px;">

                {{-- <div class="col-md-12">
                    <b> 1. {{trans('pasos.VíaAéreaObstruida')}}</b>
                </div>
                <div class="col-md-12">
                    <select class="form-control form-control-sm" name="via_aerea_obs" id="via_aerea_obs">
                        <option @if($form008->via_aerea_obs == 'Libre') selected @endif value="Libre"> {{trans('pasos.Libre')}} </option>
                        <option @if($form008->via_aerea_obs == 'Obstruida') selected @endif value="Obstruida">{{trans('pasos.Obstruida')}}</option>
                    </select>
                </div> --}}
                <div class="col-md-12">
                    <b> 1.{{trans('pasos.PielFaneras')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="piel_faneras" id="piel_faneras" value="{{$form008->piel_faneras}}">
                </div>
                <div class="col-md-12">
                    <b> 2.{{trans('pasos.Cabeza')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="cabeza" id="cabeza" value="{{$form008->cabeza}}">
                </div>
                <div class="col-md-12">
                    <b> 3.{{trans('pasos.Ojos')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="ojos" id="ojos" value="{{$form008->ojos}}">
                </div>
                <div class="col-md-12">
                    <b> 4.{{trans('pasos.Oidos')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="oidos" id="oidos" value="{{$form008->oidos}}">
                </div>
                <div class="col-md-12">
                    <b> 5.{{trans('pasos.Nariz')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="nariz" id="nariz" value="{{$form008->nariz}}">
                </div>
                <div class="col-md-12">
                    <b> 6.{{trans('pasos.Boca')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="boca" id="boca" value="{{$form008->boca}}">
                </div>
                <div class="col-md-12">
                    <b> 7.{{trans('pasos.OroFaringe')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="oro_faringe" id="oro_faringe" value="{{$form008->oro_faringe}}">
                </div>
                <div class="col-md-12">
                    <b> 8.{{trans('pasos.Cuello')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="cuello" id="cuello" value="{{$form008->cuello}}">
                </div>
                <div class="col-md-12">
                    <b> 9.{{trans('pasos.AxilasMamas')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="axilas_mamas" id="axilas_mamas" value="{{$form008->axilas_mamas}}">
                </div>

                <div class="col-md-12">
                    <b> 10.{{trans('pasos.Torax')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="torax" id="torax" value="{{$form008->torax}}">
                </div>

                <div class="col-md-12">
                    <b> 11.{{trans('pasos.Abdomen')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="abdomen" id="abdomen" value="{{$form008->abdomen}}">
                </div>

                <div class="col-md-12">
                    <b> 12.{{trans('pasos.Columna')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="columna" id="columna" value="{{$form008->columna}}">
                </div>

                {{--<div class="col-md-12">
                    <b> 7.{{trans('pasos.Pelvis')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="pelvis" id="pelvis" value="{{$form008->pelvis}}">
                </div>--}}

                <div class="col-md-12">
                    <b> 13.{{trans('pasos.InglePerine')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="ingle_perine" id="ingle_perine" value="{{$form008->ingle_perine}}">
                </div>

                {{--<div class="col-md-12">
                    <b>8.{{trans('pasos.Extremidades')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="extremidades" id="extremidades" value="{{$form008->extremidades}}">
                </div>--}}

                <div class="col-md-12">
                    <b> 14.{{trans('pasos.MiembrosSuperiores')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="miembros_superiores" id="miembros_superiores" value="{{$form008->miembros_superiores}}">
                </div>

                <div class="col-md-12">
                    <b> 15.{{trans('pasos.MiembrosInferiores')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="miembros_inferiores" id="miembros_inferiores" value="{{$form008->miembros_inferiores}}">
                </div>
                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso7();" type="button"> <i class="fa fa-save"></i> </button>
                </div>
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
    function guardar_paso7() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.septimopaso_store')}}",
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