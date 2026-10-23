@extends('contable.estado_resultados.base')
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

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>


        <div class="box-body" style="background-color: #ffffff;">
            <form id="enviar_asiento"  class="form-vertical" role="form" method="POST" action="{{ route('librodiario.store') }}">
                {{ csrf_field() }}

                <!--Fecha de Asiento-->
                <div class="form-group col-xs-6{{ $errors->has('fecha_asiento') ? ' has-error' : '' }}">
                    <label for="fecha_asiento" class="col-md-4 control-label">Fecha de Asiento</label>
                    <div class="col-md-8">
                        <input id="fecha" type="text" class="form-control"  name="fecha_asiento" value="{{ old('fecha_asiento') }}"   required autofocus >
                        @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-xs-6{{ $errors->has('observacion') ? ' has-error' : '' }}">
                    <label for="observacion" class="col-md-4 control-label">Detalle</label>
                    <div class="col-md-8">
                        <input id="observacion" type="text" class="form-control"  name="observacion" value="{{ old('observacion') }}"    required autofocus >
                        @if ($errors->has('observacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                        @endif
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
                        <button type="submit"  class="btn btn-primary">
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
            url:"{{route('librodiario.buscar_asiento')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'nombre': $('#'+elemento).val(),
             'contador': contador},
            success: function(data){
              $('#contador').val(contador);
              document.getElementById("agregar_cuentas").insertRow(0).innerHTML = data;
            },
            error: function(data){
                console.log(data);
            }
        });
    }

    document.getElementById("enviar_asiento").addEventListener("submit", function(event){

        var contador =  $('#contador').val();
        var tdebe= 0;
        var thaber=0;
        if(contador <= 0){
            alert('No se puede agregar el asiento, no tiene ninguna cuenta');
            event.preventDefault();
            return false;
        }
        for (var i = 1; contador >= i; i++) {
            debe = parseInt($('#debe'+i).val());
            haber = parseInt($('#haber'+i).val());
            tdebe = tdebe + debe;
            thaber = thaber + haber;
        }
        if(tdebe != thaber){
            alert('No se puede agregar el asiento, los calculos del Debe y Haber no cuadran');
            event.preventDefault();
            return false;
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


</script>

</section>
@endsection
