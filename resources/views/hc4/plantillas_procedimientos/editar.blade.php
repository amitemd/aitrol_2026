@include('hc4.partials.catalog_list_styles')
@php($editorId='ttecnica_quirurgica_'.$procedimiento_completo->id.'_'.date('His'))
<style>
  .v3-procedure-editor__body{background:#f7fbfd;padding:18px}
  .v3-procedure-editor__grid{background:#fff;border:1px solid #d7e6ed;border-radius:10px;display:grid;gap:16px;grid-template-columns:1fr 1fr;padding:18px}
  .v3-procedure-editor__field{min-width:0}.v3-procedure-editor__field--wide{grid-column:1/-1}
  .v3-procedure-editor__field label{color:#41677d;display:block;font-size:10px;font-weight:700;letter-spacing:.25px;margin:0 0 6px;text-transform:uppercase}
  .v3-procedure-editor__field .form-control{border:1px solid #cbdde6;border-radius:7px;box-shadow:none;color:#294f65;font-size:12px;height:38px;padding:8px 11px;width:100%}
  .v3-procedure-editor__field .form-control:focus{border-color:#269fbe;box-shadow:0 0 0 3px rgba(25,147,180,.11)}
  .v3-procedure-editor .select2-container{width:100%!important}.v3-procedure-editor .select2-container--default .select2-selection--single{background:#fff!important;border:1px solid #cbdde6!important;border-radius:7px!important;height:38px!important}.v3-procedure-editor .select2-container--default .select2-selection--single .select2-selection__rendered{color:#294f65!important;font-size:12px;line-height:36px!important;padding-left:11px!important}.v3-procedure-editor .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px!important;right:5px!important}.v3-procedure-editor .select2-container--focus .select2-selection--single,.v3-procedure-editor .select2-container--open .select2-selection--single{border-color:#269fbe!important;box-shadow:0 0 0 3px rgba(25,147,180,.11)!important}
  .v3-procedure-select-dropdown{background:#fff!important;border:1px solid #bcd6e1!important;border-radius:8px!important;box-shadow:0 10px 28px rgba(22,77,107,.2)!important;overflow:hidden}.v3-procedure-select-dropdown .select2-search--dropdown{background:#f7fbfd;padding:8px}.v3-procedure-select-dropdown .select2-search__field{border:1px solid #cbdde6!important;border-radius:6px;color:#294f65;height:34px;padding:6px 9px}.v3-procedure-select-dropdown .select2-results__option{color:#3b5e73;font-size:12px;padding:8px 11px}.v3-procedure-select-dropdown .select2-results__option[aria-selected=true]{background:#e4f6f3!important;color:#167c72!important}.v3-procedure-select-dropdown .select2-results__option--highlighted[aria-selected]{background:#118eae!important;color:#fff!important}
  .v3-procedure-editor__editor{background:#fff;border:1px solid #cbdde6;border-radius:8px;color:#294f65;font-size:13px;line-height:1.55;min-height:220px;padding:14px}
  .v3-procedure-editor__editor:focus{border-color:#269fbe;box-shadow:0 0 0 3px rgba(25,147,180,.11);outline:0}
  .v3-procedure-editor__help{color:#78909e;font-size:10px;margin:6px 0 0}
  .v3-procedure-editor__footer{align-items:center;background:#fbfdfe;border-top:1px solid #dbe8ee;display:flex;justify-content:flex-end;padding:12px 18px}
  .v3-procedure-editor__save{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;min-width:115px;padding:0 16px}
  .v3-procedure-editor__save:hover,.v3-procedure-editor__save:focus{background:#066b90;color:#fff}.v3-procedure-editor__save i{margin-right:6px}
  .v3-procedure-editor .help-block{color:#b84b4b;font-size:10px;margin:5px 0 0}
  @media(max-width:767px){.v3-procedure-editor__grid{grid-template-columns:1fr}.v3-procedure-editor__field--wide{grid-column:auto}.v3-procedure-editor__footer{justify-content:stretch}.v3-procedure-editor__save{width:100%}}
</style>

<section class="content v3-clinical-list v3-procedure-editor">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title"><div class="v3-clinical-list__icon"><i class="fas fa-file-medical" aria-hidden="true"></i></div><div><div class="v3-clinical-list__eyebrow">Catálogo clínico · V3</div><h3>Editar procedimiento</h3><p>Actualiza la información y la técnica de esta plantilla clínica.</p></div></div>
      <div class="v3-clinical-list__actions"><button type="button" class="v3-clinical-list__button" onclick="plantilla_proc()"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al listado</button></div>
    </header>

    <form id="actualizar_frm" role="form" method="POST" onsubmit="actualizar_plantilla_proc();return false;">
      {{csrf_field()}}
      <input type="hidden" name="id" value="{{$procedimiento_completo->id}}">
      <div class="v3-procedure-editor__body">
        <div class="v3-procedure-editor__grid">
          <div class="v3-procedure-editor__field{{$errors->has('nombre_general')?' has-error':''}}"><label for="nombre_general">Nombre a mostrar</label><input id="nombre_general" type="text" class="form-control" name="nombre_general" value="{{$procedimiento_completo->nombre_general}}" required autofocus>@if($errors->has('nombre_general'))<span class="help-block"><strong>{{$errors->first('nombre_general')}}</strong></span>@endif</div>
          <div class="v3-procedure-editor__field{{$errors->has('nombre_completo')?' has-error':''}}"><label for="nombre_completo">Nombre completo</label><input id="nombre_completo" type="text" class="form-control" name="nombre_completo" value="{{$procedimiento_completo->nombre_completo}}" required>@if($errors->has('nombre_completo'))<span class="help-block"><strong>{{$errors->first('nombre_completo')}}</strong></span>@endif</div>
          <div class="v3-procedure-editor__field{{$errors->has('estado_anestesia')?' has-error':''}}"><label for="estado_anestesia">Récord anestésico</label><select id="estado_anestesia" name="estado_anestesia" class="form-control js-procedure-select"><option {{$procedimiento_completo->estado_anestesia==0?'selected':''}} value="0">No requiere</option><option {{$procedimiento_completo->estado_anestesia==1?'selected':''}} value="1">Sí requiere</option></select></div>
          <div class="v3-procedure-editor__field{{$errors->has('estado')?' has-error':''}}"><label for="estado">Estado</label><select id="estado" name="estado" class="form-control js-procedure-select"><option {{$procedimiento_completo->estado==1?'selected':''}} value="1">Activo</option><option {{$procedimiento_completo->estado==0?'selected':''}} value="0">Inactivo</option></select></div>
          <div class="v3-procedure-editor__field v3-procedure-editor__field--wide{{$errors->has('id_grupo_procedimiento')?' has-error':''}}"><label for="id_grupo_procedimiento">Grupo al que pertenece</label><select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control js-procedure-select" required>@foreach($grupo_procedimiento as $value)<option @if($procedimiento_completo->id_grupo_procedimiento==$value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select>@if($errors->has('id_grupo_procedimiento'))<span class="help-block"><strong>{{$errors->first('id_grupo_procedimiento')}}</strong></span>@endif</div>
          <div class="v3-procedure-editor__field v3-procedure-editor__field--wide{{$errors->has('tecnica_quirurgica')?' has-error':''}}"><label for="{{$editorId}}">Técnica quirúrgica</label><div id="{{$editorId}}" class="v3-procedure-editor__editor">{!!strip_tags($procedimiento_completo->tecnica_quirurgica)!!}</div><input type="hidden" name="tecnica_quirurgica" id="tecnica_quirurgica_editor"><p class="v3-procedure-editor__help">Describe los pasos, hallazgos o indicaciones reutilizables del procedimiento.</p></div>
        </div>
      </div>
      <footer class="v3-procedure-editor__footer"><button id="actualizar_plantilla_btn" type="submit" class="v3-procedure-editor__save"><i class="fas fa-save" aria-hidden="true"></i>Actualizar</button></footer>
    </form>
  </div>
</section>

<script>
  $('.v3-procedure-editor .js-procedure-select').each(function(){var selector=$(this);if(selector.hasClass('select2-hidden-accessible')){selector.select2('destroy');}selector.select2({width:'100%',minimumResultsForSearch:0,dropdownCssClass:'v3-procedure-select-dropdown',language:{searching:function(){return 'Buscando...';},noResults:function(){return 'No se encontraron opciones';}}});});
  function sincronizar_tecnica_plantilla(){var editor=window.tinyMCE?tinyMCE.get('{{$editorId}}'):null;$('#tecnica_quirurgica_editor').val(editor?editor.getContent():$('#{{$editorId}}').html());}
  function actualizar_plantilla_proc(){sincronizar_tecnica_plantilla();var boton=$('#actualizar_plantilla_btn');boton.prop('disabled',true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Actualizando');$.ajax({type:'post',url:"{{route('hc4/plantilla_proc.update')}}",data:$('#actualizar_frm').serialize(),success:function(){plantilla_proc();},error:function(xhr){if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'actualizar la plantilla de procedimiento');}else{alert('No se pudo actualizar la plantilla.');}},complete:function(){boton.prop('disabled',false).html('<i class="fas fa-save" aria-hidden="true"></i>Actualizar');}});}
  tinymce.init({selector:'#{{$editorId}}',inline:true,menubar:false,content_style:'.mce-content-body{font-size:13px;}',setup:function(editor){editor.on('init change keyup',function(){sincronizar_tecnica_plantilla();});}});
</script>
