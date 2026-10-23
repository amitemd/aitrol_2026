<div class="modal-content">

    <div class="modal-header">
        <label class="modal-title" id="mymodalTratamiento">Dati del paciente:</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editar_gene',$dato->id)}}"method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Medico_general</label>
                    <input   type="text"  class="form-control" name="medico_general" id="medico_general"  value="{{$dato->medico_general}}" ></input>
                </div>
                 <div class="form-group col-md-6">
                    <label>Descripci&oacute;n</label>
                    <input  type="text" class="form-control" name="descripcion_general" id="descripcion_general"  value="{{$dato->descripcion_general}}" ></input>
                </div>
        </div>
        <div class="modal-footer">
           <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>

    </form>
    
</div>