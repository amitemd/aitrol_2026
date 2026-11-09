<style>
  .v3-generic-form { color:#294d63; font-family:"Helvetica Neue",Arial,sans-serif; padding:0; }
  .v3-generic-form * { box-sizing:border-box; }
  .v3-generic-form__card { background:#fff; border:1px solid #d2e3eb; border-radius:12px; box-shadow:0 7px 22px rgba(22,77,107,.11); overflow:hidden; width:100%; }
  .v3-generic-form__header { align-items:center; background:linear-gradient(116deg,#08719c 0%,#118eae 60%,#17aaa5 100%); color:#fff; display:flex; justify-content:space-between; min-height:70px; padding:13px 20px; position:relative; }
  .v3-generic-form__header:after { border:20px solid rgba(255,255,255,.06); border-radius:50%; content:""; height:90px; position:absolute; right:-20px; top:-42px; width:90px; }
  .v3-generic-form__heading { align-items:center; display:flex; min-width:0; position:relative; z-index:1; }
  .v3-generic-form__icon { align-items:center; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.28); border-radius:9px; display:flex; flex:0 0 42px; font-size:18px; height:42px; justify-content:center; margin-right:12px; }
  .v3-generic-form__eyebrow { color:rgba(255,255,255,.78); font-size:9px; font-weight:700; letter-spacing:1px; text-transform:uppercase; }
  .v3-generic-form__header h4 { color:#fff; font-size:18px; font-weight:700; margin:1px 0 2px; }
  .v3-generic-form__header p { color:rgba(255,255,255,.84); font-size:11px; margin:0; }
  .v3-generic-form__back { align-items:center; background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.55); border-radius:7px; color:#fff!important; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:35px; justify-content:center; padding:0 13px; position:relative; z-index:1; }
  .v3-generic-form__back:hover,.v3-generic-form__back:focus { background:#fff; color:#08749b!important; }
  .v3-generic-form__back i { margin-right:6px; }
  .v3-generic-form__body { background:#f8fbfd; padding:16px 18px; }
  .v3-generic-form__section { background:#fff; border:1px solid #dbe9f0; border-radius:10px; padding:17px; }
  .v3-generic-form__section-title { align-items:center; color:#176f96; display:flex; font-size:12px; font-weight:700; margin:0 0 14px; }
  .v3-generic-form__section-title i { color:#16a2a4; margin-right:7px; }
  .v3-generic-form__grid { align-items:start; display:grid; gap:14px 16px; grid-template-columns:minmax(220px,1fr) minmax(300px,1.45fr) minmax(150px,.5fr); }
  .v3-generic-form__field { margin:0; min-width:0; }
  .v3-generic-form__field label { color:#315b73; display:block; font-size:10px; font-weight:700; letter-spacing:.25px; margin:0 0 6px; text-transform:uppercase; }
  .v3-generic-form__field label .required { color:#e0524d; }
  .v3-generic-form .form-control { background:#fff; border:1px solid #cadde7; border-radius:7px; box-shadow:none; color:#264f67; font-size:12px; height:38px; padding:8px 11px; width:100%; }
  .v3-generic-form textarea.form-control { min-height:68px; resize:vertical; }
  .v3-generic-form .form-control:focus { border-color:#28a4c5; box-shadow:0 0 0 3px rgba(28,151,186,.12); outline:0; }
  .v3-generic-form .has-error .form-control { border-color:#df625d!important; }
  .v3-generic-form .help-block { color:#c74642; display:block; font-size:10px; margin:4px 0 0; min-height:14px; }
  .v3-generic-form__footer { align-items:center; background:#fff; border-top:1px solid #dce9ef; display:flex; justify-content:flex-end; padding:12px 18px; }
  .v3-generic-form__submit { align-items:center; background:linear-gradient(110deg,#08729d,#149cac); border:0; border-radius:7px; box-shadow:0 5px 12px rgba(8,114,157,.2); color:#fff!important; cursor:pointer; display:inline-flex; font-size:12px; font-weight:700; height:38px; justify-content:center; min-width:155px; padding:0 18px; }
  .v3-generic-form__submit:hover,.v3-generic-form__submit:focus { box-shadow:0 7px 16px rgba(8,114,157,.28); color:#fff; transform:translateY(-1px); }
  .v3-generic-form__submit:disabled { cursor:wait; opacity:.7; transform:none; }
  .v3-generic-form__submit i { margin-right:7px; }
  @media(max-width:900px){.v3-generic-form__grid{grid-template-columns:1fr 1fr}.v3-generic-form__field--description{grid-column:1/-1;grid-row:2}}
  @media(max-width:600px){.v3-generic-form__header{align-items:flex-start;flex-direction:column;gap:10px}.v3-generic-form__back{width:100%}.v3-generic-form__grid{grid-template-columns:1fr}.v3-generic-form__field--description{grid-column:auto;grid-row:auto}.v3-generic-form__submit{width:100%}}
</style>
<section class="content v3-generic-form">
  <div class="v3-generic-form__card">
    <header class="v3-generic-form__header">
      <div class="v3-generic-form__heading">
        <div class="v3-generic-form__icon"><i class="fas fa-atom" aria-hidden="true"></i></div>
        <div><div class="v3-generic-form__eyebrow">Catálogo clínico · V3</div><h4>Editar genérico</h4><p>Actualiza el principio activo utilizado en el catálogo de medicinas.</p></div>
      </div>
      <button type="button" class="v3-generic-form__back" onclick="volverListadoGenericos();"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al listado</button>
    </header>
    <form method="POST" id="actualiza_generico" onsubmit="actualiza_generico();return false;">
      {{csrf_field()}}
      <input type="hidden" name="idgenerico" value="{{$generico->id}}">
      <div class="v3-generic-form__body">
        <div class="v3-generic-form__section">
          <h5 class="v3-generic-form__section-title"><i class="fas fa-notes-medical" aria-hidden="true"></i>Información del genérico</h5>
          <div class="v3-generic-form__grid">
            <div class="form-group v3-generic-form__field cl_nombre">
              <label for="nombre">Nombre <span class="required">*</span></label>
              <input id="nombre" class="form-control" type="text" name="nombre" maxlength="100" value="{{old('nombre',$generico->nombre)}}" placeholder="Ej. Ácido acetilsalicílico" required style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase();">
              <span class="help-block"><strong id="str_nombre"></strong></span>
            </div>
            <div class="form-group v3-generic-form__field v3-generic-form__field--description cl_descripcion">
              <label for="descripcion">Descripción <span class="required">*</span></label>
              <textarea id="descripcion" class="form-control" name="descripcion" maxlength="255" placeholder="Describe el principio activo" required style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase();">{{old('descripcion',$generico->descripcion)}}</textarea>
              <span class="help-block"><strong id="str_descripcion"></strong></span>
            </div>
            <div class="form-group v3-generic-form__field cl_estado">
              <label for="estado">Estado</label>
              <select id="estado" name="estado" class="form-control" required>
                <option value="1" {{(string)old('estado',$generico->estado)==='1'?'selected':''}}>Activo</option>
                <option value="0" {{(string)old('estado',$generico->estado)==='0'?'selected':''}}>Inactivo</option>
              </select>
              <span class="help-block"><strong id="str_estado"></strong></span>
            </div>
          </div>
        </div>
      </div>
      <footer class="v3-generic-form__footer"><button id="guardar_generico" class="v3-generic-form__submit" type="submit"><i class="fas fa-save" aria-hidden="true"></i>Guardar cambios</button></footer>
    </form>
  </div>
</section>
<script>
  function cargarVistaGenerico(opciones){var area=$('#area_trabajo').css('opacity','.55').attr('aria-busy','true');return $.ajax(opciones).done(function(html){area.html(html);}).fail(function(xhr){if(xhr.status!==422){if(window.Swal){Swal.fire({icon:'error',title:'No se pudo guardar',text:'Inténtalo nuevamente.'});}else{alert('No se pudo guardar.');}}}).always(function(){area.css('opacity','1').attr('aria-busy','false');});}
  function volverListadoGenericos(){cargarVistaGenerico({type:'GET',url:"{{route('listar.medicina_generica.hc4')}}"});}
  function actualiza_generico(){$('.cl_nombre,.cl_descripcion,.cl_estado').removeClass('has-error');$('#str_nombre,#str_descripcion,#str_estado').text('');var boton=$('#guardar_generico').prop('disabled',true);cargarVistaGenerico({type:'POST',url:"{{route('actualiza_generico_hc4')}}",data:$('#actualiza_generico').serialize()}).fail(function(xhr){var respuesta=xhr.responseJSON||{},errores=respuesta.errors||respuesta;$.each(['nombre','descripcion','estado'],function(_,campo){if(errores[campo]!=null){var mensaje=$.isArray(errores[campo])?errores[campo][0]:errores[campo];$('.cl_'+campo).addClass('has-error');$('#str_'+campo).text(mensaje);}});boton.prop('disabled',false);});}
</script>
