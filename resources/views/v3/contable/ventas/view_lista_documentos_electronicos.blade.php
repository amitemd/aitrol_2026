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
                        <h3 class="card-title">{{trans('v3/new_submodulo.'.config('data.titulo'))}}</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableListaDocumentos" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Establecimiento</th>
                                    <th>Emision</th>
                                    <th>Secuencial</th>
                                    <th># completo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cont = 0;
                                foreach (config('data.resultado') as $row) {
                                    $cont++;
                                    $estado = $row->estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                                    echo '<tr>
                                    <td>' . $cont . '</td>
                                    <td align="center">' . $row->fecha . '</td>
                                    <td>' . $row->empresa . '</td>
                                    <td>' . $row->tipo . '</td>
                                    <td align="right">' . $row->establecimiento . '</td>
                                    <td align="right">' . $row->emision . '</td>
                                    <td align="right">' . $row->secuencial . '</td>
                                    <td align="right">' . $row->num . '</td>      
                                    <td align="center">' . $estado . '</td>                                  
                                    </tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Establecimiento</th>
                                    <th>Emision</th>
                                    <th>Secuencial</th>
                                    <th># completo</th>
                                    <th>Estado</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>