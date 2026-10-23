{{--
<b>Comprobantes Anulados:</b>
<div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_comp_anula" class="table table-sm table-bordered table-condensed dataTable table-striped" role="grid" aria-describedby="example2_info">

            <thead>
                <tr class="well-dark">
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                <th width="20%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Comp</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Est.</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Pto. Emi.</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia D</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia H</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Anulación</th>
                </tr>
            </thead>
            </table>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-2">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($compras)}} </div>
        </div>
        </div>
    </div>
</div> --}}



<b>Comprobantes anulados del período:</b>
<div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_comp_anula" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class="well-dark">
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Número</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                <th width="20%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">T.Comp</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Est.</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie Pto. Emi.</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">SubTotal.</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia D</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia H</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Anulación</th>
                {{-- <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Iva</th>
                <th width="5%" class="" tabindex="0" aria-controls="tbl_comp_anula" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ret. Fue</th>  --}}
                </tr>
            </thead>
            <tbody id="tbl_detalles_comp_anula" name="tbl_detalles_comp_anula">
            </tbody>
            <tfoot>
                @foreach ($anulados as $value)
                @php /*
                <tr class="well">
                    <td ><input type="checkbox" id="id_anulado_{{ $value['id'] }}" class="form-check-input" name="id_anulado[]" onchange="actualizar(this);" value="{{ $value['id'] }}"  @if(@$value['validado']=="1") checked @endif> &nbsp; {{ $value['numero'] }}</td>
                    <td >{{ $value['numero'] }} <input type="hidden"  name="anu_numero[]" value="{{ $value['numero'] }}"/> </td>
                    <td >{{ $value['tipo'] }} <input type="hidden"  name="anu_tipo[]" value="{{ $value['tipo'] }}"/> </td>
                    <td >{{ $value['nombre'] }} <input type="hidden"  name="anu_nombre[]" value="{{ $value['nombre'] }}"/> </td>
                    <td >{{ $value['tipo_comp'] }} <input type="hidden"  name="anu_tipo_comp[]" value="{{ $value['tipo_comp'] }}"/> </td>
                    <td >{{ $value['establecimiento'] }} <input type="hidden"  name="anu_establecimiento[]" value="{{ $value['establecimiento'] }}"/> </td>
                    <td >{{ $value['emision'] }} <input type="hidden"  name="anu_emision[]" value="{{ $value['emision'] }}"/> </td>
                    <td >{{ $value['secuenciad'] }} <input type="hidden"  name="anu_secuenciad[]" value="{{ $value['secuenciad'] }}"/> </td>
                    <td >{{ $value['secuenciah'] }} <input type="hidden"  name="anu_secuenciah[]" value="{{ $value['secuenciah'] }}"/> </td>
                    <td >{{ $value['autorizacion'] }} <input type="hidden"  name="anu_autorizacion[]" value="{{ $value['autorizacion'] }}"/> </td>
                    <td >{{ $value['fecha_autorizacion'] }} <input type="hidden"  name="anu_fecha_autorizacion[]" value="{{ $value['fecha_autorizacion'] }}"/> </td>
                </tr>*/ @endphp
                @endforeach
            </tfoot>
            </table>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-2">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($ventas)}} </div>
        </div>
        </div>
        <div class="row">
        <div class="col-sm-2">
            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true, 'tbl_detalles')" class="btn btn-default">
                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; Marcar Todos
            </button>
        </div>
        <div class="col-xs-2">
            <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false, 'tbl_detalles')" class="btn btn-default">
                <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; Desmarcar Todos
            </button>
        </div>
        </div>
    </div>
</div>
