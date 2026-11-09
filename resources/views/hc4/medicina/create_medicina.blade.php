@include('hc4.medicina._v3_form_styles')
<section class="content v3-medicine-form">
  <div class="v3-medicine-form__card">
    <header class="v3-medicine-form__header"><div class="v3-medicine-form__heading"><div class="v3-medicine-form__icon"><i class="fas fa-capsules" aria-hidden="true"></i></div><div><div class="v3-medicine-form__eyebrow">Catálogo clínico · V3</div><h4>Nueva medicina</h4><p>Registra la información que se utilizará en las recetas médicas.</p></div></div></header>
    <form method="POST" id="guard_medicina">{{ csrf_field() }}
      <div class="v3-medicine-form__body">
        <div class="v3-medicine-form__section"><h5 class="v3-medicine-form__section-title"><i class="fas fa-notes-medical" aria-hidden="true"></i>Información de la medicina</h5><div class="v3-medicine-form__grid">
          <div class="form-group v3-medicine-form__field cl_nombre"><label for="nombre">Nombre <span class="required">*</span></label><input id="nombre" class="form-control" type="text" name="nombre" value="{{old('nombre')}}" maxlength="50" placeholder="Ej. Amoxicilina 500 mg" required autofocus><span class="help-block"><strong id="str_nombre"></strong></span></div>
          <div class="form-group v3-medicine-form__field cl_cantidad"><label for="cantidad">Cantidad a prescribir <span class="required">*</span></label><input id="cantidad" class="form-control" type="text" name="cantidad" value="{{old('cantidad')}}" maxlength="50" placeholder="Ej. 30" required><span class="help-block"><strong id="str_cantidad"></strong></span></div>
          <div class="form-group v3-medicine-form__field v3-medicine-form__field--wide cl_dosis"><label for="dosis">Dosis <span class="required">*</span></label><textarea id="dosis" class="form-control" name="dosis" placeholder="Describe la dosis recomendada" required>{{old('dosis')}}</textarea><span class="help-block"><strong id="str_dosis"></strong></span></div>
        </div></div>
        <div class="v3-medicine-form__section"><h5 class="v3-medicine-form__section-title"><i class="fas fa-sliders-h" aria-hidden="true"></i>Clasificación y disponibilidad</h5><div class="v3-medicine-form__grid">
          <div class="form-group v3-medicine-form__field v3-medicine-form__field--wide cl_genericos"><label for="genericos">Genéricos <span class="required">*</span></label><select id="genericos" class="form-control select2" name="genericos[]" multiple="multiple" data-placeholder="Seleccione uno o varios genéricos" autocomplete="off" @if(old('dieta') != '1') required @endif>@foreach($genericos as $generico)<option value="{{$generico->id}}" {{in_array($generico->id,(array)old('genericos',array()))?'selected':''}}>{{$generico->nombre}}</option>@endforeach</select><p class="v3-medicine-form__hint">Puedes seleccionar varios principios activos o escribir uno nuevo.</p><span class="help-block"><strong id="str_genericos"></strong></span></div>
          <div class="form-group v3-medicine-form__field cl_dieta"><label for="dieta">Es una recomendación</label><select id="dieta" name="dieta" class="form-control" required><option value="0" {{old('dieta','0')=='0'?'selected':''}}>No</option><option value="1" {{old('dieta')=='1'?'selected':''}}>Sí</option></select><span class="help-block"><strong id="str_dieta"></strong></span></div>
          <div class="form-group v3-medicine-form__field cl_iess_medicina"><label for="iess_medicina">Pública / privada</label><select id="iess_medicina" name="iess_medicina" class="form-control"><option value="1" {{old('iess_medicina')=='1'?'selected':''}}>Pública</option><option value="0" {{old('iess_medicina','0')=='0'?'selected':''}}>Privada</option></select><span class="help-block"><strong id="str_iess_medicina"></strong></span></div>
        </div></div>
      </div>
      <footer class="v3-medicine-form__footer"><button id="id_crea_medicina" class="v3-medicine-form__submit" type="button" onclick="store_medicina();"><i class="fas fa-save" aria-hidden="true"></i>Crear medicina</button></footer>
    </form>
  </div>
</section>
<script>
  $('.select2').select2({tags:true,createTag:function(params){var term=$.trim(params.term);return{id:term.toUpperCase()+'xnose',text:term.toUpperCase(),newTag:true};}});
  $('#dieta').on('change',function(){if($(this).val()=='0'){$('#genericos').prop('required',true);}else{$('#genericos').removeAttr('required');}});
  function store_medicina(){$('.cl_nombre,.cl_cantidad,.cl_dosis,.cl_genericos').removeClass('has-error');$('#str_nombre,#str_cantidad,#str_dosis,#str_genericos').text('');var button=$('#id_crea_medicina').prop('disabled',true);$.ajax({type:'post',url:"{{route('guarda.medicina')}}",headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},datatype:'json',data:$('#guard_medicina').serialize(),success:function(data){$('#area_trabajo').html(data);},error:function(data){var errors=data.responseJSON||{};$.each(['nombre','cantidad','dosis','genericos'],function(_,field){if(errors[field]!=null){$('.cl_'+field).addClass('has-error');$('#str_'+field).text(errors[field]);}});button.prop('disabled',false);}});}
</script>
