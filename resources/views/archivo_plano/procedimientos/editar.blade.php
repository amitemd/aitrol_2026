@extends('archivo_plano.procedimientos.base')
@section('action-content')


<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title" style="margin-top: 7px">Editar Items</h3>
                </div><br>
                <hr>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="content">
            <form method="POST" action="{{route('procedimientos_editar.actualizar')}}">
                {{ csrf_field() }}
                <div class="row">
                    <input type="hidden" name="id_procedimiento" id="id_procedimiento" value="{{$editar_procedimiento->id}}">
                    <div class="col-md-4">
                        <label>Tipo</label><br>
                        <select name="txttipo" class="form-control">
                            @if(!$tipos->contains($editar_procedimiento->tipo))
                                <option value="{{$editar_procedimiento->tipo}}" selected>{{$editar_procedimiento->tipo}} (actual)</option>
                            @endif
                            @foreach($tipos as $tipo)
                                <option value="{{$tipo}}" @if($editar_procedimiento->tipo == $tipo) selected @endif>{{$tipo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Código</label><br>
                        <input type="text" name="txtcodigo" placeholder="Código" class="form-control" value="{{$editar_procedimiento->codigo}}">
                    </div>
                    <div class="col-md-4">
                        <label>Descripción</label><br>
                        <input type="text" name="txtdescripcion" placeholder="Descripción" class="form-control" value="{{$editar_procedimiento->descripcion}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>Cantidad</label><br>
                        <input type="number" name="txtcantidad" min="1" step="any" required class="form-control" value="{{(float) $editar_procedimiento->cantidad > 0 ? $editar_procedimiento->cantidad : 1}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>Valor</label><br>
                        <input type="text" name="txtvalor" placeholder="Valor" class="form-control" value="{{$editar_procedimiento->valor}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>IVA</label><br>
                        <select name="txtiva" class="form-control">
                            <option value="0" @if((float) $editar_procedimiento->iva <= 0 && (float) $editar_procedimiento->iva_nuevo <= 0) selected @endif>No</option>
                            <option value="1" @if((float) $editar_procedimiento->iva > 0 || (float) $editar_procedimiento->iva_nuevo > 0) selected @endif>Sí</option>
                        </select>
                    </div>
                    <div class="col-md-6"><br>
                        <label>Estado</label><br>
                        <select name="txtestado" class="form-control">
                            <option {{ $editar_procedimiento->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                            <option {{ $editar_procedimiento->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12"><br>
                        <input type="submit" name="" value="Editar" class="btn btn-success">
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>

@endsection
