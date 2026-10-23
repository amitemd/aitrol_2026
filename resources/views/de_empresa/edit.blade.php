@extends('de_empresa.base')
@section('action-content')
@php
$id_empresa= Session::get('id_empresa');
$empresa= Sis_medico\Empresa::find($id_empresa);
@endphp
<style type="text/css">
    .color_rojo {
        font-size: 15pt;
        font-weight: bold;
        color: red;
    }
</style>
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 style="font-size: 22pt;font-weight:bold;color:black;margin-left: 20px;">
                    {{trans('DeEmpresa.Editar')}}
                </h3>
            </div>
            <div class="box-header">
                @php
                $empresa= Sis_medico\Empresa::find($deempresa->id_empresa);
                @endphp
                @if($empresa->logo!=null)
                <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
                @endif
                <span class="color_rojo">
                    @if(isset($empresa)) {{$empresa->nombrecomercial}} @endif
                </span>
            </div>
            <form class="form-vertical" role="form" id="formulario" enctype="multipart/form-data" action="{{route('maestrosed.update')}}" method="POST">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <input type="hidden" id="id" name="id" value="{{$deempresa->id}}">
                    <div class="form-group col-xs-12{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-2 control-label">RUC</label>
                        <div class="col-md-7">
                            <input id="id_empresa" type="text" class="form-control" name="id_empresa" value="{{ $deempresa->id_empresa }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                            @if ($errors->has('id_empresa'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_empresa') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-2 control-label">Razón social</label>
                        <div class="col-md-7">
                            <input id="id_empresa" type="text" class="form-control" value="{{ $empresa->nombrecomercial }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                            @if ($errors->has('id_empresa'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_empresa') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('agente_retencion') ? ' has-error' : '' }}">
                        <label for="tipo_contribuyente" class="col-md-2 control-label">Tipo contribuyente</label>
                        <div class="col-md-7">
                            <select id="tipo_contribuyente" name="tipo_contribuyente" class="form-control">
                                <option {{$deempresa->tipo_contribuyente == 0 ? 'selected' : ''}} value="0">Natural</option>
                                <option {{$deempresa->tipo_contribuyente == 1 ? 'selected' : ''}} value="1">Juridica</option>
                            </select>
                            @if ($errors->has('agente_retencion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('agente_retencion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('agente_retencion') ? ' has-error' : '' }}">
                        <label for="agente_retencion" class="col-md-2 control-label">{{trans('DeEmpresa.Agente_Retencion')}}</label>
                        <div class="col-md-7">
                            <select id="agente_retencion" name="agente_retencion" class="form-control">
                                <option {{$deempresa->agente_retencion == 1 ? 'selected' : ''}} value="1">{{trans('DeEmpresa.Si')}}</option>
                                <option {{$deempresa->agente_retencion == 0 ? 'selected' : ''}} value="0">{{trans('DeEmpresa.No')}}</option>
                            </select>
                            @if ($errors->has('agente_retencion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('agente_retencion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('contribuyente_especial') ? ' has-error' : '' }}">
                        <label for="contribuyente_especial" class="col-md-2 control-label">{{trans('DeEmpresa.Contribuyente_Especial')}}</label>
                        <div class="col-md-7">
                            <input id="contribuyente_especial" type="text" class="form-control" name="contribuyente_especial" value="{{ $deempresa->contribuyente_especial }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('contribuyente_especial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('contribuyente_especial') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('ambiente') ? ' has-error' : '' }}">
                        <label for="ambiente" class="col-md-2 control-label">{{trans('DeEmpresa.Ambiente')}}</label>
                        <div class="col-md-7">
                            <select id="ambiente" name="ambiente" class="form-control">
                                <option {{$deempresa->ambiente == 1 ? 'selected' : ''}} value="1">{{trans('DeEmpresa.Prueba')}}</option>
                                <option {{$deempresa->ambiente == 2 ? 'selected' : ''}} value="2">{{trans('DeEmpresa.Produccion')}}</option>
                            </select>
                            @if ($errors->has('ambiente'))
                            <span class="help-block">
                                <strong>{{ $errors->first('ambiente') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('contabilidad') ? ' has-error' : '' }}">
                        <label for="contabilidad" class="col-md-2 control-label">{{trans('DeEmpresa.Contabilidad')}}</label>
                        <div class="col-md-7">
                            <select id="contabilidad" name="contabilidad" class="form-control">
                                <option {{$deempresa->contabilidad == 1 ? 'selected' : ''}} value="1">{{trans('DeEmpresa.Si')}}</option>
                                <option {{$deempresa->contabilidad == 0 ? 'selected' : ''}} value="0">{{trans('DeEmpresa.No')}}</option>
                            </select>
                            @if ($errors->has('contabilidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('contabilidad') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-12{{ $errors->has('tipo_rimpe') ? ' has-error' : '' }}">
                        <label for="tipo_rimpe" class="col-md-2 control-label">{{trans('DeEmpresa.Tipo_rimpe')}}</label>
                        <div class="col-md-7">
                            <select id="tipo_rimpe" name="tipo_rimpe" class="form-control">
                                <option {{$deempresa->tipo_rimpe == 0 ? 'selected' : ''}} value="0">{{trans('DeEmpresa.Rimpe_Popular')}}</option>
                                <option {{$deempresa->tipo_rimpe == 1 ? 'selected' : ''}} value="1">{{trans('DeEmpresa.Rimpe_Emprendedor')}}</option>
                                <option {{$deempresa->tipo_rimpe == 2 ? 'selected' : ''}} value="2">{{trans('DeEmpresa.Regimen_General')}}</option>
                            </select>
                            @if ($errors->has('tipo_rimpe'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo_rimpe') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('entidadFirma') ? ' has-error' : '' }}">
                        <label class="col-md-2 control-label">{{trans('DeEmpresa.Firma')}}</label>
                        <div class="col-md-7">
                            <p class="help-block"><?= isset($id) ? ($deempresa->ruta_firma == '' ? 'No Existe Firma Electr&oacute;nica' : 'Ok') : ''; ?></p>
                            <label title="A&ntilde;adir Archivo" for="entidadFirma" class="btn btn-primary btn-gray">
                                <input type="file" name="entidadFirma" id="entidadFirma" class="hide" value="{{ $deempresa->ruta_firma }}">
                                A&ntilde;adir Archivo&nbsp;&nbsp;<i class="fa fa-paperclip"></i>
                            </label>
                            <input type="hidden" id="texfirma" name="texfirma" value="<?= isset($deempresa->ruta_firma) ? $deempresa->ruta_firma : ''; ?>">&nbsp;&nbsp;<label id="rutFirma"><?= isset($deempresa->ruta_firma) != '' ? $deempresa->ruta_firma : 'No existe Firma'; ?></label>
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('clave_firma') ? ' has-error' : '' }}">
                        <label for="clave_firma" class="col-md-2 control-label">{{trans('DeEmpresa.Clave_Firma')}}</label>
                        <div class="col-md-7">
                            <input id="clave_firma" type="text" class="form-control" name="clave_firma" value="{{ $deempresa->clave_firma }}" required autofocus>
                            @if ($errors->has('clave_firma'))
                            <span class="help-block">
                                <strong>{{ $errors->first('clave_firma') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success btn-gray">
                                {{trans('DeEmpresa.Actualizar')}}
                            </button>
                            <button type="button" onclick="history.back()" class="btn btn-danger btn-gray">
                                Regresar
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>

    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    function editar() {
        var confirmar = confirm('¿seguro quiere editar?');
        if (confirmar) {
            $.ajax({
                url: "{{route('maestrosed.update')}}",
                type: 'POST',
                data: $('#formulario').serialize(),
                dataType: 'json',
                success: function(json) {
                    console.log(json);
                    //var url = "{{url('/')}}/empresa"
                    if (json == 'ok') {
                        setTimeout(function() {
                            swal("Editado!", "Correcto", "success");
                            //window.location = url;
                        }, 3000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                },
            });
        }
    }

    $(document).ready(function() {
        $('#tipo_contribuyente').change(function() {
            var option = '';
            $('#tipo_rimpe').html('');
            $('#tipo_rimpe').trigger('change');
            if ($('#tipo_contribuyente').val() == 0) {
                option += "<option {{$deempresa->tipo_rimpe == 0 ? 'selected' : ''}} value=\"0\">{{trans('DeEmpresa.Rimpe_Popular')}}</option>";
                option += "<option {{$deempresa->tipo_rimpe == 1 ? 'selected' : ''}} value=\"1\">{{trans('DeEmpresa.Rimpe_Emprendedor')}}</option>";
            } else {
                option += "<option {{$deempresa->tipo_rimpe == 0 ? 'selected' : ''}} value=\"0\">{{trans('DeEmpresa.Rimpe_Popular')}}</option>";
                option += "<option {{$deempresa->tipo_rimpe == 1 ? 'selected' : ''}} value=\"1\">{{trans('DeEmpresa.Rimpe_Emprendedor')}}</option>";
                option += "<option {{$deempresa->tipo_rimpe == 2 ? 'selected' : ''}} value=\"2\">{{trans('DeEmpresa.Regimen_General')}}</option>'";
            }
            $('#tipo_rimpe').html(option);
            $('#tipo_rimpe').trigger('change');
        });
        $('#entidadFirma').change(function() {
            var inputFile = document.getElementById('entidadFirma');
            var file = inputFile.files[0];
            var fileSize = $('#entidadFirma')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                $('#entidadFirma').val('');
                alert('Error: archivo muy grande');
            } else {
                var fd = new FormData();
                fd.append('entidadFirma', file);
                fd.append('id_empresa', $('#id_empresa').val());
                fd.append('tipo_archivo', 'entidadFirma');
                fd.append('_token', $('input[name=_token]').val());
                $.ajax({
                    url: 'guardarArchivo',
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function(data) {
                        var firma = data.split('|');
                        if (firma[0] == 'ok') {
                            $('#rutFirma').html(firma[1]);
                            $('#texfirma').val(firma[1]);
                            //alert($('#texfirma').val());
                        } else {
                            $('#entidadFirma').val('');
                        }
                    }
                });
            }
        });
    })
</script>
@endsection