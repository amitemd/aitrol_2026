<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<div id="alerta_ok" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>Perfil guardado. Selecciona los exámenes.
</div>
<div class="v3-favorite-editor__catalog">
<form id="form_buscador_favoritos" class="v3-favorite-editor__filters" onsubmit="quitar_todos();cargar_buscador();return false;">
    <div class="v3-clinical-list__field">
      <label for="buscador">Buscar examen</label>
      <div class="v3-clinical-list__input"><i class="fas fa-search" aria-hidden="true"></i><input id="buscador" name="buscador" type="search" class="form-control" placeholder="Nombre del examen" onchange="quitar_todos();cargar_buscador();"></div>
    </div>
    <div class="v3-clinical-list__field">
      <label for="buscador2">Buscar grupo</label>
      <div class="v3-clinical-list__input"><i class="fas fa-layer-group" aria-hidden="true"></i><input id="buscador2" name="buscador2" type="search" class="form-control" placeholder="Nombre del agrupador" onchange="quitar_todos();cargar_buscador();"></div>
    </div>
    <button class="v3-clinical-list__search" type="submit"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
    <div class="v3-favorite-editor__selected">
      <input id="seleccionados" name="seleccionados" type="checkbox" class="flat-red" onchange="quitar_buscador();cargar_buscador();" value="1"><label for="seleccionados">Ver seleccionados</label>
    </div>
</form>
<div id="xbuscador_fav">
  @include('hc4.ordenes.examenes_favoritos.catalogo', ['detalles_ch' => []])
</div>
</div>
<script type="text/javascript">
  
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
        type: 'post',
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
        type: 'delete',
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
