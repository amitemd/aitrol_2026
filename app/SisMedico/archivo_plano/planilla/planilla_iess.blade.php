@extends('archivo_plano.planilla.base')
@section('action-content')

<style>
	 .ui-autocomplete
        {
            soverflow-x: hidden;
			max-height: 200px;
			width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
</style>
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">INGRESO DE PLANILLAS IESS</h3>
		        </div>
		    </div>
		</div>
	  	<div class="box-body">
			<form class="form-horizontal" role="form" method="POST" action="{{ route('planilla.guardar') }}" id="form">
			{{ csrf_field() }}
				<div class="row">
				<div class="form-group col-md-4 ">
	                <label for="cedula_ben" class="col-md-4 control-label">Id. Beneficiario:</label>
	                <div class="col-md-7">
	                    <input id="cedula_ben" maxlength="15" type="text" class="form-control" name="cedula_ben" required> 
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <div class="col-md-7">
	                    <button id="buscar" type="button" class="btn btn-primary">Buscar</button>
	                </div>
                </div>
                </div>
                <div class="row">
			  	<div class="form-group col-md-4 ">
	                <label for="hc" class="col-md-4 control-label">Hc:</label>
	                <div class="col-md-7">
	                    <input id="hc" maxlength="10" type="text" class="form-control input-sm" name="hc">
	                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="cedula_afi" class="col-md-4 control-label">Cédula Afiliado:</label>
	                <div class="col-md-7">
	                    <input id="cedula_afi" maxlength="15" type="number" class="form-control input-sm" name="cedula_afi" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="nom_afi" class="col-md-4 control-label">Nombres Afiliado:</label>
	                <div class="col-md-7">
	                    <input id="nom_afi" maxlength="60" type="text" class="form-control input-sm" name="nom_afi" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="ape_afi" class="col-md-4 control-label">Apellidos Afiliado:</label>
	                <div class="col-md-7">
	                    <input id="ape_afi" maxlength="60" type="text" class="form-control input-sm" name="ape_afi" required>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="parentesco" class="col-md-4 control-label">Parentesco:</label>
	                <div class="col-md-7">
	                    <select id="parentesco" name="parentesco" class="form-control input-sm" required>
	                    	
                            <option @if(old('parentesco')== 'Titular') selected @endif value="Titular">Titular</option>
                            
                            <option @if(old('parentesco')== 'Conyugue') selected @endif value="Conyugue">Conyugue</option>
                            <option @if(old('parentesco')== 'Hijo(a)') selected @endif value="Hijo(a)">Hijo(a)</option>
                            
                            <option @if(old('parentesco')== 'Pariente') selected @endif value="Pariente">Pariente</option>
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="fecha_ing" class="col-md-4 control-label">Fecha Ingreso:</label>
	                <div class="col-md-7">
	                    
	                    <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text"  class="form-control input-sm" id="fecha_ing" name="fecha_ing" placeholder="AAAA/MM/DD" required >
                        </div>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="fecha_alt" class="col-md-4 control-label">Fecha Alta:</label>
	                <div class="col-md-7">
	                    
	                    <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text"  class="form-control input-sm" id="fecha_alt" name="fecha_alt" placeholder="AAAA/MM/DD" required >
                        </div>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="seguro" class="col-md-4 control-label">Tipo Seguro:</label>
	                <div class="col-md-7">
	                    <select id="seguro" name="seguro" class="form-control input-sm" required>
	                    	
	                    	@foreach($tipo_seguro as $value)
	                    	<option>{{$value->nombre}}</option>
	                    	@endforeach
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="seguro_priv" class="col-md-4 control-label">Seguro Privado:</label>
	                <div class="col-md-7">
	                    <select id="seguro_priv" name="seguro_priv" class="form-control input-sm" required>
	                    	<option>Ninguno</option>
	                    	@foreach($seguros as $value)
	                    	@if($value->tipo == 1)
	                    	<option>{{$value->nombre}}</option>
	                    	@endif
	                    	@endforeach
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="cob_comp" class="col-md-4 control-label">Cobertura Compartida:</label>
	                <div class="col-md-7">
	                    <select id="cob_comp" name="cob_comp" class="form-control input-sm" required>
	                    	<option @if(old('cob_comp')== 'Ninguna') selected @endif value="Ninguna">Ninguna</option>
                            <option @if(old('cob_comp')== 'Isspol') selected @endif value="Isspol">Isspol</option>
                            <option @if(old('cob_comp')== 'Issfa') selected @endif value="Issfa">Issfa</option>
                            
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="diagnostico" class="col-md-4 control-label">Diagnostico Principal:</label>
	                <div class="col-md-7">
	                    <input id="diagnostico" maxlength="100" type="text" class="form-control input-sm" name="diagnostico" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
	                <div class="col-md-7">
	                    <input id="mes_plano" maxlength="10" type="text" class="form-control input-sm" name="mes_plano" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="empresa" class="col-md-4 control-label">Empresa:</label>
	                <div class="col-md-7">
	                    <select id="empresa" name="empresa" class="form-control input-sm" required>
	                    	
	                    	@foreach($empresa as $value)
	                    	
	                    	@if($value->id == '0992704152001' || $value->id == '1307189140001')
	                    	<option>{{$value->nombrecomercial}}</option>
	                    	@endif
	                    	@endforeach
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="cod_deriva" class="col-md-4 control-label">Cod. Derivación:</label>
	                <div class="col-md-7">
	                    <select id="cod_deriva" name="cod_deriva" class="form-control input-sm" required>	
						@foreach($nombre as $value)
                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="pres_def" class="col-md-4 control-label">Dx Presuntivo o Def.:</label>
	                <div class="col-md-7">
	                    <input id="pres_def" maxlength="10" type="text" class="form-control input-sm" name="pres_def" required value="D">
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="cod_depen" class="col-md-4 control-label">Cod. Dependencia:</label>
	                <div class="col-md-7">
	                    <select id="cod_depen" name="cod_depen" class="form-control input-sm" required>
						@foreach($nombre_cod as $value)
                        <option value="{{$value->id}}">{{$value->nombre_cod}}</option>
                      @endforeach
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="nom_plantilla" class="col-md-4 control-label">Nombre de Plantilla:</label>
	                <div class="col-md-7">
	                    <input id="nom_plantilla" maxlength="40" type="text" class="form-control input-sm" name="nom_plantilla" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	               
	                <div class="col-md-7">
	                    <button type="submit" class="btn btn-primary">Guardar</button>
	                </div>
                </div>
                </div>
			  	
			</form>
	  	</div>
	</div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">



    $(function () {
        
        $('#fecha_nac').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        $('#fecha_ing').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        $('#fecha_alt').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        
    });

	 
		//ahora luego que autocompleta cambia porque le das click a uno de los que sale en la ventanita entonces captamos lo que clickeamos en este caso esta en nombre pero le pondremos el id y se usa otra funcion la de auto2 en el controlador

        $("#buscar").click( function(){ // le puse al id del boton que cuando de click haga eso siiiii
            $.ajax({
                type: 'get',
                url:"{{route('planilla.auto2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: {id_paciente: $("#cedula_ben").val()}, // lo enviamos como id_paciente 
                success: function(data){
                    // an *array* that contains the user
                    var seguro;
                    var sexo;
                    var estadoc;
                    var user = data[0];         // a simple user
                    if((user.sexo)==1){
                        sexo= 'HOMBRE';
                    }
                    else if((user.sexo)==2){
                        sexo= 'MUJER';
                    }
                               
                    var hoy = new Date(); //trae la fecha de hoy
                    var nacimiento= user.fecha; //tiene la fecha del paciente
                    var y = hoy.getFullYear(); //el año de hoy 
                    //console.log(y);
                    var res2= nacimiento.substr(0,4); //substring es quitar del index 0 hasta el 4   significa 2020/01/01 cuenta cuantro espacios por ejemplo 2020 tiene cuatrooo y asi guarda la variable
                    //console.log(res2);
                    var fe1= parseInt(y); //los hago enteros para restarlos
                    var fe2= parseInt(res2);
                    var edad= fe1-fe2; // y walaaaaaaaaaaaaaaaaaaaaaaaaaaa resto las dos fechas por ejemplo 2020-1997= 23 jajajaja D:                    
					var nombre_completo= user.nombre1+' '+user.nombre2; 
					var apellido_completo= user.apellido1+' '+user.apellido2;
					var nombre_completof= user.nombre1familiar+' '+user.nombre2familiar; 
					var apellido_completof= user.apellido1familiar+' '+user.apellido2familiar;
                   	$("#nombre").val(nombre_completo);
				   	$("#apellidosa").val(apellido_completo);
					$("#sexo").val(sexo); //ya habia hecho el if arriba si es 1 es honbre si es dos es mujer
				    $("#edad").val(edad);
					$("#hc").val(hcid);
					$("#fecha_nac").val(user.fecha);
					$("#cedula_afi").val(user.cedula);
					$("#nom_afi").val(nombre_completof);
					$("#ape_afi").val(apellido_completof);
					 //oiga una pregunta dime que es eso de fecha de alta e ingreso audiooo en que tabla está buenooo :'(
					//nos falta cedula del afiliado y todo del afiliado jeje en el query creo que no está omg cual es el afiliado será el familiar?
					//voy a comerrrrr
                     //la variable ya no se encuentra en user si no la creaste recien 
					//luego aca lo instancias con user.nombredelcontrolador
                },
                error: function(data){
                    console.log(data);
                }
            })
        });

</script>
@endsection