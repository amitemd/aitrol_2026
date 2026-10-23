@extends('layouts.app-template-apps')
@section('content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<style>
    .as {
        text-align: left;
    }
</style>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        Listado de Consultas
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                        <button type="button" class="btn btn-primary" onclick="enviar()"> Gestionar </button>
                    </div>

                </div>
            </div>




        </div>
        <div class="card-body">
            <form action="{{route('agendaapps.index')}}" method="POST">
                {{ csrf_field() }}
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-2">
                            <label>Fecha Desde</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="fecha_desde" value="@if($request->fecha_desde!=null){{$request->fecha_desde}}@endif">
                        </div>
                        <div class="col-md-2">
                            <label>Fecha Hasta</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="fecha_hasta" value="@if($request->fecha_hasta!=null){{$request->fecha_hasta}}@endif">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"> Buscar </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-md-12">
                &nbsp;
            </div>
            <div class="table table-responsive">
                <table class="table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Dr.</th>
                            <th>Url</th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th>Información</th>
                            <th>Número de Comprobante</th>
                            <th>Ride</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                            <th>Autorizacion</th>
                            <th>Pago</th>
                            <th>Meses</th>
                            <th>Estado</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php 
                        $subtotal=0;
                        $total=0;
                        @endphp
                        @foreach($agendas as $charlas)
                        @php
                        $agenda=DB::table('agenda')->where('id',$charlas->id_agenda)->first();
                        $user= DB::table('users')->where('id',$charlas->id_usuariocrea)->first();
                        $doctor1=null;
                        if($agenda!=null){
                        $doctor1= DB::table('users')->where('id',$agenda->id_doctor1)->first();
                        }else{

                        }
                        $ventas= DB::table('ct_ventas')->where('id',$charlas->id_venta)->first();
                        $subtotal+=$charlas->p_subtotal;
                        $total+= $charlas->total;
                        @endphp
                        <tr>
                            <td>{{date('d/m/Y H:i:s',strtotime($charlas->fecha))}}</td>
                            <td>{{$charlas->descripcion}}</td>
                            <td></td>
                            <td> @if($charlas->url!=null) <a class="btn btn-warning as" target="_blank" href="{{$charlas->url}}">Ingresar</a> @else No tiene. @endif </td>
                            <td>{{$user->apellido1}} {{$user->nombre1}}</td>
                            <td>{{$charlas->tipo}}</td>
                            <td @if($charlas->online==1) style="background-color: #7AD074; color: white;" @else style="background-color: #FF7152; color: white;" @endif
                                @if($charlas->online==1) style="background-color: #7AD074; color: white;" @else style="background-color: #FF7152; color: white;" @endif>@if($charlas->online==1) Gestionado @else Pendiente @endif</td>
                            <!--  <td><a class="btn btn-warning" href="{{route('charlasapps.edit',['id'=>$charlas->id])}}">Editar</a></td> -->
                            <td>@if($charlas->ride!=null) {{$charlas->ride}} @endif</td>
                            <td>@if($charlas->ride!=null) <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $charlas->ride, 'id_empresa' => session()->get('id_empresa'), 'tipo' => 'pdf']) }}" class="btn btn-success col-md-12  pk btn-margin btn-gray">Ride Fact</a> @endif</td>
                            <td>$ {{$charlas->p_subtotal}}</td>
                            <td>$ {{$charlas->total}}</td>
                            <td>{{$charlas->authorization}}</td>
                            <td>{{$charlas->payment}}</td>
                            <td>{{$charlas->months}}</td>
                            <td>@if($charlas->estado==1) ACTIVO @else INACTIVO @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="font-weight:bold;">Total: </td>
                            <td>$ {{number_format($subtotal,2,'.','')}}</td>
                            <td>$ {{number_format($total,2,'.','')}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    function enviar() {

        Swal.fire({
            title: '¿Desea revisar los pagos ?',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return fetch(`{{route('api.get_pay_app')}}`)
                    .then(response => {
                        //console.log(response);
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire("Bien!", "Envio correcto", "success");

                location.reload(true);

            }

        })

    }
</script>
@endsection