<style>
    .img {
        width: 60%;
    }

    .contendor-img {
        display: flex;
        justify-content: center;
        flex-direction: column;
        place-items: center;
    }

    .imageView {
        width: 50%;
        border-radius: 35px;
        /* object-fit: none; */
    }

    .container-input {
        text-align: center;
        /* background: #282828; */
        /* border-top: 5px solid #c39f77; */
        padding: 11px 0;
        border-radius: 6px;
        width: 50%;
        margin: 0 auto;
        /* margin-bottom: 20px; */
    }

    .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .inputfile+label {
        max-width: 80%;
        font-size: 1.25rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        padding: 0.625rem 3.25rem;
        border-radius: 10px;
    }

    .inputfile+label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        margin-right: 0.25em;
    }

    .iborrainputfile {
        font-size: 16px;
        font-weight: 600;
        /* font-family: 'Lato'; */
    }

    .inputfile-1+label {
        color: #fff;
        background-color: #004AC1;
    }

    .inputfile-1:focus+label,
    .inputfile-1.has-focus+label,
    .inputfile-1+label:hover {
        background-color: #004AC1;
    }

    .container-button {
        display: flex;
        justify-content: center;
    }

    .container-button>button {
        padding: 9px 50px;
        border-radius: 10px;
        background: #28c76f;
        color: white;
        font-weight: 900;
        border: 1px solid #28c76f;
        text-transform: uppercase;
    }
</style>


<div class="container" style="padding: 20px;">
    <form id="form-image" action="{{ route('hospital.storeImage') }}" enctype="multipart/form-data" method="post">
        <input type="hidden" name="id" value="1">
        <div class="contendor-img">
            <div class="col-md-12 contendor-img">
                <img id="img" src="{{ asset('defaultDemo.png') }}" height="200px" alt="default" class="imageView">
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <!-- <input id="input" type="file" class="img"> -->
                <div class="container-input">
                    <input accept="image/*" type="file" onchange="fileChange()" name="file_1" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} archivos seleccionados" multiple />
                    <label for="file-1">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        <span class="iborrainputfile">{{trans('transquirofano.seleccionar_archivo')}}</span>
                    </label>
                </div>
            </div>
            <div class="col-md-12 container-button">
                <button type="button" id="btnEnviarImagen">{{trans('contableM.guardar')}}</button>
            </div>
        </div>
    </form>

    <div id="container_image" class="col-md-12 row" style="margin-top: 30px; max-height: 870px; overflow-y: scroll;">
        @foreach($imagenes as $imagen)
        <div class="col-md-4" style="margin-top: 20px;">
            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar2', ['id' => $imagen->id]) }}">
                <img src="{{asset('public')}}/{{$imagen->nombre}}" style='width:100%;' alt="{{$imagen->nombre}}">
            </a>
        </div>
        @endforeach
    </div>
</div>
<script>
    document.getElementById('btnEnviarImagen').addEventListener('click', function() {
        let img = document.getElementById('file-1');
        if (img.files.length == 0) {
            alertas("error", "Error...",`{{trans("hospital.Debe_seleccionar_una_imagen")}}`);
            return;
        }
        var formData = new FormData();
        var files = $('#file-1')[0].files[0];
        // console.log(files);
        formData.append('file', files);
        formData.append('id_solicitud', `{{$id_solicitud}}`);
        $.ajax({
            url: `{{ url('hospital/imagenes/store/image') }}`,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                let html = ``;
                html = `<div class="col-md-4" style="margin-top: 20px;">
                            <img src="{{asset('public')}}/${data.data}" style='width:100%;' alt="">
                        </div>`
                // $("#container_image").append(html);
                let container = document.querySelector('#container_image');
                container.appendChild(html);
                cargarImagenesHospital()
            },
            error: function(data) {}
        })
    });

    // window.onload = function() {
    
</script>