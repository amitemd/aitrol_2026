
<div class="has-error"><span class="help-block"><strong>{{$mensaje}}</strong></span></div>
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  @if($detalles != '[]')
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
        <thead>
          <tr role="row">
            <!--th width="25%" >@lang('hc4.generico')</th-->
            <th width="25%" style="color: black" >@lang('hc4.medicina')</th>
            <th width="5%" style="color: black">@lang('hc4.cantidad')</th>
            <th width="25%" style="color: black">@lang('hc4.prescripcion')</th>
            <th width="40%" style="color: black">@lang('hc4.dosis')</th>
            <th width="5%" style="color: black">&nbsp;</th>
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
            <td><input type="number" onchange="act_detalle({{$value->id}});" id="cantidad{{$value->id}}" name="cantidad{{$value->id}}" style="width: 90%;" value="{{$value->cantidad}}"></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="detalle_cantidad{{$value->id}}" name="detalle_cantidad{{$value->id}}" style="width: 95%;">{{$value->detalle_cantidad}}</textarea></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="dosis{{$value->id}}" name="dosis{{$value->id}}" style="width: 95%;">{{$value->dosis}}</textarea></td>
            <td><button type="button" onclick="det_delete({{$value->id}}, {{$id_receta}})" class="btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>         
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