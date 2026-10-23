@extends('contable.rol_otro_anticipo.base')
@section('action-content')
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item active" aria-current="page">Anticipo 1era Quincena
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="row head-title">
            <div class="col-md-10 cabecera">
                <label class="color_texto" for="title">ANTICIPOS 1ERA QUINCENA EMPLEADOS</label>
            </div>
            <div class="col-md-2 cabecera">
                <a class="btn btn-info btn-sm" href="{{route('nominaquincena.index_quincena')}}">Regresar</a>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="form_quincena">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <input type="hidden" name="id_valida" id="id_valida" value="{{$id_valida}}">
                    <div class="form-group col-md-2 col-xs-3">
                        <label for="year" class="texto col-md-2 control-label">Año:</label>
                        <label for="anio" class="texto col-md-9 control-label">{{$anticipo_valida->anio}}</label>

                    </div>

                    <div class="form-group col-md-2 col-xs-3">
                        <label for="mes" class="texto col-md-2 control-label">Mes:</label>
                        @php
                        $Meses = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                        @endphp
                        <label for="mes" class="texto col-md-9 control-label">{{$Meses[($anticipo_valida->mes)]}}
                    </div>
                    <div class="form-group col-md-3 col-xs-3">
                        <label for="fecha_creacion" class="col-md-4 texto">Fecha:</label>
                        <div class="col-md-7">
                            <input id="fecha_creacion" type="date" class="form-control validar" name="fecha_creacion" value="" required autofocus>
                        </div>
                    </div>

                    <div class="form-group col-md-5 col-xs-5">
                        <label for="tipo_pago" class="col-md-3 texto">Tipo de Pago</label>
                        <div class="col-md-8">
                            <select class="form-control validar" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion()">
                                @foreach($tipo_pago_rol as $value)
                                <option value="{{$value->id}}">{{$value->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- Numero de Cuenta Beneficiario Cobra-->
                    <div id="num_cuenta" class="form-group col-md-4 col-xs-4">
                        <label for="numero_cuenta" class="col-md-4 texto ">N# Cuenta</label>
                        <div class="col-md-8">
                            <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="{{ $empresa->numero_cuenta }}" autocomplete="off">
                        </div>
                    </div>
                    <!--Banco Beneficiario Cobra-->
                    <div id="id_banco" class="form-group col-md-4 col-xs-4">
                        <label for="banco" class="col-md-4 texto">Banco</label>
                        <div class="col-md-8">
                            <select class="form-control " id="banco" name="banco">
                                <option value="">Seleccione...</option>
                                @foreach($lista_banco as $value)
                                <option @if($value->id == $empresa->id_banco) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--Fecha_Cheque-->
                    <div id="fech_cheq" class="form-group col-md-4 col-xs-4">
                        <label for="fecha_cheque" class="col-md-4 texto">Fecha Cheque:</label>
                        <div class="col-md-8">
                            <input id="fecha_cheque" type="date" class="form-control " name="fecha_cheque" value="{{ old('fecha_cheque') }}" required autofocus>
                        </div>
                    </div>
                    <!--Numero de Cheque-->
                    <div id="num_che" class="form-group col-md-4 col-xs-4">
                        <label for="numero_cheque" class="col-md-4 texto">N # Cheque:</label>
                        <div class="col-md-8">
                            <input id="numero_cheque" type="text" class="form-control " name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
                        </div>
                    </div>
                    <!--Cuenta Saliente Paga-->
                    <div id="id_cuenta_saliente" class="form-group col-md-4 col-xs-4">
                        <label for="cuenta_saliente" class="col-md-4 texto">Cuent Saliente</label>
                        <div class="col-md-8">
                            <select class="form-control " id="cuenta_saliente" name="cuenta_saliente">
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" id="tabla_detalle">
                    <!--div class="table-responsive col-md-12">

                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th width="5%">Id</th>
                                    <th width="10%">Identificacion</th>
                                    <th width="20%">Nombres</th>
                                    <th width="10%">Fecha de Ingreso</th>
                                    <th width="10%">Area</th>
                                    <th width="15%">Cargo</th>
                                    <th width="15%">Sueldo Mensual</th>
                                    <th width="15%">Anticipo 1RA quinc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sum_anticipos = 0; @endphp
                                @foreach($valor_anticipos as $valor)
                                @php
                                $sum_anticipos += $valor->valor_anticipo;
                                @endphp
                                <tr>
                                    <td>{{$valor->id}}</td>
                                    <td>{{$valor->id_user}}</td>
                                    <td>{{$valor->apellido1}} {{$valor->apellido2}} {{$valor->nombre1}} </td>
                                    <td>{{$valor->fecha_ingreso}}</td>
                                    <td>{{$valor->area}}</td>
                                    <td>{{$valor->cargo}}</td>
                                    <td>{{$valor->sueldo_neto}}</td>
                                    <td>
                                        <input id="val_anticipo" type="text" class="form-control" name="val_anticipo{{$valor->id}}" onchange="actualiza_anticipo('{{$valor->id}}');" @if(!is_null($valor->valor_anticipo)) value="{{$valor->valor_anticipo}}" @else value="0.00" @endif autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><b> Total Registros:</b></td>
                                    <td style="text-align:center;">{{count($valor_anticipos)}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total:</b></td>
                                    <td>{{number_format($sum_anticipos,2, '.', '')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div-->
                </div>

                <div class="form-group col-md-12">
                    <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_quincena(event);">Generar</a>
                </div>

            </form>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    

    $(document).ready(function() {
        document.getElementById("num_che").style.display = 'none';
        document.getElementById("fech_cheq").style.display = 'none';
        $('#fecha_cheque').val("");
        cargar_tabla_detalle('{{$anticipo_valida->id}}');
    });

    function obtener_seleccion() {

        var id_tipo = $("#tipo_pago").val();
        if (id_tipo == 1) { //ACREDITACION

            document.getElementById("id_cuenta_saliente").style.display = 'block';
            document.getElementById("id_banco").style.display = 'block';
            document.getElementById("num_cuenta").style.display = 'block';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#banco').val("");
            $('#numero_cuenta').val("");
            $('#numero_cheque').val("");
            $('#fecha_cheque').val("");

        } else if (id_tipo == 2) { //EFECTIVO

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");

        } else if (id_tipo == 3) { //CHEQUE

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'block';
            document.getElementById("fech_cheq").style.display = 'block';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");
            $('#fecha_cheque').val("");

        }

    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
            return false;

        }

        return true;
    }

    function checkformat(entry) {

        var test = entry.value;

        if (!isNaN(test)) {
            entry.value = parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true) {
            entry.value = '0.00';
        }
        if (test < 0) {

            entry.value = '0.00';
        }

    }

    function actualiza_anticipo(id_anticipo) {
        //alert("entra",id_anticipo);

        $.ajax({
            type: 'post',
            url: "{{ url('contable/nomina/actualiza_anticipo') }}/" + id_anticipo,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_quincena").serialize(),
            success: function(data) {
                cargar_tabla_detalle(data.id_valida);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function cargar_tabla_detalle(id_valida) {
        $.ajax({
            type: 'get',
            url: "{{ url('contable/nomina/tabla_detalle')}}/" + id_valida,
            datatype: 'json',
            success: function(datahtml) {

                $("#tabla_detalle").empty().html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function guardar_quincena(e, num) {
        e.preventDefault();
        let fecha_asiento = document.getElementById('fecha_creacion').value;
        let tipo_pago = document.getElementById('tipo_pago').value;
        let cuenta_saliente = document.getElementById('cuenta_saliente').value;
        let fecha_cheque = document.getElementById('fecha_cheque').value;
        let numero_cheque = document.getElementById('numero_cheque').value;
        let numero_cuenta = document.getElementById('numero_cuenta').value;
        let banco = document.getElementById('banco').value;

        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }    


        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
            var confirmar = confirm('Desea realizar el Anticipo de Quincena');
            if(confirmar){
                $.ajax({
                    type: 'post',
                    url: "{{ route('nomina.guarda_asiento_anticipo') }}",
                   
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#form_quincena").serialize(),
                    success: function(data) {
                        alertas(data.respuesta, data.titulos, data.msj);
                        if(data.respuesta == 'success'){
                            setTimeout(function(){
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        }
    }
</script>

@endsection