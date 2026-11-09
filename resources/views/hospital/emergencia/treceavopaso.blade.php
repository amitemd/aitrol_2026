@php
  date_default_timezone_set('America/Guayaquil');
  $fecha = date("Y-m-d");
  $id_doctor = Auth::user()->id;

@endphp

<div class="card">
  <div class="card-header bg bg-primary">
    <div class="col-md-12">
      <div class="row">
        <div class="d-flex align-items-center col-md-12">
          <span class="sradio">13</span>
          <h4 class="card-title ml-25 colorbasic">
            {{trans('paso2.PlandeTratamiento')}}
          </h4>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body">

    <div class="col-12">
      <div class="row">
        <div class="col-12">
          <form id="form_paso13">
            {{ csrf_field() }}
            @php
            $form008 = $solicitud->form008->first();
            @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <input type="hidden" name="id_seguro" id="id_seguro" value="{{$solicitud->id_seguro_publico}}">
            <input type="hidden" name="contador" id="contador" value="1">
            <input type="hidden" name="doctor" id="doctor" value="{{$id_doctor}}">
            <div class="form-group">
              <label for="inputid" class="control-label">{{trans('paso2.Medicina')}}</label>
              <div class="row">
                <div class=" col-md-9 col-sm-9 col-12">
                  <select name="generico" id="generico" class="form-control select2" >
                    <option value="">{{trans('paso2.Seleccione')}}</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <button type="button" class="btn btn-primary" style="background-color: #004AC1;" onclick="buscar_nombre_medicina()">
                    <span class="fa fa-plus"></span>{{trans('paso2.Agregar')}}
                  </button>
                  <br>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                </div>
                
                <div class="col-md-12" id="tabla_detalle">
                  <table role="table" aria-busy="false" aria-colcount="5" class="table b-table">
                    <thead role="rowgroup" class="">
                      <tr role="row" class="">
                        <th width="15%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
                        <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
                        <th width="40%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                        <th width="10%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                        <th width="25%" role="columnheader" scope="col" aria-colindex="5" class=""><div>Administración eMAR</div></th>
                      </tr>
                    </thead>
                    <tbody role="rowgroup">
                      @if(!is_null($detalles))
                      @foreach($detalles as $detalle)
                      <tr role="row" class="">
                        <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">{{$detalle->nombre}}</span></td>
                        <td aria-colindex="2" role="cell" class=""><input class="form-control" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="editar_medicina('{{$detalle->id}}')"></td>
                        <td aria-colindex="3" role="cell" class="">
                          <textarea wrap="soft" class="mb-1 mb-xl-0 form-control" style="resize: none; overflow-y: scroll; height: 92px;" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" onchange="editar_medicina('{{$detalle->id}}')">{{$detalle->dosis}}</textarea>
                        </td>
                        <td aria-colindex="4" role="cell" class="">
                          <button id="edit{{$detalle->id}}" type="button" class="btn btn-warning btn-sm" onclick="">
                            <i class="fa fa-edit"></i>
                          </button>
                          <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm" onclick="eliminar_medicina('{{$detalle->id}}');">
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                        <td aria-colindex="5" role="cell" class="">
                          @include('hospital.medication._emar_fields', ['detalle' => $detalle])
                        </td>
                      </tr>
                      @endforeach
                      @endif
                      
                    </tbody>
                  </table>
                </div>
           
               
                
              </div>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

</div>

<div class="card mt-3 border-danger">
  <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
    <strong><i class="fa fa-bolt"></i> Excepción por emergencia sin orden previa</strong>
    <button type="button" class="btn btn-light btn-sm" onclick="$('#emergency-exception-form').toggle()">Registrar excepción</button>
  </div>
  <div class="card-body" id="emergency-exception-form" style="display:none;">
    <div class="alert alert-warning">
      Solo se permite ante una emergencia real. Genera una administración eMAR y una orden provisional que debe regularizarse en máximo 60 minutos.
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Medicamento</label>
        <select id="emergency-medication" class="form-control select2-emergency" style="width:100%"><option value="">Seleccione</option></select>
      </div>
      <div class="form-group col-md-3">
        <label>Dosis aplicada</label>
        <input id="emergency-dose" class="form-control">
      </div>
      <div class="form-group col-md-3">
        <label>Vía</label>
        <select id="emergency-route" class="form-control">
          <option value="">Seleccione</option><option value="ORAL">Oral</option><option value="INTRAVENOUS">Intravenosa</option>
          <option value="INTRAMUSCULAR">Intramuscular</option><option value="SUBCUTANEOUS">Subcutánea</option>
          <option value="INHALATION">Inhalatoria</option><option value="OTHER">Otra</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4"><label>Hora real</label><input id="emergency-time" type="datetime-local" class="form-control" value="{{date('Y-m-d\TH:i')}}"></div>
      <div class="form-group col-md-4"><label>Serie</label><input id="emergency-series" class="form-control"></div>
      <div class="form-group col-md-4"><label>Código del protocolo</label><input id="emergency-protocol" class="form-control" placeholder="Ej. PARO-01"></div>
    </div>
    <div class="form-group"><label>Justificación clínica</label><textarea id="emergency-justification" class="form-control" rows="2"></textarea></div>
    <div class="form-group"><label>Resultado u observación</label><textarea id="emergency-result" class="form-control" rows="2"></textarea></div>
    <label class="d-block"><input id="emergency-patient-verified" type="checkbox"> Confirmo identidad del paciente</label>
    <label class="d-block mb-2"><input id="emergency-medication-verified" type="checkbox"> Confirmo medicamento, dosis, vía y hora</label>
    <button id="emergency-exception-save" type="button" class="btn btn-danger" onclick="registrarExcepcionEmergencia()">
      <i class="fa fa-exclamation-triangle"></i> Administrar bajo protocolo
    </button>
  </div>
</div>

@if(isset($pendingMedicationExceptions) && $pendingMedicationExceptions->count())
<div class="card mt-3 border-warning" id="pending-emergency-medications">
  <div class="card-header bg-warning"><strong><i class="fa fa-clock-o"></i> Regularizaciones médicas pendientes</strong></div>
  <div class="card-body">
    @foreach($pendingMedicationExceptions as $exception)
      <div class="alert {{\Carbon\Carbon::now()->gt($exception->regularization_due_at) ? 'alert-danger' : 'alert-warning'}}" id="exception-row-{{$exception->id}}">
        <strong>{{$exception->medicine ? $exception->medicine->nombre : 'Medicamento'}}</strong> — protocolo {{$exception->protocol_code}}.<br>
        <small>Regularizar hasta: {{$exception->regularization_due_at->format('Y-m-d H:i')}}</small>
        <div class="form-row mt-2">
          <div class="col-md-9">
            <select id="regularization-order-{{$exception->id}}" class="form-control">
              <option value="">Seleccione la orden médica creada</option>
              @foreach($detalles as $detalle)
                @if((string)$detalle->id_medicina === (string)$exception->medication_id)
                  <option value="{{$detalle->id}}">{{$detalle->nombre}} — {{$detalle->dosis}}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><button type="button" class="btn btn-success btn-block" onclick="regularizarExcepcion('{{$exception->id}}')">Vincular orden</button></div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endif
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  cargar_doctor();

  function cargar_doctor(){
    var doctor = $('#doctor').val();
    
    $.ajax({
      type: 'post',
      url:"{{route('13vopaso.actualizar_doctor',['receta' => $receta->id ])}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: { 'id_doctor' : doctor },

      success: function(data){ 
        
      },

      error: function(data){
      
      }

    }) 

  }

  $('.select2').select2({
    ajax: {
      url: '{{route("hospitalizacion.genericos")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        //console.log(data);

        return {
          results: data
        };
      }

    }
  });

  $('.select2-emergency').select2({
    dropdownParent: $('#emergency-exception-form'),
    ajax: {
      url: '{{route("hospitalizacion.genericos")}}',
      data: function(params) { return {search: params.term, type: 'public'}; },
      processResults: function(data) { return {results: data}; }
    }
  });

  function buscar_nombre_medicina(){

      $.ajax({
        type: 'post',
        url:"{{route('13vopaso.guardar_medicina',['solic' => $solicitud->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: { 'id_generico' : $("#generico").val() },

          success: function(data){

            console.log(data);
            $('#tabla_detalle').html(data)
            
            },
            error: function(data){
            }
      })
  }

  function addNewBlade(i) {
    var data = $("#generico").val();
    var iva = $('option:selected', '#generico').html();
    console.log(iva);
    var contador = $("#contador").val();
    var htmlTable = $('#tableIndicate').html();
    var midiv_pago = '<div class="card shadow-none bg-transparent border-primary"> <div class="card-header"> <h4 class="card-title">' + iva + '</h4> </div> <div class="card-body"> <table class="table b-table table-hover b-table-fixed">  <thead><tr><th>#</th><th>{{trans('paso2.INDICACIONES')}}</th><th>{{trans('paso2.MEDICAMENTO')}}</th><th>{{trans('paso2.POSOLOGIA')}}</th><th><button class="btn btn-primary" onclick="add(this)" type="button"> <i class="fa fa-plus"> </i> </button></th></tr></thead><tbody class="bodys" ><td>' + contador + '</td><td> <input class="genericoAdded" name="id_generico[]" type="hidden" value="' + data + '">  <input class="form-control" name="indicaciones[]"></td> <td><input class="form-control" type="text" name="medicamentos[]" value="' + iva + '" placeholder="{{trans('paso2.IngreseMedicamentos')}}" readonly></td><td><input class="form-control" type="text" name="posologia[]" placeholder="{{trans('paso2.IngresePosologia')}}"></td> <td>  <button class="btn btn-danger" onclick="erase(this)" type="button" > <i class="fa fa-remove">   </i> </button> </td></tbody> </table></div> </div>';
    contador++;
    $("#contador").val(contador);
    $('#addPlace').append(midiv_pago);
  }

  function erase(e) {
    var contador = $("#contador").val();
    contador--;
    if (contador < 0) {
      contador = 0;
    }
    $('#contador').val(contador);
    $(e).parent().parent().remove();
  }

  function add(e) {
    var cont = 1;
    var eu = $(e).parent().parent().parent().parent().parent().parent().find('.card-title').html();
    var midiv_pago = document.createElement("tr");
    var innerHTML = '<tr><td>' + cont + '</td> <td><input class="form-control" name="eu[]" value="1"></td> <td>' + eu + '</td> <td><input class="form-control" name="eu[]" value="1"></td> <td><button class="btn btn-danger" onclick="erase(this)" type="button" > <i class="fa fa-remove"> </i> </button></td> </tr>';
    console.log($(e).parent().parent().parent().parent().parent().parent().html());
    $(e).parent().parent().parent().parent().parent().find('tbody').append(innerHTML);
  }

  function supera() {
    /* Swal.fire({
       title: 'Atencion!',
       text: 'Estamos trabajando en estos momentos',
       imageUrl: 'https://i.ibb.co/smcnM0c/mejorando.jpg',
       imageWidth: 400,
       imageHeight: 300,
       imageAlt: 'Custom image',
     }); */
    $.ajax({
      type: "post",
      url: "{{route('hospital.guardar_terceavo')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#form_paso3").serialize(),

      success: function(datahtml, data) {
        $("#content").html(datahtml);
        return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
      },
      error: function() {
        alert("{{trans('pasos.ErrorCargar')}}");
      }
    });

  }

  function editar_medicina(id_detalle){

    $.ajax({
        type: 'post',
        url: "{{url('hospital/form005/editar_medicina')}}/"+id_detalle,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_paso13").serialize(),
        success: function(data){

        },
        error:  function(){
            alert("{{trans('pasos.ErrorCargar')}}");
        }
    });
  }

  function eliminar_medicina(id_detalle){
    //console.log(id_detalle);
    var id_solicitud = '{{$solicitud->id}}';
    var id_receta = '{{$receta->id}}';

    $.ajax({
        async: true,
        type: "DELETE",
        url: "{{url('hospital/formu_005/eliminar_medicina')}}/"+id_detalle,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {
          'id_solicitud': id_solicitud,
          'id_receta': id_receta,
        },
        datatype: "html",
        success: function(datahtml){

            $("#tabla_detalle").html(datahtml);

        },
        error:  function(){
            alert("{{trans('pasos.ErrorCargar')}}");
        }
    });
  }

  function alternarEmar(id) {
    $('#emar-form-' + id).toggle();
  }

  function cambiarEstadoEmarClinico(id) {
    var omitido = $('#emar-estado-' + id).val() === 'OMITTED';
    $('#emar-omision-grupo-' + id).toggle(omitido);
    $('#emar-serie-' + id).prop('disabled', omitido);
  }

  function registrarEmarClinico(id) {
    var button = $('#emar-guardar-' + id);
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verificando...');
    $.ajax({
      type: 'post',
      url: "{{route('clinical_medication.store')}}",
      headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
      dataType: 'json',
      data: {
        id: id,
        series: $('#emar-serie-' + id).val(),
        dose: $('#emar-dosis-' + id).val(),
        route: $('#emar-via-' + id).val(),
        scheduled_at: $('#emar-programada-' + id).val(),
        administered_at: $('#emar-real-' + id).val(),
        status: $('#emar-estado-' + id).val(),
        omission_reason: $('#emar-omision-' + id).val(),
        clinical_result: $('#emar-resultado-' + id).val(),
        patient_verified: $('#emar-paciente-' + id).is(':checked') ? 1 : 0,
        medication_verified: $('#emar-medicamento-' + id).is(':checked') ? 1 : 0
      },
      success: function(response) {
        $('#emar-panel-' + id).html('<div class="alert alert-success mb-0"><i class="fa fa-check-circle"></i> ' + response.msn + '<br><small>eMAR #' + response.administration_id + '</small></div>');
      },
      error: function(xhr) {
        var response = xhr.responseJSON || {};
        alert(response.msn || 'No fue posible registrar la administración.');
        button.prop('disabled', false).html('<i class="fa fa-shield"></i> Confirmar administración');
      }
    });
  }

  function registrarExcepcionEmergencia() {
    var button = $('#emergency-exception-save');
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validando protocolo...');
    $.ajax({
      type: 'post',
      url: "{{route('clinical_medication.emergency_exception')}}",
      headers: {'X-CSRF-TOKEN': $('input[name=_token]').first().val()},
      dataType: 'json',
      data: {
        solicitud_id: '{{$solicitud->id}}',
        medication_id: $('#emergency-medication').val(),
        dose: $('#emergency-dose').val(),
        route: $('#emergency-route').val(),
        administered_at: $('#emergency-time').val(),
        series: $('#emergency-series').val(),
        protocol_code: $('#emergency-protocol').val(),
        justification: $('#emergency-justification').val(),
        clinical_result: $('#emergency-result').val(),
        patient_verified: $('#emergency-patient-verified').is(':checked') ? 1 : 0,
        medication_verified: $('#emergency-medication-verified').is(':checked') ? 1 : 0
      },
      success: function(response) {
        alert(response.msn + '\nRegularizar hasta: ' + response.regularization_due_at);
        cargar_paso(13);
      },
      error: function(xhr) {
        var response = xhr.responseJSON || {};
        alert(response.msn || 'No fue posible registrar la excepción.');
        button.prop('disabled', false).html('<i class="fa fa-exclamation-triangle"></i> Administrar bajo protocolo');
      }
    });
  }

  function regularizarExcepcion(exceptionId) {
    $.ajax({
      type: 'post',
      url: "{{url('hospital/medicacion/emergencia/excepcion')}}/" + exceptionId + '/regularizar',
      headers: {'X-CSRF-TOKEN': $('input[name=_token]').first().val()},
      dataType: 'json',
      data: {recipe_detail_id: $('#regularization-order-' + exceptionId).val()},
      success: function(response) {
        $('#exception-row-' + exceptionId).removeClass('alert-warning alert-danger').addClass('alert-success').html('<i class="fa fa-check-circle"></i> ' + response.msn);
      },
      error: function(xhr) {
        var response = xhr.responseJSON || {};
        alert(response.msn || 'No fue posible regularizar la excepción.');
      }
    });
  }



</script>
