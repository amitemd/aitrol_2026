@extends('activosfijos.documentos.factura.base')
@section('action-content')
<div class="modal fade" id="ver_activo" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header bg bg-primary">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1">

                            </div>
                            <div class="col-md-7">
                                <label class="" style="font-size: 16px">{{trans('ContableM.SubirFotos')}}</label>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="row">
                        @foreach($factura->detalles as $detalle)
                        @if(isset($detalle->activo))
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label for="">Codigo:</label>
                                {{$detalle->activo->codigo}}
                            </div>
                            <div class="col-md-3">
                                <label for="">Nombre:</label>
                                {{$detalle->activo->nombre}}
                            </div>
                            <div class="col-md-3">
                                <label for="">Categoria:</label>
                                
                                    @if(isset($detalle->activo->tipo))
                                        {{$detalle->activo->tipo->nombre}}
                                    @endif
                                
                            </div>
                            <div class="col-md-3">
                                <a href="{{route('documento.modal_archivo',['id' => $detalle->activo->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ver_activo">
                                    <i class="fa fa-upload"> Subir Imagenes</i>
                                </a>
                            </div>



                        </div>
                        <div class="col-md-12">
                            <div class="col-md-2">

                            </div>
                            <div class="col-md-4">
                                @if(!is_null($detalle->activo->foto_activo))
                                <img src="{{asset('archivos_activofijo')}}/{{$detalle->activo->foto_activo}}" width="90%" style="max-height: 250px;">
                                @endif
                            </div>

                            <div class="col-md-2">

                            </div>
                            <div class="col-md-4">
                                @if(!is_null($detalle->activo->foto_acc))
                                <img src="{{asset('archivos_activofijo')}}/{{$detalle->activo->foto_acc}}" width="90%" style="max-height: 250px;">
                                @endif
                            </div>

                        </div>

                        @endif

                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $('#ver_activo').on('hidden.bs.modal', function() {
    //   location.reload();
      $(this).removeData('bs.modal');
    });
    function guardar_archivos($id) {
        var form = $('#form')[0];
        var data = new FormData(form);
        $.ajax({
            enctype: 'multipart/form-data',
            type: "post",
            url: `{{ url('contable/af/guardar_fotos') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            beforeSend: function() {
                Swal.fire({
                    //title: "{{trans('winsumos.procesando')}}",
                    text: "Cargando...",
                    imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
            },
            datatype: "html",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(datahtml) {
                Swal.close();
                alertas(datahtml.respuesta, `${datahtml.respuesta.toUpperCase()}...`, datahtml.msj)

            },
            error: function(datahtml) {
                alert("ocurrio un error");
            }
        });
    }
</script>


@endsection