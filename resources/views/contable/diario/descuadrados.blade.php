@extends('contable.diario.base')
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
        var opcion = confirm("¿Desea Salir?");
        if (opcion == true) {
            window.history.back();
        }
    }
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3>Asientos Descuadrados</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <form action="{{route('librodiario.descuadrados')}}" method="POST">
                            {{csrf_field ()}}
                            <h4>Seleccione el Año</h4>
                            @php
                            $anios=date("Y");
                            @endphp
                            <select class="form-control" id="exampleFormControlSelect1" name="anio_asiento">
                                @for($i = 2019; $i <= $anios; $i++)
                                    <option @if($anio_asiento==$i ) selected @endif value="{{$i}}">{{$i}}</option>
                                    @endfor
                            </select><br>
                            <input type="submit" name="anio" value="Buscar" class="btn btn-success">
                        </form>
                    </div>
                    <div class="col-md-12">
                        <h5>Total </h5> <label class="label label-danger">{{$descuadrados['original']['total']}}</label></h5>
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="table table-responsive">
                        <table id="example2" class="display compact">
                            <thead>
                                <tr>
                                    <th>Asiento</th>
                                    <th>Modulo</th>
                                    <th>Debe</th>
                                    <th>Haber</th>
                                    <th>Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $debetot=0;
                                $habertot=0;
                                @endphp
                                @foreach($descuadrados['original']['asientos'] as $d)
                                @php
                                $diferencia= $d['debe']- $d['haber']; 
                                @endphp
                                <tr>
                                    <td>
                                        <a style="color:#D1555F; font-weight:bold;" href="{{route('librodiario.edit',['id'=>$d['id_asiento']])}}" target="_blank">
                                            {{$d['id_asiento']}}
                                        </a>
                                    </td>
                                    @if(isset($d['modulo']['original']['compra']['module']))

                                    <td> <label class="label label-warning">{{$d['modulo']['original']['compra']['module']}}</label> </td>
                                    @elseif(isset($d['modulo']['original']['venta']['module']))
                                    <td> <label class="label label-info">{{$d['modulo']['original']['venta']['module']}}</label> </td>
                                    @else
                                    <td> <label class="label label-default">Sin información</label> </td>
                                    @endif
                                    <td>{{$d['debe']}}</td>
                                    <td>{{$d['haber']}}</td>
                                    <td>{{number_format($diferencia,2)}}</td>
                                </tr>
                                @php
                                $debetot+=$d['debe'];
                                $habertot+=$d['haber'];
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>

                                    <td></td>
                                    <td style="font-weight: bold;">Total</td>
                                    <td style="font-weight: bold;">{{number_format($debetot,2)}}</td>
                                    <td style="font-weight: bold;">{{number_format($habertot,2)}}</td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>
    $('#example2').DataTable({
        'paging': false,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        "scrollY": 450,
        'info': false,
        'autoWidth': true,
        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },
            {
                extend: 'excelHtml5',
                footer: true,
                title: 'INFORME COMPRAS INVENTARIO Y FACTURA GASTOS'
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'TABLOID',
                footer: true,
                title: 'INFORME COMPRAS INVENTARIO Y FACTURA GASTOS',
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '16',
                        alignment: 'center'
                    }
                }
            }
        ],
    })
</script>

@endsection