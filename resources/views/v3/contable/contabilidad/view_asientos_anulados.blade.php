<style>
    #crearCliente {
        cursor: pointer;
    }
</style>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Asientos anulados</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="">
                        <table id="tableAsientosAnulados" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="text-align: center; width: 60%;">{{trans('v3/new_tabla.observacion')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.id_asiento_anulado')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.asiento_anulacion')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.fechacreacion')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr style="text-align: center;">
                                    <th style="text-align: center; width: 60%;">{{trans('v3/new_tabla.observacion')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.id_asiento_anulado')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.asiento_anulacion')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th style="text-align: center; width: 10%;">{{trans('v3/new_tabla.fechacreacion')}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.contabilidad.view_visualizar_asiento')