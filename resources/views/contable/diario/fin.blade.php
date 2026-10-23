@extends('contable.diario.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        {{ csrf_field() }}
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

            <div class="col-md-12">
                <form method="GET" action="{{route('librodiario.cierre')}}">
                    {{ csrf_field() }}
                    <div class="col-md-5">
                        <span><b>Año Cierre:</b> </span><br>
                        @php
                            $anios=date("Y");
                        @endphp
                        <select class="form-control" name="anio_cierre" id="anio_cierre">
                            <!-- <option>Seleccione</option>
                            <option @if($end=="2020" ) selected @endif value="2020">2020</option>
                            <option @if($end=="2021" ) selected @endif value="2021">2021</option>
                            <option @if($end=="2022" ) selected @endif value="2022">2022</option> -->
                            @for($i = 2019; $i <= $anios; $i++)
                                <option @if($end == $i ) selected @endif value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        <br>

                        <button type="submit" class="btn btn-success">BUSCAR</button>
                    </div>
                </form>
            </div>

            <form id="form"  method="POST">

                <div class="box-body col-xs-12">
                    <div class="row">
                        <input type="hidden" name="final" value="1">
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <span><b>Fecha de Cierre:</b> </span><br>
                                <input class="form-control" style="width: 20%;" type="date" name="fecha_asiento" id="fecha_asiento" value="{{date('Y-m-d')}}" required> <br>

                                <p><b>Detalle: </b> </br>
                                    <textarea class="form-control" name="observacion" id="obvservacion" required cols="3" rows="3"> </textarea>
                                </p>
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
                                        @php $contador=0; @endphp
                                        @foreach ($detalle as $value)
                                        @php
                                        $finas=0;
                                        $msj = "Normal";

                                        $cuentas_id = array();

                                         // $cuentas_id = explode(".", $value->id); 
                                        $cuentas_id = explode(".", $value->plan_cuenta_empresa);

                                        if($value->d<0){ $finas=(-1) * $value->d;
                                            if(Auth::user()->id == "0957258056"){
                                            echo "finas " . $finas;
                                            if($value->plan_cuenta_empresa == "5.2.02.14.04"){
                                                dd($value);
                                            }
                                            }
                                            }else{
                                            $finas= $value->d;
                                            }
                                            @endphp
                                            <tr id="dato{{$contador}}">
                                                <td>
                                                    <select style="width: 90%;" class="form-control select2_cuentas" name="id_plan_cuenta[]" required>
                                                        <option value=""> Seleccione...</option> @foreach($cuentas as $x) <option @if(($value->id)==$x->id) selected="selected" @endif value="{{$x->id}}"> {{$x->cuenta_plan}} | {{$x->nombre}}</option> @endforeach
                                                    </select>
                                                </td>
                                                <td> <input style="width: 90%;" class="form-control input-sm" type="text" name="cuenta_nombre[]" value="{{$value->nombre}}" required></td>
                                                <td> <input style="width: 95%;" class="form-control input-sm" type="text" name="descripcion[]" value="{{$value->nombre}}" required></td>
                                                <td id="de{{$contador}}"> <input class="form-control input-sm debe" style="width: 95%;" type="text" name="debe[]" id="debe{{$contador}}" @if($cuentas_id[0]=="4" ) value="{{number_format($finas,2,'.','')}}" @else value="0.00" @endif onchange="sumatotales(); debe({{$contador}});" required> </td>
                                                <td id="h{{$contador}}"> <input class="form-control input-sm haber" style="width: 95%;" type="text" name="haber[]" id="haber{{$contador}}" @if($cuentas_id[0]=="5" ) value="{{number_format($finas,2,'.','')}}" @else value="0.00" @endif onchange="sumatotales(); haber({{$contador}});" required> </td>

                                                <td></td>
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
                                                <th><span id="haber_total"></span> <input type="hidden" name="totaldebe" id="totaldebe" value="0"> <input type="hidden" name="totalhaber" id="totalhaber" value="0"> <input type="hidden" name="total" id="total" value="0.00"> </th>
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
                    <button type="button" onclick="enviar()" class="btn btn-success btn-gray"> <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar</button>
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
        $("#debe_total").html('$ ' + sumador.toFixed(2));

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
        $("#haber_total").html('$ ' + sumador.toFixed(2));
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
        console.log("entra en haber");
        //sumahaber();
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        //console.log("eduardo maricon");
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function nuevo() {
        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = '<td> <select style="width: 90%;" class="form-control select_cuentas" name="id_plan_cuenta[]" required><option value="" > Seleccione...</option> @foreach($cuentas as $x) <option value="{{$x->id}}"> {{$x->cuenta_plan}} | {{$x->nombre_plan_empresa}}</option>@endforeach</select> <input type="hidden" name="id_asiento[]" value="-1"> </td><td><input style="width: 90%;" class="form-control input-sm" type="text" name="cuenta_nombre[]" required></td><td> <input style="width: 95%;" class="form-control input-sm" type="text" name="descripcion[]" required></td><td id="de' + id + '" ><input class="form-control input-sm debe" style="width: 95%;" type="text" name="debe[]" id="debe' + id + '" required onchange="sumatotales(); debe(' + id + ');" value="0.00"></td><td id="h' + id + '"> <input class="form-control input-sm haber" style="width: 95%;" type="text"  required name="haber[]" id="haber' + id + '" onchange="sumatotales(); haber(' + id + ');" value="0.00"> </td><input type="hidden" name="totales[]" id="totales' + id + '" value=""> <td><button type="button" class="btn btn-warning btn-gray" onclick="eliminar(' + id + ')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
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

  
    
    function enviarform() {
        // /$('#form').submit();
        $.ajax({
            type: 'post',
            url: "{{route('librodiario.store_cierre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data) {
                console.log(data)
                if(data.status == "success"){
                    Swal.fire("Exito!", `${data.msj} <br> Asiento creado: <a target="_blank" style="font-weight:bold; color:red;" href="{{url('contable/contabilidad/libro/edit/${data.id_asiento}')}}"> Asiento </a> `, data.status);
                }else if(data.status == "existe"){
                    Swal.fire("Advertencia!..", `${data.msj} <br> Asiento: <a target="_blank" style="font-weight:bold; color:red;" href="{{url('contable/contabilidad/libro/edit/${data.id_asiento}')}}"> Asiento </a> `, 'info');
                }else{
                    Swal.fire("Error!", data.msj, data.status);
                }
            },
            error: function(data) {

            }
        })
    }

    function enviar() {
        var debe = $("#totaldebe").val();
        var haber = $("#totalhaber").val();
        if ($("#form").valid()) {
            if (debe == haber) {
                $('#total').val(debe);
                Swal.fire({
                    title: '¿Desea guardar este Asiento?',
                    text: "!",
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