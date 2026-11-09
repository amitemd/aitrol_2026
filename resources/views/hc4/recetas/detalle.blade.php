<style>
  .hc4-recipe-allergy-alert {
    margin: 0 0 10px;
    padding: 8px 12px;
    border: 1px solid #087eae;
    border-left-width: 4px;
    border-radius: 5px;
    background: #eaf7fb;
    color: #000000 !important;
    font-size: 13px;
  }
  .hc4-recipe-allergy-alert strong { color: #000000 !important; }

  .hc4-recipe-medication-table {
    width: 100% !important;
    min-width: 0;
    table-layout: fixed;
    margin: 0 !important;
    border-color: #087eae !important;
  }

  #area_trabajo .hc4-recipe-table-wrapper {
    display: block !important;
    width: 100% !important;
    max-width: none !important;
  }
  #area_trabajo .hc4-recipe-table-wrapper > .row {
    width: 100% !important;
    margin: 0 !important;
  }
  #area_trabajo .hc4-recipe-table-responsive {
    flex: 0 0 100%;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    overflow-x: auto;
  }
  #area_trabajo .hc4-recipe-medication-table thead {
    background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
  }
  #area_trabajo .hc4-recipe-medication-table thead th {
    border-color: #087eae !important;
    background: transparent !important;
    color: #ffffff !important;
  }
  .hc4-recipe-medication-table tbody td {
    border-color: #c9dee8 !important;
    color: #000000 !important;
  }
  .hc4-recipe-medication-table input,
  .hc4-recipe-medication-table textarea {
    display: block;
    width: 100% !important;
    max-width: 100%;
    box-sizing: border-box;
    border-color: #9ccedf !important;
    color: #000000 !important;
  }
  .hc4-recipe-medication-table .hc4-recipe-action-column {
    width: 46px;
    min-width: 46px;
    padding: 6px 4px !important;
    text-align: center;
    vertical-align: middle;
  }
  .hc4-recipe-medication-table .hc4-recipe-delete-button {
    display: inline-flex;
    width: 30px;
    height: 30px;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 0;
    border-radius: 4px;
  }
  .hc4-recipe-medication-table .bg-blue {
    background: #087eae !important;
    color: #ffffff !important;
  }
</style>

@if(trim((string) $mensaje) !== '')
  <div class="hc4-recipe-allergy-alert"><strong>{{$mensaje}}</strong></div>
@endif
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap hc4-recipe-table-wrapper">
  @if($detalles != '[]')
  <div class="row">
    <div class="table-responsive col-md-12 hc4-recipe-table-responsive">
      <table id="example2" class="table table-bordered table-hover dataTable hc4-recipe-medication-table" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
        <colgroup>
          <col style="width: 24%;">
          <col style="width: 10%;">
          <col style="width: 24%;">
          <col>
          <col style="width: 46px;">
        </colgroup>
        <thead>
          <tr role="row">
            <!--th width="15%" >Genérico</th-->
            <th>Medicina</th>
            <th>Cantidad</th>
	    <th>Prescripción</th>
            <th>Dosis</th>
	    <th class="hc4-recipe-action-column" aria-label="Acciones"></th>
          </tr>
        </thead>
        <tbody>
        @foreach($detalles as $value)
          <tr role="row">
          @php 
            $medicina = $medicinas->where('id',$value->id_medicina)->first();
            $genericos = $medicina->genericos; 
          @endphp
            <!--td>@foreach($genericos as $gen) <span class="bg-blue" style="padding: 3px;border-radius: 10px;"><b>{{$gen->generico->nombre}}</b></span>&nbsp; @endforeach</td-->
            <td style="color: black">{{$medicina->nombre}} <br> @foreach($genericos as $gen) <span class="bg-blue" style="padding: 3px;border-radius: 10px;"><b>{{$gen->generico->nombre}}</b></span>&nbsp; @endforeach</td>
            <td><input type="number" onchange="act_detalle({{$value->id}});" id="cantidad{{$value->id}}" name="cantidad{{$value->id}}" value="{{$value->cantidad}}"></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="detalle_cantidad{{$value->id}}" name="detalle_cantidad{{$value->id}}">{{$value->detalle_cantidad}}</textarea></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="dosis{{$value->id}}" name="dosis{{$value->id}}">{{$value->dosis}}</textarea></td>
            <td class="hc4-recipe-action-column"><button type="button" onclick="det_delete({{$value->id}}, {{$id_receta}})" class="btn-danger btn-sm hc4-recipe-delete-button" title="Eliminar medicina" aria-label="Eliminar medicina"><span class="glyphicon glyphicon-trash"></span></button></td>
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
        

		        $.ajax({
		            type: 'post',
        url:"{{url('detalle_receta/detalle_editar')}}/"+id, //receta.editar_detalle
		            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		            datatype: 'json',
        data: {'cantidad' : $("#cantidad"+id).val(), 'dosis' : $("#dosis"+id).val(), 'detalle_cantidad': $('#detalle_cantidad'+id).val() },
		            success: function(data){
		                //console.log(data);
		                
		                
		            },
		            error: function(data){
		                    
		                }
		        }); 
		    }    







</script>
