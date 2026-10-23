
<style type="text/css">
  .boton-2{
    font-size: 9px ;
    width: 100%;
    background-color: #004AC1; 
    color: white;
    text-align: center;
    height: 35px;
    padding-left: 5px;
    padding-right: 5px;
    padding-bottom: 0px; 
    padding-top: 7px; 
    margin-bottom: 5px; 
  } 
 
  .boton-buscar{
    font-size: 14px ;
    width: 70%; 
    height: 35px;
    background-color: #004AC1; 
    color: white;
    text-align: center;
    
  } 
 
  .btn_ordenes{ 
    font-size: 10px ;
    width: 100%;
    background-color: #004AC1;
    color: white;
    text-align: center;
    height: 22px;
    padding-left: 5px;
    padding-right: 5px;
    padding-bottom: 0px;
    padding-top: 2px;
    margin-bottom: 5px;
    

  }


  .btn_accion{
    font-size: 9px ;
    width: 95px;
    background-color: #004AC1;
    color: white;
    text-align: center;
    height: 20px;
    padding-left: 5px;
    padding-right: 5px;
    padding-bottom: 0px;
    padding-top: 2px;
    margin-bottom: 0px;

  }
  
  .recuadro{
      height: 200px;
      margin-bottom: 0px;
      

  }
  
  .cuerpo{
    font-size: 10px;
    font-weight: bold;

  }

  .fila1{
    background-color: #004AC1;
    height: 40px;
    color: white;
  }

  .fila2{
    background-color: #0081D5;
    height: 40px;
  }

  .fila3{
    background-color: #56ABE3;
    height: 40px;
  }

  .fila4{
    background-color: #004AC1;
    height: 40px;
  }

  .contenido_btn_ordenes{
    color: white; 
    height: 20px; 
    width: 20px;
    font-size: 12px;
  
  }
  .select2-selection__choice{
    background-color: red !important;
    border-color: red !important;
  }

  .btn-block{
      background-color: #004AC1;
    }
    .boton_burbuja{
      color: white;
      border-radius: 15px; 
      padding: 5px;
      margin: 2px;
      -moz-animation: 2s bote 1;
      animation: 2s bote 1;
      -webkit-transform: 2s bote 1;
    }
    .boton_burbuja span{
      color: white;
      margin: 20px;
    }
    @keyframes bote {
    20%, 50%, 80% {
      transform: translateY(0);
      -moz-transform: translateY(0);
      -webkit-transform: translateY(0);
    }

    40% {
      transform: translateY(-30px); 
      -moz-transform: translateY(-30px);      
      -webkit-transform: translateY(-30px);
    }

    65% {
      transform: translateY(-15px); 
      -moz-transform: translateY(-15px);      
      -webkit-transform:  translateY(-15px);
    }
  }
</style>

<style type="text/css"> 
	
  .parent{
	  	overflow-y:scroll;
     	height: 600px;
	}

	.parent::-webkit-scrollbar {
	    width: 8px;
	} 
	
  .parent::-webkit-scrollbar-thumb {
	    background: #004AC1;
	    border-radius: 10px;
	}
	
  .parent::-webkit-scrollbar-track {
		width: 10px;
	    background-color: #004AC1;
	    box-shadow: inset 0px 0px 0px 3px #56ABE3;
	}

</style>

<div id="area_index"  class="container-fluid" style="padding-left: 8px" >
  @if($agenda!=null)
  <div class="row">
		<div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px; ">
			<div class="col-md-12" style="border: 2px solid #004AC1; margin-left: 0px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px; background-color: #56ABE3">
				<h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
	        <img style="width: 35px; margin-left: 15px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/escudo.png"> 
	        <b>DETALLES DE FILIACIÓN</b>
          @if(!is_null($paciente)) 
            <center> 
              <div class="col-12" style="padding-bottom: 20px;">
                <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
                  <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                    {{$paciente->nombre1}} {{$paciente->nombre2}}
                  </b>
                </h1>
              </div> 
            </center>
          @endif 
        </h1>
        <form id="frm_filiacion">
        <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
        <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px"  >
			    	<div class="box-body" style="padding: 5px;">
				    	<div class="col-md-12" style="padding-left: 30px;padding-right: 30px;">
                <div class="row">
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Primer Nombre</span>
                    <br>
                    <input id="nombre1" type="text" onchange="guardar();" name="nombre1" required class="form-control input-sm" @if(!is_null($agenda->pnombre1)) value='{{$agenda->pnombre1}}' @endif >
                  </div>
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Segundo Nombre</span>
                    <br>
                    <input id="nombre2" type="text" onchange="guardar();" name="nombre2" required class="form-control input-sm" @if(!is_null($agenda->pnombre2)) value='{{$agenda->pnombre2}}' @endif >
                  </div>
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Primer Apellido</span>
                    <br>
                    <input id="apellido1" type="text" onchange="guardar();" name="apellido1" required class="form-control input-sm" @if(!is_null($agenda->papellido1)) value='{{$agenda->papellido1}}' @endif >
                  </div>
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Segundo Apellido</span>
                    <br>
                    <input id="apellido2" type="text" onchange="guardar();" name="apellido2" required class="form-control input-sm" @if(!is_null($agenda->papellido2)) value='{{$agenda->papellido2}}' @endif >
                  </div>
                </div>
				    	  <div class="row">
                
				    	  	<div class="col-md-3 {{ $errors->has('cortesia') ? ' has-error' : '' }}" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Cortesia</span>
                    <br>
                    <select id="cortesia_paciente_filiacion" name="cortesia_paciente_filiacion" class="form-control input-sm" required onchange="actualiza_cortesia_filiacion();" style="background-color: #ccffcc; font-size: 11px">
                        
                        @php 
                          $paciente_cort = Sis_medico\Cortesia_paciente::where('id', $paciente->id)->get()->first();
                        @endphp

                        @if(!is_null($paciente_cort))
                          <option @if($paciente_cort->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                          <option @if($paciente_cort->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                        @else
                          <option value="NO" selected >NO</option>
                          <option value="SI" >SI</option>
                        @endif
                      </select>  
                  </div>
                
                
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Sexo</span>
                    <br>
                    @if(!is_null($agenda->sexo))
                    <select name="sexo" id="sexo" onchange="guardar();" class="form-control input-sm" >
                       <option @if($agenda->sexo == 1) selected @endif value="1">Masculino</option> 
                       <option @if($agenda->sexo == 2) selected @endif value="2">Femenino</option> 
                    </select>
                     @endif
                  </div>
               
                
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Fecha nacimiento</span>
                    <br>
                    <input id="fecha_nacimiento" type="date" onchange="guardar();" name="fecha_nacimiento" class="form-control input-sm" @if(!is_null($agenda->fecha_nacimiento)) value='{{$agenda->fecha_nacimiento}}' @endif >
                  </div>
                
                
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Edad</span>
                    <br>
                   <input id="edad" type="text" name="edad" value='' class="form-control input-sm" readonly>
                  </div>
               
                </div>
                <div class="row">
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Estado Civil</span>
                    <br>
                    @if(!is_null($agenda->estadocivil))
                    <select class="form-control input-sm"  name="estadocivil" onchange="guardar();">
                       <option @if($agenda->estadocivil == 1) selected @endif value="1">Soltero</option> 
                       <option @if($agenda->estadocivil == 2) selected @endif value="2">Casado</option>
                       <option @if($agenda->estadocivil == 3) selected @endif value="3">Viudo</option> 
                       <option @if($agenda->estadocivil == 4) selected @endif value="4">Divorciado</option>
                       <option @if($agenda->estadocivil == 5) selected @endif value="5">Unión Libre</option> 
                       <option @if($agenda->estadocivil == 6) selected @endif value="6">Unión de Hecho</option> 
                    </select>
                     @endif
                  </div>
                 
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Ocupación</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="ocupacion" maxlength="50" @if(!is_null($agenda->ocupacion)) value="{{$agenda->ocupacion}}" @endif  >
                  </div>
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Trabajo</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="trabajo" maxlength="100" @if(!is_null($agenda->trabajo)) value="{{$agenda->trabajo}}" @endif >
                  </div>
                   
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Seguro</span>
                    <br>
                    <input class="form-control input-sm" type="text" name="seguro" value=@if(is_null($agenda->hcid))"{{$agenda->snombre}}"@else"{{$agenda->hsnombre}}"@endif readonly>
                  </div>
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Ciudad Procedencia</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="ciudad" maxlength="50" @if(!is_null($agenda->ciudad)) value="{{$agenda->ciudad}}" @endif >
                  </div>
                  
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Ciudad Nacimiento</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="lugar_nacimiento" maxlength="50" @if(!is_null($agenda->lugar_nacimiento)) value="{{$agenda->lugar_nacimiento}}" @endif>
                  </div>
                  
                  
                  <div class="col-md-3 div_tel" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Teléfono</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="telefono1" maxlength="30" autocomplete="off" @if(!is_null($agenda->telefono1)) value="{{$agenda->telefono1}}" @endif>
                  </div>
                  
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Celular</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" maxlength="30" autocomplete="off" type="text" name="telefono2" @if(!is_null($agenda->telefono2))  value="{{$agenda->telefono2}}" @endif >
                  </div>
                  
                  @php
                    $paciente = \Sis_medico\Paciente::find($agenda->id_paciente);
                  @endphp
                  
                  <div class="col-md-3" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Religi&oacuten</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="religion" maxlength="30" autocomplete="off" @if(!is_null($paciente->religion))  value="{{$paciente->religion}}" @endif >
                  </div>
                  
                  
                  <div class="col-md-9" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Dirección Domicilio</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="direccion" maxlength="200" autocomplete="off" @if(!is_null($agenda->direccion)) value="{{$agenda->direccion}}" @endif >
                  </div>
                  
                 
                  <div class="col-md-6 div_ema" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Correo Electronico</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="email" name="mail"  @if(!is_null($mail)) value="{{$mail}}" @endif autocomplete="off">
                  </div>
                  
                  
                  <div class="col-md-6" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Observación</span>
                    <br>
                     <input class="form-control input-sm" onchange="guardar();" type="text" name="observacion" autocomplete="off" placeholder="INGRESE OBSERVACIÓN MÉDICA DEL PACIENTE" style="background-color: #ffffb3;" @if(!is_null($agenda->observacion)) value="{{$agenda->observacion}}" @endif >
                  </div>
                  
                  
                  <div class="col-md-6" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Refererencia</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="referido" @if(!is_null($agenda->referido)) value="{{$agenda->referido}}" @endif>
                  </div>
                  
                  <div class="col-md-6" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Grupo Sanguineo</span>
                    <br>
                    
                    <select id="gruposanguineo" class="form-control" name="gruposanguineo" onchange="guardar();">
                      <option value="">Seleccionar ..</option>
                      <option @if($agenda->gruposanguineo == "AB-") selected @endif value="AB-">AB-</option>
                      <option @if($agenda->gruposanguineo == "AB+") selected @endif value="AB+">AB+</option>
                      <option @if($agenda->gruposanguineo == "A-") selected @endif value="A-">A-</option>
                      <option @if($agenda->gruposanguineo == "A+") selected @endif value="A+">A+</option>
                      <option @if($agenda->gruposanguineo == "B-") selected @endif value="B-">B-</option>
                      <option @if($agenda->gruposanguineo == "B+") selected @endif value="B+">B+</option>
                      <option @if($agenda->gruposanguineo == "O-") selected @endif value="O-">O-</option>
                      <option @if($agenda->gruposanguineo == "O+") selected @endif value="O+">O+</option>
                  </select> 
                  </div>
                  
                  <div class="col-md-6" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Transfusiones</span>
                    <br>
                    
                    <select id="transfusion" name="transfusion" class="form-control" onchange="guardar();  ">
                      <option @if($agenda->transfusion=='NO'){{'selected '}}@endif value="NO">NO</option>
                      <option @if($agenda->transfusion=='SI'){{'selected '}}@endif value="SI">SI</option>
                    </select>
                    
                  </div>
                 
                  
                  <div class="col-md-6" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Hábitos</span>
                    <br>
                    <input class="form-control input-sm" onchange="guardar();" type="text" name="alcohol" @if(!is_null($agenda->alcohol)) value="{{$agenda->alcohol}}" @endif>
                  </div>
                   
                  
                  <div class="col-md-12 " style="padding: 1px;">
                    <!--<div class="col-md-12" style="padding: 0px;">
                      <span style="font-family: 'Helvetica general';">Alergias</span>
                    </div>
                    <div class="col-md-12" style="padding: 0px;">
                    @if(!$alergiasxpac->isEmpty())
                      <select id="ale_list" name="ale_list[]" class="form-control" multiple style="width: 100%;">
                        @foreach($alergiasxpac as $ale_pac)
                        <option selected value="{{$ale_pac->id_principio_activo}}">{{$ale_pac->principio_activo->nombre}}</option>
                        @endforeach
                    </select>
                    @endif 
                    </div> -->   
                  </div>
                  
                  
                  <div class="col-md-12" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Vacunas</span>
                    <br>
                    <textarea class="form-control input-sm" name="vacuna" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();"> @if(!is_null($agenda->vacuna)) {{$agenda->vacuna}}  @endif</textarea>
                  </div>
                  
                  
                  <div class="col-md-4" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Antecedentes Patologicos</span>
                    <br>
                    <textarea name="antecedentes_pat" id="antecedentes_pat" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">@if(!is_null($agenda->antecedentes_pat))  {{$agenda->antecedentes_pat}} @endif</textarea>
                  </div>
                  
                  
                  <div class="col-md-4" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Antecedentes Familiares</span>
                    <br>
                    <textarea name="antecedentes_fam" id="antecedentes_fam" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();"> @if(!is_null($agenda->antecedentes_fam)) {{$agenda->antecedentes_fam}} @endif</textarea>
                  </div>
                  
                   
                  <div class="col-md-4" style="padding: 1px;">
                    <span style="font-family: 'Helvetica general';">Antecedentes Quirurgicos</span>
                    <br>
                    <textarea name="antecedentes_quir" id="antecedentes_quir" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">@if(!is_null($agenda->antecedentes_quir)) {{$agenda->antecedentes_quir}} @endif</textarea>
                  </div>
                  
                </div>
              </div>
            </div>
        </div>
        </form>
      </div>
		</div>
	</div>
  <script type="text/javascript">
     $(document).ready(function() {
        var edad;
        edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>');
        $('#edad').val( edad );
     });
  </script>
  @else
  <script type="text/javascript"> alert("!! PACIENTE NO POSEE HISTORIA CLINICA !!"); </script>
  @endif
</div>

 <script type="text/javascript">


  
     

      function actualiza_cortesia_filiacion(e){
          cortesia = document.getElementById("cortesia_paciente_filiacion").value;
          if (cortesia == "SI"){
              //alert('hola');
              act_cortesia_si();
          }
          else if(cortesia == "NO"){
              act_cortesia_no();
          }
      }  


   function guardar(){

        //alert("ok");
        $.ajax({
          type: 'post',
          url:"{{route('hc4.datos_filiacion')}}", //CombinadoController->ingreso
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_filiacion").serialize(),
          success: function(data){
            console.log(data);
            //alert(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            
            $('#edad').val( edad );
          },
          error: function(data){

            if(data.responseJSON.telefono1!=null){
                $('.div_tel').addClass('has-error');
                alert(data.responseJSON.telefono1[0]);
            }
            if(data.responseJSON.mail!=null){
                $('.div_ema').addClass('has-error');
                alert(data.responseJSON.mail[0]);
            }
            //console.log(data.responseJSON);
             
          }
        });
    }


        $('#ale_list').select2({
        placeholder: "Seleccione Medicamento...",
        minimumInputLength: 2,
        ajax: {
            url: '{{route('generico.find')}}',
            dataType: 'json',
            data: function (params) {
                //console.log(params);   
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                //console.log(data);
                return {
                    results: data
                };
            },
            cache: true
        },
        tags: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            return {
                id: term.toUpperCase()+'xnose',
                text: term.toUpperCase(),
                newTag: true, // add additional parameters
            }
        }
    });
    $('#ale_list').on('change', function (e) {
      //alert("hola");
      guardar();
    });
 </script>
