<style type="text/css">
    
    .hc4-functional-selector-panel {
      margin: 12px 18px 0;
      padding: 16px;
      border: 1px solid rgba(255, 255, 255, .72);
      border-radius: 10px;
      background: rgba(255, 255, 255, .96);
      box-shadow: 0 5px 14px rgba(0, 67, 109, .12);
    }

    .hc4-functional-selector-form {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: end;
      gap: 16px;
    }

    .hc4-functional-selector-field label {
      display: block;
      margin: 0 0 7px;
      color: #30415a;
      font-size: 13px;
      font-weight: 600;
    }

    .hc4-functional-selector-panel .select2-container {
      width: 100% !important;
    }

    .hc4-functional-selector-panel .select2-selection--multiple {
      min-height: 42px;
      border: 1px solid #ccd9e2 !important;
      border-radius: 8px !important;
      background: #fff !important;
    }

    .hc4-functional-selector-submit {
      min-width: 200px;
      height: 42px;
      margin: 0;
      padding: 8px 18px;
      border: 1px solid rgba(255,255,255,.65);
      border-radius: 8px;
      background: linear-gradient(118deg, #075c86 0%, #089bbb 100%);
      color: #fff;
      font-size: 14px;
      white-space: nowrap;
    }

    #functional-selector-message {
      display: block;
      margin-top: 8px;
      color: #c0392b;
      font-size: 13px;
    }

    @media (max-width: 760px) {
      .hc4-functional-selector-form {
        grid-template-columns: 1fr;
      }

      .hc4-functional-selector-submit {
        width: 100%;
      }
    }
</style>

<div class="hc4-functional-selector-panel">
        <form id="functional-procedure-form" class="hc4-functional-selector-form">
            {{ csrf_field() }}
            <input type="hidden" name="paciente" value="{{$paciente->id}}">
            <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
            <div id="cambio3" class="hc4-functional-selector-field {{ $errors->has('procedimiento') ? ' has-error' : '' }}">
                  <label for="id_procedimiento">Procedimientos activos</label>
                      <select id="id_procedimiento" class="form-control input-sm select2_proc_0" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione"
                      style="width: 100%;" autocomplete="off">
                          @foreach($px as $procedimiento)
                              <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                          @endforeach
                      </select>
            </div>
            <button type="button" class="btn hc4-functional-selector-submit" id="boton" onclick="guardar_procedimiento_fun();">
              <i class="fa fa-plus" aria-hidden="true"></i> Crear procedimiento
            </button>
        </form>
        <span id="functional-selector-message"></span>
</div>

<script type="text/javascript">
    
    $('.select2_proc_0').select2({
        tags: false,  
    });
     
    $('.select2_proc_0').on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    }); 

    function guardar_procedimiento_fun(){
        var $boton = $('#boton');
        var $mensaje = $('#functional-selector-message');
        var seleccionados = $('#id_procedimiento').val() || [];

        if (!seleccionados.length) {
          $mensaje.text('Seleccione al menos un procedimiento.');
          return;
        }

        $mensaje.text('');
        $boton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creando...');
        $.ajax({
          type: 'post',
          url:"{{route('proc_fun.crear')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#functional-procedure-form").serialize(),
          success: function(data){
            //console.log(data);
            //alert("ok");
            if(data=='ok'){
              cargar_procedimientos("Procedimiento creado correctamente");  
            }else{
              $mensaje.text('No se pudo crear el procedimiento.');
              $boton.prop('disabled', false).html('<i class="fa fa-plus"></i> Crear procedimiento');
            }

          },
          error: function(data){
            $mensaje.text('No se pudo crear el procedimiento.');
            $boton.prop('disabled', false).html('<i class="fa fa-plus"></i> Crear procedimiento');
          }
        });
    }

</script>
