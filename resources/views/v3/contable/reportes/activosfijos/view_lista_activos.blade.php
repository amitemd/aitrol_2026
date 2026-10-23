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
                </div>
                <div class="card-body">
                    <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                    <table id="tableActivosFijosSucursal" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Fecha compra</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Comprobante</th>
                                <th>Color</th>
                                <th>Modelo</th>
                                <th>Serie</th>
                                <th>Costo</th>
                                <th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Fecha compra</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Comprobante</th>
                                <th>Color</th>
                                <th>Modelo</th>
                                <th>Serie</th>
                                <th>Costo</th>
                                <th>Sucursal</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>