@extends('insumos.proveedor.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box-header with-border">
                <div class="col-md-9">
                   <h3 class="box-title">{{trans('winsumos.editar_proveedor')}}</h3>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                      <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                    </a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box box-primary"> 
                    <form class="form-vertical" role="form" method="POST" action="{{ route('proveedor.update', ['id' => $proveedor[0]->id_proveedor]) }}">
                        <div class="box-body">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">    <!--RUC-->
                            <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                                <label for="id" class="col-xs-6 control-label">{{trans('winsumos.ruc')}}</label>
                                <input id="id" type="text" class="form-control" name="id" value="{{ $proveedor[0]->id_proveedor }}" required autofocus>
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--Razón Social-->
                            <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                                <label for="razonsocial" class="col-xs-6 control-label">{{trans('winsumos.razon_social')}}</label>
                                <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ $proveedor[0]->razonsocial }}" required autofocus>
                                @if ($errors->has('razonsocial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('razonsocial') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--Nombre Comercial-->
                            <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                                <label for="nombrecomercial" class="col-xs-8 control-label">{{trans('winsumos.nombre_comercial')}}</label>
                                <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ $proveedor[0]->nombrecomercial }}" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--Ciudad-->
                            <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                                <label for="ciudad" class="col-xs-6 control-label">{{trans('winsumos.ciudad')}}</label>
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $proveedor[0]->ciudad }}" required>
                                @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--direccion-->
                            <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <label for="direccion" class="col-xs-6 control-label">{{trans('winsumos.direccion')}}</label>
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $proveedor[0]->direccion }}" required>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--email-->
                            <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-xs-6 control-label">{{trans('winsumos.correo')}}</label>
                                <input id="email" type="text" class="form-control" name="email" value="{{ $proveedor[0]->email }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--telefono1-->
                            <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                                <label for="telefono1" class="col-xs-8 control-label">{{trans('winsumos.telefono_domicilio')}}</label>
                                <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $proveedor[0]->telefono1 }}" required>
                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--telefono2-->
                            <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                                <label for="telefono2" class="col-xs-6 control-label">{{trans('winsumos.telefono_celular')}}</label>
                                <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $proveedor[0]->telefono2 }}" required>
                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                                <label for="estado" class="col-xs-6 control-label">{{trans('winsumos.estado')}}</label>
                                <select id="estado" name="estado" class="form-control">
                                    <option {{$proveedor[0]->estado == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.inactivo')}}</option>
                                    <option {{$proveedor[0]->estado == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.activo')}}</option>
                                </select>  
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--id_tipo_usuario-->
                            <div class="form-group col-xs-6{{ $errors->has('id_tipo_proveedor') ? ' has-error' : '' }}">
                                <label for="id_tipo_proveedor" class="col-xs-6 control-label">{{trans('winsumos.tipo_proveedor')}}</label>
                                    <select id="id_tipo_proveedor" name="id_tipo_proveedor" class="form-control" required="required">
                                        <option value="">{{trans('winsumos.seleccione')}}..</option>
                                        @foreach($tipos as $value)
                                            @if ($value->estado != 0) 
                                                    <option {{$proveedor[0]->id_tipoproveedor == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option> 
                                            @endif    
                                        @endforeach
                                    </select>  
                                    @if ($errors->has('id_tipo_proveedor'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('id_tipo_proveedor') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group col-xs-6">
                                <label for="acreedores" class="col-xs-6 control-label">{{trans('winsumos.grupo')}}</label>
                                
                                    <select id="acreedores" onchange="grupos_acreedores()" name="acreedores" class="form-control">
                                        <option value="">{{trans('winsumos.seleccione')}}..</option>
                                        @foreach($id_padre as $value)
                                             
                                            <option {{$proveedor[0]->id_cuentas == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option> 
                                                
                                        @endforeach
                                    </select>  

                            </div>
                            <input type="hidden" name="cuenta_proovedor" id="cuenta_proovedor">
                            <div class="form-group col-xs-6{{ $errors->has('lista_contable') ? ' has-error' : '' }}">
                                <label for="lista_contable" class="col-md-6 control-label">{{trans('winsumos.cuenta_contable')}}</label>
                                    <select id="lista_contable" name="lista_contable" class="form-control">
                                    </select>  
                                    @if ($errors->has('lista_contable'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lista_contable') }}</strong>
                                        </span>
                                    @endif
                               
                            </div>
                            <div class="form-group col-xs-6">
                                
                                    <label class="col-md-6 control-label" for="retencion_iva"> % {{trans('winsumos.retencion_iva')}}</label>
                                    <select class="form-control" style="" name="retencion_iva" id="retencion_iva">
                                        <option value="0">{{trans('winsumos.seleccione')}}...</option>
                                        @foreach($retenciones as $value)
                                            <option {{$proveedor[0]->id_porcentaje_iva == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option> 
                                        @endforeach
                                    </select>
                                
                            </div>
                            <div class="form-group col-xs-6">
                                
                                <label class="col-md-6 control-label" for="retencion_iva"> % {{trans('winsumos.retencion_renta')}}</label>
                                <select class="form-control" style="" name="retencion_ft" id="retencion_ft">
                                    <option value="0">{{trans('winsumos.seleccione')}}...</option>
                                    @foreach($retencioner as $value)
                                        <option {{$proveedor[0]->id_porcentaje_ft == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option> 
                                    @endforeach
                                </select>
                            
                            </div>
                            <div class="form-group col-xs-6">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                    {{trans('winsumos.actualizar')}}
                                    </button>
                                </div>
                            </div>
                        </div>    
                    </form>
                </div>
            </div>    
            <!--right-->
            <div class="col-sm-6">
                <div class="box box-info">
                    <div class="box-header with-border"><h3 class="box-title">{{trans('winsumos.subir_logo')}}</h3></div>
                    <form  id="subir_imagen" name="subir_imagen" method="post"  action="{{ route('proveedor.subir_logo', ['id' => $proveedor[0]->id]) }}" class="formarchivo" enctype="multipart/form-data" >    
                        <input type="hidden" name="logo" value="{{$proveedor[0]->id}}">    
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                        <div class="box-body">
                            <div class="form-group col-xs-12" >
                                <input type="hidden" name="carga" value="@if($proveedor[0]->logo=='') {{$proveedor->logo='../logo/avatar.jpg'}} @endif">
                                <img src="../../logo/{{$proveedor[0]->logo}}"  alt="Logo Image"  style="width:160px;height:160px;" id="logo_empresa" >
                                <!-- User image -->
                            </div>
                            <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                                <label for="archivo">{{trans('winsumos.agregar_logo')}}</label>
                                <input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/><br /><br />
                                @if ($errors->has('archivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archivo') }}</strong>
                                        </span>
                                    @endif
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">{{trans('winsumos.actualizar_logo')}}</button>
                            </div>
                        </div>
                    </form>
                </div>                       
            </div>
        </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

   function grupos_acreedores(){

        var valor= $("#acreedores").val();
        //alert(valor);
         $.ajax({
            type: 'post',
            url:"{{route('proveedor.query_cuentas')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opcion': valor},
            success: function(data){
                //alert(data[0].nombre);
                if(data.value!='no'){
                    if(valor!=0){
                        $("#lista_contable").empty();
                        $.each(data,function(key, registro) {
                            $("#lista_contable").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                        }); 
                    }else{
                        $("#lista_contable").empty();
                    }
 
                }
            },
            error: function(data){
                console.log(data);
            }
        })
   }

</script>
@endsection