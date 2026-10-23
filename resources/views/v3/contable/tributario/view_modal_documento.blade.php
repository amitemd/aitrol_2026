<style>
    .modal-dialog.modal-custom {
        max-width: 1600px;
        /* Ancho personalizado */
        width: 90%;
        /* Asegurarse de que el modal responda a la pantalla */
    }
</style>
<div class=" modal fade modalTotal" id="myModalDocumentos">
    <div class="modal-dialog modal-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_documentos"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="cuerpoDocumento">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>