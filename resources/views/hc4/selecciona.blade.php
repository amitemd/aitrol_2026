<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
@php
    $esProcedimientoImagen = (string) $tipo === '2';
    $tituloModulo = $esProcedimientoImagen ? __('hc4.imagenes-') : __('hc4.procedimientos');
    $etiquetaAgregar = $esProcedimientoImagen ? 'Agregar procedimiento de imagen' : 'Agregar procedimiento';
    $etiquetaCrear = $esProcedimientoImagen ? 'Crear procedimiento de imagen' : 'Crear Procedimiento';
@endphp
<style type="text/css">
    #hc4-procedure-create .hc4-procedure-create__body {
      padding: 18px 24px 20px;
      background: #fff;
    }

    #hc4-procedure-create .hc4-procedure-create__form {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: end;
      gap: 18px;
      margin: 0;
    }

    #hc4-procedure-create .hc4-procedure-create__field {
      min-width: 0;
      margin: 0;
    }

    #hc4-procedure-create .hc4-procedure-create__label {
      display: block;
      margin: 0 0 7px;
      color: #07547e;
      font-family: 'Helvetica general', Arial, sans-serif;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
    }

    #hc4-procedure-create .select2-container {
      display: block !important;
      width: 100% !important;
      min-width: 0 !important;
    }

    #hc4-procedure-create .select2-selection--multiple {
      width: 100% !important;
      min-height: 42px;
      background: #fff !important;
      border: 1px solid #c9c4df !important;
      border-radius: 8px !important;
      box-sizing: border-box;
    }

    #hc4-procedure-create .select2-selection__rendered {
      display: flex !important;
      flex-wrap: wrap;
      align-items: center;
      width: 100% !important;
      min-height: 40px;
      padding: 3px 8px !important;
      box-sizing: border-box;
    }

    #hc4-procedure-create .select2-search--inline {
      flex: 1 1 180px;
      min-width: 180px;
    }

    #hc4-procedure-create .select2-search--inline .select2-search__field {
      width: 100% !important;
      min-width: 180px !important;
      height: 30px;
      margin: 0 !important;
      box-sizing: border-box;
    }

    .select2-dropdown {
      z-index: 1060;
    }

    #hc4-procedure-create .boton-proce {
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

    #hc4-procedure-create #msn {
      display: none;
      margin-bottom: 8px;
      padding: 9px 12px;
      color: #c0392b !important;
      font-weight: 700;
      background: #fff2f0;
      border: 1px solid #f0b8b1;
      border-radius: 7px;
    }

    @media (max-width: 767px) {
      #hc4-procedure-create .hc4-procedure-create__form {
        grid-template-columns: 1fr;
      }

      #hc4-procedure-create .boton-proce {
        width: 100%;
      }
    }
</style>

<section id="hc4-procedure-create" class="box hc4-standard-module-container">
  @include('hc4.partials.module_header', [
      'moduleTitle' => $tituloModulo,
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente
  ])

  <div class="hc4-procedure-create__body">
    <span id="msn"></span>
    <form id="hc4-procedure-create-form" class="hc4-procedure-create__form">
      @csrf
      <input type="hidden" name="paciente" value="{{$paciente->id}}">
      <input id="tipo_procedimiento_buscador" type="hidden" name="tipo_procedimiento" value="{{$tipo}}">

      <div id="cambio3" class="hc4-procedure-create__field {{ $errors->has('procedimiento') ? 'has-error' : '' }}">
        <label for="id_procedimiento" class="hc4-procedure-create__label">{{ $etiquetaAgregar }}</label>
        <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off">
          @foreach($px as $procedimiento)
            <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
          @endforeach
        </select>
      </div>

      <button type="button" class="btn btn-primary boton-proce" id="boton" onclick="guardar_procedimiento();">{{ $etiquetaCrear }}</button>
    </form>
  </div>
</section>

<script type="text/javascript">
    
    var $selectorProcedimiento = $('#id_procedimiento');

    function normalizarBusquedaProcedimiento(texto) {
      return (texto || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
    }

    $selectorProcedimiento.select2({
      tags: false,
      width: '100%',
      closeOnSelect: false,
      dropdownParent: $('body'),
      matcher: function(params, data) {
        var termino = normalizarBusquedaProcedimiento(params.term);
        if (!termino) {
          return data;
        }

        return normalizarBusquedaProcedimiento(data.text).indexOf(termino) !== -1 ? data : null;
      }
    });

    $selectorProcedimiento.on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    }); 

    function guardar_procedimiento(){
      var $message = $('#msn');
      var $button = $('#boton');

      $message.hide().text('');
      $button.prop('disabled', true).text('Creando...');

        $.ajax({
          type: 'post',
          url:"{{route('hc4_procedimiento.crear')}}",
          headers:{
            'X-CSRF-TOKEN': $('#hc4-procedure-create-form input[name=_token]').val(),
            'Accept': 'application/json'
          },
          dataType: 'json',
          data: $("#hc4-procedure-create-form").serialize(),
          success: function(data){
            if(data && data.status === 'ok'){
              cargar_procedimientos(data.message);
            }else{
              $message.text((data && data.message) ? data.message : 'El servidor no confirmó la creación del procedimiento.').show();
            }
          },
          error: function(xhr){
            var mensaje = 'No se pudo crear el procedimiento.';
            if(xhr.responseJSON){
              if(xhr.responseJSON.errors){
                var campos = Object.keys(xhr.responseJSON.errors);
                if(campos.length && xhr.responseJSON.errors[campos[0]].length){
                  mensaje = xhr.responseJSON.errors[campos[0]][0];
                }
              }else if(xhr.responseJSON.diagnostico && xhr.responseJSON.diagnostico.message){
                mensaje = xhr.responseJSON.diagnostico.message;
              }else if(xhr.responseJSON.error){
                mensaje = xhr.responseJSON.error;
              }else if(xhr.responseJSON.message){
                mensaje = xhr.responseJSON.message;
              }
            }
            $message.text(mensaje).show();
          },
          complete: function(){
            $button.prop('disabled', false).text({!! json_encode($etiquetaCrear) !!});
          }
        });
    }

</script>
