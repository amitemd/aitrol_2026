
@if(!is_null($registros))
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable " role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr >
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" ># de Asiento</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                            <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $value)
                        <tr>
                            <td >{{ $value->id_asiento }}</td>
                            <td >{{ $value->fecha }}</td>
                            <td >{{ $value->concepto }}</td>
                            <td >{{ $value->valor }}</td>
                            <td >@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                            <td>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{ route('notadebito.revisar', ['id' => $value->id]) }}" class="btn btn-success col-md-8 col-sm-8 col-xs-8 btn-margin">
                                Revisar Nota
                            </a>
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
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"></div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  
                  </div>
                </div>
              </div>
</div>
@else
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr >
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" ># de Asiento</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                      </tr>
                    </thead>
                    <tbody>
                        No data avaliable
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>

</div>
@endif
<script type="text/javascript">
    $(document).ready(function(){

        $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
        });

    });
</script>