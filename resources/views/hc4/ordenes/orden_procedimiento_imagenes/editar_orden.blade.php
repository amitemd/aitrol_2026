<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
    
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
     
    .boton-proce{
      font-size: 15px ;
      width: 20%;
      background-color: #004AC1; 
      color: white;
      text-align: center;
      height: 35px;
      padding-left: 5px;
      padding-right: 5px;
      padding-bottom: 0px;
      padding-top: 7px; 
      margin-bottom: 5px; 
    } 
     
    .parent{
     height: 462px;
    }

    .alerta_correcto{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;
      
    }


    .alerta_guardado{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;

    }

    .hc4-image-edit-section {
      margin: 2px 0 10px;
    }

    #area_trabajo .hc4-image-edit-section__title {
      display: flex;
      align-items: center;
      gap: 8px;
      min-height: 32px;
      margin-bottom: 6px;
      padding: 6px 10px;
      border-radius: 7px;
      background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
      color: #fff !important;
      font-family: 'Helvetica general';
      font-size: 12px;
      font-weight: 700;
      line-height: 1.25;
    }

    .hc4-image-catalog-tools {
      display: grid;
      grid-template-columns: minmax(210px, 1fr) minmax(210px, 1fr) auto;
      gap: 7px;
      margin-bottom: 7px;
    }

    .hc4-image-catalog-tools__field {
      position: relative;
    }

    #area_trabajo .hc4-image-catalog-tools__input {
      width: 100%;
      height: 34px;
      padding: 5px 32px 5px 10px;
      border: 1px solid #cbdde5;
      border-radius: 7px;
      color: #485563;
      background: #fff;
      box-shadow: none;
    }

    #area_trabajo .hc4-image-catalog-tools__input:focus {
      border-color: #13a3ad;
      box-shadow: 0 0 0 3px rgba(19,163,173,.12);
      outline: none;
    }

    .hc4-image-catalog-tools__clear {
      position: absolute;
      top: 0;
      right: 0;
      width: 32px;
      height: 34px;
      border: 0;
      background: transparent;
      color: #82939e;
    }

    .hc4-image-catalog-selected {
      display: flex;
      align-items: center;
      gap: 6px;
      min-height: 34px;
      padding: 5px 10px;
      border: 1px solid #d7e5eb;
      border-radius: 7px;
      background: #f3f9fb;
      color: #4e6070;
      white-space: nowrap;
    }

    .hc4-image-catalog-selected input {
      width: 15px;
      height: 15px;
      margin: 0;
      accent-color: #159da8;
    }

    .hc4-image-catalog {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      grid-auto-flow: dense;
      align-items: start;
      gap: 8px;
      max-height: 440px;
      padding: 1px 4px 3px 0;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .hc4-image-group {
      overflow: hidden;
      border: 1px solid #d7e3e8;
      border-radius: 7px;
      background: #fff;
    }

    .hc4-image-group--wide {
      grid-column: 1 / -1;
    }

    .hc4-image-group--medium {
      grid-column: 1 / -1;
    }

    .hc4-image-group__title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      min-height: 30px;
      margin: 0;
      padding: 5px 9px;
      background: var(--group-bg);
      color: var(--group-text);
      font-size: 11px;
      font-weight: 700;
    }

    .hc4-image-group__title small {
      color: inherit;
      font-size: 9px;
      font-weight: 500;
      opacity: .75;
      white-space: nowrap;
    }

    .hc4-image-group__items {
      display: grid;
      grid-template-columns: repeat(var(--group-columns, 1), minmax(0, 1fr));
    }

    .hc4-image-procedure {
      display: flex;
      align-items: center;
      gap: 6px;
      min-width: 0;
      min-height: 28px;
      margin: 0;
      padding: 4px 7px;
      border-bottom: 1px solid #e8eef1;
      border-right: 1px solid #eef2f4;
      color: #53606b;
      font-size: 10.5px;
      font-weight: 400;
      line-height: 1.18;
      cursor: pointer;
      overflow-wrap: anywhere;
    }

    .hc4-image-procedure:last-child {
      border-bottom: 0;
    }

    .hc4-image-procedure:hover,
    .hc4-image-procedure.is-selected {
      background: var(--group-selected);
    }

    .hc4-image-procedure input {
      flex: 0 0 auto;
      width: 14px !important;
      min-width: 14px !important;
      max-width: 14px !important;
      height: 14px !important;
      min-height: 14px !important;
      max-height: 14px !important;
      margin: 0;
      padding: 0 !important;
      position: static !important;
      accent-color: #2e9ed6;
    }

    .hc4-image-catalog__empty {
      grid-column: 1 / -1;
      padding: 25px;
      border: 1px dashed #cbdde5;
      border-radius: 9px;
      color: #667782;
      text-align: center;
    }

    .hc4-image-edit-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin: 20px 0 4px;
    }

    #area_trabajo .hc4-image-edit-actions__button {
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      gap: 7px;
      width: 210px !important;
      max-width: 100%;
      height: 42px !important;
      min-height: 42px !important;
      margin: 0 !important;
      padding: 8px 14px !important;
      border: 0 !important;
      border-radius: 9px !important;
      background: linear-gradient(105deg, #07547e, #0795b5) !important;
      color: #fff !important;
      box-shadow: 0 6px 14px rgba(5,84,126,.16) !important;
      font-size: 13px !important;
      line-height: 1.2 !important;
    }

    #area_trabajo .hc4-image-edit-actions__button:hover,
    #area_trabajo .hc4-image-edit-actions__button:focus {
      background: linear-gradient(105deg, #06486d, #0787a5) !important;
      color: #fff !important;
      outline: none;
    }

    @media (max-width: 560px) {
      .hc4-image-edit-actions { flex-direction: column; }
      #area_trabajo .hc4-image-edit-actions__button { width: 100% !important; }
      .hc4-image-catalog-tools { grid-template-columns: 1fr; }
      .hc4-image-catalog { grid-template-columns: 1fr; max-height: 460px; }
      .hc4-image-group--wide { grid-column: span 1; }
      .hc4-image-group--medium { grid-column: span 1; }
      .hc4-image-group__items { grid-template-columns: 1fr !important; }
    }

    @media (min-width: 561px) and (max-width: 1050px) {
      .hc4-image-catalog { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .hc4-image-group--wide { grid-column: 1 / -1; }
      .hc4-image-group--medium { grid-column: 1 / -1; }
      .hc4-image-group__items { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    }

</style>
@php
  $aleatorio = rand();
@endphp
<div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
       Guardado Correctamente
</div>
<div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Seleccione un Procedimiento Imagenes
</div>

<div id="hc4_image_editor_{{$data_orden_imag->id}}" class="col-md-12" style="padding-left: 15px;padding-right: 30px;">
  @php
      if(!is_null($paciente)){
            
        $seguro = Sis_medico\Seguro::find($paciente->id_seguro); 
           
      }

      $fecha = substr($data_orden_imag->fecha_orden,0,10);
      $invert = explode( '-',$fecha);
      $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
  @endphp

  <form method="POST" id="edit_orden_imag_{{$data_orden_imag->id}}">
      {{ csrf_field() }}
      <input type="hidden" name="id_ordenimag" value="{{$data_orden_imag->id}}">
      <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
      <div class="row">
        <div class="col-md-8">
          @if(!is_null($fecha_invert))
            <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
            <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
            </label>
          @endif 
        </div>
        @if($ndoctor->id != 1307189140)
          <!--div class="col-md-4">
            <span style="font-family: 'Helvetica general';font-size: 16px;color: red;">Agregar Firma CRM</span>
            <input style="width:17px;height:17px;" type="checkbox" id="firma_doctor_rob" class="flat-red" name="firma_doctor_rob" value="1" 
              @if(old('firma_doctor_rob')=="1")  
                checked 
              @elseif($data_orden_imag->check_doctor =='1') 
                checked 
              @endif>
          </div-->
        @endif
      </div>
      <div class="row">
        <div class="col-md-8">
          <div>
            @if(!is_null($ndoctor)) 
            <span style="font-family: 'Helvetica general'; font-size: 12px">DOCTOR SOLICITANTE:</span>
            <label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$ndoctor->apellido1}} {{$ndoctor->apellido2}} {{$ndoctor->nombre1}} {{$ndoctor->nombre2}}</b>
            </label>
            @endif 
          </div>  
        </div>
        <div class="col-md-4">
          @if(!is_null($seguro)) 
            <span style="font-family: 'Helvetica general';font-size: 12px">SEGURO:</span>
            <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
              <b> 
                {{$seguro->nombre}}
              </b>
            </label>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">MOTIVO:</span>
          <textarea id="xmotivo_orden" name="xmotivo_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden_imag)){{$data_orden_imag->motivo_consulta}}@endif 
          </textarea>
        </div>
      </div>
      <div class="col-12">&nbsp;</div>
      <div class="row">
            <div class="col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
              <div id="imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                @if(!is_null($data_orden_imag))<?php echo $data_orden_imag->resumen_clinico ?>@endif
              </div>
              <input type="hidden" name="imag_historia_clinica" id="imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}">
            </div>
      </div>
      <div class="row">
              <div class="col-md-12">
                <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                <div id="imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                  @if(!is_null($data_orden_imag))<?php echo $data_orden_imag->diagnosticos ?>@endif
                </div>
                <input type="hidden" name="imag_des_diagnostico" id="imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}">
              </div>
      </div>
      @php
        $paletaImagenes = [
          ['fondo' => '#dbeafe', 'texto' => '#1e3a5f', 'seleccion' => '#eff6ff'],
          ['fondo' => '#dcfce7', 'texto' => '#285943', 'seleccion' => '#f0fdf4'],
          ['fondo' => '#fef3c7', 'texto' => '#6b4f16', 'seleccion' => '#fffbeb'],
          ['fondo' => '#fce7f3', 'texto' => '#6b3150', 'seleccion' => '#fdf2f8'],
          ['fondo' => '#cffafe', 'texto' => '#245766', 'seleccion' => '#ecfeff'],
          ['fondo' => '#ede9fe', 'texto' => '#493d72', 'seleccion' => '#f5f3ff'],
          ['fondo' => '#e2e8f0', 'texto' => '#34445a', 'seleccion' => '#f8fafc'],
        ];
      @endphp
      <div class="hc4-image-edit-section">
          <div class="hc4-image-edit-section__title">
            <i class="fa fa-picture-o" aria-hidden="true"></i>
            <span>PROCEDIMIENTOS IMÁGENES</span>
          </div>
          <div class="hc4-image-catalog-tools">
            <div class="hc4-image-catalog-tools__field">
              <input type="search" class="hc4-image-catalog-tools__input hc4-image-search-exam" placeholder="Buscar examen" autocomplete="off">
              <button type="button" class="hc4-image-catalog-tools__clear" data-clear="exam" title="Limpiar examen">
                <i class="glyphicon glyphicon-remove-circle"></i>
              </button>
            </div>
            <div class="hc4-image-catalog-tools__field">
              <input type="search" class="hc4-image-catalog-tools__input hc4-image-search-group" placeholder="Buscar agrupador" autocomplete="off">
              <button type="button" class="hc4-image-catalog-tools__clear" data-clear="group" title="Limpiar agrupador">
                <i class="glyphicon glyphicon-remove-circle"></i>
              </button>
            </div>
            <label class="hc4-image-catalog-selected">
              <input type="checkbox" class="hc4-image-show-selected">
              <span>Ver seleccionados (<b class="hc4-image-selected-count">{{count($procedimientosSeleccionados)}}</b>)</span>
            </label>
          </div>

          <div class="hc4-image-catalog">
            @foreach($gruposImagenes as $indiceGrupo => $grupoImagen)
              @php
                $colorGrupo = $paletaImagenes[$indiceGrupo % 6];
                $cantidadGrupo = $grupoImagen->procedimientos->count();
                $grupoAmplio = $cantidadGrupo > 24;
                $grupoMediano = $cantidadGrupo > 12 && $cantidadGrupo <= 24;
                $columnasGrupo = $cantidadGrupo > 70 ? 5 : ($cantidadGrupo > 40 ? 4 : ($cantidadGrupo > 24 ? 3 : ($cantidadGrupo > 12 ? 2 : 1)));
              @endphp
              <section class="hc4-image-group @if($grupoAmplio) hc4-image-group--wide @elseif($grupoMediano) hc4-image-group--medium @endif"
                       data-group-name="{{$grupoImagen->nombre}}"
                       style="--group-bg:{{$colorGrupo['fondo']}};--group-text:{{$colorGrupo['texto']}};--group-selected:{{$colorGrupo['seleccion']}};--group-columns:{{$columnasGrupo}};">
                <h4 class="hc4-image-group__title">
                  <span>{{$grupoImagen->nombre}}</span>
                  <small><span class="hc4-image-group-count">{{$cantidadGrupo}}</span> exámenes</small>
                </h4>
                <div class="hc4-image-group__items">
                  @foreach($grupoImagen->procedimientos as $procedimientoImagen)
                    @php $estaSeleccionado = in_array($procedimientoImagen->id, $procedimientosSeleccionados); @endphp
                    <label class="hc4-image-procedure @if($estaSeleccionado) is-selected @endif" data-procedure-name="{{$procedimientoImagen->nombre}}">
                      <input type="checkbox"
                             class="hc4-image-procedure-check"
                             name="x_procedimiento_imag[]"
                             value="{{$procedimientoImagen->id}}"
                             @if($estaSeleccionado) checked @endif>
                      <span>{{$procedimientoImagen->nombre}}</span>
                    </label>
                  @endforeach
                </div>
              </section>
            @endforeach

            @if($procedimientosAnteriores->isNotEmpty())
              @php $colorAnterior = $paletaImagenes[6]; @endphp
              <section class="hc4-image-group" data-group-name="PROCEDIMIENTOS ANTERIORES"
                       style="--group-bg:{{$colorAnterior['fondo']}};--group-text:{{$colorAnterior['texto']}};--group-selected:{{$colorAnterior['seleccion']}};--group-columns:1;">
                <h4 class="hc4-image-group__title">
                  <span>PROCEDIMIENTOS ANTERIORES</span>
                  <small>{{$procedimientosAnteriores->count()}} exámenes</small>
                </h4>
                <div class="hc4-image-group__items">
                  @foreach($procedimientosAnteriores as $procedimientoAnterior)
                    <label class="hc4-image-procedure is-selected" data-procedure-name="{{$procedimientoAnterior->nombre}}">
                      <input type="checkbox" class="hc4-image-procedure-check" name="x_procedimiento_imag[]" value="{{$procedimientoAnterior->id}}" checked>
                      <span>{{$procedimientoAnterior->nombre}}</span>
                    </label>
                  @endforeach
                </div>
              </section>
            @endif

            <div class="hc4-image-catalog__empty" hidden>No se encontraron exámenes con los filtros ingresados.</div>
          </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
          <textarea id="xobservacion_orden" name="xobservacion_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_imagenes({{$data_orden_imag->id}});">@if(!is_null($data_orden_imag))<?php echo $data_orden_imag->observacion_medica ?>@endif</textarea>
        </div>
      </div>
      <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span><textarea id="xobservacion_recepcion" name="xobservacion_recepcion" style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_imagenes({{$data_orden_imag->id}});">@if(!is_null($data_orden_imag))<?php echo $data_orden_imag->observacion_recepcion?>@endif</textarea>
          </div>
      </div>
      <div class="hc4-image-edit-actions">
          <button type="button" class="btn hc4-image-edit-actions__button" onclick="actualiza_orden_imagenes({{$data_orden_imag->id}})">
            <span class="fa fa-floppy-o" aria-hidden="true"></span>
            <span>Guardar</span>
          </button>
          <button type="button" class="btn hc4-image-edit-actions__button" onclick="descargar_orden_imagenes({{$data_orden_imag->id}});">
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
            <span>Descargar Orden</span>
          </button>
      </div>
  </form>
  <div class="row" style="border-radius: 8px;" id="area_trabajo_formato012_pi"></div>
</div>

<script type="text/javascript">

  tinymce.init({
      selector: '#imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}');
            $('#imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}');
              $('#imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
              
          });
      }
    });


    tinymce.init({
      selector: '#imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}');
            $('#imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}');
              $('#imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
              
          });
      }
    });

</script>

<script type="text/javascript">
  (function configurarCatalogoImagenes{{$data_orden_imag->id}}(){
    var idOrden = '{{$data_orden_imag->id}}';
    var $editor = $('#hc4_image_editor_' + idOrden);
    var $catalogo = $editor.find('.hc4-image-catalog');
    var $buscarExamen = $editor.find('.hc4-image-search-exam');
    var $buscarGrupo = $editor.find('.hc4-image-search-group');
    var $soloSeleccionados = $editor.find('.hc4-image-show-selected');

    function normalizar(texto){
      var valor = String(texto || '').toLowerCase();
      return typeof valor.normalize === 'function'
        ? valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        : valor;
    }

    function actualizarContador(){
      var cantidad = $editor.find('.hc4-image-procedure-check:checked').length;
      $editor.find('.hc4-image-selected-count').text(cantidad);
    }

    function filtrarCatalogo(){
      var examen = normalizar($buscarExamen.val());
      var grupo = normalizar($buscarGrupo.val());
      var seleccionados = $soloSeleccionados.prop('checked');
      var gruposVisibles = 0;

      $catalogo.find('.hc4-image-group').each(function(){
        var $grupo = $(this);
        var coincideGrupo = !grupo || normalizar($grupo.data('group-name')).indexOf(grupo) !== -1;
        var visibles = 0;

        $grupo.find('.hc4-image-procedure').each(function(){
          var $fila = $(this);
          var coincideExamen = !examen || normalizar($fila.data('procedure-name')).indexOf(examen) !== -1;
          var coincideSeleccion = !seleccionados || $fila.find('.hc4-image-procedure-check').prop('checked');
          var mostrar = coincideGrupo && coincideExamen && coincideSeleccion;
          $fila.toggle(mostrar);
          if (mostrar) visibles++;
        });

        $grupo.find('.hc4-image-group-count').text(visibles);
        $grupo.toggle(visibles > 0);
        if (visibles > 0) gruposVisibles++;
      });

      $catalogo.find('.hc4-image-catalog__empty').prop('hidden', gruposVisibles > 0);
    }

    $editor.find('.hc4-image-procedure-check').on('change.hc4imagenes', function(){
      var $check = $(this);
      if (!$check.prop('checked') && !$editor.find('.hc4-image-procedure-check:checked').length) {
        $check.prop('checked', true);
        $check.closest('.hc4-image-procedure').addClass('is-selected');
        $('#select_proc').stop(true, true).fadeIn(300).delay(1700).fadeOut(500);
        return;
      }

      $check.closest('.hc4-image-procedure').toggleClass('is-selected', $check.prop('checked'));
      actualizarContador();
      filtrarCatalogo();
      actualiza_orden_imagenes(idOrden);
    });

    $buscarExamen.add($buscarGrupo).on('input.hc4imagenes', filtrarCatalogo);
    $soloSeleccionados.on('change.hc4imagenes', filtrarCatalogo);
    $editor.find('.hc4-image-catalog-tools__clear').on('click.hc4imagenes', function(){
      var objetivo = $(this).data('clear') === 'group' ? $buscarGrupo : $buscarExamen;
      objetivo.val('').focus();
      filtrarCatalogo();
    });

    actualizarContador();
    filtrarCatalogo();
  })();

</script>

<script type="text/javascript">
  
    //Funcion para actualizar Orden de Imagenes
    function actualiza_orden_imagenes(id_orden){
        var $editor = $('#hc4_image_editor_' + id_orden);
        var $formulario = $('#edit_orden_imag_' + id_orden);
        var $procedimientos = $editor.find('.hc4-image-procedure-check:checked');
        var estadoGuardado = $editor.data('estadoGuardadoImagenes') || {guardando: false, pendiente: false};

        if ($procedimientos.length){
          if (estadoGuardado.guardando) {
            estadoGuardado.pendiente = true;
            $editor.data('estadoGuardadoImagenes', estadoGuardado);
            return;
          }

          estadoGuardado.guardando = true;
          estadoGuardado.pendiente = false;
          $editor.data('estadoGuardadoImagenes', estadoGuardado);

          $.ajax({
              type: "POST",
              url: "{{route('actualiza.ordenhc4_procedimagenes')}}",
              headers:{'X-CSRF-TOKEN':$formulario.find('input[name=_token]').val()},
              dataType: "json",
              data: $formulario.serialize(),
              success: function(){},
              error: function(xhr){
                var mensaje = xhr.responseJSON && xhr.responseJSON.message
                  ? xhr.responseJSON.message
                  : 'No se pudo guardar la orden de imágenes.';
                if (window.Swal && typeof window.Swal.fire === 'function') {
                  window.Swal.fire({icon: 'error', title: 'No se guardó', text: mensaje});
                } else {
                  window.alert(mensaje);
                }
              },
              complete: function(){
                estadoGuardado.guardando = false;
                $editor.data('estadoGuardadoImagenes', estadoGuardado);
                if (estadoGuardado.pendiente) {
                  estadoGuardado.pendiente = false;
                  actualiza_orden_imagenes(id_orden);
                }
              }
            });
        }else{
          $("#select_proc").fadeIn(1000);
          $("#select_proc").fadeOut(3000);
        }   

    };

    function descargar_orden_imagenes(id_or){
       window.open('{{url('imprimir/orden_hc4/imagenes')}}/'+id_or,'_blank');  
    }
</script>
<script type="text/javascript">

function formato_012(id) {

$.ajax({
  type: 'GET',
  url:"{{url('hc4/formato012')}}/"+id,
  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
  datatype: 'json',
  data: $("#edit_orden").serialize(),
  success: function(data){
    $("#area_trabajo_formato012_pi").empty().html(data);
    //console.log(data);
  },
  error: function(data){
    console.log(data);
  }
});
}
  
</script>
