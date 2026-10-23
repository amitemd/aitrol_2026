<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label id="lblCedula" class="font-normal">Cedula </label>
            <div>
                <label title="Archivo pdf o imagen de la cedula del representante" for="usuarioCedula" class="ladda-button ladda-button-demo-ced btn btn-primary" data-style="zoom-in">
                    <input type="file" name="usuarioCedula" id="usuarioCedula" style="display:none">
                    <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                </label>
            </div>
        </div>
        <div class="form-group">
            <label id="lblVotacion" class="font-normal">{{trans('v3/new_varios.certificadoVotación')}} </label>
            <div>
                <label title="Archivo pdf o imagen del certificado de votacion del representante" for="usuarioVotacion" class="ladda-button ladda-button-demo-cer btn btn-primary" data-style="zoom-in">
                    <input type="file" name="usuarioVotacion" id="usuarioVotacion" style="display:none">
                    <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                </label>
            </div>
        </div>
        <div class="form-group"> 
        <label class="font-normal">New system</label>
            <div class="form-group">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="chenewsystem">
                    <label class="custom-control-label" for="chenewsystem">Estado</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <div style="width: 60%;">
                <div>
                    <div class="card-body">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="card-title">Logo</h5>
                                <div class="card-tools">
                                    <a href="javascript:;" class="btn btn-tool">
                                        <label title="Archivo imágen del usuario" for="usuarioLogo" class="ladda-button ladda-button-demo-usuariologo btn btn-success" data-style="zoom-in">
                                            <input type="file" name="usuarioLogo" id="usuarioLogo" style="display:none">
                                            <i class="fas fa-pen"></i>
                                        </label>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body" style="position: flex;text-align: center;">
                                <center>
                                    <div style="width: 204px;height: 204px;" id="logousuario">
                                        <!--<img src="../storage/app/logo/0401182274_Mifoto.jpg" id="imgusuario" style="width: 150px;height: 204px;" />-->
                                        <i class="fas fa-user-tie fa-10x" style="color:#91B2F3;"></i>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>