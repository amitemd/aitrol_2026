@include('hc4.partials.catalog_list_styles')
<style>
  .v3-procedure-editor__body{background:#f7fbfd;padding:18px}
  .v3-procedure-editor__grid{background:#fff;border:1px solid #d7e6ed;border-radius:10px;display:grid;gap:16px;grid-template-columns:1fr 1fr;padding:18px}
  .v3-procedure-editor__field{min-width:0}.v3-procedure-editor__field--wide{grid-column:1/-1}
  .v3-procedure-editor__field label{color:#41677d;display:block;font-size:10px;font-weight:700;letter-spacing:.25px;margin:0 0 6px;text-transform:uppercase}
  .v3-procedure-editor__field .form-control{border:1px solid #cbdde6;border-radius:7px;box-shadow:none;color:#294f65;font-size:12px;height:38px;padding:8px 11px;width:100%}
  .v3-procedure-editor__field .form-control:focus{border-color:#269fbe;box-shadow:0 0 0 3px rgba(25,147,180,.11)}
  .v3-procedure-editor .select2-container{width:100%!important}.v3-procedure-editor .select2-container--default .select2-selection--single{background:#fff!important;border:1px solid #cbdde6!important;border-radius:7px!important;height:38px!important}.v3-procedure-editor .select2-container--default .select2-selection--single .select2-selection__rendered{color:#294f65!important;font-size:12px;line-height:36px!important;padding-left:11px!important}.v3-procedure-editor .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px!important;right:5px!important}.v3-procedure-editor .select2-container--focus .select2-selection--single,.v3-procedure-editor .select2-container--open .select2-selection--single{border-color:#269fbe!important;box-shadow:0 0 0 3px rgba(25,147,180,.11)!important}
  .v3-procedure-select-dropdown{background:#fff!important;border:1px solid #bcd6e1!important;border-radius:8px!important;box-shadow:0 10px 28px rgba(22,77,107,.2)!important;overflow:hidden}.v3-procedure-select-dropdown .select2-search--dropdown{background:#f7fbfd;padding:8px}.v3-procedure-select-dropdown .select2-search__field{border:1px solid #cbdde6!important;border-radius:6px;color:#294f65;height:34px;padding:6px 9px}.v3-procedure-select-dropdown .select2-results__option{color:#3b5e73;font-size:12px;padding:8px 11px}.v3-procedure-select-dropdown .select2-results__option[aria-selected=true]{background:#e4f6f3!important;color:#167c72!important}.v3-procedure-select-dropdown .select2-results__option--highlighted[aria-selected]{background:#118eae!important;color:#fff!important}
  .v3-procedure-editor__field.has-error .form-control{border-color:#d76a6a}.v3-procedure-editor .help-block{color:#b84b4b;font-size:10px;margin:5px 0 0}
  .v3-procedure-editor__footer{align-items:center;background:#fbfdfe;border-top:1px solid #dbe8ee;display:flex;justify-content:flex-end;padding:12px 18px}
  .v3-procedure-editor__save{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;min-width:115px;padding:0 16px}
  .v3-procedure-editor__save:hover,.v3-procedure-editor__save:focus{background:#066b90;color:#fff}.v3-procedure-editor__save i{margin-right:6px}
  @media(max-width:767px){.v3-procedure-editor__grid{grid-template-columns:1fr}.v3-procedure-editor__field--wide{grid-column:auto}.v3-procedure-editor__footer{justify-content:stretch}.v3-procedure-editor__save{width:100%}}
</style>

<section class="content v3-clinical-list v3-procedure-editor">
  <div class="v3-clinical-list__card">
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title"><div class="v3-clinical-list__icon"><i class="fas fa-file-medical" aria-hidden="true"></i></div><div><div class="v3-clinical-list__eyebrow">Catálogo clínico · V3</div><h3>Nuevo procedimiento</h3><p>Registra una nueva plantilla clínica para reutilizarla posteriormente.</p></div></div>
      <div class="v3-clinical-list__actions"><button type="button" class="v3-clinical-list__button" onclick="plantilla_proc()"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al listado</button></div>
    </header>

    <form id="plantilla_crear" role="form" method="POST" onsubmit="guardar_plantilla_proc();return false;">
      {{csrf_field()}}
      <div class="v3-procedure-editor__body">
        <div class="v3-procedure-editor__grid">
          <div class="v3-procedure-editor__field v3-procedure-editor__field--wide cl_nombre_general"><label for="nombre_general">Nombre del procedimiento</label><input id="nombre_general" type="text" class="form-control" name="nombre_general" required autofocus placeholder="Escriba el nombre de la plantilla"><span class="help-block"><strong id="str_nombre_general"></strong></span></div>
          <div class="v3-procedure-editor__field cl_id_grupo_procedimiento"><label for="id_grupo_procedimiento">Grupo al que pertenece</label><select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control js-procedure-select" required><option value="">Seleccione un grupo</option>@foreach($grupo_procedimiento as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></div>
          <div class="v3-procedure-editor__field cl_anestesia"><label for="estado_anestesia">Récord anestésico</label><select id="estado_anestesia" name="estado_anestesia" class="form-control js-procedure-select"><option value="0">No requiere</option><option value="1">Sí requiere</option></select></div>
        </div>
      </div>
      <footer class="v3-procedure-editor__footer"><button id="btn_save" type="submit" class="v3-procedure-editor__save"><i class="fas fa-plus" aria-hidden="true"></i>Agregar</button></footer>
    </form>
  </div>
</section>

<script>
  $('.v3-procedure-editor .js-procedure-select').each(function(){var selector=$(this);if(selector.hasClass('select2-hidden-accessible')){selector.select2('destroy');}selector.select2({width:'100%',minimumResultsForSearch:0,dropdownCssClass:'v3-procedure-select-dropdown',language:{searching:function(){return 'Buscando...';},noResults:function(){return 'No se encontraron opciones';}}});});
  function guardar_plantilla_proc(){
    var boton=$('#btn_save');$('.cl_nombre_general').removeClass('has-error');$('#str_nombre_general').text('');
    boton.prop('disabled',true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Guardando');
    $.ajax({type:'post',url:"{{route('hc4/plantilla_proc.store')}}",headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},data:$('#plantilla_crear').serialize(),success:function(){plantilla_proc();},error:function(xhr){var errores=xhr.responseJSON||{};if(errores.nombre_general){$('.cl_nombre_general').addClass('has-error');$('#str_nombre_general').text(errores.nombre_general);}else if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'crear la plantilla de procedimiento');}else{alert('No se pudo crear la plantilla.');}},complete:function(){boton.prop('disabled',false).html('<i class="fas fa-plus" aria-hidden="true"></i>Agregar');}});
  }
</script>
