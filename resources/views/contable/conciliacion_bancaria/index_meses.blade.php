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
        <div class="box-body dobra">
            <br>
            <form action="{{route('conciliacionbanc.meses_conciliados')}}" method="POST">
                {{ csrf_field() }}
                <label for="banco">Banco:</label>
                <select name="bancos" id="bancos" class="select2" style="width: 22%;">
                    <option value="">Seleccionar Banco</option>
                    @foreach($bancos_nombre as $ban)
                    <option value="{{ $ban->caja_banco->id }}" @if(!is_null($idBanco) && $ban->caja_banco->id == $idBanco) selected @endif>{{ $ban->caja_banco->nombre }}</option>
                    @endforeach
                </select>



                <label for="anio">Año:</label>
                <select name="anio" id="anio" style="width: 20%;" class="select_anio">
                    <option value="">Seleccione un año</option>
                    @for ($i = 2020; $i <= 2025; $i++) <option value="{{ $i }}" @if(!is_null($anio) && $anio==$i) selected @endif>{{ $i }}</option>
                        @endfor
                </select>

                <label for="mes">Mes:</label>
                <select name="mes" id="mes" class="selectmes" style="width: 22%;">
                    <option value="">Seleccione un mes</option>
                    <?php
                    $meses = [
                        '01' => 'Enero',
                        '02' => 'Febrero',
                        '03' => 'Marzo',
                        '04' => 'Abril',
                        '05' => 'Mayo',
                        '06' => 'Junio',
                        '07' => 'Julio',
                        '08' => 'Agosto',
                        '09' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre'
                    ];
                    ?>
                    @foreach ($meses as $numero => $nombre)
                    <option value="{{ $numero }}" @if(!is_null($month) && $month==$numero) selected @endif>{{ $nombre }}</option>
                    @endforeach
                </select>



                <button type="submit">Buscar</button>
            </form>
            <br>
            @php

            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            @endphp
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto" for="title">Saldo en Libros</label>
                </div>
            </div>

            <div class="col-md-12">
                <table id="id_libros" class="table table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th width="5%">Id</th>
                            <th width="20%">Banco</th>
                            <th width="10%">{{trans('laboratorio.anio')}}</th>
                            <th width="10%">{{trans('laboratorio.mes')}}</th>
                            <th width="20%">{{trans('laboratorio.fecha')}}</th>
                            <th width="20%">{{trans('contableM.saldoactual')}}</th>
                            <th width="15%">{{trans('laboratorio.accion')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($libro as $l)
                        @php
                        $ms = intval($l->mes) -1;
                        @endphp
                        <tr>
                            <td>{{$l->id}}</td>
                            <td>{{$l->caja_banco->nombre}}</td>
                            <td>{{$l->anio}}</td>
                            <td>{{$meses[$ms]}}</td>
                            <td>{{substr($l->fecha,0,10)}}</td>
                            <td>{{$l->saldo_actual}}</td>
                            <td>
                                <a class="btn btn-danger" onclick="anular('{{$l->id}}');"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        <div class="box-body dobra">

            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto" for="title">Saldo en Banco</label>
                </div>
            </div>
            <div class="col-md-12">
                <table id="id_banco" class="table table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th width="5%">Id</th>
                            <th width="20%">Banco</th>
                            <th width="10%">{{trans('laboratorio.anio')}}</th>
                            <th width="10%">{{trans('laboratorio.mes')}}</th>
                            <th width="20%">{{trans('laboratorio.fecha')}}</th>
                            <th width="20%">{{trans('contableM.saldoactual')}}</th>
                            <th width="15%">{{trans('laboratorio.accion')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banco as $b)
                        @php
                        $msb = intval($b->mes) -1;
                        @endphp
                        <tr>
                            <td>{{$b->id}}</td>
                            <td>{{$b->caja_banco->nombre}}</td>
                            <td>{{$b->anio}}</td>
                            <td>{{$meses[$msb]}}</td>
                            <td>{{substr($b->fecha, 0, 10)}}</td>
                            <td>{{$b->saldo_actual}}</td>
                            <td>
                                <a class="btn btn-danger" onclick="anular('{{$b->id}}');"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $('#id_libros').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
    });

    $('#id_banco').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
    });

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function anular(id) {

        Swal.fire({
            title: '¿Desea Anular esta factura?',
            text: "No puedes revertir esta acccion!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('contable/banco/conciliacion/anular_mes')}}/" + id,
                    success: function(data) {
                        alertas(data.respuesta, data.titulos, data.msj);
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000)
                    },
                    error: function(data) {

                    }
                })
            }
        })
    }
    $(document).ready(function() {
        $('.select2').select2();
        $('.selectmes').select2();
        $('.select_anio').select2();
    });
</script>
@endsection