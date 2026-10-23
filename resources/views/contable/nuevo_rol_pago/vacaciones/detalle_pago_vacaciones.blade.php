<section class="content" style="position: fixed;">

    <div class="box-header">
        <div class="col-md-7">
            <h5><b> Valores Correspondientes al Pago de Vacaciones</b></h5>
        </div>

    </div>
    <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{ $nomina->nombres }}</label>
        </div>
    </div>
    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="form-row">

                <div id="contenedor">
                    <div id="example2det_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                            <table id="example2det" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2det_info" style="width: 100%">
                                    <thead>
                                        <tr class='well-dark'>
                                            <th width="30%" style="text-align: center;"> Id </th>
                                            <th width="40%" style="text-align: center;"> Tipo </th>
                                            <th width="40%" style="text-align: center;">{{trans('nomina.valor')}}</th>
                                            <th width="30%" style="text-align: center;"> Tipo de Pago </th>
                                            <th width="30%" style="text-align: center;"> Asiento </th>
                                        </tr>
                                    </thead>
                                    <tbody>@php $acum = 0 @endphp

                                        @foreach ($detalles  as $value)
                                        @php $acum += $value->valor @endphp
                                        <tr style="text-align: center;">
                                            <td>{{$value->id}}</td>
                                            <td>{{$value->tipo}}</td>
                                            <td>${{$value->valor}}</td>
                                            <td>{{$value->tipo_comprobante}}</td>
                                            <td> 
                                            @if($value->id_asiento_pago != null)
                                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $value->id_asiento_pago ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                                Asiento
                                                </a>
                                            @endif 
                                            </td>
                                            
                                        </tr>
                                        @endforeach

                                        <tr>
                                            <td style="text-align: center;"><b>TOTAL</b></td>
                                            <td></td>
                                           
                                            <td style="text-align: center;">${{number_format($acum,4)}}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                           
                                    <tfoot>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                    </div>

                </div>
            </div>


</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">





</script>