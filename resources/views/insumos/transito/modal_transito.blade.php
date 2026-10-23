<div class="modal-body">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"> Transito </h3>
        </div>
        <div class="card-body">
            <form action="{{route('transito.storenew')}}" method="POST">
                {{ csrf_field() }}
                <div class="row">

                    <div class="form-group col-md-6">
                        <label> Bodega Saliente: </label>
                    </div>
                    <div class="form-group col-md-6">
                        <select name="bodega_entrante" id="bodega_entrante" class="form-control select2" required>
                            <option value="">Seleccione ...</option>
                            @foreach($bodega as $x)
                            <option value="{{$x->id}}">{{$x->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label> Bodega Entrante </label>
                    </div>
                    <div class="form-group col-md-6">
                        <select name="bodega_saliente" id="bodega_saliente" class="form-control select2" required>
                            <option value="">Seleccione ...</option>
                            @foreach($bodega as $x)
                            <option value="{{$x->id}}">{{$x->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Observaciones</label>
                    </div>
                    <div class="form-group col-md-6">
                        <textarea class="form-control" name="observaciones" id="observaciones" cols="3" rows="3"></textarea>
                    </div>
                    <div class="form-group col-md-12" style="text-align: center;">
                        <button class="btn btn-primary" type="submit"> <i class="fa fa-save"></i> &nbsp; Guardar </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>