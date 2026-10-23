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
		          <h3 class="box-title" style="margin-top: 7px">Información de Plantillas</h3>
		        </div><br><hr>
	    	</div>
  		</div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" action="">
            {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_plantilla" class="form-control" value="{{$id}}">
              <div class="col-md-3">
                <label>Código</label><br>
                <input type="text" name="codigo" placeholder="Código"  readonly class="form-control" value="{{$plantilla->codigo}}">
              </div>
              <div class="col-md-4">
                <label>Nombre</label><br>
                <input type="text" name="nombre" placeholder="Nombre"  readonly class="form-control" value="{{$plantilla->nombre}}">
              </div>
              <div class="col-md-2">
                <label>Estado</label><br>
                <input type="text" name="estado" placeholder="estado"  readonly class="form-control" value="@if($plantilla->estado == 1) Activo @else Inactivo @endif">
                <!--select name="estado" class="form-control">
                  <option @if($plantilla->estado == 1) selected @endif value="1">Activo</option>
                  <option @if($plantilla->estado == 0) selected @endif value="0">Inactivo</option>
                </select-->
              </div>
				<br>
				<div class="col-md-12"><br>
				<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
     			<br/>

                <input name='contador_items' id='contador_items' type='hidden' value="0">
                <div class="col-md-12 table-responsive">
                    <table id="items" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr class='well-darks'>
                                <th width="60%" tabindex="0">Items</th>
                                <th width="20%" tabindex="0">Tipo</th>
                                <th width="20%" tabindex="0">Cantidad</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plantillas_items as $value)
                            <tr class="fila-fija">
                                <td>
                                    <input type="hidden" name="id_item[]" class="id_item" value="{{$value->id_producto}}" /><input readonly name="item_id[]" id="item_id" onchange="agregar_item(this)" class=" buscador_items form-control"  style="height:25px;width:90%;" value="{{$value->nom_prod}}" name="producto[]"/>
                                </td>

                                <td>
                                    @php
                                        //dd($value);
                                        $tipo = "";
                                        if($value->tipo == 1){
                                            $tipo = "ENFERMERIA";
                                        }else{
                                            $tipo = "ANESTESIOLOGO";
                                        }
                                    @endphp
                                    <input type="text" readonly name="tipo[]"  class="form-control" style="height:25px;width:75%;" value="{{$tipo}}">
                                </td>

                                <td>
                                    <input type="number" readonly name="item_cant[]" id="item_cant" class="form-control" style="height:25px;width:75%;" value="{{$value->cantidad}}">
                                   
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> 

				</div>
				<br>

              	<div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
                <!--button type="submit" class="btn btn-success">
                    Agregar
                </button-->
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
            var tr = '<tr>' + '<td><input type="hidden" name="id_item[]" class="id_item"/><input required name="item_id[]" id="item_id" onchange="agregar_item(this)" class=" buscador_items form-control"  style="height:25px;width:90%;" name="producto[]"/></td>' + '<td><input type="number" name="item_cant[]" id="item_cant" class="form-control" style="height:25px;width:75%;" value="1"><input type="number" name="orden[]" id="orden" class="form-control" style="height:25px;width:75%;"></td>'+'<td class="remover" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#items').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_items").value = parseInt(sum);


            $(".buscador_items").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('planilla.find_item')}}",
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
            });

        };
        $(document).on("click", ".remover", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_item(e) {

            $.ajax({
                type: 'post',
                url: "{{route('planilla.find_id_item')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_item').val(data[0].id);
                    }
                    if (data.value != 'no resultados') {
                        console.log(data);
                    } else {}
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
</script>

@endsection