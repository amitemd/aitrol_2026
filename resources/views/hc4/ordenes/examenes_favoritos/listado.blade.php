<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div id="alerta_ok" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>Seleccione los examenes
</div>
<form id="form_buscador_favoritos">
  <div class="row">
    <div class="form-group col-4" style="margin-bottom: 0;">
      <div class="input-group mb-3" style="margin-bottom: 0;">
          <input id="buscador" name="buscador" type="text" class="form-control" placeholder="Buscar Examen" onchange="quitar_todos();cargar_buscador();">
          <div class="input-group-append">
            <span class="input-group-text"><i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador').value = '';cargar_buscador()"></i></span>
          </div>
       </div>
    </div>
    <div class="form-group col-4" style="margin-bottom: 0;">
      <div class="input-group mb-3" style="margin-bottom: 0;">
          <input id="buscador2" name="buscador2" type="text" class="form-control" placeholder="Buscar Agrupador" onchange="quitar_todos();cargar_buscador();">
          <div class="input-group-append">
            <span class="input-group-text"><i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador2').value = '';cargar_buscador()"></i></span>
          </div>
       </div>
    </div>
    <div class="form-group col-1" style="margin-bottom: 0;">
      <button class="btn btn-success" onclick="" type="button"><span class="glyphicon glyphicon-search"></span></button>
    </div>  
    <div class="form-group col-3" style="margin-bottom: 0;">
      <input id="seleccionados" name="seleccionados" type="checkbox" class="flat-red" onchange="quitar_buscador();cargar_buscador();" value="1" ><label style="color: red;font-size: 14px;"> Ver Seleccionados</label>
    </div>
  </div>
</form>  
<div id="xbuscador_fav">
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="table-responsive col-12">
      <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <tbody>
       		@php  $cambia = 0; $contador = 0; @endphp	
       		@foreach($examenes_labs as $examen)
       			@if($cambia != $examen->id_examen_agrupador_labs)
       				@php $contador = 0; @endphp
       				<tr>
              	<td colspan="4" style="background-color: #ff6600;color: white;margin: 0px;padding: 0;">{{$agrupador_labs->where('id',$examen->id_examen_agrupador_labs)->first()->nombre}}</td>
              </tr>
              @php $cambia = $examen->id_examen_agrupador_labs; @endphp	
       			@endif
       			@if($contador == 0)
       			<tr >
       			@endif	
                  <td style="padding: 5px;"><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange"></td>
                  <td style="padding: 5px;" >{{$examen->descripcion}}</td>

                  @php $contador ++; @endphp
                  @if($contador == 2) @php $contador = 0; @endphp @endif
                @if($contador == 0)   
                </tr>
                @endif
       		@endforeach

        </tbody>
      </table>
    </div>
  </div>
</div>  
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  }); 

  $('input[type="checkbox"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  });

  $('input[type="checkbox"].flat-red').on('ifChecked', function(event){
    quitar_buscador();
    cargar_buscador();
  });

  $('input[type="checkbox"].flat-red').on('ifUnchecked', function(event){
    cargar_buscador();
  });

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
    //console.log(this.name.substring(2));
    crear_examen_favorito(this.name.substring(2));
  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
    //cotizador_crear();
    eliminar_examen_favorito(this.name.substring(2));
  });

  function quitar_todos(){
    $('#seleccionados').iCheck('uncheck');
  }

  function cargar_buscador(){
    $.ajax({
        type: 'post',
        url:"{{route('hc4_examenes.buscador',['id' => $id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_buscador_favoritos").serialize(),
        success: function(data){
            console.log(data);
            $('#xbuscador_fav').empty().html(data);
        },
        error: function(data){
                
            }
    })
  } 

  function crear_examen_favorito (id){
    $.ajax({
        type: 'get',
        url:"{{url('hc4/laboratorio/examenes/favoritos/editar/actualizar/seleccionar')}}/{{$id}}/" + id,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        success: function(data){
            console.log(data);
        },
        error: function(data){
                
            }
    })  
  } 

  function eliminar_examen_favorito (id){
    $.ajax({
        type: 'get',
        url:"{{url('hc4/laboratorio/examenes/favoritos/editar/actualizar/eliminar')}}/{{$id}}/" + id,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        success: function(data){
            console.log(data);
        },
        error: function(data){
                
            }
    })  
  }

  function quitar_buscador(){
    $('#buscador').val('');
    $('#buscador2').val('');
  }

</script>