@extends('contable.diario.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <form id="form">
            {{ csrf_field() }}

            <input type="hidden" name="id_asiento_cabecera" value="{{$id_asiento_cabecera}}">
            <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
                <div class="col-md-9">
                    <h3 class="box-title">Datos del Asiento Contable</h3>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <button onclick="goBack()" type="button" class="btn btn-danger btn-gray" <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="box-body col-xs-12">
                    @if(!is_null($empresa->logo))
                    <img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa">
                    @endif
                </div>
                <div class="box-body col-xs-6">
                    <label style="font-size: 14px">CALLE:{{$empresa->direccion}}</label>
                </div>
                <div class="form-group col-xs-4">
                    <label for="aparece_sri" class="col-md-4 control-label" style="text-align: right">Aparece en el SRI</label>
                    <div class="col-md-8">
                        <input id="aparece_sri" type="checkbox" name="aparece_sri" @if(($registro->aparece_sri)==1) checked @elseif(($registro->aparece_sri)==0) unchecked @endif>
                    </div>
                </div>
                @php
                $id_usuario = Auth::user()->id;
                $permiso = \Sis_medico\UsuarioEspecial::where('id_empresa', session('id_empresa'))
                ->where('id_usuario', $id_usuario)->first();
                @endphp
                @if(!is_null($permiso))
                <div class="form-group col-xs-2">
                    <label for="especial" class="col-md-4 control-label" style="text-align: right">Permiso Especial</label>
                    <div class="col-md-8">
                        <input id="especial" type="checkbox" name="especial" @if(($registro->especial)==1) checked @elseif(($registro->especial)==0) unchecked @endif>
                    </div>
                </div>
                @endif

                <div class="box-body col-xs-12">
                    <div class="row">
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <span><b>Fecha de Registro:</b> </span><br>
                                <input class="form-control" style="width: 20%;" type="date" name="fecha_asiento" id="fecha_asiento" value="{{substr($registro->fecha_asiento,0,10)}}"> <br>
                                <span><b>Estado:</b> @if($registro->estado == '1') Activo @elseif($registro->estado =='2') Retenciones @elseif($registro->estado =='3') Comprobante Egreso @else Anulada @endif</span>
                                <p><b>Valor Registrado:</b> {{$registro->valor}}</p>
                                @php
                                $usuario = \Sis_medico\User::find($registro->id_usuariocrea);
                                @endphp
                                <p><b>Usuario Crea:</b> {{$usuario->nombre1}} {{$usuario->apellido1}} {{$usuario->apellido2}}</p>
                                <p><b>Fecha Crea:</b> {{$registro->created_at}}</p>
                                <p><b>Detalle: </b> </br>
                                    <textarea class="form-control" name="observacion" id="obvservacion" cols="3" rows="3"> {{$registro->observacion}}</textarea>
                                </p>
                                <br>
                                @php
                                $log = Sis_medico\Log_Contable::where ('id_ant', $id_asiento_cabecera) -> orWhere ('id_referencia', $id_asiento_cabecera)->first();
                                @endphp
                                @if(!is_null($log))
                                    @if($log->id_ant == $id_asiento_cabecera)
                                        <label style="font-size: 15px;" class='label label-danger'>EL ASIENTO SE ENCUENTRA ANULADO</label> <br> <br>
                                        <label style="font-size: 15px;" class='label label-danger'>El ASIENTO QUE SE CREO POR LA ANULACIÓN ES: <a target="_blank" style="color:white; text-decoration:underline;"  href="{{route('librodiario.edit',['id'=>$log->id_referencia])}}">{{$log->id_referencia}}</a></label>
                                    @elseif($log->id_referencia == $id_asiento_cabecera)
                                        <label style="font-size: 15px;" class='label label-info'>ASIENTO QUE SE CREO POR UNA ANULACIÓN</label> <br> <br>
                                        <label style="font-size: 15px;" class='label label-info'>EL ASIENTO ANULADO ES : <a target="_blank" style="color:white; text-decoration:underline;" href="{{route('librodiario.edit',['id'=>$log->id_ant])}}">{{$log->id_ant}}</a></label>
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-12" style="text-align: right;">
                                <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="col-md-12 table table-responsive" style="width: 100%;">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Código</th>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                                            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                                        </tr>
                                    </thead>


                                    <tbody id="agre">
                                        @php
                                        $contador=0;
                                        //dd($detalle);
                                        if(Auth::user()->id == "0957258056"){

                                        // dd($cuentas);
                                        }

                                        @endphp
                                        @foreach ($detalle as $key => $value)



                                        <tr id="dato{{$contador}}">
                                            <td> <select style="width: 90%;" class="form-control select2_cuentas" name="id_plan_cuenta[]" required>
                                                    <option value=""> Seleccione...</option>
                                                    @foreach($cuentas as $x)
                                                    <option @if($value->accountPlanId == $x->id_plan || $value->accountPlanId == $x->plan )
                                                        selected="selected"
                                                        @endif
                                                        value="{{$x->id_plan}}"> {{$x->plan}} | {{$x->nombre}}</option>
                                                    @endforeach
                                                </select> <input type="hidden" name="id_asiento[]" value="{{$value->sourceLineId}}"> </td>
                                            <td> <input style="width: 90%;" class="form-control input-sm" type="text" name="cuenta_nombre[]" value="{{$value->accountName}}" required></td>


                                            <td> <input style="width: 95%;" class="form-control input-sm" type="text" name="descripcion[]" value="{{$value->description}}" required></td>
                                            <td id="de{{$contador}}"> <input class="form-control input-sm debe" style="width: 95%;" type="text" name="debe[]" id="debe{{$contador}}" value="{{$value->debit}}" onchange="sumatotales(); debe({{$contador}});" required> </td>
                                            <td id="h{{$contador}}"> <input class="form-control input-sm haber" style="width: 95%;" type="text" name="haber[]" id="haber{{$contador}}" value="{{$value->credit}}" onchange="sumatotales(); haber({{$contador}});" required> </td>
                                            <input type="hidden" name="totales[]" id="totales{{$contador}}" value="@if($value->debit != 0) {{$value->debit}} @elseif($value->credit != 0) {{$value->credit}} @else 0 @endif">

                                            <td id="el{{$contador}}">
                                                <button type="button" onclick="eliminardet({{$contador}})" class="btn btn-danger btn-gray" ><i class="fa fa-trash"></i></button>
                                            </td>
      
                                        </tr>
                                        @php $contador++; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <thead>
                                            <tr>
                                                <th>TOTALES</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th> <span id="debe_total"></span> </th>
                                                <th><span id="haber_total"></span> <input type="hidden" name="totaldebe" id="totaldebe" value="0"> <input type="hidden" name="totalhaber" id="totalhaber" value="0"> </th>
                                            </tr>
                                        </thead>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <input type="hidden" name="contador" id="contador" value="{{$contador}}">
                    <button type="button" onclick="enviar(event)" class="btn btn-success btn-gray"> <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar</button>
                </div>
        </form>
    </div>
    </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    function eliminardet(e) {

        let element = document.getElementById("dato" + e).remove();
        let contador = document.getElementById('contador').value;
        let tot = document.getElementById('contador').value = contador - 1;
        sumadebe();
        sumahaber();
    };
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
        sumadebe()
        sumahaber()
    });

    function goBack() {
        var url = "{{route('librodiario.index')}}";
        window.location = url;
    }

    function sumadebe() {
        var contador = parseInt($("#contador").val());
        var sumador = 0;
        $('.debe').each(function(i, obj) {
            sumador += parseFloat($(this).val());
        });
        //alert(sumador);
        var tot = redondeafinal(sumador);
        $("#totaldebe").val(tot);
        $("#debe_total").html('$ ' + sumador.toFixed(4));

    }

    function sumahaber() {
        var contador = parseInt($("#contador").val());
        var sumador = 0;

        $('.haber').each(function(i, obj) {
            sumador += parseFloat($(this).val());
        });

        //alert(totales);
        var tot = redondeafinal(sumador);
        $("#totalhaber").val(tot);
        $("#haber_total").html('$ ' + sumador.toFixed(4));
    }

    function sumatotales() {
        console.log("entra");
        sumadebe();
        sumahaber();
    }

    function debe(e) {
        var debe = $("#debe" + e).val();
        debe_ = redondeafinal(debe);
        console.log("entra en debe" + debe_);
        $("#debe" + e).val(debe_);
        //sumadebe();

    }

    function haber(e) {
        var debe = $("#haber" + e).val();
        debe_ = redondeafinal(debe);
        $("#haber" + e).val(debe_);

        //sumahaber();
    }

    function redondeafinal(num, decimales = 4) {
        return (Math.round(num * Math.pow(10, decimales)) / Math.pow(10, decimales)).toFixed(decimales);
    }

    function nuevo() {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td> <select style="width: 90%;" class="form-control select_cuentas" name="id_plan_cuenta[]" required>@foreach($cuentas as $x) <option value="{{$x->id_plan}}"> {{$x->plan}} | {{$x->nombre}}</option>@endforeach</select> <input type="hidden" name="id_asiento[]" value="-1"> </td><td><input style="width: 90%;" class="form-control input-sm" type="text" name="cuenta_nombre[]" required></td><td> <input style="width: 95%;" class="form-control input-sm" type="text" name="descripcion[]" value="." required></td><td id="de' + id + '" ><input class="form-control input-sm debe" style="width: 95%;" type="text" name="debe[]" id="debe' + id + '" required onchange="sumatotales(); debe(' + id + ');" value="0.00"></td><td id="h' + id + '"> <input class="form-control input-sm haber" style="width: 95%;" type="text"  required name="haber[]" id="haber' + id + '" onchange="sumatotales(); haber(' + id + ');" value="0.00"> </td><input type="hidden" name="totales[]" id="totales' + id + '" value=""> <td><button type="button" class="btn btn-warning btn-gray" onclick="eliminar(' + id + ')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('agre').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;
        $('.select_cuentas').select2({
            tags: false
        });
    }

    function eliminar(id) {
        $("#dato" + id).remove();
        id = document.getElementById('contador').value;
        id = id - 1;
        document.getElementById('contador').value = id;
        console.log("eliminar");
        sumatotales();
    }

    function anular_asiento_edit(id) {

        Swal.fire({
            title: '¿Desea anular este Asiento?',
            text: "No puedes revertir esta acccion!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                test(id);
            }
        })


    }
    async function test(id) {
        try {
            const {
                value: text
            } = await Swal.fire({
                title: 'Ingresa tu contraseña',
                input: 'password',
                inputPlaceholder: 'Ingrese contraseña',
                inputAttributes: {
                    maxlength: 18,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true
            })

            if (text) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('librodiario.checkpass')}}",
                    datatype: 'json',
                    data: {
                        'userpass': text,
                    },
                    success: function(data) {
                        //console.log(data+" dsada "+id);
                        console.log(data);
                        //console.log(acumulate);
                        if (data == 'ok') {
                            $.ajax({
                                type: 'get',
                                url: "{{ url('contable/contabilidad/libro/diario/anularasiento_edit/')}}/" + id,
                                datatype: 'json',
                                data: {
                                    'concepto': text,
                                },
                                success: function(data) {
                                    Swal.fire("Correcto!", "Anulación Correcta", "success");
                                    location.reload();
                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            });
                        } else {
                            Swal.fire("Mensaje", "Error contraseña incorrecta, intente de nuevo...", "error");
                        }

                    },
                    error: function(data) {
                        console.log(data);
                    }
                });


            }

        } catch (err) {
            console.log(err);
        }
    }


    function enviarform() {
        // $('#form').submit();

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('librodiarios.update')}}",
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data) {
                console.log(data);
                if (data.status == 'success') {
                    Swal.fire("Mensaje", `${data.msj}`, `${data.status}`);
                    //enviarform();
                } else {
                    Swal.fire("Mensaje", `${data.msj}`, `${data.status}`);
                }
            },
            error: function(data) {
                console.log(data);
            }
        });

    }

    function enviar(e) {
        e.preventDefault();
        var debe = $("#totaldebe").val();
        var haber = $("#totalhaber").val();
        if ($("#form").valid()) {
            if (debe == haber) {
                Swal.fire({
                    title: '¿Desea modificar este Asiento?',
                    text: "No puedes revertir esta acccion!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarform();
                    }
                })
            } else {
                Swal.fire("Error!", "Error no coninciden los valores por favor verifique de nuevo", "error");
            }

        }


    }
    async function test() {
        try {
            const {
                value: text
            } = await Swal.fire({
                title: 'Ingresa tu contraseña',
                input: 'password',
                inputPlaceholder: 'Ingrese contraseña',
                inputAttributes: {
                    maxlength: 18,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true
            })

            if (text) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('librodiario.checkpass')}}",
                    datatype: 'json',
                    data: {
                        'userpass': text,
                    },
                    success: function(data) {
                        console.log(data);
                        if (data == 'ok') {

                            enviarform();
                        } else {
                            Swal.fire("Mensaje", "Error contraseña incorrecta, intente de nuevo...", "error");
                        }

                    },
                    error: function(data) {
                        console.log(data);
                    }
                });


            }

        } catch (err) {
            console.log(err);
        }
    }
</script>
</section>
@endsection
