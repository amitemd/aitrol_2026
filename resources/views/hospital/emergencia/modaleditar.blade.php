<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title" id="mymodalTratamiento">Medicamento:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editarevolucion',$campo->id)}}" method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Notas Evoluci&oacute;n</label>
                    <input  type="text" class="form-control" name="nota_evolucion" id="nota_evolucion" value="{{$campo->nota_evolucion}}"></input>
                </div>
                <div class="form-group col-md-6">
                    <label>Examen Fisico</label>
                    <input   type="text"  class="form-control" name="examen_fisico" id="examen_fisico" value="{{$campo->examen_fisico}}"></input>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>

    </form>
    
</div>