@extends('contable.facturacion.base')
@section('action-content')
<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .vtdobra {
        background-color: #eafcff;
    
        padding: 0;
    }
    .vtdobra2 {
        background-color: #eafcff;
    
        padding: 0;
    }
    .select2 {
        width: 100% !important;
        background-color: #eafcff !important;
    }
    .vt_middle{
        vertical-align: middle;
    }
</style>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h4>Recibos de Cobro Pendientes de Aprobación</h4>
        </div>
        <div class="box-body">

            <div class="panel panel-default">
                <div class="panel-body" style="padding:0;">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr class='well' style="color: black;">
                                <th width="5%" tabindex="0">Id</th>
                                <th width="15%" tabindex="0">Fecha</th>
                                <th width="20%" tabindex="0">Paciente</th>
                                <th width="5%" tabindex="0">Total</th>
                                <th width="5%" tabindex="0">Descuento</th>
                                <th width="45%" tabindex="0">Observacion</th>
                                <th width="5%" tabindex="0">Acción</th>
                            </tr>
                        </thead>
                        <tbody >
                            @foreach($ordenes as $orden)
                                <tr>
                                    <td>{{ $orden->id }}</td>
                                    <td>{{ $orden->fechaini }}</td>
                                    <td>
                                        {{ $orden->agenda->paciente->apellido1 }} {{ $orden->agenda->paciente->apellido2 }} {{ $orden->agenda->paciente->nombre1 }} {{ $orden->agenda->paciente->nombre2 }}
                                    </td>@php $total = $orden->subtotal_0 + $orden->subtotal_12; @endphp
                                    <td style="vertical-align: right;">
                                       $ {{ number_format( $total , 2, ',', ' ') }}
                                    </td>
                                    <td style="vertical-align: right;">
                                        $ {{ number_format( $orden->descuento , 2, ',', ' ') }}
                                    </td>
                                    <td style="vertical-align: left;">
                                        {{ $orden->observacion }} creado por: {{ $orden->usercrea->apellido1 }}
                                    </td>
                                    
                                    <td style="vertical-align: middle;">
                                        <button type="button" class="btn btn-success" onclick="aprobar('{{ $orden->id }}')"><i class="fa fa-thumbs-o-up"></i></button>
                                        
                                    </td>   
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</section>        
    

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    
    function aprobar( id ){

        Swal.fire({
            title: 'Aprobar Descuento en Recibo de Cobro No. '+id,
            text: "Esta seguro que desa Aprobar?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Aprobar!'
        }).then((result) => {
            if (result.isConfirmed){
            
                $.ajax({
                    url: "{{url('nrc_descuentos/aprobar')}}/" + id,
                    type: 'get',
                    datatype: 'html',
                    success: function(data) {
                        
                        swal({
                            title: "Aprobado!",
                            type: "success",
                            html: 'Aprobado'
                        });
                        location.reload();
                            
                    
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })    
                        
            }else{
                
            }
        }) 

    }

</script>
@endsection
