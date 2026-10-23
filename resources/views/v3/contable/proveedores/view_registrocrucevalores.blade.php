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
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{trans('v3/new_tabla.buscador')}}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.desde')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="desde" name="desde" class="form-control"
                                            value="{{ config('data.d') == '' ? date('Y-m-d') : '' }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.hasta')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="hasta" name="hasta" class="form-control"
                                            value="{{ config('data.h') == '' ? date('Y-m-d') : '' }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <a href="javascript:;" class="btn btn-primary" onclick="buscarCruce()"><i
                                        class="fas fa-search"></i> <span class="botones">Buscar</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="{{ url('v3/') }}/{{ config('data.controlador') }}/crearCruceValor"
                                    class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i> <span
                                        class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{ config('data.id_proveedor') }}">
                        <table id="tableCruceValor" class="table table-bordered table-striped tablaDatos"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Secuencia</th>
                                    <th>Fecha</th>
                                    <th>Asiento</th>
                                    <th>Proveedor</th>
                                    <th>Detalle</th>
                                    <th>Total</th>
                                    <th>Creado por</th>
                                    <th>Anulado por</th>
                                    <th>Estado</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.contabilidad.view_visualizar_asiento')

