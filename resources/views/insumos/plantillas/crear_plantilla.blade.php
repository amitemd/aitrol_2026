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

    .columnas td {
        margin: 10px;
    }

</style>
    <section class="content">
      <div class="box">
  		<div class="box-header">
	    	<div class="row">
		        <div class="col-sm-6">
		          <h3 class="box-title" style="margin-top: 7px">Crear Plantillas</h3>
		        </div><br><hr>
	    	</div>
  		</div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" id="form_plantilla">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-3">
                <label>Código</label><br>
                <input type="text" name="codigo" placeholder="Código"  class="form-control">
              </div>
              <div class="col-md-4">
                <label>Nombre</label><br>
                <input type="text" name="nombre" placeholder="Nombre"  class="form-control" style="text-transform: uppercase">
              </div>
              <div class="col-md-2">
                <label>Estado</label><br>
                <select name="estado" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
				<br>
				<div class="col-md-12"><br>
				<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
     			<br/>

                <input name='contador_items' id='contador_items' type='hidden' value="0">
                <div class="col-md-12 table-responsive">
                    <table id="items" class="table table table-bordered table-hover dataTable"  role="grid" aria-describedby="example2_info" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr class='well-darks'>
                                <th width="50%" tabindex="0">Items</th>
                                <th width="20%" tabindex="0">Tipo</th>
                                <th width="20%" tabindex="0">Cantidad</th>
                                <th width="10%" tabindex="0">
                                    <button type="button" class="btn btn-success btn-gray agregar_items">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                                    <tbody id="contenido">
                                       
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

                <a onclick="guardar_plantilla();" type="button" class="btn btn-success" id="btn_guardar">Agregar</a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script>

        $('.agregar_items').on('click', function() {
            agregar_items();
        });

        function agregar_items() {
            var id = document.getElementById('contador_items').value;
            var tr = `<tr id="fila${id}"> 
                            <td>
                                <input type="hidden" name="id_item[]" id="id_item${id}" class="iditem">
                                <input required name="item_id[]" id="item_id" onfocus="agregar_item(${id})" class="form-control buscador_items " style="height:25px;width:90%;" name="producto[]">
                            </td>

                            <td>
                                <select class="form-control" id="tipo${id}" name="tipo[]"  style="height:25px;width:75%;text-transform:uppercase;">
                                    <option value="1">Enfermeria</option>
                                    <option value="2">Anestesiologo</option>
                                </select>
                            </td>

                            <td>
                                <input type="number" name="item_cant[]" min="1" id="item_cant${id}" class="form-control cant" style="height:25px;width:75%;" value="0">
                            </td>

                            <td>
                                <button  type="button" onclick="deleteRow(${id})" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                            </td>                    
                        </tr> `;
            $('#contenido').append(tr);
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
        function guardar_plantilla(){
            document.getElementById('btn_guardar').disabled = true;
            $.ajax({
                type: 'post',
                url: "{{route('insumo_planilla.guardar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_plantilla").serialize(),
                success: function(data) {
                    console.log(data);
                    if (data == "ok") {
                        alertas('success', 'Exito', 'Guardado Correctamente');
                        $('#btn_guardar').attr('disabled',true);
                        location.href = "{{route('plantilla.index')}}"; 
                    }else{
                        document.getElementById('btn_guardar').disabled = true;
                    }
                    
                },
                error: function(data) {
                    alertas('error', 'Error', 'Ocurrio un problema');
                    console.log(data);
                }
            });
        }
</script>

@endsection