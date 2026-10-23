<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">SELECCIONE UN PRODUCTO O SERVICIO</h4>
</div>

<div class="modal-body">
  <div class="row" style="padding: 10px;">
        <div style="padding-top: 10px;padding-left: 100px" class="form-group col-md-12">
              <center>
                  <div id="example2" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                      <div class="table-responsive col-md-12" style="min-height: 210px;">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                          <thead>
                            <tr>
                              <th >Acci&oacute;n</th>
                              <th >Codigo</th>
                              <th >Nombre</th>
                              <th >Código Barra</th>
                              <th >Descripción</th>
                              <th >Stock Minimo</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($lista_productos as $value)
                            <tr>
                              <td style="font-size: 11px;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <a href="#" class="btn btn-success col-md-3 col-xs-3 btn-margin"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></a>
                              </td>
                              <td style="font-size: 11px;">{{$value->codigo}}</td>
                              <td style="font-size: 11px;">{{$value->nombre}}</td>
                              <td style="font-size: 11px;">{{$value->codigo_barra}}</td>
                              <td style="font-size: 11px;">{{$value->descripcion}}</td>
                              <td style="font-size: 11px;">{{$value->stock_minimo}}</td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-5">
                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($lista_productos)}} / {{count($lista_productos)}} de {{$lista_productos->total()}} registros</div>
                      </div>
                      <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                          {{$lista_productos->links()}}
                        </div>
                      </div>
                    </div>
                  </div>
              </center>
        </div>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
 
</script>


