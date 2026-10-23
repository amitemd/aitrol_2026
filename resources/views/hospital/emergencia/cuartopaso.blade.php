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
                        {{trans('pasos.AntecedentesPersonalesyFamiliares')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="frm_cuartopaso">
            <input type="hidden" name="id_paciente" value="{{$solicitud->paciente->id}}">
            <input type="hidden" name="id_solicitud" value="{{$solicitud->id}}">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <h4>{{trans('pasos.AntecedentesPersonales')}}</h4>
                </div>
                <div class="col-md-12">
                    <b>1. {{trans('pasos.Alergias')}}</b>
                </div>
                <!--div class="col-md-12">
                    <span class="badge badge-danger"> {{$txt_al}} </span>
                </div-->
                <div class="col-md-12">
                    <select id="ale_list" name="ale_list[]" class="form-control" multiple>
                        @foreach($alergias as $ale_pac)
                        <option selected value="{{$ale_pac->id_principio_activo}}">{{$ale_pac->principio_activo->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                @php $datos_pac = $solicitud->paciente->ho_datos_paciente; @endphp
                @php $paciente = $solicitud->paciente; @endphp
                <div class="col-md-12">
                    <b>2. {{trans('pasos.Clinicos')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="clinicos" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="clinicos" value="{{ $datos_pac->clinico}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu clinico">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>3. {{trans('pasos.Ginecologico')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="ginecologico" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="ginecologico" value="@if($datos_pac->ginecologico==null)@if($paciente->sexo=='1'){{'NO APLICA'}}@endif @else{{ $datos_pac->ginecologico }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu ginecologico">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>4. {{trans('pasos.Traumatologicos')}}</b>
                </div>
                <div class="col-md-12">

                    <div class="input-group dropdown">
                        <input id="traumatologicos" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="traumatologicos" value="{{ $datos_pac->traumatologico }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu trauma">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>5. {{trans('pasos.Pediatricos')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="antecedentes_pediatricos" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="antecedentes_pediatricos" value="{{ $datos_pac->antecedentes_pediatricos}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu pediatricos">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>6. {{trans('pasos.Quirurgicos')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="antecedentes_quir" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="antecedentes_quir" value="{{ $paciente->antecedentes_quir}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu quirurgico">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>7. {{trans('pasos.Farmacologico')}}</b>
                </div>
                <div class="col-md-12">

                    <div class="input-group dropdown">
                        <input id="farmacologico" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="farmacologico" value="{{ $datos_pac->farmacologico }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu farmacologico">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                {{-- <div class="col-md-12">
                    <b>7. {{trans('pasos.Psiquiatrico')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="psiquiatrico" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="psiquiatrico" value="{{ $datos_pac->psiquiatrico }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu psiquiatrico">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div> --}}

                <div class="col-md-12">
                    <b>8. {{trans('pasos.Habitos')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="habitos" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="habitos" value="{{ $datos_pac->habitos }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu habitos">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>9. {{trans('pasos.AntecedentesFamiliares')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="antecedentes_fam" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="antecedentes_fam" value="{{ $paciente->antecedentes_fam}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu familiar">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>10. {{trans('pasos.Otros')}}</b>
                </div>
                <div class="col-md-12">
                    <div class="input-group dropdown">
                        <input id="otros_antecedente" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="otros_antecedente" value="{{ $datos_pac->otros_antecedente}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                        <ul class="dropdown-menu otros_antecedente">
                            <li><a data-value="No Aplica">{{trans('pasos.NoAplica')}}</a></li>
                        </ul>
                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                    </div>
                </div>
                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso4();" type="button"> <i class="fa fa-save"></i> </button>
                </div>
            </div>
        </form>
    </div>

</div>
<style>

</style>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<!--script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script-->
<script type="text/javascript">
    $('#ale_list').select2({
        placeholder: "Seleccione Medicamento...",
        minimumInputLength: 2,
        ajax: {
            url: "{{route('generico.find')}}",
            dataType: 'json',
            data: function(params) {
                //console.log(params);
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                //console.log(data);
                return {
                    results: data
                };
            },
            cache: true
        },
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);
            return {
                id: term.toUpperCase() + 'xnose',
                text: term.toUpperCase(),
                newTag: true, // add additional parameters
            }
        }
    });

    $('#ale_list').on('change', function(e) {
        guardar_alergia();
    });

    function guardar_alergia() {

        $.ajax({
            type: 'post',
            url: "{{route('n_filiacion')}}", // hc4/HistoriaPacienteController->ingreso
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },

            datatype: 'json',
            data: $("#frm_cuartopaso").serialize(),
            success: function(data) {
                console.log(data);

            },
            error: function(data) {

                console.log(data.responseJSON);

            }
        });
    }

    function guardar_paso4() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.cuartopaso_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#frm_cuartopaso").serialize(),

            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
            },
            error: function() {
                alert('error al cargar');
            }
        });

    }


    $('.clinico a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.ginecologico a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.trauma a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.quirurgico a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.farmacologico a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.psiquiatrico a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });

    $('.familiar a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');
    });
</script>