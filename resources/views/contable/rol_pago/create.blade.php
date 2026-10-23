<style type="text/css">
  
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .info_nomina{
      width: 69%;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .datos_nomina
    {
      font-size: 0.8em;
    }

    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    #rol_pago{
      width: 100%;
      margin-bottom: 10px;
    }


    .info_nomina .col-xs-8 {
        padding-left:10px;
        font-size: 0.9em;
    }
    .info_nomina .round{
        padding-top:10px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .modal-body .form-group {
        margin-bottom: 0px;
    }

    .h3.modal_h3{
        font-family: 'BrixSansBlack';
        font-size: 8pt;
        display: block;
        background: #3d7ba8;
        color: #FFF;
        text-align: center;
        padding: 3px;
        margin-bottom: 5px;
        padding: 7px;
        font-size: 1em;
        margin-bottom: 15px;
    }
    .h3.modal_h3_2{
        margin-top: -20px !important;
        margin-bottom: 25px !important;
        padding: 7px;
        font-size: 1em;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }

    .separator1{
      width:100%;
      height:5px;
      clear: both;
    }

    
    /* Nuevo CSS*/

    .mLabel{
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 10px;
    }

    .texto {
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 15px;
    }

    .color_texto{
      color:#FFF;
    }

    .head-title{
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

</style>
<div class="modal-content">
        <div style="text-align: left" class="box-body dobra">
              <div class="col-md-9">
                <h3 class="box-title"></h3>
              </div>
              <div class="col-md-3">
                <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
              </div>
            <div class="titulo-wrapper">
                <label class="texto" for="title">{{$empresa->razonsocial}}</label>           
                <label class="texto">{{$empresa->direccion}}</label></br>
                <label class="texto">{{$empresa->id}}</label></br>
                <!--<div class="row head-title">
                  <div class="col-md-12">
                  <label class="color_texto" for="title">DATOS EMPLEADO</label>
                  </div>
                </div>-->
            </div>
            <!--<table id="rol_pago">
              <tr>
                <td class="info_nomina header">
                    <div class="round" >
                        <div class="col-md-12">
                           <div class="row">
                                <div class="col-xs-6">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label class="mLabel">NOMBRE:</label> 
                                        </div>
                                        <div class="col-xs-8">
                                            {{$usuario->apellido1}} @if($usuario->apellido2!='(N/A)'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} @if($usuario->nombre2!='(N/A)'){{$usuario->nombre2}}@endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6">     
                                    <div class="row">
                                        <div class="col-xs-4">
                                    <label class="mLabel">CÉDULA:</label>  
                                        </div>
                                        <div class="col-xs-8">
                                    @if(!is_null($registro->id_user)){{$registro->id_user}}@endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="row">
                                        <div class="col-xs-4">
                                    <label class="mLabel">ÁREA:</label>  
                                        </div>
                                        <div class="col-xs-8">
                                    @if(!is_null($registro->area)){{$registro->area}}@endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6">     
                                    <div class="row">
                                        <div class="col-xs-4">
                                    <label class="mLabel">CARGO:</label>  
                                        </div>
                                        <div class="col-xs-8">
                                    @if(!is_null($registro->cargo)){{$registro->cargo}}@endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
              </tr>
            </table>-->
        </div>
        <div class="box-body dobra">
            <div class="row head-title">
                  <div class="col-md-12">
                  <label class="color_texto" for="title">DETALLES ROL PAGO</label>
                  </div>
            </div>
            <form id="guardar_rol_pago" method="post">
                <input  name="id_nomina" id="id_nomina" type="text" class="hidden" value="@if(!is_null($registro->id)){{$registro->id}}@endif">
                <input  name="id_user" id="id_user" type="text" class="hidden" value="@if(!is_null($registro->id_user)){{$registro->id_user}}@endif">
                <input  name="id_empresa" id="id_empresa" type="text" class="hidden" value="@if(!is_null($registro->id_empresa)){{$registro->id_empresa}}@endif">
                <input  name="val_aport_personal" id="val_aport_personal" type="text" class="hidden" value="@if(!is_null($val_aport_pers->valor)){{$val_aport_pers->valor}}@endif">
                
                <!--Anio-->
                <div class="form-group  col-xs-4">
                  <label for="year" class="col-md-2 texto">Año</label>
                  <div class="col-md-12">
                    <select id="year" name="year" class="form-control">
                        <option value="">Seleccione...</option>  
                        <?php
                          for($i=2019;$i<=2030;$i++)
                          {
                           echo "<option value='".$i."'>".$i."</option>";
                          }
                        ?>
                    </select>
                  </div>
                </div>
                <!--Mes-->
                <div class="form-group  col-xs-4">
                  <label for="mes" class="col-md-2 texto">Mes</label>
                  <div class="col-md-12">
                    <select id="mes" name="mes" class="form-control">
                        <?php    
                          $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                          for ($i=1; $i<=12; $i++) {
                               if ($i == date('m'))
                          echo '<option value="'.$i.'"selected>'.$Meses[($i)-1].'</option>';
                               else
                          echo '<option value="'.$i.'">'.$Meses[($i)-1].'</option>';
                               }
                        ?>
                    </select>
                  </div>
                </div>
                <!--Tipo Rol -->
                <div class="form-group  col-xs-4">
                  <label for="tipo_rol" class="col-md-2 texto">Tipo</label>
                  <div class="col-md-12">
                    <select id="tipo_rol" name="tipo_rol" class="form-control">
                      <option>Seleccione...</option>
                      @foreach($ct_tipo_rol as $value)
                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                      @endforeach 
                    </select>
                  </div>
                </div>

                <div class="separator"></div>
                <!--Sueldo Mensual-->
                <div class="form-group  col-xs-12">
                  <label for="sueldo_mensual" class=" col-md-2 texto">Sueldo Mensual:</label>
                  <div class="col-md-8">
                    <input id="sueldo_mensual" name="sueldo_mensual" type="text" class="form-control" value="@if(!is_null($registro)){{$registro->sueldo_neto}}@endif" onkeypress="return isNumberKey(event)"  onblur="checkformat(this);calculo_porcentaje_iess()">
                  </div>
                </div>
                <div class="separator1"></div>
                <!--Cantidad Horas al 50%-->
                <div class="form-group  col-xs-6">
                  <label for="cant_horas_50" class="col-md-5 texto">Cantidad Horas 50%:</label>
                  <div class="col-md-7">
                    <input id="cant_horas_50" name="cant_horas_50" type="text" class="form-control" value="0"  onkeypress="return isNumberKey(event)" onblur="calculo_horas_50();calculo_porcentaje_iess()">
                  </div> 
                </div>
                <!--Sobre Tiempo 50%-->
                <div class="form-group  col-xs-6">
                  <label for="sobre_tiempo_50" class="col-md-5 texto">Horas al 50%:</label>
                  <div class="col-md-7">
                    <input id="sobre_tiempo_50" name="sobre_tiempo_50" type="text" class="form-control" value="0.00"  onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                  </div>
                </div>
                <div class="separator1"></div>
                <!--Cantidad Horas al 100%-->
                <div class="form-group  col-xs-6">
                  <label for="cant_horas_100" class="col-md-5 texto">Cantidad Horas 100%:</label>
                  <div class="col-md-7">
                    <input id="cant_horas_100" name="cant_horas_100" type="text" class="form-control" value="0"  onkeypress="return isNumberKey(event)" onblur="calculo_horas_100();calculo_porcentaje_iess()"> 
                  </div>
                </div>
                <!--Sobre Tiempo 100%-->
                <div class="form-group  col-xs-6">
                  <label for="sobre_tiempo_100" class="col-md-5 texto">Horas al 100%:</label>
                  <div class="col-md-7">
                    <input id="sobre_tiempo_100" name="sobre_tiempo_100" type="text" class="form-control" value="0.00"  onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                  </div>
                </div>
                <div class="separator1"></div>
                <!--iess-->
                <div class="form-group  col-xs-12">
                  <label for="iess" class="col-md-2 texto">Aporte Personal IESS:</label>
                  <div class="col-md-8">
                    <input id="iess" name="iess" type="text" class="form-control" value="0.00"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
                  </div>
                </div>
                <div class="separator1"></div>
                <!--atrasos-->
                <div class="form-group  col-xs-12">
                  <label for="atrasos" class="col-md-2 texto">Atrasos:</label>
                  <div class="col-md-8">
                    <input id="atrasos" name="atrasos" type="text" class="form-control" value="0.00"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
                  </div>
                </div>
                <div class="separator1"></div>
                <!--Anticipo_quincena-->
                <div class="form-group  col-xs-12">
                  <label for="anticipo_quincena" class="col-md-2 texto">Anticipo 1 era Quincena:</label>
                  <div class="col-md-8">
                    <input id="anticipo_quincena" name="anticipo_quincena" type="text" class="form-control" value="0.00"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
                  </div>
                </div>
            </form>
        </div>
        <div class="separator1"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
            <button type="button" onclick="guardar_rol();" class="btn btn-primary">CREAR</button>
        </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>


<script type="text/javascript">

    
    $(document).ready(function(){
        
      limpiar_datos();
      calculo_porcentaje_iess();
        
    });

    
    function guardar_rol(){

          var desc_mes = 0;

          var formulario = document.forms["guardar_rol_pago"];
          var sueldo_mensual = formulario.sueldo_mensual.value;
          var year = formulario.year.value;
          var mes = parseInt(formulario.mes.value);

          var tipo_rol = formulario.tipo_rol.value;

          switch(mes){

            case 1:
              desc_mes = 'Enero';
              break;

            case 2: 
              desc_mes = 'Febrero';
              break;

            case 3: 
              desc_mes = 'Marzo';
              break;

            case 4: 
              desc_mes = 'Abril';
              break;

            case 5: 
              desc_mes = 'Mayo';
              break;

            case 6: 
              desc_mes = 'Junio';
              break;

            case 7: 
              desc_mes = 'Julio';
              break;

            case 8: 
              desc_mes = 'Agosto';
              break;

            case 9: 
              desc_mes = 'Septiembre';
              break;

            case 10: 
              desc_mes = 'Octubre';
              break;

            case 11: 
              desc_mes = 'Noviembre';
              break;

            case 12: 
              desc_mes = 'Diciembre';
              break;

            //default: 
            //break;
          }

          //var sobre_tiempo_50 = formulario.sobre_tiempo_50.value;
          //var sobre_tiempo_100 = formulario.sobre_tiempo_100.value;
         

          var msj = "";


          if(sueldo_mensual == ""){
        
              msj = msj + "Por favor, Ingrese el Sueldo Mensual<br/>";
          }

          if(year == ""){
              msj = msj + "Por favor, Ingrese el año<br/>";
          }
          if(mes == ""){
              msj = msj + "Por favor, Ingrese el mes<br/>";
          }
          if(tipo_rol == ""){
              msj = msj + "Por favor, seleccione el tipo de Rol<br/>";
          }


    
          /*if(sobre_tiempo_50 == ""){
              msj = msj + "Por favor, Ingrese el sobre Tiempo 50%<br/>";
          }
          if(sobre_tiempo_100 == ""){
              msj = msj + "Por favor, Ingrese el sobre Tiempo 100%<br/>";
          }*/
          

          if(msj != ""){
            
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
          }

          //if(msj==""){
            $.ajax({
                    type: 'post',
                    url:"{{route('rol_pago.store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $('#guardar_rol_pago').serialize(),
                    success: function(data){
                      //console.log(data);

                      if(data.msj =='ok'){
                          
                        swal("Error!","Ya existe un rol creado en el año"+" : "+year+" "+"mes"+" : "+desc_mes,"error" );

                      }else{

                        swal("Correcto!","Se creo el rol de pago correctamente");
                        window.open("{{asset('/comprobante/rol/pago')}}/"+data.id_rol_pago, '_blank ');
                        location.href="{{route('nomina.index')}}";
                      }
                      
                  
                    },
                    error: function(data){
                        console.log(data);
                    }
            })

          //}else{
              //alert(msj);
          //}
         
    }  

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) { 


      var test = entry.value;

      if (!isNaN(test)) {
          entry.value=parseFloat(entry.value).toFixed(2);
      }
      
      if (isNaN(entry.value) == true){      
          entry.value='0.00';        
      }
      if (test < 0) {
 
          entry.value = '0.00';
      }
    }


    function limpiar_datos(){

      //$('#sueldo_mensual').val(" ");
      //$('#sobre_tiempo_50').val(" ");
      //$('#sobre_tiempo_100').val(" ");
      //$('#iess').val(" ");
      //$('#atrasos').val(" ");
      //$('#anticipo_quincena').val(" ");
    }


    function calculo_porcentaje_iess(){

      sueldo_mensual  =  0;
      Sobre_Tiempo_50  =  0;
      Sobre_Tiempo_100  =  0;
      val_aport_per  =  0;

      sum_total = 0;

      calculo_aporte_iess  = 0;

      sueldo_mensual = parseFloat($('#sueldo_mensual').val());
      Sobre_Tiempo_50 = parseFloat($('#sobre_tiempo_50').val());
      Sobre_Tiempo_100 = parseFloat($('#sobre_tiempo_100').val());
      val_aport_per = parseFloat($('#val_aport_personal').val());


      sum_total = (sueldo_mensual)+(Sobre_Tiempo_50)+(Sobre_Tiempo_100);

      calculo_aporte_iess = ((sum_total)*(val_aport_per))/100;

      //calculo_aporte_iess = (sum_total)*(0.0945);

      if(!isNaN(calculo_aporte_iess))
      {  
        $('#iess').val(calculo_aporte_iess.toFixed(2));
      }

      //$('#iess').val(calculo_aporte_iess.toFixed(2));

    }

    function calculo_horas_50(){

      sueldo_mensual  =  0;
      cal_hor_50 = 0; 
      cant_hor_50 = 0; 

      sueldo_mensual = parseFloat($('#sueldo_mensual').val());

      cant_hor_50 = parseFloat($('#cant_horas_50').val());

      //cal_hor_50 = (sueldo_mensual/240)*(1.5)*(cant_hor_50);
      //Calculo de Horas Extras al 50%
      cal_hor_50 = (((sueldo_mensual/30)/8) *(1.50))*(cant_hor_50);

      if(!isNaN(cal_hor_50))
      {  
        $('#sobre_tiempo_50').val(cal_hor_50.toFixed(2));
      }
    
    }

    function calculo_horas_100(){

      sueldo_mensual  =  0;
      cal_hor_100 = 0;
      cal_hor_100 = 0;

      sueldo_mensual = parseFloat($('#sueldo_mensual').val());

      cant_hor_100 = parseFloat($('#cant_horas_100').val());

      //cal_hor_100  = (sueldo_mensual/240)*(2)*(cant_hor_100);
      //Calculo de Horas Extras al 100%
      cal_hor_100 = (((sueldo_mensual/30)/8) *(2))*(cant_hor_100);

      if(!isNaN(cal_hor_100))
      {  
        $('#sobre_tiempo_100').val(cal_hor_100.toFixed(2));
      }
    
    }






</script>










