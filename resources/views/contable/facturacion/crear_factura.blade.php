<style type="text/css">

    .alerta_correcto{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }

    .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }

</style>
<div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
       Guardado Correctamente
</div>
<div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Ingrese Cedula y Ruc
</div>
<div class="box box-warning box-solid">
    <div class="header box-header with-border" >
        <div class="box-title col-md-12" ><b style="font-size: 16px;">CREAR FACTURA</b></div>
    </div>

    <div class="box-body">

        <form class="form-vertical" id="crear_form">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <input type="hidden" name="empresa" id="empresa" value="{{$empresa->id}}">

            <div class="alert alert-danger oculto">
                <ul id="ms_error"></ul>
            </div>

            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="factura" class="col-md-12 col-xs-12 control-label">Factura</label>
                <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="suc" id="suc"
                    value="@if($id_suc != 0){{$id_suc}}@endif" placeholder="001" maxlength="3" >
                </div>

                <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="caj" id="caj" value="@if($id_caj != 0){{$id_caj}}@endif" placeholder="001" maxlength="3">
                </div>

                <div class="col-md-6 col-xs-6 input-group" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="factura" id="factura" value="@if($id_factura != 0){{$id_factura}}@endif" placeholder="17439"  maxlength="10">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('suc').value = '';document.getElementById('caj').value = '';document.getElementById('factura').value = '';"></i>
                    </div>
                </div>

            </div>
            <div class="form-group col-md-12 col-xs-12"></div>
            <!--cedula-->
            <div class="col-md-6 col-xs-6" style="padding-left: 2px;padding-right: 2px;">
                <label for="id" class="control-label">Cédula</label>
                <div class="input-group">
                    <input id="idpaciente" maxlength="10" type="text" class="form-control" name="idpaciente"
                    value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('idpaciente').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Nombre Paciente-->
            <div class="col-md-6 col-xs-6 {{ $errors->has('nombres') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;" >
                <label for="nombres" class="control-label">Paciente</label>
                <div class="input-group">
                    <input  type="text" class="form-control input-sm" name="nombres" id="nombres"
                    value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
                    </div>
                    @if ($errors->has('nombres'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombres') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <!--seguro-->
            <div class="col-md-7 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="id_seguro" class="control-label">Seguro</label>
                <select class="form-control input-sm" name="id_seguro" id="id_seguro">
                    <option value="">Seleccione ...</option>
                    @foreach($seguros as $seguro)
                        <option @if($paciente != Array()) @if($paciente->id_seguro == $seguro->id) selected @endif @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <!--Division Doble-->
            <div class="form-group col-md-12 col-xs-12"></div>
            <!--Ruc/Cedula-->
            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;">
                <label for="cedula" class="control-label">Ruc/Cédula</label>
                <div class="input-group">
                    <input id="cedula" maxlength="15" type="text" class="form-control" name="cedula"
                    value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@elseif($id_cliente != 0){{$id_cliente}}@else{{old('cedula')}}@endif" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Razon Social-->
            <div class="form-group col-md-8 col-xs-6 {{ $errors->has('razon_social') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;">
                <label for="razon_social" class="control-label">Cliente</label>
                <div class="input-group">
                    <input type="text" class="form-control input-sm" name="razon_social" id="razon_social"
                    value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre_representante}}@endif" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Ciudad-->
            <div class="form-group col-md-4 col-xs-6 {{ $errors->has('ciudad') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;">
                <label for="ciudad" class="control-label">Ciudad</label>
                <div class="input-group">
                    <input  type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Direccion-->
            <div class="form-group col-md-8 col-xs-6 {{ $errors->has('direccion') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;" >
                <label for="direccion" class="control-label">Dirección</label>
                <div class="input-group">
                    <input  type="text" class="form-control input-sm" name="direccion" id="direccion" value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Telefono-->
            <div class="form-group col-md-4 col-xs-6 {{ $errors->has('telefono') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;" >
                <label for="telefono" class="control-label">Teléfono</label>
                <div class="input-group">
                    <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                    </div>
                </div>
            </div>
            <!--Email-->
            <div class="form-group col-md-8 col-xs-6 {{ $errors->has('email') ? ' has-error' : '' }}" style="padding-left: 2px;padding-right: 2px;" >
                <label for="email" class="control-label">Mail</label>
                <div class="input-group">
                    <input type="text" class="form-control input-sm" name="email" id="email" value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif" placeholder="Mail" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                    </div>
                </div>
            </div>
        </form>

        <div class="form-group col-xs-6">
            <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-primary" onclick="crear_factura()">
                Crear Factura
                </button>
            </div>
        </div>
    </div>


</div>

<script type="text/javascript">

    //Funcion para obtener el numero de cedula
    function buscar()
    {
        vcedula = document.getElementById("idpaciente").value;
        vcedula_ruc = document.getElementById("cedula").value;
        vid_empresa = document.getElementById("empresa").value;
        v_suc = document.getElementById("suc").value;
        v_caj = document.getElementById("caj").value;
        v_factura = document.getElementById("factura").value;

        //alert(vid_empresa);
        //alert(vcedula_ruc);

         //if((vcedula != "")&&(vcedula_ruc!= "")){

            $.ajax({
            type: 'get',
            url: "{{url('contable/paciente/crear')}}/"+ vcedula+'/'+vcedula_ruc+'/'+vid_empresa+'/'+v_suc+'/'+v_caj+'/'+v_factura,
            success: function(data){
                //console.log(data);

                    $('#work').empty().html(data);
                    $('#work').removeClass( "col-md-12" );
                    $('#work').addClass( "col-md-6" );
                    $('#data').removeClass( "col-md-12" );
                    $('#data').addClass( "col-md-6" );


                }
            })

        // }else{

          //  $("#msj_ingreso").fadeIn(1000);
          //  $("#msj_ingreso").fadeOut(3000);

        //}


    }


    /*$(function () {
    });*/


    function crear_factura(){

        $('#suc').parent().removeClass('has-error');
        $('#caj').parent().removeClass('has-error');
        $('#factura').parent().removeClass('has-error');
        $('#idpaciente').parent().removeClass('has-error');
        $('#cedula').parent().removeClass('has-error');
        $('#ms_error').parent().hide();
        $('#ms_error').empty().html('');


        $.ajax({
            type: 'post',
            url:"{{route('factura.store_contable')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data){
                //console.log(data);
                $("#alerta_guardado").fadeIn(1000);
                $("#alerta_guardado").fadeOut(3000);
            },
            error: function(data){
                //console.log(data);
                    if(data.responseJSON!=null){
                        if(data.responseJSON.suc!=null){
                            $('#suc').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.suc[0]+'</li>');
                        }
                        if(data.responseJSON.caj!=null){
                            $('#caj').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.caj[0]+'</li>');
                        }
                        if(data.responseJSON.factura!=null){
                            $('#factura').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.factura[0]+'</li>');
                        }
                        if(data.responseJSON.idpaciente!=null){
                            $('#idpaciente').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.idpaciente[0]+'</li>');
                        }
                        if(data.responseJSON.cedula!=null){
                            $('#cedula').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.cedula[0]+'</li>');
                        }

                    }
                }
        })
    }

</script>
