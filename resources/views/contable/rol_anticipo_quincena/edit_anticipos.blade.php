@extends('contable.rol_anticipo_quincena.base')
@section('action-content')

<style type="text/css">

  
    .separator{
        width:100%;
        height:30px;
        clear: both;
    }

    .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }

 
</style>

<script type="text/javascript">
    
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

    function goBack() {
      window.history.back();
    }


</script>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Actualizar Valor Anticipo</h3>
            </div>
            <div class="col-md-1 text-right">
                <button id="act_valor" onclick="actualizar_valor_anticip()" class="btn btn-primary btn-gray">
                   Guardar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
          @php
            $user = Sis_medico\User::find($anticip_quincena->id_user);
            $obtener_nombre = Sis_medico\Empresa::find($anticip_quincena->id_empresa);

            if($anticip_quincena->quincena == '1'){
              $mes_anticip = 'Enero';
            }elseif($anticip_quincena->quincena == '2'){
              $mes_anticip = 'Febrero';
            }elseif($anticip_quincena->quincena == '3'){ 
              $mes_anticip = 'Marzo';
            }elseif($anticip_quincena->quincena == '4'){ 
              $mes_anticip = 'Abril';
            }elseif($anticip_quincena->quincena == '5'){ 
              $mes_anticip = 'Mayo';
            }elseif($anticip_quincena->quincena == '6'){ 
              $mes_anticip = 'Junio';
            }elseif($anticip_quincena->quincena == '7'){ 
              $mes_anticip = 'Julio';
            }elseif($anticip_quincena->quincena == '8'){ 
              $mes_anticip = 'Agosto';
            }elseif($anticip_quincena->quincena == '9'){ 
              $mes_anticip = 'Septiembre';
            }elseif($anticip_quincena->quincena == '10'){ 
              $mes_anticip = 'Octubre';
            }elseif($anticip_quincena->quincena == '11'){ 
              $mes_anticip = 'Noviembre';
            }elseif($anticip_quincena->quincena == '12'){ 
              $mes_anticip = 'Diciembre';
            }
   
          @endphp
            <form class="form-vertical"  id="actualiza_val_anticipo" role="form" method="POST" autocomplete="off">
                {{ csrf_field() }}
                <input type="hidden" name="id_anticip_val" id="id_anticip_val" value="{{$anticip_quincena->id}}">
                
                <div class="separator"></div>
                <div class="box-body col-xs-24">

                      <div class="clearfix"></div>
                      <!--Identificacion-->
                      <div class="form-group col-xs-6">
                          <label for="identificacion" class="col-md-4 texto">Identificaci&oacute;n</label>
                          <div class="col-md-7">
                              <input id="identificacion"  type="text" class="form-control"  name="identificacion" value="@if(!is_null($user->id)){{$user->id}}@endif" readonly>
                          </div>
                      </div>
                      <!--primer nombre-->
                      <div class="form-group col-xs-6">
                          <label for="nombre1" class="col-md-4 texto">Primer Nombre</label>
                          <div class="col-md-7">
                              <input id="nombre1" type="text" class="form-control" name="nombre1" value="@if(!is_null($user)){{$user->nombre1}}@endif" readonly>
                          </div>
                      </div>
                      <!--segundo nombre-->
                    <div class="form-group col-xs-6">
                        <label for="nombre2" class="col-md-4 texto">Segundo Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre2" type="text" class="form-control" name="nombre2" value="@if(!is_null($user)){{$user->nombre2}}@endif" readonly>
                        </div>
                    </div>
                    <!--primer apellido-->
                    <div class="form-group col-xs-6">
                        <label for="apellido1" class="col-md-4 texto">Primer Apellido</label>
                        <div class="col-md-7">
                            <input id="apellido1" type="text" class="form-control" name="apellido1" value="@if(!is_null($user)){{$user->apellido1}}@endif" readonly>
                        </div>
                    </div>
                    <!--Segundo apellido-->
                    <div class="form-group col-xs-6">
                        <label for="apellido2" class="col-md-4 texto">Segundo Apellido</label>
                        <div class="col-md-7">
                            <input id="apellido2" type="text" class="form-control" name="apellido2" value="@if(!is_null($user)){{$user->apellido2}}@endif" readonly>
                        </div>
                    </div>
                    <!--Empresa-->
                    <div class="form-group col-xs-6">
                        <label for="empresa" class="col-md-4 texto">Empresa</label>
                        <div class="col-md-7">
                            <input id="empresa" type="text" class="form-control" name="empresa" value="@if(!is_null($obtener_nombre)){{$obtener_nombre->nombrecomercial}}@endif" readonly>
                        </div>
                    </div>
                    <!--Ano-->
                    <div class="form-group col-xs-6">
                        <label for="anio" class="col-md-4 texto">Año</label>
                        <div class="col-md-7">
                            <input id="anio" type="text" class="form-control" name="anio" value="@if(!is_null($anticip_quincena)){{$anticip_quincena->anio}}@endif" readonly>
                        </div>
                    </div>
                    <!--Mes-->
                    <div class="form-group col-xs-6">
                        <label for="mes" class="col-md-4 texto">Mes</label>
                        <div class="col-md-7">
                            <input id="mes" type="text" class="form-control" name="mes" value="@if(!is_null($mes_anticip)){{$mes_anticip}}@endif" readonly>
                        </div>
                    </div>
                    <!--Sueldo Mensual-->
                    <div class="form-group col-xs-6">
                        <label for="sueldo_mensual" class="col-md-4 texto">Sueldo Mensual</label>
                        <div class="col-md-7">
                            <input id="sueldo_mensual" type="text" class="form-control" name="sueldo_mensual" value="@if(!is_null($obtener_sueldo)){{$obtener_sueldo->sueldo_neto}}@endif" readonly>
                        </div>
                    </div>
                    <!--% Porcentaje-->
                    <div class="form-group col-xs-6 cl_porcentaje_quincena">
                        <label for="porcentaje_quincena" class="col-md-4 texto">% Porcentaje Quincena</label>
                        <div class="col-md-7">
                            <input id="porcentaje_quincena" type="text" class="form-control" name="porcentaje_quincena" value="@if(!is_null($anticip_quincena)){{$anticip_quincena->porcentaje}}@endif">
                            <span class="help-block">
                                <strong id="str_porcentaje_quincena"></strong>
                            </span>
                        </div>
                    </div>
                    <!--Valor Anticipo-->
                    <div class="form-group col-xs-6">
                        <label for="valor_anticipo" class="col-md-4 texto">Valor Anticipo</label>
                        <div class="col-md-7">
                            <input id="valor_anticipo" type="text" class="form-control" name="valor_anticipo" value="@if(!is_null($anticip_quincena)){{$anticip_quincena->valor_anticipo}}@endif" readonly>
                        </div>
                    </div>
                     
                </div>
                <div class="separator"></div>
            </form>
        </div>
    </div>
   
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

    function actualizar_valor_anticip(){

      $('.cl_porcentaje_quincena').removeClass('has-error');
      $('#str_porcentaje_quincena').text('');

        $.ajax({
          type: 'post',
          url:"{{route('anticipos_quincena.update')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#actualiza_val_anticipo").serialize(),
          success: function(data){
            
            location.href ="{{route('nomina_anticipos.index')}}";

          },
          error: function(data){

              if(data.responseJSON.porcentaje_quincena!=null){
              $('.cl_porcentaje_quincena').addClass('has-error');
              $('#str_porcentaje_quincena').text(data.responseJSON.porcentaje_quincena);
              }

          }
      
        });

    }

  </script>

</section>
@endsection
