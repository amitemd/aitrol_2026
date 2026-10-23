<style type="text/css">
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

</style>
<div class="modal-content">
      <div style="text-align: left" class="modal-header">
          <div class="box-header">
            <div class="col-md-8">
              <h3 class="box-title">EDITAR PAGO ROL</h3>
            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-3">
              <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
            </div>
          </div> 

          <div class="box-header">
            <div class="form-group col-md-3 col-xs-2">
              <label class="texto" for="identificacion">Tipo de Pago: </label>
            </div>
            <div class="form-group col-md-5 col-xs-10 container-4">
              <select class="form-control" id="id_empresa" name="id_empresa">
                <option>Seleccione...</option>
                @foreach($tipo_pago_rol as $value)
                  <option value="{{$value->id}}">{{$value->tipo}}</option>
                @endforeach
              </select>
            </div>
          </div>   
          <div class="form-group col-md-12">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Select</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>  
                            @if($rol_pag->id_tipo_pago == '1') 
                                EFECTIVO
                              @elseif($rol_pag->id_tipo_pago == '2') 
                                CHEQUE
                            @endif
                        </td>
                        <td>@if(!is_null($rol_pag->neto_recibido)){{$rol_pag->neto_recibido}}@endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="#" class="btn btn-warning  btn-xs" ><span class="glyphicon glyphicon-edit"></span></a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
       </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
          <button type="button" onclick="#" class="btn btn-primary">GUARDAR</button>
      </div>

</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : false,
    'autoWidth'   : false
  })

</script>


