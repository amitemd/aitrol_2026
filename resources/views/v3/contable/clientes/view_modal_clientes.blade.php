<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_cliente"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true"><i class="fa fa-user" aria-hidden="true"></i> General</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ubicacion-tab" data-toggle="tab" href="#ubicacion" role="tab" aria-controls="ubicacion" aria-selected="false"><i class="fa fa-map-marker" aria-hidden="true"></i> Ubicaci&oacute;n</a>
                    </li>
                </ul>
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="viene" name="viene" />
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveCliente">
                    <input type="hidden" id="action_cliente" value="{{ url('v3/') }}/clientes/saveCliente">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            @include('v3.contable.clientes.view_general')
                        </div>
                        <div class="tab-pane fade" id="ubicacion" role="tabpanel" aria-labelledby="ubicacion-tab">
                            @include('v3.contable.clientes.view_ubicacion')
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCliente"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>