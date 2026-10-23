<div class="modal-content">

    <div class="modal-header">
        <label class="modal-title" id="mymodalTratamiento">Medicamento:</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{route('hospital.editardiagnostico',$campo->id)}}"method="POST"  id="formulario">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Operaci&oacute;n</label>
                    <input  type="text" class="form-control" name="operacion" id="operacion"  value="{{$campo->operacion_diagnostico}}" ></input>
                </div>
                <div class="form-group col-md-4">
                    <label>Cie</label>
                    <input   type="text"  class="form-control" name="cie" id="cie"  value="{{$campo->cie_diagnostico}}" ></input>
                </div>
                <div class="form-group col-md-4">
                    <label>Tipo</label>
                    <input   type="text"  class="form-control" name="tipo" id="tipo"  value="{{$campo->tipo_diagnostico}}" ></input>
                </div>
        </div>
        <div class="modal-footer">
           <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
            <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
        </div>

    </form>
    
</div>