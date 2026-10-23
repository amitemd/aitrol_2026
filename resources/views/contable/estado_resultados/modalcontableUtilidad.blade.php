<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.1/css/buttons.dataTables.min.css">
<div class="modal-content">
    <div class="modal-header">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;font-size:30pxt;font-weight:bold" class="modal-title">DETALLE CUENTA</h3>
    </div>
    <div style="margin-top: 2px;" class="col-md-12">
        <div class="form-row">
            <label for="titulo">INGRESO</label>
            <table id="tableVentasaa" class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" style="width:25%">Cuenta</th>
                        <th scope="col" style="width:25%">Id asiento</th>
                        <th scope="col" style="width:25%">Detalle</th>
                        <th scope="col" style="width:25%">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$resultados_combinados["ingreso"]->isEmpty())
                    @foreach($resultados_combinados["ingreso"]["asientos"] as $val)
                    <tr>
                        <td>{{$val->plan}}</td>
                        <td>{{$val->id_asiento_cabecera}}</td>
                        <td>{{$val->nombre}}</td>
                        <td class="destra">
                            @if($val->debe != '0.0000')
                            {{$val->debe}}
                            @else
                            {{$val->haber}}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            Total
                        </td>

                        <td>

                            {{$resultados_combinados["totalIngreso"]}}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-row">
            <label for="titulo">COSTO</label>
            <table id="tableVentasaaa" class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" style="width:25%">Cuenta</th>
                        <th scope="col" style="width:25%">Aparece SRI</th>
                        <th scope="col" style="width:25%">Id asiento</th>
                        <th scope="col" style="width:25%">Detalle</th>
                        <th scope="col" style="width:25%">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$resultados_combinados["costo"]->isEmpty())
                    @foreach($resultados_combinados["costo"]["asientos"] as $val)
                    <tr>
                        <td>{{$val->plan}}</td>
                        <td>{{$val->aparece_sri}}</td>
                        <td>{{$val->id_asiento_cabecera}}</td>
                        <td>{{$val->nombre}}</td>
                        <td>
                            @if($val->debe != '0,0000')
                            {{$val->debe}}
                            @else
                            {{$val->haber}}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            Total
                        </td>

                        <td>
                            {{$resultados_combinados["totalCosto"]}}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-row">
            <label for="titulo">GASTO</label>
            <table id="tableVentas" class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" style="width:25%">Cuenta</th>
                        <th scope="col" style="width:25%">Aparece SRI</th>
                        <th scope="col" style="width:25%">Id asiento</th>
                        <th scope="col" style="width:25%">Detalle</th>
                        <th scope="col" style="width:25%">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$resultados_combinados["gasto"]->isEmpty())
                    @foreach($resultados_combinados["gasto"]["asientos"] as $val)
                    <tr>
                        <td>{{$val->plan}}</td>
                        <td>{{$val->aparece_sri}}</td>
                        <td>{{$val->id_asiento_cabecera}}</td>
                        <td>{{$val->nombre}}</td>
                        <td>
                            @if($val->debe != '0,0000')
                            {{$val->debe}}
                            @else
                            {{$val->haber}}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            Total
                        </td>

                        <td>
                            {{$resultados_combinados["totalGasto"]}}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-row">

            <table class="table table-striped tableVentasaa" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" colspan="3">Total</th>
                        <th>{{ number_format($total, 2, ',', '.') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {


        var buttons = [{
            extend: 'excel',
            text: 'Excel',
            action: function(e, dt, button, config) {
                var wb = XLSX.utils.book_new();

                function addSheet(tableId, sheetName) {
                    var tableData = $('#' + tableId).DataTable().data().toArray();
                    var data = [
                        [sheetName]
                    ];
                    data.push([]);
                    data = data.concat(tableData);
                    var footData = $('#' + tableId + ' tfoot').find('td').map(function() {
                        return $(this).text();
                    }).get();

                    if (footData.length > 0) {
                        data.push([]);
                        data.push(['Total'].concat(footData.slice(1)));
                    }
                    var ws = XLSX.utils.aoa_to_sheet(data);
                    XLSX.utils.book_append_sheet(wb, ws, sheetName);
                }
                addSheet('tableVentasaa', 'INGRESO');
                addSheet('tableVentasaaa', 'COSTO');
                addSheet('tableVentas', 'GASTO');
                XLSX.writeFile(wb, 'datos.xlsx');
            }
        }, ];

        $("#tableVentasaa").DataTable({
            "searching": false,
            "lengthChange": false,
            "paging": false,
            dom: 'Bfrtip',
            buttons: buttons
        });
    });
</script>