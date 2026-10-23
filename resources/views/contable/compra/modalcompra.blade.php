<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
    input[type=button] {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 16px 32px;
        text-decoration: none;
        margin: 4px 2px;
        cursor: pointer;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" style="text-align: center;font-weight:bold;line-height: normal;">Compras Pedidos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <table id="table_data" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Proveedor</th>
                    <th scope="col">Numero de Pedido</th>
                    <th scope="col">Numero de Factura</th>
                    <th scope="col">Realizado por</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pedidos as $value)
                <tr>
                    <td>{{ $value->created_at }}</td>
                    <td>{{ $value->nombrecomercial }}</td>
                    <td>{{ $value->pedido }}</td>
                    <td>{{ $value->factura }}</td>
                    <td>{{ $value->nombre1}} {{ $value->apellido1}}</td>
                    <td>@if($value->tipo == 3) Conglomerado @else Normal @endif</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-gray" onclick='valortrapsasar("{{$value->pedido}}")'>Agregar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" id="limpiar1" class=" borrar btn btn-success btn-gray" onclick="limpiasr()">Borrar</button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table_data').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            "order": [
                [0, "desc"]
            ],
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });

    function valortrapsasar(valor) {
        console.log(valor);
        $("#pedido_nombre").val(valor);
        buscar_pedido();
    }
    function limpiasr() {
        var contador = $("#contador").val();
        for (var i = 0; i < contador; i++) {
            $("#dato" + i).remove();
            //$('input[type="text"]').val('');
        }
        $("#contador").val(0);
    }
</script>