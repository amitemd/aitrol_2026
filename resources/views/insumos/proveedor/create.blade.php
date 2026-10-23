@extends('insumos.proveedor.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }
    
    function goBack() {
      window.history.back();
    }

</script>

<section class="content">
    <div class="row">
       <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">{{trans('winsumos.agregar_proveedor')}}</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
                    </div>
                </div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('proveedor.store') }}">
                 {{ csrf_field() }}
                    <div class="box-body col-xs-24">
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">{{trans('winsumos.ruc')}}</label>
                            <div class="col-md-7">
                                <input id="id" type="text"  class="form-control" onkeypress="return check(event)" name="id" value="{{ old('id') }}" style="text-transform:uppercase;"  maxlength="13" required autofocus>
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Razon Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-md-4 control-label">{{trans('winsumos.razon_social')}}</label>
                            <div class="col-md-7">
                                <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ old('razonsocial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                    @if ($errors->has('razonsocial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('razonsocial') }}</strong>
                                    </span>
                                    @endif
                                </div>
                        </div>
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-md-4 control-label">{{trans('winsumos.nombre_comercial')}}</label>
                            <div class="col-md-7">
                                <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ old('nombrecomercial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-md-4 control-label">{{trans('winsumos.ciudad')}}</label>
                            <div class="col-md-7">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">{{trans('winsumos.direccion')}}</label>
                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{trans('winsumos.correo')}}</label>
                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">{{trans('winsumos.telefono_domicilio')}}</label>
                            <div class="col-md-7">
                                <input id="telefono1" type="number" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>
                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">{{trans('winsumos.telefono_celular')}}</label>
                            <div class="col-md-7">
                                <input id="telefono2" type="number" class="form-control" name="telefono2" value="{{ old('telefono2') }}" required autofocus>
                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_proveedor') ? ' has-error' : '' }}">
                            <label for="id_tipo_proveedor" class="col-md-4 control-label">{{trans('winsumos.tipo_proveedor')}}</label>
                            <div class="col-md-7">
                                <select id="id_tipo_proveedor" name="id_tipo_proveedor" class="form-control" required="required">
                                    <option value="">{{trans('winsumos.seleccione')}}..</option>
                                    @foreach($tipos as $value)
                                        @if ($value->estado != 0)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endif    
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_tipo_proveedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipo_proveedor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                                <label  for="acreedores" class="col-md-4 control-label">{{trans('winsumos.grupo')}}</label>
                                <div class="col-md-7">
                                    <select id="acreedores" onchange="grupos_acreedores()" name="acreedores" class="form-control col-md-8">
                                        <option value="">{{trans('winsumos.seleccione')}}..</option>
                                        @foreach($id_padre as $value)                                            
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                                
                                        @endforeach
                                    </select>                                                           
                                </div>

                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('lista_contable') ? ' has-error' : '' }}">
                            <label for="lista_contable" class="col-md-4 control-label">{{trans('winsumos.cuenta_contable')}}</label>
                            <div class="col-md-7">
                                <select id="lista_contable" name="lista_contable" class="form-control">
                                </select>  
                                @if ($errors->has('lista_contable'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lista_contable') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                                <label class="control-label col-md-4" for="retencion_iva">% {{trans('winsumos.retencion_iva')}}</label>
                                <div class="col-md-7">
                                    <select class="form-control col-md-4" name="retencion_iva" id="retencion_iva">
                                        <option value="0">{{trans('winsumos.seleccione')}}...</option>
                                        @foreach($retenciones as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>                        
                        <div class="form-group col-xs-6">
                                <label class="control-label col-md-4" for="retencion_ft"> % {{trans('winsumos.retencion_renta')}}</label>
                                <div class="col-md-7">
                                    <select class="form-control" name="retencion_ft" id="retencion_ft">
                                        <option value="0">{{trans('winsumos.seleccione')}}...</option>
                                        @foreach($retencioner as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>                            
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('winsumos.agregar')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
   
   function validarRuc(val){

    ruc = document.getElementById('id').value;

   /* $proveedor_id = \Sis_medico\Proveedor::where('id', $request['id'])->first();
    dd($proveedor_id);*/

    /* Verifico que el campo no contenga letras */                  
        var ok=1;
        for (i=0; i<ruc.length && ok==1 ; i++){
            var n = parseInt(ruc.charAt(i));
            if (isNaN(n)) ok=0;
        }

        if (ok==0){
         alert("No puede ingresar caracteres en el número");         
         return false;
        }

        if (ruc.length<13){              
         alert('El número ingresado no es válido');                  
         return true;
        }


   } 

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
   $("#id").on("change",function(){

     //validar_ruc($(this).val());
   })
   function validar_ruc(id){
            
        
         $.ajax({
            type: 'post',
            url:"{{route('proveedor.validar_ruc')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id': id},
            success: function(data){
                //alert(data[0].nombre);
                data= JSON.parse(data);
                switch(data.rs){
                    case 1:
                        $("#id").next().remove();
                        break;
                    case 0:
                        //alertaExito(data.error);
                        $("#id").val('');
                        $("#id").next().remove();
                        $("#id").after('<span class="validationMessage" style="color:red;">Ruc no válido</span>');
                        break;
                }
            },
            error: function(data){
                console.log(data);
            }
        })
   }

</script>

@endsection
