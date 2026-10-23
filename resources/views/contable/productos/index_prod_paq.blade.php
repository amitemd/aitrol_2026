<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row" id="listado">
            <div class="table col-md-12" >
              <div>
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                  <thead>
                    <tr role="row">
                      <th width="20%">Cant.</th>
                      <th width="20%">Paquete</th>
                      <th width="20%">PvP</th>
                      <th width="20%">Acción</th>
                      <th width="20%">Tarifario</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($prod_paq as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                        <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                        <td>@if(!is_null($value->precio)){{$value->precio}}@endif</td>
                        <td>
                          <a onclick="elimina_producto_paquete('{{$value->id_prod_paq}}')" class="btn btn-danger btn-xs" id="planillar"> <span  class="glyphicon glyphicon-trash"></span></a>
                        </td>
                        <td>
                         <a id="tar_paq" class="btn btn-success btn-xs" data-remote="{{route('crea_producto_tarifario.paquete',['id_prod_paq' =>$value->id_prod_paq,'id_producto' => $value->id_producto,'id_paquete' => $value->id_paquete])}}" data-toggle="modal" data-target="#tarifario_paquete" ><span>Agregar</span> </a>
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
		