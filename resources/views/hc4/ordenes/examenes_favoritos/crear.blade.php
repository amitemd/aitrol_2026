@include('hc4.partials.catalog_list_styles')
<style>
  .v3-favorite-editor__body{background:#f7fbfd;padding:18px}
  .v3-favorite-editor__form{align-items:start;background:#fff;border:1px solid #d7e6ed;border-radius:10px;display:grid;gap:12px;grid-template-columns:minmax(280px,620px) 110px;justify-content:start;padding:16px}
  .v3-favorite-editor__hint{color:#78909e;font-size:11px;margin:6px 0 0}
  .v3-favorite-editor__save{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;margin-top:15px;padding:0 16px;width:110px}
  .v3-favorite-editor__save:hover,.v3-favorite-editor__save:focus{background:#066b90;color:#fff}.v3-favorite-editor__save i{margin-right:6px}
  .v3-favorite-editor__empty{border:1px dashed #bdd4df;border-radius:10px;color:#6f8998;margin-top:14px;padding:28px;text-align:center}
  .v3-favorite-editor__empty i{color:#8fb0c2;display:block;font-size:28px;margin-bottom:8px}
  .v3-favorite-editor .alerta_correcto{border:0;border-radius:8px;box-shadow:0 8px 24px rgba(22,77,107,.18);position:fixed;right:24px;top:90px;z-index:9999}
  .v3-favorite-editor__catalog{background:#fff;border:1px solid #d7e6ed;border-radius:10px;margin-top:14px;overflow:hidden}
  .v3-favorite-editor__filters{align-items:end;background:#f7fbfd;border-bottom:1px solid #dbe8ee;display:grid;gap:12px;grid-template-columns:1fr 1fr auto auto;padding:14px 16px}
  .v3-favorite-editor__selected{align-items:center;color:#41677d;display:flex;font-size:11px;font-weight:700;gap:7px;min-height:36px}
  .hc4-lab-catalog{align-items:start;display:grid;gap:10px;grid-auto-flow:dense;grid-template-columns:repeat(3,minmax(0,1fr));max-height:520px;overflow-x:hidden;overflow-y:auto;padding:10px;scrollbar-color:#24a6b4 #e6f3f5;scrollbar-width:thin}
  .hc4-lab-group{background:#fff;border:1px solid #dce7ec;border-radius:8px;min-width:0;overflow:hidden}
  .hc4-lab-group--wide{grid-column:1/-1}
  .hc4-lab-group--wide .hc4-lab-group__items{column-count:var(--group-columns,3);column-gap:0;column-rule:1px solid #e2e8ed}
  .hc4-lab-group--wide .hc4-lab-exam-row{break-inside:avoid;page-break-inside:avoid}
  .hc4-lab-group__title{align-items:center;background:var(--group-bg);color:var(--group-text);display:flex;font-size:12px;font-weight:700;gap:8px;justify-content:center;line-height:1.25;margin:0;padding:7px 10px;text-align:center;text-transform:uppercase}
  .hc4-lab-group__title small{color:inherit;font-size:10px;font-weight:600;opacity:.72;text-transform:none}
  .hc4-lab-exam-row{align-items:center;border-bottom:1px solid #edf1f3;color:#3f4652;cursor:pointer;display:flex;gap:8px;min-height:32px;margin:0;padding:5px 9px}
  .hc4-lab-exam-row:last-child{border-bottom:0}.hc4-lab-exam-row:hover,.hc4-lab-exam-row.is-selected{background:var(--group-selected)}
  .hc4-lab-exam-row__check{flex:0 0 22px;text-align:center}.hc4-lab-exam-row__name{font-size:12px;font-weight:500;line-height:1.25;min-width:0;overflow-wrap:anywhere}
  .hc4-lab-catalog__empty{border:1px dashed #cfdde4;border-radius:8px;color:#647481;grid-column:1/-1;padding:24px;text-align:center}
  @media(max-width:1050px){.hc4-lab-catalog{grid-template-columns:repeat(2,minmax(0,1fr))}.hc4-lab-group--wide .hc4-lab-group__items{column-count:2}}
  @media(max-width:767px){.v3-favorite-editor__form,.v3-favorite-editor__filters{grid-template-columns:1fr}.v3-favorite-editor__save{margin-top:0;width:100%}.hc4-lab-catalog{grid-template-columns:1fr;max-height:440px}.hc4-lab-group--wide .hc4-lab-group__items{column-count:1}}
</style>

<section class="content v3-clinical-list v3-favorite-editor">
  <div id="alerta_datos" class="alert alert-danger alerta_correcto alert-dismissable" role="alert" style="display:none"><button type="button" class="close" data-dismiss="alert">&times;</button></div>
  <input type="hidden" name="xid" id="xid" value="0">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title">
        <div class="v3-clinical-list__icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
        <div><div class="v3-clinical-list__eyebrow">Laboratorio clínico · V3</div><h3>Crear lista de exámenes favoritos</h3><p>Asigna un nombre y selecciona los exámenes que deseas reutilizar.</p></div>
      </div>
      <div class="v3-clinical-list__actions"><button type="button" class="v3-clinical-list__button" onclick="volver_a_favoritos()"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al listado</button></div>
    </header>
    <div class="v3-favorite-editor__body">
      <form class="v3-favorite-editor__form" onsubmit="guardar_favorito();return false;">
        <div class="v3-clinical-list__field">
          <label for="nombre">Nombre del perfil</label>
          <div class="v3-clinical-list__input"><i class="fas fa-tag" aria-hidden="true"></i><input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ej. Control metabólico" autocomplete="off" onchange="guardar_favorito()"></div>
          <p class="v3-favorite-editor__hint">Al guardar el nombre podrás buscar y seleccionar los exámenes.</p>
        </div>
        <button id="guardar_favorito_btn" type="submit" class="v3-favorite-editor__save"><i class="fas fa-save" aria-hidden="true"></i>Guardar</button>
      </form>
      <div id="xlistado"><div class="v3-favorite-editor__empty"><i class="fas fa-vials" aria-hidden="true"></i>Guarda el nombre del perfil para comenzar a seleccionar exámenes.</div></div>
    </div>
  </div>
</section>

<script type="text/javascript">
  function mostrar_error_favorito(mensaje){$('#alerta_datos').empty().append('<button type="button" class="close" data-dismiss="alert">&times;</button>').append(document.createTextNode(mensaje)).stop(true,true).fadeIn(200).delay(2600).fadeOut(400);}
  function guardar_favorito(){
    var xid=$('#xid').val(),nombre=$.trim($('#nombre').val()),boton=$('#guardar_favorito_btn');
    if(!nombre){mostrar_error_favorito('Ingrese el nombre del perfil.');$('#nombre').focus();return;}
    boton.prop('disabled',true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Guardando');
    $.ajax({type:'post',url:xid==='0'?"{{route('hc4_examenes.guardar')}}":"{{route('hc4_examenes.actualizar')}}",headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},datatype:'json',data:xid==='0'?{nombre:nombre}:{xid:xid,nombre:nombre},success:function(data){if(data.estado==='err'){mostrar_error_favorito(data.respuesta);return;}if(data.estado==='ok'){$('#xid').val(data.respuesta);ingresar_examenes(data.respuesta);}},error:function(xhr){if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'guardar la lista de exámenes favoritos');}else{mostrar_error_favorito('No se pudo guardar el perfil.');}},complete:function(){boton.prop('disabled',false).html('<i class="fas fa-save" aria-hidden="true"></i>Guardar');}});
  }
  function ingresar_examenes(id){$.ajax({type:'get',url:"{{url('hc4/laboratorio/examenes/favoritos/crear/guardar/listado')}}/"+id,datatype:'html',success:function(data){$('#xlistado').html(data);$('#alerta_ok').stop(true,true).fadeIn(200).delay(1800).fadeOut(400);},error:function(xhr){if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'cargar los exámenes disponibles');}}});}
  function volver_a_favoritos(){if(typeof examenes_favoritos==='function'){examenes_favoritos();return;}$.get("{{route('hc4_examenes.favoritos')}}",function(data){$('#area_trabajo').html(data);});}
</script>
