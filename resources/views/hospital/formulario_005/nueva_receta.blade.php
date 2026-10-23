<div class="card">
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
                          <div class="row">
                            <div class=" col-md-9 col-sm-9 col-12">
                              <select name="generico" id="generico" class="form-control select2" >
                                <option value="">{{trans('paso2.Seleccione')}}</option>
                              </select>
                            </div>
                            <div class="col-md-3">
                              <button type="button" class="btn btn-primary" style="background-color: #004AC1;" onclick="buscar_nombre_medicina()">
                                <span class="fa fa-plus"></span>{{trans('paso2.Agregar')}}
                              </button>
                              <br>
                            </div>
                            <div class="col-md-12" style="margin-top: 10px;">
                            </div>
                            <div class="col-md-12" id="tabla_detalle">
                              <table role="table" aria-busy="false" aria-colcount="4" class="table b-table">
                                <thead role="rowgroup" class="">
                                  <tr role="row" class="">
                                    <th width="20%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
                                    <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
                                    <th width="55%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                                    <th width="15%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                                  </tr>
                                </thead>
                                <tbody role="rowgroup">
                                  @foreach($detalles as $detalle)
                                    @php
                                    //dd($detalle->descargo);
                                      $desabilitar ="";
                                      if($detalle->descargo == 1){
                                        $desabilitar = "disabled";
                                      }
                                    @endphp
                                  <tr role="row" class="">
                                    <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">{{$detalle->nombre}}</span></td>
                                    <td aria-colindex="2" role="cell" class=""><input class="form-control" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')" ></td>
                                    <td aria-colindex="3" role="cell" class="">
                                      <textarea wrap="soft" class="mb-1 mb-xl-0 form-control" style="resize: none; overflow-y: scroll; height: 92px;" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')" >{{$detalle->dosis}}</textarea>
                                    </td>
                                    <td aria-colindex="4" role="cell" class="">
                                      @if($detalle->descargo == 0)
                                        <button id="edit{{$detalle->id}}" type="button" class="btn btn-warning btn-sm" onclick="">
                                          <i class="fa fa-edit"></i>
                                        </button>
                                        <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm" onclick="eliminar_medicina('{{$detalle->id}}');">
                                          <i class="fa fa-trash"></i>
                                        </button>
                                      @else
                                        <span class="text-success">{{trans('paso2.Entregado')}}</span>
                                      @endif
                                      
                                    </td>
                                  </tr>
                                  @endforeach
                                </tbody>
                              </table>
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

  function buscar_nombre_medicina(){
    var id_solicitud = $('#solicitud_id').val();

      $.ajax({
        type: 'post',
        url:"{{route('formulario005.f05_medicina_guardar',['id_rec' => $receta->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: { 
          'id_generico' : $("#generico").val(),
          'id_solicitud': id_solicitud,
        },

          success: function(data){

            console.log(data);
            $('#tabla_detalle').html(data)
            
            },
            error: function(data){
            }
      })
  }

  function eliminar_medicina(id_detalle){
    console.log(id_detalle);
    var id_solicitud = $('#solicitud_id').val();
    var id_receta = $('#id_receta').val();

    $.ajax({
        async: true,
        type: "GET",
        url: "{{url('hospital/formu_005/eliminar_medicina')}}/"+id_detalle,
        data: {
          'id_solicitud': id_solicitud,
          'id_receta': id_receta,
        },
        datatype: "html",
        success: function(datahtml){

            $("#tabla_detalle").html(datahtml);

        },
        error:  function(){
            alert('error al cargar');
        }
    });
  }

  function editar_medicina(id_detalle){

    $.ajax({
        type: 'post',
        url: "{{url('hospital/form005/editar_medicina')}}/"+id_detalle,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_medicina").serialize(),
        success: function(data){

        },
        error:  function(){
            alert('error al cargar');
        }
    });
  }

</script>
