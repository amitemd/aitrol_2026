@extends('contable.debito_bancario.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.banco')}}</a></li>
            <li class="breadcrumb-item active">{{trans('Kconciliacion.ConciliacionBancaria')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('Kconciliacion.ConciliarCuentaBancaria')}}</h3>
            </div>
            <div class="col-md-1 text-right">
            </div>
        </div>
        <form class="form-horizontal" action="{{ route('conciliacionbancaria.conciliacion_excel') }}" method="POST" role="form" id="form_saldos">
            {{ csrf_field() }}
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto" for="title">{{trans('Kconciliacion.ConciliacionBancaria')}} - {{trans('Kconciliacion.SaldoBanco')}}</label>
                </div>
            </div>
            <div class="text-right" style="margin-right: 20px;">
                <button class="btn btn-success bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center" type="submit">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    Excel
                </button>
            </div>

            <div class="box-body dobra">
                @php
                $anio = date('Y', strtotime($fecha_desde));
                $mes = date('m', strtotime($fecha_hasta));
                $consulta_mes = Sis_medico\Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 2)->where('id_caja_banco', $banco)->first();
                @endphp
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="fecha">{{trans('Kconciliacion.desde')}} </label>
                </div>

                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input type="date" name="fecha_desde" class="form-control" id="fecha_desde" value="{{$fecha_desde}}" readonly>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="fecha">{{trans('Kconciliacion.hasta')}} </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input type="date" name="fecha_hasta" class="form-control" id="fecha_hasta" value="{{$fecha_hasta}}" readonly>
                    <input type="hidden" name="banco" id="banco" value="{{$banco}}">
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <button id="btn_guardar" @if(!is_null($consulta_mes)) disabled @endif class="btn btn-success" onclick="guardar_mes(event);">{{trans('Kconciliacion.Guardar')}}</button>
                </div>
            </div>


            <div class="box_body dobra">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive col-md-12">
                            <table id="tbl_saldo_bancos" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" width="100%">
                                <thead>
                                    <tr class="well-dark">
                                        <th width="5%">&nbsp;</th>
                                        <th width="5%">{{trans('Kconciliacion.Fecha')}} Banco</th>
                                        <th width="10%">{{trans('Kconciliacion.tipo')}}</th>
                                        <th width="35%">{{trans('Kconciliacion.Detalle')}}</th>
                                        <th width="10%">{{trans('Kconciliacion.Valor')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conciliados as $value)
                                    <tr>
                                        <td>@if(isset($value->caja_banco)) {{$value->caja_banco->nombre}} @endif</td>
                                        <td>{{substr($value->fecha_conciliacion, 0, 10)}}</td>
                                        <td>{{$value->tipo}}</td>
                                        <td>{{$value->detalle}}</td>
                                        <td>{{$value->valor}}
                                            <input type="hidden" name="val" class="form-control valorc {{$value->tipo}}" id="val" value="{{$value->valor}}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-2">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('Kconciliacion.TotalRegistros')}} {{count($conciliados)}} </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-body dobra">
                <div class="row">

                    <div class="col-md-12">
                        <table width="100%">
                            <thead>
                                <tr class="well-dark">
                                    <th style="text-align: center;">{{trans('Kconciliacion.SaldoAnterior')}}</th>
                                    <th style="text-align: center;">{{trans('Kconciliacion.Depositos')}}</th>
                                    <th style="text-align: center;">{{trans('Kconciliacion.ValorAcreditado')}}</th>
                                    <th style="text-align: center;">{{trans('Kconciliacion.ChequesPagados')}}</th>
                                    <th style="text-align: center;">{{trans('Kconciliacion.ValoresDebitados')}}</th>
                                    <th style="text-align: center;">{{trans('Kconciliacion.SaldoActual')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <input type="hidden" name="tipo" id="tipo" value="2">
                                    <td><input id="saldo_ant" style="text-align: right; width: 100%" type="text" class="form-control" value="{{$anterior}}" name="saldo_anterior" readonly></td>
                                    <td><input id="depositos" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="depositos" readonly></td>
                                    <td><input id="valor_acreditado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_acreditado" readonly></td>
                                    <td><input id="cheques_pag" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="cheques_pag" readonly></td>
                                    <td><input id="valor_debitado" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="valor_debitado" readonly></td>
                                    <td><input id="saldo_act" style="text-align: right; width: 100%" type="text" class="form-control" value="0.00" name="saldo_actual" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </form>
    </div>
</section>

<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
    $('#tbl_saldo_bancos').DataTable({
        'paging': false,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        'responsive': true,
        'info': false,
        'autoWidth': true,

        language: {
            zeroRecords: " "
        },
        buttons: [{
                extend: 'excelHtml5',
                footer: true,
                title: 'SALDO LIBROS'
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer: true,
                title: 'SALDO LIBROS',
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    });
    $(document).ready(function() {
        setTimeout(function() {
            calcular_mov();
        }, 1500);
        // calcular_mov();
    });

    function calcular_mov() {
        let eg = document.querySelectorAll(".EG");
        let totalEG = sumaTotal(eg);

        let egv = document.querySelectorAll(".EGV");
        let totalEGV = sumaTotal(egv);

        let egm = document.querySelectorAll(".EGM");
        let totalEGM = sumaTotal(egm);

        let ban_tr = document.querySelectorAll(".BAN-TR");
        let totalBAN_TR = sumaTotal(ban_tr);

        let ban_tr_db = document.querySelectorAll(".BAN-TR-DB");
        let totalBAN_TR_db = sumaTotal(ban_tr_db);

        let ban_dp = document.querySelectorAll(".BAN-DP");
        let totalBAN_DP = sumaTotal(ban_dp);

        let ban_nc = document.querySelectorAll(".BAN-NC");
        let totalBAN_NC = sumaTotal(ban_nc);

        let ban_nctr = document.querySelectorAll(".BAN-TRANF");
        let totalBAN_TRANF = sumaTotal(ban_nctr);

        let ban_nd = document.querySelectorAll(".BAN-ND");
        let totalBAN_ND = sumaTotal(ban_nd);

        let ban_nd_ac = document.querySelectorAll(".BAN-ND-AC");
        let totalBAN_NC_AC = sumaTotal(ban_nd_ac);

        let saldo_anterior = document.getElementById("saldo_ant").value;


        let cheques = totalEG + totalEGV;
        let acreditados = totalBAN_TR + totalBAN_NC + totalBAN_TRANF;
        let debitados = totalBAN_ND + totalBAN_NC_AC + totalEGM  +totalBAN_TR_db;
        console.log(debitados);

        let saldo_act = (totalBAN_DP + acreditados - cheques - debitados) + parseFloat(saldo_anterior);

        document.getElementById("cheques_pag").value = parseFloat(cheques).toFixed(2);
        document.getElementById("valor_acreditado").value = parseFloat(acreditados).toFixed(2);
        document.getElementById("depositos").value = parseFloat(totalBAN_DP).toFixed(2);
        document.getElementById("valor_debitado").value = parseFloat(debitados).toFixed(2);
        document.getElementById("saldo_act").value = parseFloat(saldo_act).toFixed(2);
    }
    const sumaTotal = data => {
        let total = 0;
        for (let i = 0; i < data.length; i++) {
            total += parseFloat(data[i].value);
        }
        return total;
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function guardar_mes(e) {
        e.preventDefault();
        $('#btn_guardar').prop("disabled", true);
        $.ajax({

            type: 'post',
            url: "{{route('conciliacionbancaria.guardar_mes')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#form_saldos').serialize(),
            success: function(data) {

                alertas(data.respuesta, data.titulos, data.msj);

            },
            error: function(data) {

            }
        });
    }
</script>
@endsection