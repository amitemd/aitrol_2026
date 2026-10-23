<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row" id="listado">
            <div class="table col-md-12" >
              <div>
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                  <thead>
                    <tr role="row">
                      <th width="20%">Nivel.</th>
                      <th width="20%">Precio</th>
                      <th width="20%">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($prod_precio as $value)
                      <tr>
                        <td>@if(!is_null($value->nivel_precio)){{$value->nivel_precio}}@endif</td>
                        <td>@if(!is_null($value->precio_producto)){{$value->precio_producto}}@endif</td>
                        <td>
                          <a onclick="elimina_producto_precio('{{$value->id_prec_prod}}')" class="btn btn-danger btn-xs" id="planillar"> <span  class="glyphicon glyphicon-trash"></span></a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
</div>
		