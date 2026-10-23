@extends('contable.ventas.base')
@section('action-content')

<div class="container">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-6">
                <h2 class="box-title">Visualizador - Fact: #{{$ventas->id}} - {{$ventas->nro_comprobante}}</h2>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <button class="btn btn-primary btn-margin btn-gray" type="button" onclick="enviar_correo('{{$ventas->id}}')"> <i class="fa fa-envelope"></i> Enviar Correo</button>
                    <button class="btn btn-primary btn-margin btn-gray" type="button" onclick="return location.href='{{route("venta_index")}}'"> <i class="fa fa-arrow-left"></i> </button>
                </div>
            </div>
           
        </div>
        <div class="box-body">
        <iframe src="{{route('venta.pdf_ieced',['id'=>$ventas->id])}}" style="width: 100%; height:100vh;" ></iframe>
        </div>
    </div>
</div>
<script>
    function enviar_correo(id){
        Swal.fire({
            title: '¿Desea enviar correo a {{$ventas->cliente->nombre}} ?',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return fetch(`{{route('ventas.envio_correo',['id'=>$ventas->id])}}`)
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
                Swal.fire("Bien!","Envio correcto","success");

            }

        })
    }
</script>
@endsection