<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
  #hc4-surgical-procedure-create .hc4-surgical-procedure-create__body {
    padding: 18px 24px 20px;
    background: #fff;
  }

  #hc4-surgical-procedure-create .hc4-surgical-procedure-create__form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 18px;
    margin: 0;
  }

  #hc4-surgical-procedure-create .hc4-surgical-procedure-create__field {
    min-width: 0;
    margin: 0;
  }

  #hc4-surgical-procedure-create .hc4-surgical-procedure-create__label {
    display: block;
    margin: 0 0 7px;
    color: #07547e;
    font-family: 'Helvetica general', Arial, sans-serif;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
  }

  #hc4-surgical-procedure-create .select2-container {
    display: block !important;
    width: 100% !important;
    min-width: 0 !important;
  }

  #hc4-surgical-procedure-create .select2-selection--multiple {
    width: 100% !important;
    min-height: 42px;
    background: #fff !important;
    border: 1px solid #c9c4df !important;
    border-radius: 8px !important;
    box-sizing: border-box;
  }

  #hc4-surgical-procedure-create .select2-selection__rendered {
    display: flex !important;
    flex-wrap: wrap;
    align-items: center;
    width: 100% !important;
    min-height: 40px;
    padding: 3px 8px !important;
    box-sizing: border-box;
  }

  #hc4-surgical-procedure-create .select2-search--inline {
    flex: 1 1 180px;
    min-width: 180px;
  }

  #hc4-surgical-procedure-create .select2-search--inline .select2-search__field {
    width: 100% !important;
    min-width: 180px !important;
    height: 30px;
    margin: 0 !important;
    box-sizing: border-box;
  }

  .select2-dropdown {
    z-index: 1060;
  }

  #hc4-surgical-procedure-create .boton-proce {
    min-width: 200px;
    height: 42px;
    margin: 0;
    padding: 0 18px;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    background: #087eae;
    border: 1px solid #087eae;
    border-radius: 8px;
  }

  #hc4-surgical-procedure-create #msn {
    display: none;
    margin-bottom: 8px;
    padding: 9px 12px;
    color: #c0392b !important;
    font-weight: 700;
    background: #fff2f0;
    border: 1px solid #f0b8b1;
    border-radius: 7px;
  }

  #hc4-surgical-procedure-create #msn:not(:empty) {
    display: block;
  }

  @media (max-width: 767px) {
    #hc4-surgical-procedure-create .hc4-surgical-procedure-create__form {
      grid-template-columns: 1fr;
    }

    #hc4-surgical-procedure-create .boton-proce {
      width: 100%;
    }
  }
</style>

<section id="hc4-surgical-procedure-create" class="box hc4-standard-module-container">
  @include('hc4.partials.module_header', [
      'moduleTitle' => __('hc4.procedimientos'),
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente
  ])

  <div class="hc4-surgical-procedure-create__body">
    <span id="msn"></span>
    <form id="form" class="hc4-surgical-procedure-create__form">
      @csrf
      <input type="hidden" name="paciente" value="{{$paciente->id}}">
      <input id="tipo_procedimiento_buscador" type="hidden" name="tipo_procedimiento" value="{{$tipo}}">

      <div id="cambio3" class="hc4-surgical-procedure-create__field {{ $errors->has('procedimiento') ? 'has-error' : '' }}">
        <label for="id_procedimiento" class="hc4-surgical-procedure-create__label">Agregar procedimiento</label>
        <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off">
          @foreach($px as $procedimiento)
            <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
          @endforeach
        </select>
      </div>

      <button type="button" class="btn btn-primary boton-proce" id="boton" onclick="guardar_procedimiento();">@lang('hc4.crearProcedimiento')</button>
    </form>
  </div>
</section>

<script type="text/javascript">
  var $selectorProcedimientoQuirurgico = $('#hc4-surgical-procedure-create #id_procedimiento');

  function normalizarBusquedaProcedimientoQuirurgico(texto) {
    return (texto || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase();
  }

  $selectorProcedimientoQuirurgico.select2({
    tags: false,
    width: '100%',
    closeOnSelect: false,
    dropdownParent: $('body'),
    language: {
      noResults: function() {
        return 'No se encontraron coincidencias';
      }
    },
    matcher: function(params, data) {
      var termino = normalizarBusquedaProcedimientoQuirurgico(params.term);
      if (!termino) {
        return data;
      }

      return normalizarBusquedaProcedimientoQuirurgico(data.text).indexOf(termino) !== -1 ? data : null;
    }
  });

  $selectorProcedimientoQuirurgico.on("select2:select", function(evt) {

    var element = evt.params.data.element;
    //console.log(element);
    var $element = $(element);

    $element.detach();
    $(this).append($element);
    $(this).trigger("change");
  });

  function guardar_procedimiento() {
    var tipo = $('#tipo_procedimiento_buscador').val();
    var procedimientosSeleccionados = $selectorProcedimientoQuirurgico.val() || [];

    if (!procedimientosSeleccionados.length) {
      $('#msn').text('Seleccione al menos un procedimiento');
      return;
    }

    $('#msn').empty();
    //alert('entra');
    $.ajax({
      type: 'post',
      url: "{{route('hc4_procedimiento.crear_quirurgicos')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },

      datatype: 'json',
      data: $("#form").serialize(),
      success: function(data) {
        console.log(tipo);
        //alert("ok");
        if (data == 'ok') {
          if (tipo == 5) {
            cargar_procedimiento_quirurgicos("Procedimiento creado correctamente");

          }
        } else {
          //alert("Ingrese el Procedimiento");
          $('#msn').text("Ingrese el Procedimiento");
        }

      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  
</script>
