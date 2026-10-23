<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row" id="listado">
            <div class="table col-md-10" style="min-height: 100px;padding-left: 100px;min-height: 100px">
              <div>
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                  <thead>
                    <tr role="row">
                      <!--<th width="5%">ID</th>-->
                      <th width="20%">Seguro</th>
                      <th width="20%">Nivel</th>
                      <th width="20%">Valor</th>
                      <th width="20%">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($prod_tar_paq as $value)
                      @php
                        $inf_seguro = Sis_medico\Seguro::find($value->id_seguro);
                        $inf_nivel = Sis_medico\Nivel::where('id',$value->id_nivel)
                                                      ->where('estado',1)->first();
                      @endphp
                      <tr class="well">
                        <td>@if(!is_null($inf_seguro->nombre)){{$inf_seguro->nombre}}@endif</td>
                        <td>@if(!is_null($inf_nivel)){{$inf_nivel->nombre}}@endif</td>
                        <td>@if(!is_null($value->precio)){{$value->precio}}@endif</td>
                        <td>
                          <a class="btn btn-warning btn-xs" data-remote="{{route('actualiza_producto_tarifario.paquete', ['id_prod_tar_paq' => $value->id_prod_tar_paq,'id_prod' => $value->id_producto, 'id_seguro' => $value->id_seguro,'id_nivel' => $value->id_nivel])}}" data-toggle="modal" data-target="#edit_prod_tar_paq"   style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>

                          <a onclick="elimina_producto_tarifario_paquete('{{$value->id_prod_tar_paq}}')" class="btn btn-danger btn-xs" id="planillar"> <span  class="glyphicon glyphicon-trash"></span></a>
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

      
