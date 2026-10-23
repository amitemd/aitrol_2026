@extends('insumos.plantillas.base')
@section('action-content')

<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 300px;
        width: 25%;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000px;
        font-size: 11px;
        float: left;
        display: none;
        min-width: 160px;
        _width: 140px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
    <section class="content">
      <div class="box">
  		<div class="box-header">
	    	<div class="row">
		        <div class="col-sm-6">
		          <h3 class="box-title" style="margin-top: 7px">{{trans('winsumos.crear_plantillas')}}</h3>
		        </div><br><hr>
	    	</div>
  		</div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" id="form_plantilla">
            {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_plantilla" class="form-control" value="{{$id}}">
              <div class="col-md-3">
                <label>{{trans('winsumos.codigo')}}</label><br>
                <input type="text" name="codigo" placeholder="{{trans('winsumos.codigo')}}"  class="form-control" value="{{$plantilla->codigo}}">
              </div>
              <div class="col-md-4">
                <label>{{trans('winsumos.nombre')}}</label><br>
                <input type="text" name="nombre" placeholder="{{trans('winsumos.nombre')}}"  class="form-control" value="{{$plantilla->nombre}}" style="text-transform: uppercase">
              </div>
              <div class="col-md-2">
                <label>{{trans('winsumos.estado')}}</label><br>
                <select name="estado" class="form-control">
                  <option @if($plantilla->estado == 1) selected @endif value="1">{{trans('winsumos.activo')}}</option>
                  <option @if($plantilla->estado == 0) selected @endif value="0">{{trans('winsumos.inactivo')}}</option>
                </select>
              </div>
				<br>
				<div class="col-md-12"><br>
				<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i>{{trans('winsumos.items_plantilla')}}</h4><br>
     			<br/>

                           
                            <div class="col-md-12 table-responsive">
                                <table id="items" class="table  table-bordered table-hover dataTable" role="grid"
                                    aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="50%" tabindex="0">{{ trans('winsumos.items') }}</th>
                                            <th width="20%" tabindex="0">{{ trans('winsumos.Tipo') }}</th>
                                            <th width="10%" tabindex="0">{{ trans('winsumos.cantidad') }}</th>
                                            <th width="10%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_items">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        @php $contador = 0;  @endphp
                            @foreach($plantillas_items as $value) 

                                            <tr id="fila{{ $contador }}">
                                                <td>
                                                    <input type="hidden" name="id_item[]" id="id_item{{$contador}}" class="id_item"
                                                        value="{{ $value->id_producto }}" />
                                                    <input required name="item_id[]" 
                                                        onfocus="agregar_item({{$contador}})" class="buscador_items form-control"
                                                        style="height:25px;width:90%;"
                                                        value="{{ $value->codigo }} | {{ $value->nom_prod }}"
                                                        name="producto[]" />
                                                </td>
                                                <td>
                                                    <select id="tipo{{ $contador }}" class="form-control"
                                                        name="tipo[]"
                                                        style="height:25px;width:75%;text-transform:uppercase;">
                                                        <option @if ($value->tipo == 1) selected @endif
                                                            value="1">{{ trans('winsumos.enfermeria') }}</option>
                                                        <option @if ($value->tipo == 2) selected @endif
                                                            value="2">{{ trans('winsumos.anestesiologo') }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" min="1"  name="item_cant[]"
                                                        id="item_cant{{ $contador }}" class="form-control"
                                                        style="height:25px;width:75%;" value="{{ $value->cantidad }}">

                                                </td>
                                                <td> <button type="button" onclick="deleteRow({{ $contador }})"
                                                        class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash"
                                                            aria-hidden="true"></i></button>
                                                </td>
                                                </>
                                                @php
                                                    $contador++;
                                                @endphp
                                        @endforeach
                                      
                                    </tbody>
                                </table>
                            </div>
                            <input name='contador_items' id='contador_items' type='hidden' value="{{$contador}}">
                        </div>
                        <br>

              	<div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
                <!--button type="submit" class="btn btn-success">
                    Actualizar
                </button-->

                <a onclick="actualizar_plantilla('{{$plantilla->id}}');" type="button" class="btn btn-success" id="btn_guardar">{{trans('winsumos.actualizar')}}</a>
              </div>
            
          </form>
      
    </div>
  <!-- /.box-body -->
</div>
</section>
    <!-- /.content -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script>

$('.agregar_items').on('click', function() {
            agregar_items();
        });

        function agregar_items() {
            var id = document.getElementById('contador_items').value;
            var tr = `<tr id="fila${id}"> 
                            <td>
                                <input type="hidden" name="id_item[]" id="id_item${id}" class="iditem">
                                <input required name="item_id[]" id="item_id"onfocus="agregar_item(${id})" class="form-control buscador_items" style="height:25px;width:90%;" name="producto[]">
                            </td>

                            <td>
                                <select id="tipo${id}" class="form-control" name="tipo[]"  style="height:25px;width:75%;text-transform:uppercase;">
                                    <option value="1">{{ trans('winsumos.enfermeria') }}</option>
                                    <option value="2">{{ trans('winsumos.anestesiologo') }}</option>
                                </select>
                            </td>

                            <td>
                                <input type="number" name="item_cant[]" id="item_cant${id}" min="1" class="form-control cant" style="height:25px;width:75%;" value="1">
                            </td>

                            <td>
                                <button  type="button" onclick="deleteRow(${id})"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                            </td>                    
                        </tr> `;
            $('#items').append(tr);
            id++;
            document.getElementById('contador_items').value = id;
        }


        function deleteRow(id) {
            $("#fila" + id).remove();
        }

        function agregar_item(id) {
          
            $(".buscador_items").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('planilla.buscar_item_producto')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },

                minLength: 2,
                select: function(data, ui) {
                    $('#id_item' + id).val(ui.item.value1);
                }
            });
        }

        function alertas(icon, title, text) {
            Swal.fire({
                icon: `${icon}`,
                title: `${title}`,
                html: `${text}`,
            })
        }

        function actualizar_plantilla(id){
            $('#btn_guardar').attr('disabled',true);
            $.ajax({
                type: 'post',
                url: "{{url('insumos/plantillas/actualizar')}}/"+id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_plantilla").serialize(),
                success: function(data) {

                    console.log(data);

                    if (data == "ok") {
                        alertas('success', "{{ trans('winsumos.exito') }}",
                            "{{ trans('winsumos.guardado_exito') }}");
                        location.href = "{{ route('plantilla.index') }}";
                    } else {
                        $('#btn_guardar').attr('disabled', false);
                    }
                    
                },
                error: function(data) {
                    alertas('error', "{{trans('winsumos.error')}}", "{{trans('winsumos.error')}}");
                    console.log(data);
                }
            });
        }
</script>

@endsection