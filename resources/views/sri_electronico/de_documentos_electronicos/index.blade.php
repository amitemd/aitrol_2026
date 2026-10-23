@extends('sri_electronico.de_maestro_documentos.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{route('documentosElectronicos.index')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title"> {{trans('documentosElectronicos.Mantenimiento_Documentos_Electronicos')}}</h3>
                </div>
                <div id="example2_wrapper" class="">
                    <div class="ibox-content">
                        <div class="table-responsive col-md-12">
                            <table id="tableDocumentos" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{trans('documentosElectronicos.Paso')}}</th>
                                        <th>{{trans('documentosElectronicos.Maestro_Documentos')}}</th>
                                        <th>{{trans('documentosElectronicos.numDocumento')}}</th>
                                        <th>{{trans('documentosElectronicos.Ruc_Receptor')}}</th>
                                        <th>{{trans('documentosElectronicos.Ruc_Emisor')}}</th>
                                        <th>{{trans('documentosElectronicos.Clave_Acceso')}}</th>
                                        <th>{{trans('documentosElectronicos.Usuario')}}</th>
                                        <th>{{trans('documentosElectronicos.Respuesta_Recepcion')}}</th>
                                        <th>{{trans('documentosElectronicos.Respuesta_Autorizacion')}}</th>
                                        <th>{{trans('documentosElectronicos.Errores')}}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<div class="modal fade" id="modalErrores" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-list"></i> Historial de errores</h5>
                <div style="position: relative;">
                    <div style="position: absolute;top:-28px;right: 0px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection