<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('v3/admin')}}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{config('data.titulo')}}</h3>
                    <div>
                        <div style="position: absolute; right: 5px;top:7px">

                            <a href="javascript:;">
                                <label title="Importar archivo txt del SRI" for="docSri" class="btn btn-success btn-sm" data-style="zoom-in">
                                    <input type="file" name="docSri" id="docSri" style="display:none">
                                    <i class="fa fa-download"></i> Importar
                                </label>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                    <table id="tableImportarDocumentos" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>...</th>
                                <th>{{trans('v3/new_tabla.estado')}}</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Total</th>
                                <th>Ruc</th>
                                <th>Nombre</th>
                                <th>Fecha autorización</th>
                                <th>Clave de acceso</th>
                                <th>Autorización</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>...</th>
                                <th>Estado</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Total</th>
                                <th>Ruc</th>
                                <th>Nombre</th>
                                <th>Fecha autorización</th>
                                <th>Clave de acceso</th>
                                <th>Autorización</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="loading"></div>