<style>
    .dropzone {
        border: 2px dashed #d3d3d3;
        padding: 20px;
        height: auto;
    }
    .dropzone .dz-preview .dz-image {
        width: 100%;
        height: auto;
    }
    .dropzone .dz-preview .dz-details {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.foto_activo')}}</label>
            <div class="input-group mb-3">
                <div id="foto_activo" name="foto_activo" class="dropzone form-control text-right" style="border: 2px dashed #d3d3d3; padding: 20px; height: auto;">
                    <div class="dz-message" data-dz-message>
                        <span>{{trans('v3/new_varios.foto_activo')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>{{trans('v3/new_varios.foto_acc')}}</label>
            <div class="input-group mb-3">
                <div id="foto_acc" name="foto_acc" class="dropzone form-control text-right" style="border: 2px dashed #d3d3d3; padding: 20px; height: auto;">
                    <div class="dz-message" data-dz-message>
                        <span>{{trans('v3/new_varios.foto_activo')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>