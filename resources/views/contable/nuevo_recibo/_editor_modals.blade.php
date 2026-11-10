<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        </div>
    </div>
</div>

<div class="modal fade" id="log_rc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-content">
                <div style="text-align: left" class="modal-header">
                    <h3 class="box-title">LOG</h3>
                </div>  
                <div class="modal-body">
                    @php $logs = $orden->logs; @endphp
                    @foreach($logs as $log)
                        <p><b>{{$log->usercrea->apellido1}} {{$log->usercrea->nombre1}}: </b>{{ $log->descripcion }}</p>
                    @endforeach
                </div>   
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
                </div> 
            </div>    
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="datos_factura">
      </div>
    </div>
</div>

<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
