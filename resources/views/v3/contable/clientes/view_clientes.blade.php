<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_menu.inicio') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarCliente();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-contenido">
                        <table id="tableClientes" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>{{trans('v3/new_tabla.direccion')}}</th>
                                    <th>{{trans('v3/new_tabla.correo')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>{{trans('v3/new_tabla.direccion')}}</th>
                                    <th>{{trans('v3/new_tabla.correo')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.clientes.view_modal_clientes')