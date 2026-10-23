@extends('archivo_plano.plantillas.base')
@section('action-content')

<style type="text/css">
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 700px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Crear Plantillas</h3>
        </div>
        <div class="col-sm-6 text-right">
          <button onclick="regresar();" class="btn btn-primary btn-sm">
            {{trans('winsumos.regresar')}}
          </button>
        </div>
        <hr>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="content">
      <form method="post" id="dynamic_form" novalidate>
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6">
            <label>Código</label><br>
            <input type="text" name="txtcodigo" placeholder="Código" value="@if(isset($codigo)) {{$codigo}} @endif" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Descripción</label><br>
            <input type="text" name="txtdescripcion" placeholder="Descripción" class="form-control" required>
          </div>
          <div class="col-md-6"><br>
            <label>Descripción Completa</label><br>
            <input type="text" name="txtdescripcioncompleta" placeholder="Descripción Completa" class="form-control" required>
          </div>
          <div class="col-md-6"><br>
            <label>Estado</label><br>
            <select name="txtestado" class="form-control" required>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <!--
          <div class="col-md-6"><br>
            <label>Procedimiento</label><br>
            <select name="id_procedimiento" class="form-control select2">
              <option>seleccione..</option>
              @foreach ($procedimiento as $pro)
              <option value="{{$pro->id}}">{{$pro->nombre}}</option>
              @endforeach
            </select>
          </div>
          <br>
          -->
          <div class="col-md-12"><br>
            <h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>

            <!-- bloque 5 -->

            <br />
            <div class="table-responsive">

              <span id="result"></span>
              <table class="table table-bordered table-striped" id="user_table">
                <thead>
                  <tr>
                    <th width="10%">Item</th>
                    <th width="15%">Cantidad</th>
                    <th width="15%">Honorario</th>
                    <!-- <th width="15%">Orden</th> -->
                    <th width="15%">Separado</th>
                    <th width="15%">Porcentaje Modificador</th>
                    <th width="10%">Action</th>

                  </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" align="right">&nbsp;</td>
                    <td>
                      <button name="save" id="save" type="submit" class="btn btn-primary">Guardar</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <br>

          <div class="col-md-6"><br></div>
          <div id="proced_list"></div>
          <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
        </div>

      </form>

    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  
   

  $(document).ready(function() {

    initAutocomplete();
    var nextItemId = 0;

    dynamic_field(++nextItemId);

    $(document).on('click', '#add', function() {
      dynamic_field(++nextItemId);

    });

    $(document).on('click', '.remove', function() {
      $(this).closest("tr").remove();
    });

    $(document).on('input', '.codigo_elemento', function() {
      $(this).removeData('autocomplete-value');
    });

    $('#dynamic_form').on('submit', guardar);
  });

  function regresar() {
    location.href = "{{route('applantilla.index')}}"; //window.history.back();
  }


  function initAutocomplete() {
    $(".codigo_elemento").not('.ui-autocomplete-input').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('plantillas.search')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: { term: request.term },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
          var $input = $(this);
          setTimeout(function() {
            $input.data('autocomplete-value', $.trim(ui.item.value));
          }, 0);
        },
    });
}

  function dynamic_field(number) {
    var html = '<tr>';
    html += '<td><input type="text" id="codigo' + number + '" name="codigo[]" class="codigo_elemento form-control" required></td>';
    html += '<td><input type="number" name="cantidad[]" id="cantidad' + number + '" class="form-control" min="0.000001" step="any" required></td>';
    html += '<td><select name="honorario[]" class="form-control"><option value="N">Ninguno</option><option value="100">100%</option><option value="50">50%</option></select></td>';
    // html += '<td><input type="number" name="orden[]" class="form-control" /></td>';
    html += '<td><select name="separado[]" class="form-control"><option value="No">No</option><option value="Si">Si</option></select></td>';
    html += '<td><input type="number" name="porcentaje_modificador[]" id="porcentaje_modificador' + number + '" class="form-control" min="0" max="100" step="any" value="0"></td>';
    if (number > 1) {
      html += '<td><button type="button" name="remove" id="remove" class="btn btn-danger remove">Eliminar</button></td></tr>';
      $('#user_table tbody').append(html);
    } else {
      html += '<td><button type="button" name="add" id="add" class="btn btn-success">Nuevo Item</button></td></tr>';
      $('#user_table tbody').html(html);
    }

    initAutocomplete();
  }

  function mostrarErroresValidacion(errores) {
    Swal.fire({
      title: 'Revise la información',
      html: errores.join('<br>'),
      icon: 'warning',
      confirmButtonText: 'Aceptar'
    });
  }

  function esItemSeleccionado($input) {
    var valor = $.trim($input.val() || '');
    var partes = valor.split('|');
    var valorSeleccionado = $.trim($input.data('autocomplete-value') || '');

    return valorSeleccionado !== '' &&
      valor === valorSeleccionado &&
      partes.length >= 3 &&
      $.trim(partes[0]) !== '' &&
      /^Codigo:\s*.+$/i.test($.trim(partes[1])) &&
      $.trim(partes[2]) !== '';
  }

  function validarFormulario() {
    var errores = [];
    var campos = [
      { selector: '[name="txtcodigo"]', mensaje: 'Ingrese el código de la plantilla.' },
      { selector: '[name="txtdescripcion"]', mensaje: 'Ingrese la descripción de la plantilla.' },
      { selector: '[name="txtdescripcioncompleta"]', mensaje: 'Ingrese la descripción completa de la plantilla.' },
      { selector: '[name="txtestado"]', mensaje: 'Seleccione el estado de la plantilla.' }
    ];

    $.each(campos, function(index, campo) {
      if ($.trim($(campo.selector).val() || '') === '') {
        errores.push(campo.mensaje);
      }
    });

    var filas = $('#user_table tbody tr');
    if (filas.length === 0) {
      errores.push('Debe ingresar al menos un ítem en la plantilla.');
    }

    filas.each(function(index) {
      var numeroItem = index + 1;
      var $fila = $(this);
      var $codigo = $fila.find('[name="codigo[]"]');
      var codigo = $.trim($codigo.val() || '');
      var cantidad = $.trim($fila.find('[name="cantidad[]"]').val() || '');
      var honorario = $fila.find('[name="honorario[]"]').val();
      var separado = $fila.find('[name="separado[]"]').val();
      var porcentaje = $.trim($fila.find('[name="porcentaje_modificador[]"]').val() || '');

      if (codigo === '') {
        errores.push('Ingrese el procedimiento del ítem ' + numeroItem + '.');
      } else if (!esItemSeleccionado($codigo)) {
        errores.push('Seleccione un procedimiento válido de la lista en el ítem ' + numeroItem + '.');
      }

      if (cantidad === '') {
        errores.push('Ingrese la cantidad del ítem ' + numeroItem + '.');
      } else if (!isFinite(cantidad) || Number(cantidad) <= 0) {
        errores.push('La cantidad del ítem ' + numeroItem + ' debe ser un número mayor que cero.');
      }

      if ($.inArray(honorario, ['N', '100', '50']) === -1) {
        errores.push('Seleccione un honorario válido en el ítem ' + numeroItem + '.');
      }

      if ($.inArray(separado, ['No', 'Si']) === -1) {
        errores.push('Seleccione una opción válida para separado en el ítem ' + numeroItem + '.');
      }

      if (porcentaje !== '' && (!isFinite(porcentaje) || Number(porcentaje) < 0 || Number(porcentaje) > 100)) {
        errores.push('El porcentaje modificador del ítem ' + numeroItem + ' debe estar entre 0 y 100.');
      }
    });

    if (errores.length > 0) {
      mostrarErroresValidacion(errores);
      return false;
    }

    return true;
  }

  function guardar(event) {
    event.preventDefault();

    if (!validarFormulario()) {
      return false;
    }

    $('#save').prop('disabled', true);
    $.ajax({
      url: '{{ route("plantillas.insert") }}',
      method: 'post',
      data: $('#dynamic_form').serialize(),
      dataType: 'json',

      success: function(data) {
        if (data.status == 'error') {
          console.log(data.msj);
          var mensajes = data.msj || 'No se pudo guardar la plantilla.';
          if (!$.isArray(mensajes)) {
            mensajes = [mensajes];
          }
          Swal.fire({
            title: 'Error',
            text: mensajes.join('\n'),
            type: 'error',
            confirmButtonText: 'Aceptar'
          });
          $('#save').prop('disabled', false);
        } else {
          dynamic_field(1);
          Swal.fire("Correcto!", data.msj, "success");
          setTimeout(function() {
            location.href = "{{route('applantilla.index')}}";
          }, 1500)
        }
      },
      error: function(xhr) {
        var response = xhr.responseJSON || {};
        var mensajes = response.msj || 'Ocurrió un error al guardar la plantilla.';
        if (!$.isArray(mensajes)) {
          mensajes = [mensajes];
        }
        Swal.fire({
          title: 'Error',
          text: mensajes.join('\n'),
          type: 'error',
          confirmButtonText: 'Aceptar'
        });
        $('#save').prop('disabled', false);
      }
    })
  }

  $(document).ready(function() {
    $('.select2').select2();
  });


  
</script>

@endsection
