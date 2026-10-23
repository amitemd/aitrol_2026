<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title" id="mymodalTratamiento">Plan:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editar_plan',$dato->id)}}" method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Preescripci&oacute;n:</label>
                    <input  type="text" class="form-control" name="descripcion_plan" id="descripcion_plan" value="{{$dato->descripcion_plan}}"></input>
                </div>
                <div class="form-group col-md-6">
                    <label>Medico:</label>
                    <input   type="text"  class="form-control" name="medico_plan" id="medico_plan" value="{{$dato->medico_plan}}"></input>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>

    </form>
    
</div>