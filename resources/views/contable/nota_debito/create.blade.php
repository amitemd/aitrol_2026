@extends('contable.nota_debito.base')
@section('action-content')
<script type="text/javascript">
    function check(e) {
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
        //window.location.reload(history.back());
    }

    function goNew() {
        location.href = "{{route('notadebito.crear')}}";

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
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-8">
                <h3 class="box-title">Crear Nota de D&eacute;bito</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('notadebito.imprimir', ['id' => 3]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i><!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>


        <div class="box-body dobra">
            <form id="form" class="form-horizontal" role="form">
                {{ csrf_field() }}

                <!--Fecha-->
                <div class="header row">
                    <div class="form-group col-xs-6 col-md-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">Id</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="">
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">N&uacute;mero</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="">
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">Tipo</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-ND" readonly>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Asiento</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" value="">
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">Proyecto2</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="">
                            @if ($errors->has('proyecto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('proyecto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">Fecha</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input id="fecha" type="date" class="form-control validar" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" name="fecha_asiento" value="">
                            </div>
                        </div>
                        <div class="form-group col-xs-4  px-1">
                            <div class="col-md-12 px-0">
                                <label for="observacion" class="label_header">{{ trans('contableM.concepto') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="observacion" type="text" class="form-control" name="observacion"
                                    value="{{ old('observacion') }}" required autofocus>
                                @if ($errors->has('observacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-xs-2  px-1">
                            <div class="col-md-12 px-0">
                                <label for="referencia_deposito" class="label_header">Referencia deposito</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="referencia_deposito" type="text" class="form-control" name="referencia_deposito"
                                     autofocus>
                            </div>
                        </div>


                        <div class="form-group col-xs-4  col-md-4 px-1">
                            <div class="col-md-12 px-0">
                                <label for="observacion"
                                    class="label_header">{{ trans('contableM.beneficiario') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="beneficiario" type="text" class="form-control" name="beneficiario"
                                    value="{{ old('beneficiario') }}" required autofocus>
                                @if ($errors->has('beneficiario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('beneficiario') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-2 col-md-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="observacion"
                                    class="label_header">{{ trans('contableM.sucursal') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select name="sucursal" id="sucursal" class="form-control" required>
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-4 px-1">
                            <div class="col-md-12 px-0">
                                <label for="id_banco" class="label_header">{{ trans('contableM.banco') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select name="id_banco" id="id_banco" class="form-control select2_cuentas" required>
                                    <option value="">{{ trans('agenda.seleccione') }}...</option>
                                    @foreach ($bancos as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6 col-md-2  px-1">
                            <div class="col-md-12 px-0">
                                <label for="id_divisa" class="label_header">{{ trans('contableM.divisas') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="divisa" id="divisa" required>
                                    @foreach ($divisas as $value)
                                        <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-2  px-1">
                            <div class="col-md-12 px-0">
                                <label for="cambio" class="label_header">{{ trans('contableM.cambio') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="cambio" type="number" class="form-control" value="1.00" name="cambio"
                                    required autofocus>
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-2  px-1">
                            <div class="col-md-12 px-0">
                                <label for="valor" class="label_header">{{ trans('contableM.valor') }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <input id="valor" type="text" class="form-control" name="valor" readonly
                                    autofocus>
                            </div>
                        </div>

                    <div class="form-group col-xs-6  col-md-2  px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">Estado</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="estado" type="text" class="form-control" name="estado" value="Activa" readonly autofocus>
                        </div>
                    </div>
                </div>





                <div class="col-md-12 table-responsive">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="total" id="total" value="0">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr class='well-dark'>
                                <th width="5%" tabindex="0"></th>
                                <th width="55%" class="" tabindex="0">Nombre</th>
                                <th width="20%" class="" tabindex="0">Debe</th>
                                <th width="20%" class="" tabindex="0">Haber</th>
                                <th width="5%" class="" tabindex="0">
                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <tr class="well">
                                <td>
                                    <input type="hidden" name="no_no[]" class="no_no" />
                                </td>
                                <td>

                                    <select id="nombre[]" name="nombre[]" class="form-control select2_cuentas" style="width:100%" required>
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id_plan}}" data-name="{{$value->nombre}}">{{$value->plan}} - {{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control input-sm debe" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" value="0.00" name="debe[]" required>
                                </td>
                                <td>
                                    <input class="form-control input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" disabled="true" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="haber[]" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr style="display:none" id="mifila">
                                <td>
                                    <input type="hidden" name="no_no[]" class="no_no" value="qw" />
                                </td>

                                <td>
                                    <select name="nombre[]" class="form-control select2_cuentas class_nombre" style="width:100%" required>
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id_plan}}" data-name="{{$value->nombre}}"> {{$value->plan}} - {{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control input-sm debe" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="debe[]" required>
                                </td>
                                <td>
                                    <input class="form-control input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="haber[]" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class='well'>
                            <td></td>
                            <td class="text-right">Totales</td>
                            <td id="debe_contable">

                            </td>
                            <td id="haber_contable">0.00</td>
                            <td></td>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group col-xs-10 text-center">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="button" class="btn btn-default btn-gray btn_add">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
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
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

    <script type="text/javascript">
        var fila = $("#mifila").html();
        $(".print").css('visibility', 'hidden');
        $('.select2_cuentas').select2({
            tags: false
        });
        /*$(function() {
            $('#fecha').datetimepicker({
                format: 'YYYY/MM/DD',
                //defaultDate: '{{date("Y-m-d")}}',
            });
        });*/



        $(".btn_add").click(function() {
            var sucursal = $("#sucursal").val();
            if (sucursal == 0 || sucursal == "") {
                alertas("error", "Error...", "Seleccione la sucursal")
                return;
            }
            var debeco = parseFloat($("#debe_contable").html());
            var debe = parseFloat($("#valor").val());
            if (debeco == debe) {

                if ($("#form").valid()) {
                    $(".print").css('visibility', 'visible');
                    $(".btn_add").attr("disabled", true);
                    $.ajax({
                        url: "{{route('notadebito.store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        type: 'POST',
                        datatype: 'json',
                        data: $("#form").serialize(),
                        success: function(data) {
                            $("#asiento").val(data.idasiento);
                            $("#id").val(data.iddebito);
                            $("#numero").val(data.iddebito);
                            var url = '{{ route("notadebito.imprimir", ":id") }}';
                            url = url.replace(':id', data.iddebito);
                            console.log('url', url);
                            $('#imprimir').html('<a href="' + url + '" target="_blank" class="btn btn-info btn-gray"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></a>');
                            swal("Exito!", "Nota de Debito creada con exito!", "success");
                            //swal("Correcto!","Por favor ingrese correctamente los valores..","error");


                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            } else {
                alert("hay un error en las cuentas");
                //swal("Error");
                console.log(debeco, debe);
            }

            //console.log($("#form").serialize());
            /**/
        });


        $('body').on('click', '.delete', function() {
            console.log($(this));
            console.log($(this).parent().prev().prev().children().closest('.debe').val());
            var borrar = $(this).parent().prev().prev().children().closest('.debe').val();
            var total = $("#valor").val();
            var dc = $("#debe_contable").val();

            total = parseFloat(total) - parseFloat(borrar);
            total = parseFloat(total).toFixed(2)
            $("#valor").val(total);

            dc = parseFloat(dc) - parseFloat(borrar);
            dc = parseFloat(dc).toFixed(2)
            $("#dc").val(total);

            $(this).parent().parent().remove();
        });
        $('body').on('change', '.select2_cuentas', function() {
            var eas = $(this).val();
            console.log($(this).data("name"));
            var selectedText = $(".select2_cuentas option:selected").data("name");
            console.log(selectedText);
            var sdf = $(this).find('option:selected').data("name");
            console.log(sdf);
            console.log(eas);

            sumacontable();

            $(this).parent().parent().first().children().children().closest(".no_no").val(sdf);

        });

        function nuevo() {
            var nuevafila = $("#mifila").html();
            var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            rowk.innerHTML = fila;
            rowk.className = "well";
            //  console.log(rowk);

            $('.select2_cuentas').select2({
                tags: false
            });

        }


        function agregar(elemento) {
            var anterior = $('#agregar_cuentas').html();
            var contador = $('#contador').val();
            contador = parseInt(contador) + 1;
            $.ajax({
                type: 'post',
                url: "{{route('notadebito.buscar_asiento')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: {
                    'nombre': $('#' + elemento).val(),
                    'contador': contador
                },
                success: function(data) {
                    console.log(data);
                    //$('#contador').val(contador);
                    //document.getElementById("agregar_cuentas").append(data);
                    //document.getElementById("agregar_cuentas").insertRow(-1).innerHTML = data;
                    //document.getElementById("agregar_cuentas").insertRow(contador-1).innerHTML = data;
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }


        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }

        function sumacontable() {
            var total = 0;
            $(".debe").each(function() {
                console.log("sc", $(this).parent().prev().children().closest(".select2_cuentas").val());
                var cuenta = $(this).parent().prev().children().closest(".select2_cuentas").val();
                if ($(this).val().length > 0 && cuenta != "") {
                    total = parseFloat(total) + parseFloat($(this).val());
                }
            });
            total = parseFloat(total).toFixed(2)
            $("#debe_contable").html(total);
        }

        function addvalue() {
            sumacontable();
            var total = 0;
            $(".debe").each(function() {
                if ($(this).val().length > 0) {
                    total = parseFloat(total) + parseFloat($(this).val());
                }
            });
            total = parseFloat(total).toFixed(2)
            $("#valor").val(total);
        }

        function validarCierre() {
            <?php
            if (isset($min_date) && $min_date != null) {
            ?>
                let fechaAc = new Date();
                var fecha1 = '<?= $min_date; ?>';
                var fecha2 = document.getElementById("fecha").value;
                //console.log(fecha2);
                const fechaInicial = new Date(fecha1);
                const fechaFinal = new Date(fecha2);
                if (fechaFinal <= fechaInicial) {
                    //console.log(fechaFinal,fechaInicial);
                    swal("Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.', "error");
                    const fechaFormateada = fechaAc.toISOString().split('T')[0];
                    document.getElementById("fecha").value = fechaFormateada;
                }
            <?php
            }
            ?>
        }
    </script>

</section>
@endsection