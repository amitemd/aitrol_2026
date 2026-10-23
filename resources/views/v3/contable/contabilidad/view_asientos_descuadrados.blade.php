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
                            <div class="col-3 col-sm-3">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.seleccioneanio')}}</label>
                                    <div class="input-group mb-3">
                                        @php
                                        $anios=date("Y");
                                        @endphp
                                        <select class="form-control" name="anio_asiento" id="anio_asiento">
                                        @for($i = 2019; $i <= $anios; $i++)
                                        <option @if($anios == $i ) selected @endif value="{{$i}}">{{$i}}</option>
                                        @endfor
                                        </select><br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 col-sm-2">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarDescuadrados()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
                                </div>
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
                        <h3 class="card-title">Asientos descuadrados</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="">
                        <table id="tableDescuadrados" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_tabla.asiento')}}</th>
                                    <th style="text-align: center; width: 35%;">{{trans('v3/new_tabla.modulo')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_varios.debe')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_varios.haber')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_tabla.diferencia')}}</th>
                                    <th style="text-align: center; width: 5%;">...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr style="text-align: center;">
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_tabla.asiento')}}</th>
                                    <th style="text-align: center; width: 35%;">{{trans('v3/new_tabla.modulo')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_varios.debe')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_varios.haber')}}</th>
                                    <th style="text-align: center; width: 15%;">{{trans('v3/new_tabla.diferencia')}}</th>
                                    <th style="text-align: center; width: 5%;">...</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{ csrf_field() }}
                    <div class="row hero-datosfactura">
                        <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/updateAsientoDescuadrado" />
                        <input type="hidden" name="id_asiento_cabecera" id="id_asiento_cabecera" value="0">
                        <div class="col-md-12" style="overflow-x: auto;">
                        <table style="width:100%; font-size: 15px">
                            <!-- <tr>
                                <td colspan="5"><strong> Fecha de Registro: </strong><br><div class="col-md-3"><input type="date" class="form-control" id="fecha" name="fecha" value="" /></div></td>
                            </tr> -->
                            <tr>
                                <td colspan="5"><strong> Número de asiento: </strong><span id="id_asiento"></span></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Sucursal:</strong>   <strong>Modulo:</strong> <span id="modulo"></span></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Estado:</strong> <span id="estado"></span> <strong>Aparece en SRI:</strong> <input id='sri' name='sri' type="checkbox"/></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Valor registrado:</strong> $<span id="valor"></span></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Fecha de creación:</strong> <span id="f_creacion"></span></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Creado por:</strong> <span id="u_crea"></span></td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>Detalle:</strong>  <textarea type="text" class="form-control" value="" id="observacion" name="observacion"></textarea>
                                    
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
                    <div class="row" >
                        <div id="log_contable" class="col-md-12" >
                        </div>
                    </div><br>
                    
                    <div class="row">
                        <div class="col-md-12" >
                            <table class="table table-striped table-bordered table-hover dataTables-example " width="100%">
                                <thead>
                                    <tr style="background-color: #F1F1F1">
                                        <th style="text-align: center; width: 25%;">Codigo/Cuenta</th>
                                        <th style="text-align: center; width: 40%;">Detalle</th>
                                        <th style="text-align: center; width: 15%;">Debe</th>
                                        <th style="text-align: center; width: 15%;">Haber</th>
                                        <th style="text-align: center; width: 5%;" id="agregarlineaDetalleAsiento"><a href="javascript:;" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody id="detalleAsiento"></tbody>
                                    
                                <tfoot>
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;" colspan="2">TOTALES</th>
                                            <th style="text-align: right;"> <span id="debe_total"></span> </th>
                                            <th style="text-align: right;"><span id="haber_total"></span> <input type="hidden" name="totaldebe" id="totaldebe" value="0"> <input type="hidden" name="totalhaber" id="totalhaber" value="0"> </th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                </tfoot>
                            </table>
                        </div>
                    </div><br><br>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-center w-100">
                    <a href="javascript:;" class="btn btn-success" id="btnUpdateAsientoDescuadrado"><i class="fa-solid fa-save"></i> Actualizar</a>
                    <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button> -->
                </div>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')