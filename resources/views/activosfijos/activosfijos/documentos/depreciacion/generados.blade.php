<b>Depreciaciones generadas en la fecha</b>
<div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_generadas" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class="well-dark">
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha de generado</th>
                <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Activo</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Costo</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Depreciado</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                </tr>
            </thead>
            <tbody id="tbl_detalles_generados" name="tbl_detalles_generados">
                @foreach ($generados as $value)
                <tr class="well"> 
                    <td >{{ $value->id }}</td>
                    <td >{{ date('d/m/Y', strtotime($value->created_at)) }}</td>
                    <td >{{ $value->created_at }}</td>
                    <td >{{ $value->id_usuariocrea }}</td>
                    <td >
                        <button type="button" class="btn btn-primary" id="btn_exportar_xls" onclick="exportarxls({{ $value->id }})">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar excel
                        </button>
                    </td>
                    <td >
                        <button type="button" class="btn btn-primary" id="btn_exportar_xml" name="btn_exportar_xml" onclick="exportarxml({{ $value->id }})">
                            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar xml
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
            </tfoot>
            </table>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-2">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($generados)}} </div>
        </div> 
        </div> 
    </div>
</div>