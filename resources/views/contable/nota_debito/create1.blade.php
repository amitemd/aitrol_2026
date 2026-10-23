@extends('contable.nota_debito.base')
@section('action-content')
<style>
    .header{
        padding: 10px;
        margin-left: 15px;
        border-radius: 10px;
        margin-right: 15px;
        border: 1px solid #eee;
        box-shadow: 2px 2px 4px 1px;
        margin-bottom: 10px;
    }
    th{
        background-color:#ccc;
    }
    .form-group {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
</style>
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

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Contable</a></li>
    <li class="breadcrumb-item"><a href="#">Banco</a></li>
    <li class="breadcrumb-item"><a href="../notadebito">Nota de Debito</a></li>
    <li class="breadcrumb-item active" aria-current="page">Crear</li>
  </ol>
</nav>
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">Crear Nota de Debito</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>


        <div class="box-body" style="background-color: #ffffff;">
            <form id="form"  class="form-horizontal" role="form" >
                {{ csrf_field() }}

                <!--Fecha-->
                <div class="header row">
                    <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="id" class="col-md-2 control-label">Id</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control"  name="id" id="id" value="" >
                            @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-2 control-label">Tipo</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-ND" readonly>
                            @if ($errors->has('tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('asiento') ? ' has-error' : '' }}">
                        <label for="asiento" class="col-md-2 control-label">Asiento</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control"  name="asiento" id="asiento" value="" >
                            @if ($errors->has('asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('proyecto') ? ' has-error' : '' }}">
                        <label for="id" class="col-md-2 control-label">Proyecto</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" value="0000"  name="proyecto" id="proyecto" value="" >
                            @if ($errors->has('proyecto'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('proyecto') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('fecha_asiento') ? ' has-error' : '' }}">
                        <label for="fecha_asiento" class="col-md-2 control-label">Fecha</label>
                        <div class="col-md-10">
                            <input id="fecha" type="text" class="form-control"  name="fecha_asiento" value="{{ old('fecha_asiento') }}"   required autofocus >
                            @if ($errors->has('fecha_asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('banco') ? ' has-error' : '' }}">
                        <label for="id_banco" class="col-md-2 control-label">Banco</label>
                        <div class="col-md-10">
                            <select class="form-control" name="id_banco" id="id_banco" required>
                                <option value="">Seleccione...</option> 
                                @foreach($bancos as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                   
                    <div class="form-group col-xs-6{{ $errors->has('divisa') ? ' has-error' : '' }}">
                        <label for="id_divisa" class="col-md-2 control-label">Divisa</label>
                        <div class="col-md-10">
                        <select class="form-control" name="divisa" id="divisa" required>
                                @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('cambio') ? ' has-error' : '' }}">
                        <label for="cambio" class="col-md-2 control-label">Cambio</label>
                        <div class="col-md-10">
                            <input id="cambio" type="number" class="form-control"  value="1.00" name="cambio" required autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('divisa') ? ' has-error' : '' }}">
                        <label for="valor" class="col-md-2 control-label">Valor</label>
                        <div class="col-md-10">
                            <input id="valor" type="text" class="form-control"  name="valor" readonly autofocus >
                        </div>
                    </div>

                    <div class="form-group col-xs-12{{ $errors->has('observacion') ? ' has-error' : '' }}">
                        <label for="observacion" class="col-md-1 control-label">Concepto</label>
                        <div class="col-md-11">
                            <input id="observacion" type="text" class="form-control"  name="observacion" value="{{ old('observacion') }}"    required autofocus >
                            @if ($errors->has('observacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                </div>
                
                
                
                
                <div class="col-md-12 table-responsive">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="total" id="total" value="0">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="10%" class="" tabindex="0"># de Cuenta</th>
                                <th width="30%" class="" tabindex="0">Nombre</th>
                                <th width="20%" class="" tabindex="0">Debe</th>
                                <th width="20%" class="" tabindex="0">Haber</th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <tr>
                                <td>
                                    <select id="cuenta0" name="nombre"  class="form-control select2_cuentas" style="width: 100%;" onchange="agregar('cuenta0');">
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                            <option value="{{$value->id}}">{{$value->id}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select id="nombre0" name="nombre" class="form-control select2_cuentas" style="width: 100%;" onchange="agregar('nombre0');">
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="button"  class="btn btn-primary btn_add">
                            Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });
      });
        $(function () {
            $('#fecha').datetimepicker({
                format: 'YYYY/MM/DD',
                defaultDate: '{{date("Y-m-d")}}',
                });
      });

      
      $(".btn_add").click(function(){
        $(".btn_add").attr("disabled", true);
        $.ajax({
            url:"{{route('notadebito.store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
              console.log(data);
               $("#asiento").val(data.idasiento);
               $("#id").val(data.iddebito);
               
            },
            error: function(data){
                console.log(data);
            }
        });
      });
      
    function buscar_prod(){
        $.ajax({
            type: 'post',
            url:"{{route('contable_find_insumo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#codigo_prod"),
            success: function(data){
              console.log(data);
               if(data != 'no'){
                    //console.log("siii");
                    $('#codigo').val(data[0].codigo);
                    $('#nombre').val(data[0].nombre);
                    $('#descripcion').val(data[0].descripcion);
                    $('#stock_minimo').val(data[0].minimo);
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    };
    
    function agregar(elemento){
        var anterior =  $('#agregar_cuentas').html();
        var contador = $('#contador').val();
        contador = parseInt(contador) + 1;
        $.ajax({
            type: 'post',
            url:"{{route('notadebito.buscar_asiento')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'nombre': $('#'+elemento).val(),
             'contador': contador},
            success: function(data){
              $('#contador').val(contador);
              document.getElementById("agregar_cuentas").insertRow(contador-1).innerHTML = data;
            },
            error: function(data){
                console.log(data);
            }
        });
    }

    document.getElementById("enviar_nota").addEventListener("submit", function(event){

        var contador =  $('#contador').val();
        var tdebe= 0;
        var thaber=0;
        if(contador <= 0){
            alert('No se puede agregar el asiento, no tiene ninguna cuenta');
            event.preventDefault();
            return false;
        }
        for (var i = 1; contador >= i; i++) {
            debe = parseFloat($('#debe'+i).val());
            haber = parseFloat($('#haber'+i).val());
            tdebe = tdebe + debe;
            thaber = thaber + haber;
        }
        $('#total').val(tdebe);
    });
  function isNumberKey(evt)
   {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
   }

   function addvalue(){
       var total = 0;
       $(".debe").each(function() {
            total = parseFloat(total) + parseFloat($(this).val());
       });
       total = parseFloat(total).toFixed(2)
       $("#valor").val(total);
   }


</script>

</section>
@endsection
