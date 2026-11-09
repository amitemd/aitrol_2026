<style>
  .recipe-editor {
    border: 0;
    box-shadow: none;
  }

  .recipe-editor__picker {
    align-items: flex-end;
  }

  .recipe-editor__add {
    min-height: 38px;
    border-radius: 8px;
    font-weight: 700;
  }

  .recipe-autosave-field.is-saving {
    border-color: #e3b64f;
    box-shadow: 0 0 0 3px rgba(227, 182, 79, .12);
  }

  .recipe-autosave-field.is-saved {
    border-color: #33a96b;
    box-shadow: 0 0 0 3px rgba(51, 169, 107, .1);
  }

  .recipe-autosave-field.is-save-error {
    border-color: #e45b64;
    box-shadow: 0 0 0 3px rgba(228, 91, 100, .1);
  }

  .recipe-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 72px;
    border: 1px dashed #bfd0db;
    border-radius: 10px;
    background: #fbfdfe;
    color: #728591;
  }

  .recipe-delete-button {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 9px;
  }

  .recipe-detail-container {
    margin-top: 16px;
  }

  .recipe-table-wrap {
    overflow: hidden;
    border: 1px solid #dce8ef;
    border-radius: 12px;
    background: #fff;
  }

  .recipe-table {
    width: 100%;
    min-width: 720px;
    margin-bottom: 0 !important;
    table-layout: fixed;
  }

  .recipe-table__autosave {
    display: flex;
    align-items: center;
    gap: 4px;
    margin: 0 0 6px;
    padding: 0;
    color: #718593;
    font-size: 11px;
    text-align: left;
  }

  .recipe-table__autosave i {
    margin-right: 0;
  }

  .recipe-table thead,
  .recipe-table thead tr,
  .recipe-table thead th {
    background-color: #c9f3f6 !important;
    background-image: none !important;
  }

  .recipe-table thead th {
    padding: 13px 15px;
    border: 0;
    border-bottom: 1px solid #dce8ef;
    color: #174866;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .recipe-table tbody td {
    padding: 14px 15px;
    border-top: 0;
    border-bottom: 1px solid #e5edf2;
    vertical-align: middle;
  }

  .recipe-table tbody,
  .recipe-table tbody tr,
  .recipe-table tbody td,
  .recipe-table tbody tr:hover,
  .recipe-table tbody tr:hover td,
  .recipe-table tbody tr.selected,
  .recipe-table tbody tr.selected td,
  .recipe-table tbody tr.table-active,
  .recipe-table tbody tr.table-active td {
    background-color: #fff !important;
    background-image: none !important;
  }

  .recipe-table tbody tr:last-child td {
    border-bottom: 0;
  }

  .recipe-medication {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #123d59;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.35;
  }

  .recipe-medication__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: #e8f5fa;
    color: #087fa7;
  }

  .recipe-table .recipe-quantity {
    min-height: 40px;
    text-align: center;
    font-weight: 600;
  }

  .recipe-table .recipe-posology {
    min-height: 72px;
    resize: vertical;
  }

  .recipe-table .form-control {
    border-color: #cddde7;
    border-radius: 8px;
    color: #294f66;
  }

  .recipe-table .form-control:focus {
    border-color: #159dbb;
    box-shadow: 0 0 0 3px rgba(21, 157, 187, .12);
  }

  .recipe-table__action {
    text-align: center;
  }

  @media (max-width: 767.98px) {
    .recipe-table-wrap {
      overflow-x: auto;
    }
  }
</style>

<div class="card recipe-editor">
  <div class="card-body" style="padding: 0;">
        <div class="row" style="padding-top: 10px;">
            <div class="card-body">
                <div class="col-12">
                  <div class="row">
                    <div class="col-12">
                      <form id="form_medicina">
                        {{ csrf_field() }}
                        @php

                        $form008 = $solicitud->form008->first();

                        @endphp
                        <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">
                        <input type="hidden" name="id_seguro" id="id_seguro" value="{{$solicitud->id_seguro_publico}}">
                        <input type="hidden" name="id_receta" id="id_receta" value="{{$receta->id}}">
                        <input type="hidden" name="contador" id="contador" value="1">
                        <div class="form-group">
                          <label for="inputid" class="control-label">{{trans('paso2.Medicina')}}</label>
                          <div class="row recipe-editor__picker">
                            <div class=" col-md-9 col-sm-9 col-12">
                              <select name="generico" id="generico" class="form-control select2" >
                                <option value="">{{trans('paso2.Seleccione')}}</option>
                              </select>
                            </div>
                            <div class="col-md-3">
                              <button type="button" class="btn btn-primary btn-block recipe-editor__add" onclick="buscar_nombre_medicina(this)">
                                <span class="fa fa-plus"></span> {{trans('paso2.Agregar')}}
                              </button>
                            </div>
                            <div class="col-12 recipe-detail-container" id="tabla_detalle">
                              @include('hospital.formulario_005.recetajs', ['detalles' => $detalles])
                            </div>
                           
                            
                          </div>
                        </div>

                      </form>

                    </div>
                  </div>
                </div>
            </div>
        </div>

        
    </div>
</div>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

  $('.select2').select2({
    ajax: {
      url: '{{route("hospitalizacion.genericos")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        //console.log(data);

        return {
          results: data
        };
      }

    }
  });

  function buscar_nombre_medicina(button){
    var $button = $(button);
    var $editor = $button.closest('.recipe-editor');
    var id_solicitud = $editor.find('[name="solicitud_id"]').val();
    var idGenerico = $editor.find('[name="generico"]').val();
    var originalHtml = $button.html();
    if (!idGenerico) {
      Swal.fire({icon: 'warning', title: 'Seleccione una medicina', text: 'Busque y seleccione una medicina antes de agregar.'});
      return;
    }

    $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Agregando…');
      $.ajax({
        type: 'post',
        url:"{{route('formulario005.f05_medicina_guardar',['id_rec' => $receta->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: { 
          'id_generico' : idGenerico,
          'id_solicitud': id_solicitud,
        },

          success: function(data){

            $editor.find('#tabla_detalle').html(data);
            $editor.find('[name="generico"]').val(null).trigger('change');
            },
            error: function(xhr){
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible agregar la medicina.';
              Swal.fire({icon: 'error', title: 'No se pudo agregar', text: message});
            },
            complete: function(){
              $button.prop('disabled', false).html(originalHtml);
            }
      })
  }

  function eliminar_medicina(id_detalle, button){
    var $editor = $(button).closest('.recipe-editor');
    var id_solicitud = $editor.find('[name="solicitud_id"]').val();
    var id_receta = $editor.find('[name="id_receta"]').val();

    $.ajax({
        async: true,
        type: "DELETE",
        url: "{{url('hospital/formu_005/eliminar_medicina')}}/"+id_detalle,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {
          'id_solicitud': id_solicitud,
          'id_receta': id_receta,
        },
        datatype: "html",
        success: function(datahtml){

            $editor.find('#tabla_detalle').html(datahtml);

        },
        error:  function(xhr){
            var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible eliminar la medicina.';
            Swal.fire({icon: 'error', title: 'No se pudo eliminar', text: message});
        }
    });
  }

  function editar_medicina(id_detalle, field){
    var $field = $(field);
    var $form = $field.closest('form');
    var $fields = $form.find('#cantidad' + id_detalle + ', #dosis' + id_detalle);

    $fields.removeClass('is-saved is-save-error');
    $field.addClass('is-saving');

    $.ajax({
        type: 'post',
        url: "{{url('hospital/form005/editar_medicina')}}/"+id_detalle,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $form.serialize(),
        success: function(data){
          $field.removeClass('is-saving').addClass('is-saved');
          setTimeout(function () { $field.removeClass('is-saved'); }, 1800);
        },
        error:  function(xhr){
            var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible guardar los cambios.';
            $field.removeClass('is-saving').addClass('is-save-error');
            Swal.fire({icon: 'error', title: 'No se pudo guardar', text: message});
        }
    });
  }

</script>
