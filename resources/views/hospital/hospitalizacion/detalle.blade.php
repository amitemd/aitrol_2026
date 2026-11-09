<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<style>
  .medication-cards {
    display: grid;
    gap: 14px;
  }
  .medication-card {
    overflow: hidden;
    border: 1px solid #dce9f1;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 5px 18px rgba(7, 72, 112, .08);
  }
  .medication-card__summary {
    display: grid;
    grid-template-columns: minmax(0, 1.8fr) minmax(90px, .55fr) minmax(0, 1.5fr);
    gap: 18px;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #d9e8f1;
    background: linear-gradient(90deg, #eef8fb 0%, #f6f9fc 58%, #fff 100%);
  }
  .medication-card__datum {
    min-width: 0;
  }
  .medication-card__label {
    display: block;
    margin-bottom: 3px;
    color: #508096;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .055em;
    text-transform: uppercase;
  }
  .medication-card__value {
    display: block;
    overflow-wrap: anywhere;
    color: #123f5d;
    font-size: 14px;
    line-height: 1.3;
  }
  .medication-card__value--name {
    color: #074b70;
    font-weight: 800;
  }
  .medication-card__body {
    padding: 12px;
  }
  .emar-panel {
    padding: 0;
  }
  .emar-history {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    margin-bottom: 10px;
    border: 1px solid #d8e8ef !important;
    border-left: 4px solid #16a8be !important;
    border-radius: 8px;
    background: #fff !important;
  }
  .emar-history__title {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-right: 2px;
    color: #315a71;
    white-space: nowrap;
  }
  .emar-history__event {
    padding: 5px 8px;
    border-radius: 7px;
    background: #f4f8fb;
    color: #4e6777;
    line-height: 1.25;
  }
  .emar-history__event .badge {
    margin-right: 4px;
  }
  .emar-control-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 9px 10px;
  }
  .emar-control-grid .form-group {
    min-width: 0;
    margin-bottom: 0;
  }
  .emar-control-grid label {
    display: block;
    margin-bottom: 4px;
    color: #16445f;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .02em;
  }
  .emar-control-grid .form-control {
    width: 100%;
    min-width: 0;
    height: 36px;
    border-color: #cfe0eb;
    border-radius: 7px;
    font-size: 12px;
  }
  .emar-control-grid textarea.form-control {
    height: 68px;
    resize: vertical;
  }
  .emar-span-all {
    grid-column: 1 / -1;
  }
  .emar-footer {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(230px, 1fr) auto;
    gap: 10px;
    align-items: stretch;
    margin-top: 10px;
  }
  .emar-observation label {
    margin-bottom: 4px;
    color: #16445f;
    font-size: 10px;
    font-weight: 700;
  }
  .emar-observation textarea {
    min-height: 72px;
    border-color: #cfe0eb;
    border-radius: 7px;
    font-size: 12px;
    resize: vertical;
  }
  .emar-checks {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 8px 10px;
    border-radius: 8px;
    background: #f4f8fb;
  }
  .emar-checks .custom-control-label {
    color: #173e58;
    font-size: 12px;
    line-height: 1.25;
  }
  .emar-action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  .emar-action .btn {
    width: auto;
    min-width: 150px;
    max-width: 190px;
    min-height: 38px;
    padding: 8px 14px;
    border: 0;
    border-radius: 8px;
    background: linear-gradient(135deg, #0879b6, #0ba5bc);
    box-shadow: 0 5px 12px rgba(8, 121, 182, .18);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    white-space: normal;
  }
  @media (max-width: 900px) {
    .emar-footer {
      grid-template-columns: 1fr;
    }
    .emar-action { justify-content: flex-start; }
  }
  @media (max-width: 560px) {
    .medication-card__summary {
      grid-template-columns: 1fr;
      gap: 9px;
    }
    .emar-control-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="card">

  <div class="card-body" style="padding: 1px,">
    <div class="row">
      <input type="hidden" name="contador" id="contador" value="1">
      <div class="col-12">
        <div class="row">
          <div class="col-md-12" id="tabla_detalle">
            <div class="medication-cards" aria-live="polite">
                @forelse($detalles as $detalle)
                @php $producto = $productosPorDetalle->get((string) $detalle->id); @endphp
                <article class="medication-card">
                  <input type="hidden" name="d{{$detalle->id_medicina}}" value="{{$detalle->id_medicina}}">
                  @if($producto!=null)
                  <input type="hidden" name="p{{$producto->id}}" value="{{$producto->id}}">
                  @endif
                  <header class="medication-card__summary">
                    <div class="medication-card__datum">
                      <span class="medication-card__label">Medicamento</span>
                      <span class="medication-card__value medication-card__value--name">{{$detalle->nombre}}</span>
                    </div>
                    <div class="medication-card__datum">
                      <span class="medication-card__label">Cantidad</span>
                      <span class="medication-card__value">{{$detalle->cantidad}}</span>
                    </div>
                    <div class="medication-card__datum">
                      <span class="medication-card__label">Posología</span>
                      <span class="medication-card__value">{{$detalle->dosis}}</span>
                    </div>
                  </header>
                  <div class="medication-card__body">
                    <div class="emar-panel">
                    @php $eventosEmar = $administracionesPorDetalle[(string) $detalle->id] ?? []; @endphp
                    @if(count($eventosEmar))
                    <div class="emar-history" id="historialEmar{{$detalle->id}}">
                      <strong class="emar-history__title"><i class="fa fa-clock-o"></i> Historial</strong>
                      @foreach(array_reverse($eventosEmar) as $eventoEmar)
                        <div class="small emar-history__event">
                          <span class="badge {{$eventoEmar['estado'] === 'ADMINISTERED' ? 'badge-success' : 'badge-warning'}}">{{$eventoEmar['estado'] === 'ADMINISTERED' ? 'ADMINISTRADO' : 'OMITIDO'}}</span>
                          <span id="horaEmar{{$eventoEmar['emar_id']}}">{{$eventoEmar['fecha']}} {{$eventoEmar['hora']}}</span> · {{$eventoEmar['responsable']}}
                          @if($eventoEmar['programada'] !== '')<br><span class="text-muted">Programada: {{$eventoEmar['programada']}}</span>@endif
                          @if($eventoEmar['motivo_omision'] !== '')<br><span class="text-muted">Motivo: {{$eventoEmar['motivo_omision']}}</span>@endif
                        </div>
                      @endforeach
                    </div>
                    @endif
                    {{-- Cada dosis programada se registra por separado; una administración
                         previa no bloquea las horas posteriores de la misma prescripción. --}}
                    @if(!is_null($producto))
                    <div class="emar-fields" id="emar{{$detalle->id}}">
                      <div class="emar-control-grid">
                        <div class="form-group">
                          <label>Dosis aplicada</label>
                          <input id="dosis{{$detalle->id}}" type="text" class="form-control form-control-sm" value="{{$detalle->dosis}}">
                        </div>
                        <div class="form-group">
                          <label>Vía</label>
                          <select id="via{{$detalle->id}}" class="form-control form-control-sm">
                            <option value="">Seleccione</option>
                            <option value="ORAL">Oral</option>
                            <option value="INTRAVENOUS">Intravenosa</option>
                            <option value="INTRAMUSCULAR">Intramuscular</option>
                            <option value="SUBCUTANEOUS">Subcutánea</option>
                            <option value="TOPICAL">Tópica</option>
                            <option value="INHALATION">Inhalatoria</option>
                            <option value="OTHER">Otra</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label>Hora programada</label>
                          <input id="programada{{$detalle->id}}" type="datetime-local" class="form-control form-control-sm" value="{{date('Y-m-d\TH:i')}}">
                        </div>
                        <div class="form-group">
                          <label>Hora real de entrega/administración</label>
                          <input id="real{{$detalle->id}}" type="datetime-local" class="form-control form-control-sm" value="{{date('Y-m-d\TH:i')}}">
                        </div>
                        <div class="form-group">
                          <label>Resultado</label>
                          <select id="estado{{$detalle->id}}" class="form-control form-control-sm" onchange="cambiarEstadoEmar('{{$detalle->id}}')">
                            <option value="ADMINISTERED">Administrado</option>
                            <option value="OMITTED">Omitido</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label>Serie de inventario</label>
                          <input id="serie{{$detalle->id}}" type="text" class="form-control form-control-sm" placeholder="Se asignará automáticamente por FIFO" readonly>
                        </div>
                        <div class="form-group emar-span-all" id="omisionGrupo{{$detalle->id}}" style="display:none">
                          <label>Motivo de omisión</label>
                          <textarea id="omision{{$detalle->id}}" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                      </div>
                      <div class="emar-footer">
                        <div class="emar-observation">
                          <label>Resultado u observación</label>
                          <textarea id="resultado{{$detalle->id}}" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="emar-checks">
                          <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="pacienteVerificado{{$detalle->id}}">
                            <label class="custom-control-label" for="pacienteVerificado{{$detalle->id}}">Identidad del paciente verificada</label>
                          </div>
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="medicamentoVerificado{{$detalle->id}}">
                            <label class="custom-control-label" for="medicamentoVerificado{{$detalle->id}}">Medicamento comparado con la orden activa</label>
                          </div>
                        </div>
                        <div class="emar-action">
                          <button type="button" class="btn btn-primary btn-sm" id="administrar{{$detalle->id}}" onclick="registrarAdministracion('{{$detalle->id}}')">
                            <i class="fa fa-medkit"></i> {{count($eventosEmar) ? 'Registrar otra hora' : 'Descargar y registrar administración'}}
                          </button>
                        </div>
                      </div>
                    </div>
                    @else
                    <div class="alert alert-warning py-2 mb-0">
                      No se encontró un producto de inventario inequívoco para este medicamento. Revise que el código operacional y la presentación coincidan con el catálogo de productos.
                    </div>
                    @endif
                    </div>
                  </div>
                </article>
                @empty
                <div class="alert alert-warning mb-0">
                  <i class="fa fa-exclamation-triangle"></i>
                  Esta receta todavía no contiene medicamentos. El médico debe agregarlos y guardar cantidad y posología antes del descargo.
                </div>
                @endforelse
            </div>
          </div>
        </div>
        <br>
        <div class="row" id="descargoReceta{{$receta->id}}">
          <input type="hidden" id="procedimientoDescargo{{$receta->id}}" value="{{$procedimientoDescargo ? $procedimientoDescargo->id : ''}}">
          <input type="hidden" class="js-descargo-bodega" name="id_bodega" id="id_bodega{{$receta->id}}" value="{{$bodegaAdministracion ? $bodegaAdministracion->inventory_id : ''}}">

          <div class="col-12 alert alert-info" role="alert">
            <strong>Entrega y administración de medicamentos</strong><br>
            <small>Complete los datos eMAR de cada medicamento y pulse <strong>Descargar y registrar administración</strong>. El sistema trasladará por FIFO una unidad desde la bodega principal cuando sea necesario y la egresará desde la bodega de administración al paciente; ambos movimientos quedarán en Kardex y en el Formulario 022.</small>
          </div>
          @if(!$procedimientoDescargo)
          <div class="col-12 alert alert-danger">
            No existe un procedimiento asociado a esta receta; no es posible registrar el descargo.
          </div>
          @endif
          @if($bodegaAdministracionError)
          <div class="col-12 alert alert-danger">
            {{$bodegaAdministracionError}} Revise la configuración empresarial de bodegas antes de realizar el descargo.
          </div>
          @endif
          <div class="col-md-6">
            <label for="bodega_administracion_nombre{{$receta->id}}">Bodega de administración de medicamentos</label>
            <input type="text" id="bodega_administracion_nombre{{$receta->id}}" class="form-control" value="{{$bodegaAdministracion ? $bodegaAdministracion->name : 'No configurada'}}" readonly>
            <small class="text-muted d-block">Bodega exclusiva de la empresa para la entrega y administración al paciente.</small>
          </div>
          <div class="col-12 mt-2" id="mensajeDescargo{{$receta->id}}" aria-live="polite"></div>
          <div class="col-12" id="listado{{$receta->id}}"></div>
        </div>


        <!-- <div class="row">
          <div class="col-md-12">
            <h3><b>{{trans('eenfermeria.MedicamentosProcedimiento')}}: </b></h3>

          </div>
          <div class="col-md-4">
            <label>{{trans('eenfermeria.SeleccioneProcedimiento')}}</label>
          </div>
          <div class="col-md-6">
            <label>{{trans('eenfermeria.SeleccioneMedicamento')}}</label>
          </div>
          <div class="col-md-4">
            <select name="hc_procedimientos" id="hc_procedimientos" class="form-control select2">
              <option value="">{{trans('hospital.Seleccione')}} ...</option>

            </select>
          </div>

          <div class="input-group input-group col-md-4">
            <input type="hidden" id="id_plantilla" name="id_plantilla">
            <input type="hidden" id="id_plantilla_2" name="id_plantilla_2">
            <input type="text" class="form-control" id="nom_plantilla" name="nom_plantilla" required>
            <span class="input-group-btn">
              <button type="button" class="btn btn-info btn-flat" onclick="mostrar_planilla();">{{trans('erol.Buscar')}}</button>
            </span>
          </div>
          <br>
          <div class="input-group input-group col-md-12">
            <center>
              <button type="button" onclick="guardar_plantilla_basica()" class="btn btn-success">{{trans('econtrolsintomas.Guardar')}}</button>
            </center>
          </div>
          <div class="col-md-12" id="detalle"></div>
        </div> -->
      </div>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    listado_prod('{{$receta->id}}');
  });

  $("#nom_plantilla").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('enfermeria.nombre_plantilla')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          response(data);

        }
      })
    },
    minLength: 2,
    select: function(data, ui) {
      //console.log("select",ui.item.id);
      $('#id_plantilla').val(ui.item.id);
      $('#id_plantilla_2').val(ui.item.id);

    }
  });

  $("#nom_plantilla").change(function() {
    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.nombre_plantilla2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#nom_plantilla"),
      success: function(data) {
        if (data != '0') {
          $('#id_plantilla').val(data.id);
          $('#id_plantilla_2').val(data.id);

        }
      },
      error: function(data) {

      }
    })
  });

  function mostrar_planilla() {
    var plantilla = document.getElementById("id_plantilla").value;
    if (plantilla == "") {
      Swal.fire({
        icon: 'error',
        title: 'Mensaje!!',
        text: 'Digite la planilla que desea agregar',
      });
      return;
    }
    $.ajax({
      type: 'get',
      url: "{{ url('insumos/plantillas/item_lista2')}}/" + plantilla + "/{{$hcid}}/1",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      success: function(data) {
        $('#detalle').empty().html(data);
        $(".oculto").show();
      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function cambiarEstadoEmar(id) {
    var omitido = $('#estado' + id).val() === 'OMITTED';
    $('#omisionGrupo' + id).toggle(omitido);
    $('#serie' + id).prop('disabled', omitido);
    $('#administrar' + id).html(omitido
      ? '<i class="fa fa-ban"></i> Registrar omisión'
      : '<i class="fa fa-medkit"></i> Descargar y registrar administración');
  }

  function registrarAdministracion(id) {
    var status = $('#estado' + id).val();
    var dose = $.trim($('#dosis' + id).val());
    var route = $('#via' + id).val();
    var result = $.trim($('#resultado' + id).val());
    var patientVerified = $('#pacienteVerificado' + id).is(':checked');
    var medicationVerified = $('#medicamentoVerificado' + id).is(':checked');
    if (!dose || !route || !$('#programada' + id).val() || !$('#real' + id).val()) {
      Swal.fire({icon: 'warning', title: 'Complete la administración', text: 'Registre dosis, vía, hora programada y hora real.'});
      return;
    }
    if (!patientVerified || !medicationVerified) {
      Swal.fire({icon: 'warning', title: 'Verificación requerida', text: 'Confirme la identidad del paciente y el medicamento indicado.'});
      return;
    }
    if (status === 'ADMINISTERED' && !result) {
      Swal.fire({icon: 'warning', title: 'Resultado requerido', text: 'Registre el resultado u observación posterior a la administración.'});
      return;
    }
    if (status === 'OMITTED' && !$.trim($('#omision' + id).val())) {
      Swal.fire({icon: 'warning', title: 'Motivo requerido', text: 'Indique por qué se omitió la dosis.'});
      return;
    }
    var button = $('#administrar' + id);
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verificando...');
    $.ajax({
      type: "post",
      url: "{{route('hospitalizacion.descargo_enfermeria_detalle_store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        id: id,
        series: $('#serie' + id).val(),
        dose: dose,
        route: route,
        scheduled_at: $('#programada' + id).val(),
        administered_at: $('#real' + id).val(),
        status: status,
        omission_reason: $('#omision' + id).val(),
        clinical_result: result,
        patient_verified: patientVerified ? 1 : 0,
        medication_verified: medicationVerified ? 1 : 0,
        auto_discharge: status === 'ADMINISTERED' ? 1 : 0,
        id_bodega: $('#id_bodega{{$receta->id}}').val(),
        id_hc_procedimientos: $('#procedimientoDescargo{{$receta->id}}').val()
      },

      success: function(datahtml) {
        var notice = $('<div class="alert alert-success mb-1"><i class="fa fa-check-circle"></i> <span></span></div>');
        notice.find('span').text(datahtml.msn + ' · Entregado ' + datahtml.fecha + ' ' + datahtml.hora + ' · eMAR #' + datahtml.administration_id);
        $('#emar' + id).prepend(notice);
        $('#sp_ex' + id).text('Último registro eMAR #' + datahtml.administration_id);
        $('#resultado' + id).val('');
        $('#omision' + id).val('');
        if (datahtml.serie) $('#serie' + id).val(datahtml.serie);
        listado_prod('{{$receta->id}}');
        button.prop('disabled', false);
        cambiarEstadoEmar(id);
      },
      error: function(xhr) {
        var response = xhr.responseJSON || {};
        Swal.fire({icon: 'error', title: 'No se registró la administración', text: response.msn || 'No fue posible registrar la administración.'});
        button.prop('disabled', false).html('<i class="fa fa-medkit"></i> Descargar y registrar administración');
      }
    });
  }

</script>
