@extends('contable.rh_configuracion_valores.base')
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

<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('config_valor.index')}}">Aporte y Salario</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>CREAR APORTES Y SALARIOS</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button id="crear_rol_pago" onclick="guardar_configuracion_valor()" class="btn btn-primary btn-gray">
                   Guardar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <form class="form-vertical"  id="crear_conf_val" role="form" method="POST" autocomplete="off">
            {{ csrf_field() }}
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--Tipo-->
                <div class="form-group col-xs-12 cl_tipo" style="margin: 20px">
                    <label for="tipo" class="col-md-2 texto">Tipo</label>
                    <div class="col-md-7">
                        <select id="tipo" name="tipo" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach($tipo_aport as $value)
                            <option value="{{$value->id}}">{{$value->descripcion}}</option>
                        @endforeach
                        </select>
                        <span class="help-block">
                            <strong id="str_tipo"></strong>
                        </span>
                    </div>
                </div>
                <!--Valor-->
                <div class="form-group col-xs-12 cl_valor" style="margin: 20px">
                    <label for="valor" class="col-md-2 texto">Valor</label>
                    <div class="col-md-7">
                        <input id="valor" type="text" class="form-control" name="valor" value="{{ old('valor') }}" onkeypress="return isNumberKey(event)"  onblur="checkformat(this);">
                        <span class="help-block">
                            <strong id="str_valor"></strong>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="separator"></div>
        </form>
    </div>
   
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

    $(document).ready(function(){

       $('.select2_cuentas').select2({
            tags: false
        });

    });


     function guardar_configuracion_valor(){

            //$('.cl_id_empresa').removeClass('has-error');
            //$('#str_id_empresa').text('');

            $('.cl_tipo').removeClass('has-error');
            $('#str_tipo').text('');

            $('.cl_valor').removeClass('has-error');
            $('#str_valor').text('');



            $.ajax({
                type: 'post',
                url:"{{route('config_valor.store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#crear_conf_val").serialize(),
                success: function(data){
                    //console.log();
                    //$("#alerta_datos").fadeIn(1000);
                    //$("#alerta_datos").fadeOut(3000);

                    location.href ="{{route('config_valor.index')}}";

                    //$("#id_empresa").val("");
                    //$("#tipo").val("");
                    //$("#valor").val("");


                   
                },
                error: function(data){

                   /*if(data.responseJSON.id_empresa!=null){
                    $('.cl_id_empresa').addClass('has-error');
                    $('#str_id_empresa').text(data.responseJSON.id_empresa);
                   }*/

                   if(data.responseJSON.tipo!=null){
                    $('.cl_tipo').addClass('has-error');
                    $('#str_tipo').text(data.responseJSON.tipo);
                   }

                   if(data.responseJSON.valor!=null){
                    $('.cl_valor').addClass('has-error');
                    $('#str_valor').text(data.responseJSON.valor);
                   }

                  

                }
            
            });

        }


     
</script>

</section>
@endsection
