<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label id="lblRuc" class="font-normal">Ruc </label>
            <div>
                <label title="Archivo pdf o imagen del RUC del proveedor" for="proveedorRuc" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                    <input type="file" name="proveedorRuc" id="proveedorRuc" style="display:none">
                    <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                </label>
            </div>
        </div>
        <div class="form-group">
            <label id="lblCedula" class="font-normal">Cedula </label>
            <div>
                <label title="Archivo pdf o imagen de la cedula del representante" for="proveedorCedula" class="ladda-button ladda-button-demo-ced btn btn-primary" data-style="zoom-in">
                    <input type="file" name="proveedorCedula" id="proveedorCedula" style="display:none">
                    <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                </label>
            </div>
        </div>
        <div class="form-group">
            <label id="lblVotacion" class="font-normal">{{trans('v3/new_varios.certificadoVotación')}} </label>
            <div>
                <label title="Archivo pdf o imagen del certificado de votacion del representante" for="proveedorVotacion" class="ladda-button ladda-button-demo-cer btn btn-primary" data-style="zoom-in">
                    <input type="file" name="proveedorVotacion" id="proveedorVotacion" style="display:none">
                    <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                </label>
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
                                <h5 class="card-title">Imagen del proveedor</h5>
                                <div class="card-tools">
                                    <a href="javascript:;" class="btn btn-tool">
                                        <label title="Archivo imágen del proveedor" for="proveedorLogo" class="ladda-button ladda-button-demo-provlogo btn btn-success" data-style="zoom-in">
                                            <input type="file" name="proveedorLogo" id="proveedorLogo" style="display:none">
                                            <i class="fas fa-pen"></i>
                                        </label>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body" style="position: flex;text-align: center;">
                                <center>
                                    <div style="width: 204px;height: 204px;" id="logoProveedor">
                                        <!--<img src="../storage/app/logo/0401182274_Mifoto.jpg" id="imgProveedor" style="width: 150px;height: 204px;" />-->
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