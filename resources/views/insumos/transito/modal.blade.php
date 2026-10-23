<style type="text/css">
    .ui-autocomplete {
        z-index: 2147483647;
    }
    .colorB {
    background-color: #1DE9B6;
    border-radius: 10px;
    margin-right: 10px !important;
    width: 20%;
    text-align: center;
    color: white;
  }

  .colorA {
    background-color: #82B1FF;
    border-radius: 10px;
    width: 20%;
    margin-right: 10px !important;
    text-align: center;
    color: white;
  }

  .colorC {
    background-color: #A5D6A7;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorE {
    background-color: #DD2C00;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorD {
    background-color: #80CBC4;
    border-radius: 10px;
    text-align: center;
    margin-right: 10px !important;
    color: white;
    width: 20%;
  }

  .colorB1 {
    background-color: #1DE9B6;

    margin-right: 10px !important;
    font-weight: bold;
    text-align: center;
    color: white;
  }

  .colorA1 {
    background-color: #82B1FF;

    font-weight: bold;
    margin-right: 10px !important;
    text-align: center;
    color: white;
  }

  .colorC1 {
    background-color: #A5D6A7;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }

  .colorE1 {
    background-color: #DD2C00;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }

  .colorD1 {
    background-color: #80CBC4;
    font-weight: bold;
    text-align: center;
    margin-right: 10px !important;
    color: white;

  }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title" id="myModalLabel">Visualizar Movimiento</h4>
</div>

<div class="modal-body">
    <div class="card-header">
        <div class="col-md-6">
            <label>COLORES DE REFERENCIA</label>
        </div>
        <div class="col-md-6">
            <table class="col-md-12">
                <thead>
                    <tr>
                        <th class="colorB">Ingreso</th>
                        <th class="colorA">Salida</th>
                        <th class="colorC">Transito</th>
                        <th class="colorD">Reingreso</th>
                        <th class="colorE">De baja</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
    <div class="card-body">
        <div class="card-header">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table id="example22" class="table" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Estado del Producto</th>
                                    <th>Nombre del Encargado</th>
                                    <th>Cantidad</th>
                                    <th>Bodega Destino</th>
                                    <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"></div>
                    </div>
                    <div class="col-sm-7" style="padding-right: 30px">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                          
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">

</div>

<script type="text/javascript">
    $(document).ready(function() {

        src2 = "{{route('transito.nombre')}}";
        $("#nombre_encargado").autocomplete({
            source: function(request, response) {

                $.ajax({
                    url: "{{route('transito.nombre')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: {
                        term: request.term
                    },
                    dataType: "json",
                    type: 'post',
                    success: function(data) {
                        response(data);
                    }
                })
            },
            minLength: 3,
        });

        $("#nombre_encargado").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('transito.nombre2')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#nombre_encargado"),
                success: function(data) {
                    $('#mostrarCedula').val(data);
                    $('#id_encargado').val(data);
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        $("#serie").change(function() {
            var elemento = document.getElementById("serie").value;

            $.ajax({
                type: 'post',
                url: "{{route('transito.codigo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#serie"),
                success: function(data) {
                    $('#mostrarproducto').val(data);
                    $('#nombre_producto').val(data);
                    $('#nombre_producto').val("1");
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $("#guardar").click(function() {
            var nombre = document.getElementById("id_encargado").value;
            var validacion = document.getElementById("valida").value;
            var msj = ""
            if (nombre == "") {
                msj = "Porfavor ingrese la persona encargada \n";
            }
            if (nombre == "") {
                msj = "Porfavor ingrese la persona encargada \n";
            }
            if (nombre == "") {
                msj += "Porfavor ingrese el numero de serie del producto \n";
            }
            if (msj == "") {
                $('#enviar2').submit();
            } else {
                alert(msj)
            }
        });
    });
    $('#example22').DataTable({
             processing: true,
             serverSide: true,
             ajax: "{{route('trans.getData',['id'=>$id])}}",
             columns: [
                { data: 'fecha' },
                { data: 'serie' },
                { data: 'producto_nombre' },
                { data: 'tipo' },
                { data: 'nombre' },
                { data: 'cantidad' },
                { data: 'bodega' },
             ],
             order: [[0, 'desc']],
             rowCallback: function (row, data) {
                if ( data.tipo=='Ingreso') {
                    $(row).addClass('colorB1');
                }else if(data.tipo=='Salida'){
                    $(row).addClass('colorA');
                }else if(data.tipo=='Transito'){
                    $(row).addClass('colorC');
                }else if(data.tipo=='Reingreso'){
                    $(row).addClass('ColorD');
                }else if(data.tipo=='De baja'){
                    $(row).addClass('colorE');
                }
            }
    })
</script>