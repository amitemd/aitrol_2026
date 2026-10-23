@php
  date_default_timezone_set('America/Guayaquil');
  $fecha = date("Y-m-d");
  $id_doctor = Auth::user()->id;

@endphp

<div class="card">
  <div class="card-header bg bg-primary">
    <div class="col-md-12">
      <div class="row">
        <div class="d-flex align-items-center col-md-12">
          <span class="sradio">13</span>
          <h4 class="card-title ml-25 colorbasic">
            {{trans('paso2.PlandeTratamiento')}}
          </h4>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body">

    <div class="col-12">
      <div class="row">
        <div class="col-12">
          <form id="form_paso13">
            {{ csrf_field() }}
            @php
            $form008 = $solicitud->form008->first();
            @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <input type="hidden" name="id_seguro" id="id_seguro" value="{{$solicitud->id_seguro_publico}}">
            <input type="hidden" name="contador" id="contador" value="1">
            <input type="hidden" name="doctor" id="doctor" value="{{$id_doctor}}">
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
                        <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{('paso2.Cantidad')}}</div></th>
                        <th width="55%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                        <th width="15%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                      </tr>
                    </thead>
                    <tbody role="rowgroup">
                      @if(!is_null($detalles))
                      @foreach($detalles as $detalle)
                      <tr role="row" class="">
                        <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">{{$detalle->nombre}}</span></td>
                        <td aria-colindex="2" role="cell" class=""><input class="form-control" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="editar_medicina('{{$detalle->id}}')"></td>
                        <td aria-colindex="3" role="cell" class="">
                          <textarea wrap="soft" class="mb-1 mb-xl-0 form-control" style="resize: none; overflow-y: scroll; height: 92px;" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" onchange="editar_medicina('{{$detalle->id}}')">{{$detalle->dosis}}</textarea>
                        </td>
                        <td aria-colindex="4" role="cell" class="">
                          <button id="edit{{$detalle->id}}" type="button" class="btn btn-warning btn-sm" onclick="">
                            <i class="fa fa-edit"></i>
                          </button>
                          <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm" onclick="eliminar_medicina('{{$detalle->id}}');">
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                      @endforeach
                      @endif
                      
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
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  cargar_doctor();

  function cargar_doctor(){
    var doctor = $('#doctor').val();
    
    $.ajax({
      type: 'post',
      url:"{{route('13vopaso.actualizar_doctor',['id' => $receta->id ])}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: { 'id_doctor' : doctor },

      success: function(data){ 
        
      },

      error: function(data){
      
      }

    }) 

  }

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

      $.ajax({
        type: 'post',
        url:"{{route('13vopaso.guardar_medicina',['id' => $solicitud->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: { 'id_generico' : $("#generico").val() },

          success: function(data){

            console.log(data);
            $('#tabla_detalle').html(data)
            
            },
            error: function(data){
            }
      })
  }

  function addNewBlade(i) {
    var data = $("#generico").val();
    var iva = $('option:selected', '#generico').html();
    console.log(iva);
    var contador = $("#contador").val();
    var htmlTable = $('#tableIndicate').html();
    var midiv_pago = '<div class="card shadow-none bg-transparent border-primary"> <div class="card-header"> <h4 class="card-title">' + iva + '</h4> </div> <div class="card-body"> <table class="table b-table table-hover b-table-fixed">  <thead><tr><th>#</th><th>INDICACIONES</th><th>MEDICAMENTO</th><th>POSOLOGIA</th><th><button class="btn btn-primary" onclick="add(this)" type="button"> <i class="fa fa-plus"> </i> </button></th></tr></thead><tbody class="bodys" ><td>' + contador + '</td><td> <input class="genericoAdded" name="id_generico[]" type="hidden" value="' + data + '">  <input class="form-control" name="indicaciones[]"></td> <td><input class="form-control" type="text" name="medicamentos[]" value="' + iva + '" placeholder="Ingrese medicamentos" readonly></td><td><input class="form-control" type="text" name="posologia[]" placeholder="Ingrese posologia"></td> <td>  <button class="btn btn-danger" onclick="erase(this)" type="button" > <i class="fa fa-remove">   </i> </button> </td></tbody> </table></div> </div>';
    contador++;
    $("#contador").val(contador);
    $('#addPlace').append(midiv_pago);
  }

  function erase(e) {
    var contador = $("#contador").val();
    contador--;
    if (contador < 0) {
      contador = 0;
    }
    $('#contador').val(contador);
    $(e).parent().parent().remove();
  }

  function add(e) {
    var cont = 1;
    var eu = $(e).parent().parent().parent().parent().parent().parent().find('.card-title').html();
    var midiv_pago = document.createElement("tr");
    var innerHTML = '<tr><td>' + cont + '</td> <td><input class="form-control" name="eu[]" value="1"></td> <td>' + eu + '</td> <td><input class="form-control" name="eu[]" value="1"></td> <td><button class="btn btn-danger" onclick="erase(this)" type="button" > <i class="fa fa-remove"> </i> </button></td> </tr>';
    console.log($(e).parent().parent().parent().parent().parent().parent().html());
    $(e).parent().parent().parent().parent().parent().find('tbody').append(innerHTML);
  }

  function supera() {
    /* Swal.fire({
       title: 'Atencion!',
       text: 'Estamos trabajando en estos momentos',
       imageUrl: 'https://i.ibb.co/smcnM0c/mejorando.jpg',
       imageWidth: 400,
       imageHeight: 300,
       imageAlt: 'Custom image',
     }); */
    $.ajax({
      type: "post",
      url: "{{route('hospital.guardar_terceavo')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#form_paso3").serialize(),

      success: function(datahtml, data) {
        $("#content").html(datahtml);
        return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
      },
      error: function() {
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
        data: $("#form_paso13").serialize(),
        success: function(data){

        },
        error:  function(){
            alert('error al cargar');
        }
    });
  }

  function eliminar_medicina(id_detalle){
    //console.log(id_detalle);
    var id_solicitud = '{{$solicitud->id}}';
    var id_receta = '{{$receta->id}}';

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



</script>