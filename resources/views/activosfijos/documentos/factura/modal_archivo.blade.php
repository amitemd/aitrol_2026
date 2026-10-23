
<div class="modal-content">
    <div class="modal-header">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">Seleccionar Imagenes {{$activo->nombre}}</h3>
    </div>

    <div style="margin-top: 5px;" class="col-md-12">
        <form id="form" method="post" action="{{ route('documentofactura.guardar_fotos') }}" enctype="multipart/form-data">
            {{ csrf_field() }}

            <input type="hidden" name="id_activo" id="id_activo" value="{{$activo->id}}">
            <div class="col-md-4">
                <label>{{trans('ContableM.FotoActivo')}}</label>
            </div>
            <div class="col-md-8">
                <input type="file" class="files" name="fotoaf" id="fotoaf" accept="image/*">
            </div>

            <div class="col-md-4">
                <label>{{trans('ContableM.FotoAccesorio')}}</label>
            </div>
            <div class="col-md-8">
                <input type="file" class="files" name="fotoacc" id="fotoacc" accept="image/*">
            </div>
            <div class="col-md-12">
                <button class="btn btn-success" type="submit" name="btn_guardar" id="btn_guardar" ><i class="fa fa-save"> Guardar</i></button>
            </div>
        </form>

    </div>

    <div class="modal-footer">

    </div>
</div>