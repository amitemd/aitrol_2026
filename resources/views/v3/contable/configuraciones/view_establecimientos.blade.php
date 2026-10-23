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
        <form id="frm_">
            {{csrf_field()}}
            <div class="row">
                <input type="hidden" id="d" name="d" value="|">
                <input type="hidden" id="id_sucursal" name="id_sucursal" value="">
                <input type="hidden" id="estado" name="estado">
                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveEstablecimiento">
                <div class="col-md-4">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Establecimiento</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nombre</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del establecimiento">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Número de establecimiento</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="establecimiento" name="establecimiento">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Punto de emisión</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="emision" name="emision">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>País</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <select class="form-control select2 pais" id="pais" style="width: 88%;">
                                        <option value="">--Seleccione--</option>
                                        <?php

                                        use Sis_medico\v3\contable\Utilidades;

                                        if (config('data.paises') != null && count(config('data.paises')) > 0) {
                                            foreach (config('data.paises') as $row) {
                                                echo '<option value="' . $row->id . '">' .  ucwords(strtolower(Utilidades::sanear_string_tildes($row->nombre))) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Provincia</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <select class="form-control select2 provincia" id="provincia" style="width: 88%;">
                                        <option value="">--Seleccione--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Ciudad</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 88%;">
                                        <option value="">--Seleccione--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Dirección</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>No. de factura</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="num_factura" name="num_factura" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>No. retenciones</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="num_retencion" name="num_retencion" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>No. nota de crédito</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="num_nota_credito" name="num_nota_credito" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>No. nota de debito</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="num_nota_debito" name="num_nota_debito" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>No. liquidación</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" class="form-control text-right" id="num_liquidacion" name="num_liquidacion" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="cheestado">
                                    <label class="custom-control-label" for="cheestado">Estado</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger"><i class="fas fa-times-circle"></i> Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnGuardarEstablecimiento"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Lista de establecimientos</h3>
                        </div>
                        <div class="card-body">
                            <table id="tableEstablecimientos" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Establecimiento</th>
                                        <th>Emisión</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>E-mail</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (config('data.establecimientos') != '' && count(config('data.establecimientos')) > 0) {
                                        foreach (config('data.establecimientos') as $row) {
                                            echo '<tr>
                                        <td>' . $row->id . '</td>
                                        <td>' . $row->codigo_sucursal . '</td>
                                        <td>' . $row->codigo_caja . '</td>
                                        <td>' . $row->nombre_sucursal . '</td>
                                        <td>' . $row->direccion_sucursal . '</td>  
                                        <td>' . $row->telefono_sucursal . '</td>
                                        <td>' . $row->email_sucursal . '</td>
                                        <td>' . $row->estado . '</td>
                                        </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>Establecimiento</th>
                                        <th>Emisión</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>E-mail</th>
                                        <th>Estado</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>