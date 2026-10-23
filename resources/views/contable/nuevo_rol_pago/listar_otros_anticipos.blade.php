<div class="modal-content">
    <div class="modal-header">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="" class="modal-title">@lang('nomina.anticipos_no_aplicados')</h3>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="box-body dobra">
                <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="contenedor">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                            <thead>
                                <tr class='well-dark'>
                                <th >{{trans('nomina.anio')}}</th>
                                <th >{{trans('nomina.mes')}}</th>
                                <th >{{trans('nomina.monto')}}</th>
                                <th >{{trans('nomina.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($otros_anticipos as $value)
                                <tr>
                                    <td>{{$value->anio_cobro_anticipo}}</td>
                                    <td>{{$value->mes_cobro_anticipo}}</td>
                                    <td>{{$value->monto_anticipo}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" onclick="aplicar_anticipo('{{ $value->id }}','{{ $rol->id }}');" ><span id="anticipo{{$value->id}}"> Aplicar</span></button>
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
                        
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal" >@lang('nomina.cerrar')</button>
    </div>

   
</div>


