<style>
    .btn-custom {
        background-color: #f0f0f0;
        border: 1px solid #d1d1d1;
        color: #333;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-custom:hover {
        background-color: #e0e0e0;
        border-color: #c1c1c1;
    }
</style>
<div class="modal fade modalTotal" id="myModalAsientos">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_asiento"><i class="fas fa-edit" style="color:blue;"></i>{{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body" id="contenidoAsiento">

                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <button class="btn btn-custom" onclick="printDiv()">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
            </div>
        </div>
    </div>
</div>