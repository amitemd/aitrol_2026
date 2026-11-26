<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

@include('hospital.emergencia.partials.clinical-step-styles')

<div class="card clinical-step-card">
    <div class="card-header clinical-step-primary-header">
        <h4 class="card-title clinical-step-primary-title">{{trans('pasos.ExamenFisicoyDiagnostico')}}</h4>
    </div>
    <div class="card-body clinical-step-body">
        <form id="form_paso7">
            @php
            $form008 = $solicitud->form008->first();
            $examFieldGroups = \Sis_medico\Services\HospitalEpisodePhysicalExam::fieldGroups();
            @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="clinical-exam-sections">
                @foreach($examFieldGroups as $group => $options)
                    <section class="clinical-exam-section" aria-labelledby="exam-group-{{ \Illuminate\Support\Str::slug($group) }}">
                        <h5 id="exam-group-{{ \Illuminate\Support\Str::slug($group) }}" class="clinical-exam-section__title">{{ $group }}</h5>
                        <div class="clinical-exam-grid">
                            @foreach($options as $code => $option)
                                <div class="col-md-12">
                                    <b>{{ $code }}. {{ $option['label'] }}</b>
                                </div>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" name="{{ $option['field'] }}" id="{{ $option['field'] }}" value="{{ $form008->{$option['field']} }}">
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            @include('hospital.emergencia.partials.examen-fisico-svg')
            <div class="col-md-12 clinical-form-actions">
                <button class="btn btn-primary clinical-save-button" onclick="guardar_paso7();" type="button">
                    <i class="fa fa-save"></i> Guardar
                </button>
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
    (function initializePhysicalExamBodyMap() {
        var map = document.querySelector('#form_paso7 .clinical-body-map-svg');
        var status = document.getElementById('clinical-body-map-status');
        if (!map) return;
        var regions = map.querySelectorAll('[data-exam-target]');

        function setActiveRegion(target, shouldFocus) {
            var input = document.getElementById(target);
            Array.prototype.forEach.call(regions, function(region) {
                region.classList.toggle('is-active', region.getAttribute('data-exam-target') === target);
            });
            if (!input) return;
            status.textContent = input.closest('.col-md-12').previousElementSibling.textContent.trim();
            if (shouldFocus) {
                input.focus();
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function refreshCompletedRegions() {
            Array.prototype.forEach.call(regions, function(region) {
                var input = document.getElementById(region.getAttribute('data-exam-target'));
                region.classList.toggle('has-value', input && input.value.trim() !== '');
            });
        }

        Array.prototype.forEach.call(regions, function(region) {
            var target = region.getAttribute('data-exam-target');
            var input = document.getElementById(target);
            region.addEventListener('click', function() { setActiveRegion(target, true); });
            region.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    setActiveRegion(target, true);
                }
            });
            if (input) {
                input.addEventListener('focus', function() { setActiveRegion(target, false); });
                input.addEventListener('input', refreshCompletedRegions);
            }
        });
        refreshCompletedRegions();
    })();

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
                alert("{{trans('pasos.ErrorCargar')}}");
            }
        });

    }
</script>
