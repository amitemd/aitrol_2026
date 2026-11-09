@include('hc4.partials.catalog_list_styles')
@include('hc4.ordenes.examenes_favoritos.editor_styles')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<section class="content v3-clinical-list v3-favorite-editor">
  <div id="alerta_datos" class="alert alert-danger alerta_correcto alert-dismissable" role="alert" style="display:none"><button type="button" class="close" data-dismiss="alert">&times;</button></div>
  <div id="alerta_ok" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none"><button type="button" class="close" data-dismiss="alert">&times;</button>Perfil actualizado correctamente.</div>
  <input type="hidden" name="xid" id="xid" value="{{$protocolo->id}}">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title"><div class="v3-clinical-list__icon"><i class="fas fa-edit" aria-hidden="true"></i></div><div><div class="v3-clinical-list__eyebrow">Laboratorio clínico · V3</div><h3>Editar lista de exámenes favoritos</h3><p>Actualiza el nombre y administra los exámenes incluidos en el perfil.</p></div></div>
      <div class="v3-clinical-list__actions"><button type="button" class="v3-clinical-list__button" onclick="volver_a_favoritos()"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al listado</button></div>
    </header>
    <div class="v3-favorite-editor__body">
      <form class="v3-favorite-editor__form" onsubmit="actualizar_favorito();return false;">
        <div class="v3-clinical-list__field"><label for="nombre">Nombre del perfil</label><div class="v3-clinical-list__input"><i class="fas fa-tag" aria-hidden="true"></i><input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" value="{{$protocolo->nombre}}" autocomplete="off"></div><p class="v3-favorite-editor__hint">Guarda el nombre y selecciona o retira exámenes en el catálogo inferior.</p></div>
        <button id="actualizar_favorito_btn" type="submit" class="v3-favorite-editor__save"><i class="fas fa-save" aria-hidden="true"></i>Guardar</button>
      </form>
      <div class="v3-favorite-editor__catalog">
        <form id="form_buscador_favoritos" class="v3-favorite-editor__filters" onsubmit="buscar_catalogo_favorito();return false;">
          <div class="v3-clinical-list__field"><label for="buscador">Buscar examen</label><div class="v3-clinical-list__input"><i class="fas fa-search" aria-hidden="true"></i><input id="buscador" name="buscador" type="search" class="form-control" placeholder="Nombre del examen" autocomplete="off"></div></div>
          <div class="v3-clinical-list__field"><label for="buscador2">Buscar grupo</label><div class="v3-clinical-list__input"><i class="fas fa-layer-group" aria-hidden="true"></i><input id="buscador2" name="buscador2" type="search" class="form-control" placeholder="Nombre del agrupador" autocomplete="off"></div></div>
          <button class="v3-clinical-list__search" type="submit"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
          <div class="v3-favorite-editor__selected"><input id="seleccionados" name="seleccionados" type="checkbox" class="flat-red" value="1"><label for="seleccionados">Ver seleccionados</label></div>
        </form>
        <div id="xbuscador_fav">@include('hc4.ordenes.examenes_favoritos.catalogo')</div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  $('#seleccionados').iCheck({checkboxClass:'icheckbox_flat-red',radioClass:'iradio_flat-red'}).off('.favoriteFilter').on('ifChecked.favoriteFilter ifUnchecked.favoriteFilter',function(){cargar_buscador();});
  function mostrar_error_edicion(mensaje){$('#alerta_datos').empty().append('<button type="button" class="close" data-dismiss="alert">&times;</button>').append(document.createTextNode(mensaje)).stop(true,true).fadeIn(200).delay(2600).fadeOut(400);}
  function actualizar_favorito(){var nombre=$.trim($('#nombre').val()),boton=$('#actualizar_favorito_btn');if(!nombre){mostrar_error_edicion('Ingrese el nombre del perfil.');return;}boton.prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i>Guardando');$.ajax({type:'post',url:"{{route('hc4_examenes.actualizar')}}",headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},data:{xid:$('#xid').val(),nombre:nombre},success:function(data){if(data.estado==='err'){mostrar_error_edicion(data.respuesta);}else{$('#alerta_ok').stop(true,true).fadeIn(200).delay(1800).fadeOut(400);}},error:function(xhr){if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'actualizar el perfil favorito');}},complete:function(){boton.prop('disabled',false).html('<i class="fas fa-save"></i>Guardar');}});}
  function buscar_catalogo_favorito(){if($('#seleccionados').prop('checked')){$('#seleccionados').iCheck('uncheck');}else{cargar_buscador();}}
  function cargar_buscador(){$.ajax({type:'post',url:"{{route('hc4_examenes.buscador',['id'=>$protocolo->id])}}",headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},data:$('#form_buscador_favoritos').serialize(),success:function(data){$('#xbuscador_fav').html(data);},error:function(xhr){if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'buscar exámenes');}}});}
  function crear_examen_favorito(id){$.ajax({type:'post',url:"{{url('hc4/laboratorio/examenes/favoritos/editar/actualizar/seleccionar')}}/{{$protocolo->id}}/"+id,headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()}});}
  function eliminar_examen_favorito(id){$.ajax({type:'delete',url:"{{url('hc4/laboratorio/examenes/favoritos/editar/actualizar/eliminar')}}/{{$protocolo->id}}/"+id,headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()}});}
  function volver_a_favoritos(){if(typeof examenes_favoritos==='function'){examenes_favoritos();return;}$.get("{{route('hc4_examenes.favoritos')}}",function(data){$('#area_trabajo').html(data);});}
</script>
