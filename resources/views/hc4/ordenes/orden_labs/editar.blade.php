<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .dataTable > tbody> tr:hover{
     background-color: #99ffe6;
  }

  .hc4-lab-editor {
    padding: 4px 0 0;
  }

  .hc4-lab-editor__filters {
    display: grid;
    grid-template-columns: minmax(220px, 1.25fr) minmax(220px, 1.25fr) minmax(220px, 1fr) auto;
    align-items: center;
    gap: 12px;
    margin: 0;
  }

  .hc4-lab-editor__field {
    min-width: 0;
    margin: 0 !important;
  }

  .hc4-lab-editor__field .input-group {
    display: flex;
    flex-wrap: nowrap !important;
    align-items: stretch;
    width: 100%;
    margin: 0 !important;
  }

  .hc4-lab-editor__field .form-control {
    flex: 1 1 auto !important;
    width: 1% !important;
    min-width: 0;
    height: 42px;
    border: 1px solid #d7e2ea;
    border-radius: 9px 0 0 9px;
    box-shadow: none;
    font-size: 14px;
  }

  .hc4-lab-editor__field input[type="search"]::-webkit-search-cancel-button {
    display: none;
    -webkit-appearance: none;
  }

  .hc4-lab-editor__field--profile .form-control {
    border-radius: 9px;
  }

  .hc4-lab-editor__clear {
    display: inline-flex;
    flex: 0 0 42px;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    padding: 0;
    border: 1px solid #d7e2ea;
    border-left: 0;
    border-radius: 0 9px 9px 0;
    background: #f4f8fa;
    color: #647481;
    cursor: pointer;
  }

  .hc4-lab-editor__clear:hover,
  .hc4-lab-editor__clear:focus {
    color: #087eae;
    background: #eaf5f8;
    outline: none;
  }

  .hc4-lab-editor__search {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    padding: 0;
    border: 0;
    border-radius: 9px;
    background: linear-gradient(135deg, #087eae, #16aaa5);
    color: #fff;
    box-shadow: 0 4px 10px rgba(8, 126, 174, .20);
  }

  .hc4-lab-editor__selected {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    padding: 10px 12px;
    border: 1px solid #dce9ee;
    border-radius: 9px;
    background: #f5fafb;
  }

  .hc4-lab-editor__selected label {
    margin: 0;
    color: #34495e !important;
    font-size: 13px !important;
    font-weight: 600;
  }

  .hc4-lab-editor #listado_final {
    margin-top: 14px;
  }

  .hc4-lab-results { margin-top: 14px; }

  .hc4-lab-catalog {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    grid-auto-flow: dense;
    align-items: start;
    gap: 10px;
    max-height: 520px;
    padding: 4px 8px 8px 4px;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-color: #24a6b4 #e6f3f5;
    scrollbar-width: thin;
  }

  .hc4-lab-group {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #dce7ec;
    border-radius: 8px;
    background: #fff;
    break-inside: avoid;
  }

  .hc4-lab-group--wide {
    grid-column: 1 / -1;
  }

  .hc4-lab-group--wide .hc4-lab-group__items {
    column-count: var(--group-columns, 3);
    column-gap: 0;
    column-rule: 1px solid #e2e8ed;
  }

  .hc4-lab-group--wide .hc4-lab-exam-row {
    break-inside: avoid;
    page-break-inside: avoid;
  }

  .hc4-lab-group__title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 0;
    padding: 7px 10px;
    background: var(--group-bg);
    color: var(--group-text);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.25;
    text-align: center;
    text-transform: uppercase;
  }

  .hc4-lab-group__title small {
    color: inherit;
    font-size: 10px;
    font-weight: 600;
    opacity: .72;
    text-transform: none;
  }

  .hc4-lab-exam-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 32px;
    margin: 0;
    padding: 5px 9px;
    border-bottom: 1px solid #edf1f3;
    color: #3f4652;
    cursor: pointer;
  }

  .hc4-lab-exam-row:last-child { border-bottom: 0; }

  .hc4-lab-exam-row:hover,
  .hc4-lab-exam-row.is-selected { background: var(--group-selected); }

  .hc4-lab-exam-row__check {
    flex: 0 0 22px;
    text-align: center;
  }

  .hc4-lab-exam-row__name {
    min-width: 0;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.25;
    overflow-wrap: anywhere;
  }

  .hc4-lab-catalog__empty {
    grid-column: 1 / -1;
    padding: 18px;
    border: 1px dashed #cfdde4;
    border-radius: 8px;
    color: #647481;
    text-align: center;
  }

  .hc4-lab-editor__field--exam,
  .hc4-lab-editor__field--group {
    position: relative;
  }

  .hc4-lab-suggestions {
    position: absolute;
    z-index: 2147483647;
    top: calc(100% + 5px);
    right: 0;
    left: 0;
    max-height: 280px;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 6px;
    border: 1px solid #cddfe7;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(5, 84, 126, .18);
  }

  .hc4-lab-suggestions[hidden] {
    display: none !important;
  }

  .hc4-lab-suggestion {
    display: block;
    width: 100%;
    padding: 9px 10px;
    border: 0;
    border-radius: 5px;
    background: #fff;
    color: #34495e;
    text-align: left;
    font-size: 13px;
    cursor: pointer;
  }

  .hc4-lab-suggestion:hover,
  .hc4-lab-suggestion:focus,
  .hc4-lab-suggestion.is-active {
    background: #0b91ad;
    color: #fff;
    outline: none;
  }

  .hc4-lab-suggestion-status {
    padding: 9px 10px;
    color: #647481;
    font-size: 13px;
  }

  #area_trabajo .hc4-lab-editor__field--profile .select2-container {
    width: 100% !important;
  }

  #area_trabajo .hc4-lab-editor__field--profile .select2-selection--single {
    height: 42px !important;
    border: 1px solid #d7e2ea !important;
    border-radius: 9px !important;
    background: #fff !important;
    box-shadow: none !important;
  }

  #area_trabajo .hc4-lab-editor__field--profile .select2-selection__rendered {
    padding-right: 38px !important;
    padding-left: 13px !important;
    color: #4f5661 !important;
    font-size: 14px;
    line-height: 40px !important;
  }

  #area_trabajo .hc4-lab-editor__field--profile .select2-selection__arrow {
    top: 1px !important;
    right: 8px !important;
    height: 40px !important;
  }

  #area_trabajo .hc4-lab-editor__field--profile .select2-container--focus .select2-selection--single,
  #area_trabajo .hc4-lab-editor__field--profile .select2-container--open .select2-selection--single {
    border-color: #13a3ad !important;
    box-shadow: 0 0 0 3px rgba(19, 163, 173, .12) !important;
  }

  @media (max-width: 1050px) {
    .hc4-lab-editor__filters {
      grid-template-columns: 1fr 1fr;
    }
    .hc4-lab-editor__search { width: 100%; }
    .hc4-lab-catalog { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .hc4-lab-group--wide .hc4-lab-group__items { column-count: 2; }
  }

  @media (max-width: 620px) {
    .hc4-lab-editor__filters { grid-template-columns: 1fr; }
    .hc4-lab-catalog { grid-template-columns: 1fr; max-height: 440px; }
    .hc4-lab-group--wide .hc4-lab-group__items { column-count: 1; }
  }
</style>

<div id="hc4_lab_editor_{{$orden->id}}" class="hc4-lab-editor">
<form class="hc4-lab-search-form">
	{{ csrf_field() }}

	<div class="hc4-lab-editor__filters">
		<div class="form-group hc4-lab-editor__field hc4-lab-editor__field--exam">
			<div class="input-group">
			    <input name="buscador" type="search" class="form-control hc4-lab-search-input" placeholder="Buscar Examen" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false">
			    <button class="hc4-lab-editor__clear" type="button" title="Limpiar examen">
			      <i class="glyphicon glyphicon-remove-circle"></i>
			    </button>
			 </div>
			 <div class="hc4-lab-suggestions" role="listbox" hidden></div>
		</div>
		<div class="form-group hc4-lab-editor__field hc4-lab-editor__field--group">
			<div class="input-group">
			    <input name="buscador2" type="search" class="form-control hc4-lab-search-input" placeholder="Buscar Agrupador" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false">
			    <button class="hc4-lab-editor__clear" type="button" title="Limpiar agrupador">
			      <i class="glyphicon glyphicon-remove-circle"></i>
			    </button>
			 </div>
			 <div class="hc4-lab-group-suggestions hc4-lab-suggestions" role="listbox" hidden></div>
		</div>
    <div class="form-group hc4-lab-editor__field hc4-lab-editor__field--profile">
      <div class="input-group">
          <select name="id_protocolo" class="form-control hc4-lab-profile-select">
            <option value="">Seleccione Perfil</option>
            @foreach($protocolos as $protocolo)
              <option @if($orden->id_protocolo == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
            @endforeach
          </select>  
       </div>
    </div>
    <div class="form-group hc4-lab-editor__field">
      <button class="btn hc4-lab-editor__search" type="submit" title="Buscar">
        <span class="glyphicon glyphicon-search"></span>
      </button>
    </div>
	</div>

		<div class="form-group hc4-lab-editor__selected">
			<input name="seleccionados" type="checkbox" class="flat-red hc4-lab-selected" value="1" @if(count($detalles_ch) > 0) checked @endif><label style="color: red;font-size: 14px;"> Ver Seleccionados</label>
		</div>
		<!--div class="form-group col-4" style="margin-bottom: 0;">
			<input id="firma_dr" name="firma_dr" type="checkbox" class="flat-green" onchange="cargar_buscador();" value="1" @if($orden->doctor_firma=='1307189140') checked @endif><label style="color: green;font-size: 14px;"> Firma del Dr. Robles</label>
		</div-->	

</form>

<div class="hc4-lab-results">
  @include('hc4.ordenes.orden_labs.listado')
</div>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  (function configurarEditorLaboratorio{{$orden->id}}(){
    var $editor = $('#hc4_lab_editor_{{$orden->id}}');
    var $form = $editor.find('.hc4-lab-search-form');
    var $listado = $editor.find('.hc4-lab-results');
    var $seleccionados = $editor.find('.hc4-lab-selected');
    var peticionBusqueda = null;
    var omitirEventoSeleccionados = false;
    var restaurandoExamen = false;

    function token(){
      return $form.find('input[name="_token"]').val();
    }

    function actualizarTotales(data){
      $('#scantidad').empty().html(data.cantidad);
      $('#svalor').empty().html(data.valor);
      $('#sdescuento_valor').empty().html(data.descuento_valor);
      $('#srecargo_valor').empty().html(data.recargo_valor);
      $('#stotal_valor').empty().html(data.total_valor);
    }

    function mensajeErrorGuardado(xhr){
      var mensaje = xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : 'No se pudo guardar el examen. Intente nuevamente.';

      if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire({icon: 'error', title: 'No se guardó', text: mensaje});
      } else if (typeof window.swal === 'function') {
        window.swal('No se guardó', mensaje, 'error');
      } else {
        window.alert(mensaje);
      }
    }

    function cambiarExamen(id, seleccionar, $check){
      $check.iCheck('disable');
      $.ajax({
        type: 'post',
        url: "{{url('hc4/orden/laboratorio/doctor/examen')}}/{{$orden->id}}/" + id,
        headers: {'X-CSRF-TOKEN': token()},
        data: {seleccionar: seleccionar ? 1 : 0},
        dataType: 'json',
        success: function(data){
          actualizarTotales(data);
          $check.closest('.hc4-lab-exam-row').toggleClass('is-selected', seleccionar);
        },
        error: function(xhr){
          restaurandoExamen = true;
          $check.iCheck(seleccionar ? 'uncheck' : 'check');
          restaurandoExamen = false;
          mensajeErrorGuardado(xhr);
        },
        complete: function(){
          $check.iCheck('enable');
        }
      });
    }

    function inicializarExamenes(){
      var evento = '.hc4lab{{$orden->id}}';
      var $checks = $listado.find('input[type="checkbox"].hc4-lab-exam-check');

      $checks.iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
      });
      $checks.off(evento)
        .on('ifChecked' + evento, function(){
          if (!restaurandoExamen) {
            cambiarExamen($(this).data('examen-id'), true, $(this));
          }
        })
        .on('ifUnchecked' + evento, function(){
          if (!restaurandoExamen) {
            cambiarExamen($(this).data('examen-id'), false, $(this));
          }
        });
    }

    function desmarcarSeleccionados(){
      omitirEventoSeleccionados = true;
      $seleccionados.iCheck('uncheck');
      omitirEventoSeleccionados = false;
    }

    function cargarBuscador(){
      if (peticionBusqueda) {
        peticionBusqueda.abort();
      }

      $listado.html('<div class="text-center" style="padding:18px;color:#647481;"><i class="fa fa-spinner fa-spin"></i> Buscando...</div>');
      peticionBusqueda = $.ajax({
        type: 'post',
        url: "{{route('hc4_orden_lab.buscar',['orden' => $orden->id])}}",
        headers: {'X-CSRF-TOKEN': token()},
        data: $form.serialize(),
        success: function(data){
          $listado.empty().html(data);
          inicializarExamenes();
        },
        error: function(xhr, estado){
          if (estado !== 'abort') {
            $listado.html('<div class="alert alert-danger" style="margin:0;">No se pudo consultar el catálogo de laboratorio.</div>');
          }
        },
        complete: function(){
          peticionBusqueda = null;
        }
      });
    }

    var $perfil = $editor.find('.hc4-lab-profile-select');
    if ($perfil.length && $.fn.select2) {
      $perfil.select2({
        width: '100%',
        placeholder: 'Seleccione Perfil',
        allowClear: true,
        minimumResultsForSearch: 0,
        language: {
          noResults: function(){ return 'No se encontraron perfiles'; },
          searching: function(){ return 'Buscando...'; }
        }
      });
    }

    var $buscadorExamen = $form.find('input[name="buscador"]');
    var $sugerenciasExamen = $editor.find('.hc4-lab-editor__field--exam .hc4-lab-suggestions');
    var temporizadorAutocomplete = null;
    var peticionAutocomplete = null;
    var indiceSugerencia = -1;

    function cerrarSugerencias(){
      indiceSugerencia = -1;
      $sugerenciasExamen.empty().prop('hidden', true);
      $buscadorExamen.attr('aria-expanded', 'false');
    }

    function mostrarEstadoSugerencias(mensaje){
      $sugerenciasExamen.empty()
        .append($('<div>', {'class': 'hc4-lab-suggestion-status', text: mensaje}))
        .prop('hidden', false);
      $buscadorExamen.attr('aria-expanded', 'true');
    }

    function activarSugerencia(indice){
      var $opciones = $sugerenciasExamen.find('.hc4-lab-suggestion');
      if (!$opciones.length) {
        return;
      }
      indiceSugerencia = (indice + $opciones.length) % $opciones.length;
      $opciones.removeClass('is-active').eq(indiceSugerencia).addClass('is-active');
    }

    function seleccionarSugerencia($opcion){
      var valor = $opcion.attr('data-value');
      if (!valor) {
        return;
      }
      $buscadorExamen.val(valor);
      cerrarSugerencias();
      desmarcarSeleccionados();
      cargarBuscador();
    }

    function mostrarSugerencias(examenes){
      $sugerenciasExamen.empty();
      indiceSugerencia = -1;

      if (!$.isArray(examenes) || !examenes.length) {
        mostrarEstadoSugerencias('No se encontraron exámenes.');
        return;
      }

      $.each(examenes, function(indice, examen){
        var valor = examen.value || examen.label || '';
        $('<button>', {
          type: 'button',
          'class': 'hc4-lab-suggestion',
          role: 'option',
          text: examen.label || valor
        }).attr('data-value', valor).appendTo($sugerenciasExamen);
      });

      $sugerenciasExamen.prop('hidden', false);
      $buscadorExamen.attr('aria-expanded', 'true');
    }

    function consultarSugerencias(termino){
      if (peticionAutocomplete) {
        peticionAutocomplete.abort();
      }
      mostrarEstadoSugerencias('Buscando...');
      peticionAutocomplete = $.ajax({
        type: 'get',
        url: "{{route('hc4_orden_lab.autocomplete_examenes')}}",
        dataType: 'json',
        data: {term: termino},
        success: function(examenes){
          if ($.trim($buscadorExamen.val()) === termino) {
            mostrarSugerencias(examenes);
          }
        },
        error: function(xhr, estado){
          if (estado !== 'abort') {
            mostrarEstadoSugerencias('No se pudo consultar el catálogo de exámenes.');
          }
        },
        complete: function(){
          peticionAutocomplete = null;
        }
      });
    }

    $buscadorExamen.off('.hc4labautocomplete{{$orden->id}}')
      .on('input.hc4labautocomplete{{$orden->id}}', function(){
        var termino = $.trim($(this).val());
        if ($buscadorAgrupador.val()) {
          $buscadorAgrupador.val('');
          cerrarAgrupadores();
        }
        clearTimeout(temporizadorAutocomplete);
        if (peticionAutocomplete) {
          peticionAutocomplete.abort();
          peticionAutocomplete = null;
        }
        if (termino.length < 2) {
          cerrarSugerencias();
          return;
        }
        temporizadorAutocomplete = setTimeout(function(){
          consultarSugerencias(termino);
        }, 250);
      })
      .on('keydown.hc4labautocomplete{{$orden->id}}', function(event){
        var $opciones = $sugerenciasExamen.find('.hc4-lab-suggestion');
        if (event.key === 'ArrowDown' && $opciones.length) {
          event.preventDefault();
          activarSugerencia(indiceSugerencia + 1);
        } else if (event.key === 'ArrowUp' && $opciones.length) {
          event.preventDefault();
          activarSugerencia(indiceSugerencia - 1);
        } else if (event.key === 'Enter' && indiceSugerencia >= 0) {
          event.preventDefault();
          seleccionarSugerencia($opciones.eq(indiceSugerencia));
        } else if (event.key === 'Escape') {
          cerrarSugerencias();
        }
      });

    $sugerenciasExamen.off('.hc4labautocomplete{{$orden->id}}')
      .on('mousedown.hc4labautocomplete{{$orden->id}}', '.hc4-lab-suggestion', function(event){
        event.preventDefault();
        seleccionarSugerencia($(this));
      });

    var $buscadorAgrupador = $form.find('input[name="buscador2"]');
    var $sugerenciasAgrupador = $editor.find('.hc4-lab-group-suggestions');
    var temporizadorAgrupador = null;
    var peticionAgrupador = null;
    var indiceAgrupador = -1;

    function cerrarAgrupadores(){
      indiceAgrupador = -1;
      $sugerenciasAgrupador.empty().prop('hidden', true);
      $buscadorAgrupador.attr('aria-expanded', 'false');
    }

    function activarAgrupador(indice){
      var $opciones = $sugerenciasAgrupador.find('.hc4-lab-suggestion');
      if (!$opciones.length) {
        return;
      }
      indiceAgrupador = (indice + $opciones.length) % $opciones.length;
      $opciones.removeClass('is-active').eq(indiceAgrupador).addClass('is-active');
    }

    function seleccionarAgrupador($opcion){
      var valor = $opcion.attr('data-value');
      if (!valor) {
        return;
      }
      $buscadorAgrupador.val(valor);
      cerrarAgrupadores();
      desmarcarSeleccionados();
      cargarBuscador();
    }

    function mostrarAgrupadores(agrupadores){
      $sugerenciasAgrupador.empty();
      indiceAgrupador = -1;
      if (!$.isArray(agrupadores) || !agrupadores.length) {
        $sugerenciasAgrupador
          .append($('<div>', {'class': 'hc4-lab-suggestion-status', text: 'No se encontraron agrupadores.'}))
          .prop('hidden', false);
      } else {
        $.each(agrupadores, function(indice, agrupador){
          var valor = agrupador.value || agrupador.label || '';
          $('<button>', {
            type: 'button',
            'class': 'hc4-lab-suggestion',
            role: 'option',
            text: agrupador.label || valor
          }).attr('data-value', valor).appendTo($sugerenciasAgrupador);
        });
        $sugerenciasAgrupador.prop('hidden', false);
      }
      $buscadorAgrupador.attr('aria-expanded', 'true');
    }

    function consultarAgrupadores(termino){
      if (peticionAgrupador) {
        peticionAgrupador.abort();
      }
      $sugerenciasAgrupador.empty()
        .append($('<div>', {'class': 'hc4-lab-suggestion-status', text: 'Buscando...'}))
        .prop('hidden', false);
      $buscadorAgrupador.attr('aria-expanded', 'true');
      peticionAgrupador = $.ajax({
        type: 'get',
        url: "{{route('hc4_orden_lab.autocomplete_agrupadores')}}",
        dataType: 'json',
        data: {term: termino},
        success: function(agrupadores){
          if ($.trim($buscadorAgrupador.val()) === termino) {
            mostrarAgrupadores(agrupadores);
          }
        },
        error: function(xhr, estado){
          if (estado !== 'abort') {
            $sugerenciasAgrupador.empty()
              .append($('<div>', {'class': 'hc4-lab-suggestion-status', text: 'No se pudo consultar el catálogo de agrupadores.'}))
              .prop('hidden', false);
          }
        },
        complete: function(){
          peticionAgrupador = null;
        }
      });
    }

    $buscadorAgrupador.off('.hc4labgroupautocomplete{{$orden->id}}')
      .on('input.hc4labgroupautocomplete{{$orden->id}}', function(){
        var termino = $.trim($(this).val());
        if ($buscadorExamen.val()) {
          $buscadorExamen.val('');
          clearTimeout(temporizadorAutocomplete);
          if (peticionAutocomplete) {
            peticionAutocomplete.abort();
            peticionAutocomplete = null;
          }
          cerrarSugerencias();
        }
        clearTimeout(temporizadorAgrupador);
        if (peticionAgrupador) {
          peticionAgrupador.abort();
          peticionAgrupador = null;
        }
        if (termino.length < 2) {
          cerrarAgrupadores();
          return;
        }
        temporizadorAgrupador = setTimeout(function(){
          consultarAgrupadores(termino);
        }, 250);
      })
      .on('keydown.hc4labgroupautocomplete{{$orden->id}}', function(event){
        var $opciones = $sugerenciasAgrupador.find('.hc4-lab-suggestion');
        if (event.key === 'ArrowDown' && $opciones.length) {
          event.preventDefault();
          activarAgrupador(indiceAgrupador + 1);
        } else if (event.key === 'ArrowUp' && $opciones.length) {
          event.preventDefault();
          activarAgrupador(indiceAgrupador - 1);
        } else if (event.key === 'Enter' && indiceAgrupador >= 0) {
          event.preventDefault();
          seleccionarAgrupador($opciones.eq(indiceAgrupador));
        } else if (event.key === 'Escape') {
          cerrarAgrupadores();
        }
      });

    $sugerenciasAgrupador.off('.hc4labgroupautocomplete{{$orden->id}}')
      .on('mousedown.hc4labgroupautocomplete{{$orden->id}}', '.hc4-lab-suggestion', function(event){
        event.preventDefault();
        seleccionarAgrupador($(this));
      });

    $(document).off('mousedown.hc4labautocomplete{{$orden->id}}')
      .on('mousedown.hc4labautocomplete{{$orden->id}}', function(event){
        if (!$(event.target).closest($editor.find('.hc4-lab-editor__field--exam')).length) {
          cerrarSugerencias();
        }
        if (!$(event.target).closest($editor.find('.hc4-lab-editor__field--group')).length) {
          cerrarAgrupadores();
        }
      });

    $seleccionados.iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    }).off('.hc4lab{{$orden->id}}')
      .on('ifChecked.hc4lab{{$orden->id}}', function(){
        $form.find('input[name="buscador"], input[name="buscador2"]').val('');
        cargarBuscador();
      })
      .on('ifUnchecked.hc4lab{{$orden->id}}', function(){
        if (!omitirEventoSeleccionados) {
          cargarBuscador();
        }
      });

    $form.off('.hc4lab{{$orden->id}}')
      .on('submit.hc4lab{{$orden->id}}', function(event){
        event.preventDefault();
        desmarcarSeleccionados();
        cargarBuscador();
      })
      .on('change.hc4lab{{$orden->id}}', '.hc4-lab-search-input', function(){
        desmarcarSeleccionados();
        cargarBuscador();
      })
      .on('click.hc4lab{{$orden->id}}', '.hc4-lab-editor__clear', function(){
        var $input = $(this).siblings('input');
        $input.val('');
        if ($input.is($buscadorExamen)) {
          clearTimeout(temporizadorAutocomplete);
          if (peticionAutocomplete) {
            peticionAutocomplete.abort();
            peticionAutocomplete = null;
          }
          cerrarSugerencias();
        } else if ($input.is($buscadorAgrupador)) {
          clearTimeout(temporizadorAgrupador);
          if (peticionAgrupador) {
            peticionAgrupador.abort();
            peticionAgrupador = null;
          }
          cerrarAgrupadores();
        }
        desmarcarSeleccionados();
        cargarBuscador();
      });

    $perfil.off('change.hc4lab{{$orden->id}}').on('change.hc4lab{{$orden->id}}', function(){
      if (!confirm('Al cambiar el perfil, perderá los exámenes previamente seleccionados')) {
        return;
      }

      $.ajax({
        type: 'post',
        url: "{{route('hc4_orden_lab.cambia_perfil',['orden' => $orden->id])}}",
        headers: {'X-CSRF-TOKEN': token()},
        data: $form.serialize(),
        success: function(data){
          $listado.empty().html(data);
          inicializarExamenes();
        }
      });
    });

    inicializarExamenes();
  })();
</script>
