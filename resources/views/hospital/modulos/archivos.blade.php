<link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
<script src="{{ asset ("/js/dropzone.js") }}"></script>
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    
                </div>
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px">{{trans('hospitalizacion.SubirArchivos')}}</label>
                </div>

            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0px;">
        <br><br>
        <div class="row">
            <div class="col-md-12">
                <form id="form" method="post" action="{{ route('hospital.guardar_formularios') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
                    <div class="col-md-4">
                        <label>{{trans('hospital.SeleccioneArchivos')}}</label>
                    </div>
                    <div class="col-md-8">
                        <input type="file" class="files" name="archivo" id="archivo"> &nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="guardar_archivos()" class="btn btn-primary"><i class="fa fa-upload" aria-hidden="true"></i> </button>
                    </div>


                </form>
            </div>

        </div>
        <div class="row" id="lista">

        </div>


    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        listado_archivos();
    });

    function guardar_archivos() {


        var form = $('#form')[0];
        var data = new FormData(form);
        $.ajax({
            enctype: 'multipart/form-data',
            type: "post",
            url: `{{ route('hospital.guardar_formularios') }}`,
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
                listado_archivos()

            },
            error: function(datahtml) {
                alert("ocurrio un error");
            }
        });
    }

    function listado_archivos() {

        $.ajax({
            type: "GET",
            url: "{{route('hospital.detalle_formularios',['id_solicitud'=>$solicitud->id])}}",
            datatype: "html",
            success: function(datahtml) {
                $("#lista").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function eliminar2(id) {
        var opcion = confirm("{{trans('contableM.estasegdeseaelimin')}}");
        if (opcion == true) {
            $.ajax({
                type: 'get',
                url: '{{ url("hospital/eliminar/formulario")}}/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                success: function(data) {
                    //console.log(data);
                    // alert(data);
                    location.reload();
                }
            })

        }
    }
</script>