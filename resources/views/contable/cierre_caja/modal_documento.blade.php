<div class="modal-body">
    <div class="modal-header">
        <h5 class="modal-title text-center">SUBIR DOCUMENTO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body" style="text-align: center;">
        <form method="POST" action="{{route('subir_documento_laboratorio_save')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$registro->id}}">
            <div class="row">
                <div class="col-md-3">
                    <label for="documento">Tipo Documento</label>
                </div>
                <div class="col-md-4">
                    <select name="tipo"  class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($maestroDocumento as $val)
                        <option value="{{$val->id}}">{{$val->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="file" required class="form-control" name="documento" >
                </div>
            </div>
            <div class="row" style="margin-top:5px">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-info">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    </div>
</div>