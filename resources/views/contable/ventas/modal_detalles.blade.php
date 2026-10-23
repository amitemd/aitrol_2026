<div class="modal-content">
  <div class="modal-header">
        <h5 class="modal-title" style="text-align: left;font-weight:bold;line-height: normal;">Detalle Examenes</h5>
        <div class="box-body dobra">
       </div>
    </div>
    <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                               
                            </div>
                            <div class="col-md-12 table table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                  <thead>
                                    <tr >
                                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Id</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Precio</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Copago</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($examen_detalle as $value)
                                    @php
                                        $total_item = $value->valor - $value->valor_con_oda;
                                        $total += $total_item;
                                    @endphp
                                        <tr>
                                            <td>{{$value->examen->id}}</td>
                                            <td>{{$value->examen->nombre}}</td>
                                            <td>{{$value->cantidad}}</td>
                                            <td>{{number_format($value->valor,2,'.','0')}}</td>
                                            <td>{{number_format($value->valor_con_oda,2,'.','0')}}</td>
                                            <td>{{number_format($total_item,2,'.','')}}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="font-weight: bold;" >Total</td>
                                        <td  >{{ number_format($total,2,'.', '0') }}</td>
                                    </tr>
                                  </tbody>
                                </table>
                                <div style ="text-align: center">
                                  <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
