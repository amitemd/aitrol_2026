<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title" id="mymodalTratamiento">Salas:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editar_modal',$dato->id)}}" method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Area Salas:</label>
                    <input  type="text" class="form-control" name="area_salas" id="area_salas" value="{{$dato->area_salas}}"></input>
                </div>
                <div class="form-group col-md-4">
                    <label>Medicina:</label>
                    <input   type="text"  class="form-control" name="medicina_salas" id="medicina_salas" value="{{$dato->medicina_salas}}"></input>
                </div>
                <div class="form-group col-md-4">
                    <label>Descripci&oacute;n</label>
                    <input   type="text"  class="form-control" name="descripcion_salas" id="descripcion_salas" value="{{$dato->descripcion_salas}}"></input>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>

    </form>
    
</div>