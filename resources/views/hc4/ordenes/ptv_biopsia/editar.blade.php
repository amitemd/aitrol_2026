
<style type="text/css">
  .dataTable > tbody> tr:hover{
     background-color: #99ffe6;
  }
</style>
@php
    $var_tiempo_endos  = rand(0,99999);
@endphp

<form id="form_editar_biopsia_ptv{{$biopsia_ptv->id}}">
  {{ csrf_field() }}
  <input type="hidden" name="id_biopsia" id="id_biopsia" value="{{$biopsia_ptv->id}}">
	
	<div class="row">
    @foreach($biopsia_ptv->detalles as $detalle)
    
      <div class="form-group col-md-6 col-sm-12 col-12">
        <label for="detalle" class="control-label">{{$detalle->descripcion}}</label>
          <input id="detalle{{$biopsia_ptv->id}}-{{$detalle->id}}" class="form-control input-sm" type="text" name="detalle-{{$detalle->id}}" maxlength="255" value="{{$detalle->detalle}}">
          <span class="help-block">
            <strong id="str_otras_localizaciones{{$biopsia_ptv->id}}"></strong>
          </span>
      </div>

    @endforeach
    
    <div class="form-group col-md-12 col-sm-12 col-12"></div>

    <div class="form-group col-md-6 col-sm-12 col-12">
      <label for="otras_localizaciones" class="control-label">Otras Localizaciones</label>
        <input id="otras_localizaciones{{$biopsia_ptv->id}}" class="form-control input-sm" type="text" name="otras_localizaciones" maxlength="255" value="{{$biopsia_ptv->otras_localizaciones}}">
        <span class="help-block">
          <strong id="str_otras_localizaciones{{$biopsia_ptv->id}}"></strong>
        </span>
    </div>
    <div class="form-group col-md-6 col-sm-12 col-12">
      <label for="otros_organos" class="control-label">Otros Órganos</label>
        <input id="otros_organos{{$biopsia_ptv->id}}" class="form-control input-sm" type="text" name="otros_organos" maxlength="255" value="{{$biopsia_ptv->otros_organos}}">
        <span class="help-block">
          <strong id="str_otros_organos{{$biopsia_ptv->id}}"></strong>
        </span>
    </div>

    <div class="form-group col-md-12 col-sm-12 col-12">
      <label for="datos_clinicos{{$biopsia_ptv->id}}" class="control-label">Datos Clínicos</label>
      <div id="tdatos_clinicos{{$biopsia_ptv->id}}{{$var_tiempo_endos}}" style="border: solid 1px;"><?php echo $biopsia_ptv->datos_clinicos ?></div>
      <input type="hidden" name="datos_clinicos" id="datos_clinicos{{$biopsia_ptv->id}}">
      <span class="help-block">
        <strong id="str_datos_clinicos"></strong>
      </span>
    </div>
      
    <div class="form-group col-md-12 col-sm-12 col-12">
      <label for="diagnostico" class="control-label">Diagnóstico</label>
        <input id="diagnostico{{$biopsia_ptv->id}}" class="form-control input-sm" type="text" name="diagnostico" maxlength="255" value="{{$biopsia_ptv->diagnostico}}">
        <span class="help-block">
          <strong id="str_diagnostico{{$biopsia_ptv->id}}"></strong>
        </span>
    </div>
   	
	</div>

  <button class="btn btn-primary btn-sm" type="button" onclick="guardar_orden_biopsia_ptv('{{$biopsia_ptv->id}}')" >Guardar</button>

</form>	


<script type="text/javascript">

  tinymce.init({
    selector: '#tdatos_clinicos{{$biopsia_ptv->id}}{{$var_tiempo_endos}}',
    inline: true,
    menubar: false,
    content_style: ".mce-content-body {font-size:14px;}",
    toolbar: [
      'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
    ],
        
    setup: function (editor) {
      editor.on('init', function (e) {
        var ed = tinyMCE.get('tdatos_clinicos{{$biopsia_ptv->id}}{{$var_tiempo_endos}}');
        $("#datos_clinicos{{$biopsia_ptv->id}}").val(ed.getContent());
      });
    },
       
    init_instance_callback: function (editor) {
      editor.on('Change', function (e) {
        var ed = tinyMCE.get('tdatos_clinicos{{$biopsia_ptv->id}}{{$var_tiempo_endos}}');
        $("#datos_clinicos{{$biopsia_ptv->id}}").val(ed.getContent());
        //GUARDAR  
      }); 
    }
  });
  
</script>    