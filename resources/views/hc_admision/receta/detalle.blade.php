
<div class="has-error"><span class="help-block"><strong>{{$mensaje}}</strong></span></div>
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  @if($detalles != '[]')
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
        <thead>
          <tr role="row">
            <th width="15%" >Genérico</th>
            <th width="15%" >Medicina</th>
            <th width="25%" >Cantidad</th>
            <th width="40%" >Dosis</th>
            <th width="1%" >&nbsp;</th>
            <th width="25%">Administración en consulta</th>
          </tr>
        </thead>
        <tbody>
        @foreach($detalles as $value)
          <tr role="row">
          	@php $genericos = $medicinas->where('id',$value->id_medicina)->first()->genericos; @endphp
            <td>@foreach($genericos as $gen) <span class="bg-blue" style="padding: 3px;border-radius: 10px;"><b>{{$gen->generico->nombre}}</b></span>&nbsp; @endforeach</td>
            <td>{{$value->medicina->nombre}}</td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="cantidad{{$value->id}}" name="cantidad{{$value->id}}" style="width: 100%;">{{$value->cantidad}}</textarea></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="dosis{{$value->id}}" name="dosis{{$value->id}}" style="width: 100%;">{{$value->dosis}}</textarea></td>
            <td><button onclick="det_delete({{$value->id}})" class="btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>
            <td>@include('hospital.medication._emar_fields', ['detalle' => $value])</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>

<script type="text/javascript">
	
	function act_detalle(id){
        
	    @foreach($detalles as $value)

	    	if({{$value->id}}==id){
		        $.ajax({
		            type: 'post',
		            url:"{{url('receta_detalle/editar_detalle')}}/"+{{$receta}}+"/"+{{$value->id}}, //receta.editar_detalle
		            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		            datatype: 'json',
		            data: {'cantidad' : $("#cantidad{{$value->id}}").val(), 'dosis' : $("#dosis{{$value->id}}").val() },
		            success: function(data){
		                //console.log(data);
		                
		                
		            },
		            error: function(data){
		                    
		                }
		        }); 
		    }    

	    @endforeach    
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
            headers: {'X-CSRF-TOKEN': $('input[name=_token]').first().val()},
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
                $('#emar-panel-' + id).html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + response.msn + '<br><small>eMAR #' + response.administration_id + '</small></div>');
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                alert(response.msn || 'No fue posible registrar la administración.');
                button.prop('disabled', false).html('<i class="fa fa-shield"></i> Confirmar administración');
            }
        });
    }





</script>
