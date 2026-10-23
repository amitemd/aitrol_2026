<div class="modal-content" style="width: 100%;">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
        <div class="col-md-5 size_text">
             <h5 class="modal-title">@lang('contableM.log_acceso')</h5>
        </div>
        <div class="col-md-7">
            <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
        </div>  
    </div>
    <div class="box-body dobra ">
        <div id="contenedor" class="container" style="padding: 0;">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
  
                <div class="col-md-12" style="padding: 0;">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr class='well-dark'>
                        <th width="10%"  >{{trans('agenda.fecha')}}</th>
                        <th width="10%"  >{{trans('adminusuarios.cedula')}}</th>
                        <th width="20%"  >{{trans('agenda.usuario')}}</th>
                        <th width="10%"  >{{trans('nomina.ip')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($logs as $log)
                        <p> </p> 
                      <tr class="well">
                        <td><b>{{ $log->created_at }} </b></td>
                        <td>{{ $log->usuario_crea->id }}</td>
                        <td>{{ $log->usuario_crea->apellido1 }} {{ $log->usuario_crea->nombre1 }}</td>
                        <td>{{ $log->ip_creacion }}</td>
                        
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
    <div class="separator1"></div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
    </div>
</div>
