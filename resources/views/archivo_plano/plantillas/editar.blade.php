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
          <h3 class="box-title" style="margin-top: 7px">Actualizar Plantillas</h3>
        </div><br>
        <hr>

      </div>
    </div>
    <!-- /.box-header -->
    <div class="content">
      <form method="post" id="dynamic_form">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6">
            <label>Código</label><br>
            <input name='contador_items' id='contador_items' type='hidden' value="{{$itemc}}">
            <input type="hidden" name="chivo" value="{{ $cabecera->id }}">
            <input type="text" name="txtcodigo" value="{{ $cabecera->codigo }}" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label>Descripción</label><br>
            <input type="text" name="txtdescripcion" value="{{ $cabecera->descripcion }}" placeholder="Descripción" class="form-control">
          </div>
          <div class="col-md-6"><br>
            <label>Descripción Completa</label><br>
            <input type="text" name="txtdescripcioncompleta" value="{{ $cabecera->desc_comp }}" class="form-control">
          </div>
          <div class="col-md-6"><br>
            <label>Estado</label><br>
            <select name="txtestado" class="form-control">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <br>
          <div class="col-md-12"><br>
            <h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>

            <!-- bloque 5 -->

            <br />
            <div class="table-responsive">

              <span id="result"></span>
              <table class="table table-bordered table-striped" id="user_table">
                <thead>
                  <tr>
                    <th width="30%">Item</th>
                    <th width="15%">Cantidad</th>
                    <th width="15%">Honorario</th>
                    <!-- <th width="15%">Orden</th> -->
                    <th width="15%">Separado</th>
                    <th width="15%">Porcentaje Modificador</th>

                    <th width="10%">Action
                      <button id="agrandar" type="button" class="btn btn-success btn-gray">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                      </button>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <div style="display:none">
                    {{ $cont=0 }}
                  </div>
                  @foreach($items as $value)
                  <tr>
                    <td>
                      <input type="text"
                        value="{{ $value->id }}|Codigo: {{ $value->codigo }}| {{ $value->descripcion }}"
                        name="codigo[]"
                        id="codigo{{ $cont }}"
                        class="codigo_elemento form-control" />
                    </td>
                    <td>
                      <input type="text"
                        name="cantidad[]"
                        value="{{ $value->cantidad }}"
                        id="cantidad{{ $cont }}"
                        class="form-control" />
                    </td>
                    <td>
                      <select name="honorario[]" class="form-control">
                        <option @if($value->honorario=='N') selected @endif value="N">Ninguno</option>
                        <option @if($value->honorario=='100') selected @endif value="100">100%</option>
                        <option @if($value->honorario=='50') selected @endif value="50">50%</option>
                      </select>
                    </td>
                    <td>
                      <select name="separado[]" class="form-control">
                        <option @if($value->separado=='No') selected @endif value="No">No</option>
                        <option @if($value->separado=='Si') selected @endif value="Si">Si</option>
                      </select>
                    </td>
                    <td>
                      <input type="text"
                        name="porcentaje_modificador[]"
                        value="{{ $value->porcentaje_modificador }}"
                        id="porcentaje_modificador{{ $cont }}"
                        class="form-control" />
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger remove">Eliminar</button>
                    </td>
                  </tr>
                  <div style="display:none">
                    {{ $cont++ }}
                  </div>
                  @endforeach

                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4" align="right">&nbsp;</td>
                    <td>
                      <button name="save" id="save" type="button" onclick="guardar()" class="btn btn-primary">Guardar</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <br>
          <div class="col-md-6"><br></div>
          <div id="proced_list"></div>
        </div>
      </form>
    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script>
  // Autocomplete para cualquier input con clase .codigo_elemento (existente o nuevo)
  $(document).on("focus", ".codigo_elemento", function() {
    var $input = $(this);

    // evitar inicializar dos veces
    if ($input.data("ui-autocomplete")) {
      return;
    }

    $input.autocomplete({
      source: function(request, response) {
        $.ajax({
          url: "{{ route('plantillas.search') }}",
          type: "POST",
          dataType: "json",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          data: {
            term: request.term
          },
          success: function(data) {
            console.log("DATA AUTOCOMPLETE:", data);
            response(data); // data debe venir como [{label:'...', value:'...'}, ...]
          },
          error: function(xhr, status, error) {
            console.error("ERROR AUTOCOMPLETE:", status, error);
          }
        });
      },
      minLength: 2
    });
  });

  $(document).ready(function() {
    // contador inicial desde backend
    var count = parseInt($('#contador_items').val() || '0', 10);
    // botón + del encabezado
    $('#agrandar').on('click', function(e) {
      e.preventDefault();
      crearFila();
    });

    // eliminar fila
    $(document).on('click', '.remove', function() {
      $(this).closest("tr").remove();
    });

    $('.select2').select2();
  });

  function regresar() {
    location.href = "{{route('applantilla.index')}}"; //window.history.back();
  }

  function crearFila() {
    var count = $('#contador_items').val();
    html = '<tr>';
    html += '<td><input type="text" id=codigo' + count + ' name="codigo[]" class="codigo_elemento form-control"  ></td>';
    html += '<td><input type="text" name="cantidad[]"  id="cantidad' + count + '" class="form-control" /></td>';
    html += '<td><select name="honorario[]" class="form-control"><option value="N">Ninguno</option><option value="100">100%</option><option value="50">50%</option></select></td>';
    // html += '<td><input type="number" name="orden[]" class="form-control" /></td>';
    html += '<td><select name="separado[]" class="form-control"><option value="No">No</option><option value="Si">Si</option></select></td>';
    html += '<td><input type="text" name="porcentaje_modificador[]"  id="porcentaje_modificador' + count + '" class="form-control" /></td>';
    html += '<td><button type="button" name="remove" id="remove" class="btn btn-danger remove">Eliminar</button></td></tr>';
    $('tbody').append(html);
    count++;
    $('#contador_items').val(count);
  }

  function guardar() {
    event.preventDefault();
    $('#save').attr('disabled', true);
    var tieneItems = false;

    $("input[name='codigo[]']").each(function() {
      if ($(this).val().trim() !== '') {
        tieneItems = true;
        return false; // romper el each
      }
    });

    if (!tieneItems) {
      $('#save').removeAttr('disabled');
      Swal.fire("Atención", "Debe ingresar al menos un item en la plantilla.", "warning");
      return; // NO envía el AJAX
    }
    $.ajax({
      url: '{{ route("plantillas.update") }}',
      method: 'post',
      data: $('#dynamic_form').serialize(),
      dataType: 'json',

      success: function(data) {
        if (data.status == 'error') {
          console.log(data.msj);
          var error_html = '';
          for (var count = 0; count < data.msj.length; count++) {
            error_html += data.msj[count] + '<br>';
          }
          Swal.fire("Error!", error_html, "error");
          $('#save').removeAttr('disabled');
        } else {
          Swal.fire("Correcto!", data.msj, "success");
          setTimeout(function() {
            location.reload(); // Recargar la página
          }, 1500);
        }
      },
      error: function(xhr) {
        $('#save').removeAttr('disabled');
        console.log(xhr);
        Swal.fire("Error!", "Ocurrió un error en el servidor", "error");
      }
    });
  }

  $(document).ready(function() {
    $('.select2').select2();
  });
</script>

@endsection