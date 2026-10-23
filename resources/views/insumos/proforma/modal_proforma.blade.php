<script src="https://cdn.tailwindcss.com"></script>
<style>
    .files {
        margin: 0px;
        border: 1px solid #C6C9CB;
        width: 393px;
        border-radius: 10px;
    }

    input[type=file]::file-selector-button {
        margin-right: 20px;
        border: none;
        background: #084cdf;
        padding: 5px 10px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        transition: background .2s ease-in-out;
    }
</style>
<div class="p-5">
    <div class="flex justify-between">
        <h2 class="font-bold text-2xl uppercase">Modal excel de proforma</h2>
    </div>
    <div class="mt-5">
        <form id="form" method="post" action="{{ route('insumos.proformaInsumos.subirExcel') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div style="padding: 20px;">
                <div class="container-subida">
                    <div class="">
                        <label>Subir Excel</label>
                    </div>
                    <div class="w-full flex justify-center">
                        <div class="w-[90%] sm:w-[80%] md:w-[70%] lg:w-[50%]" style="display: flex;justify-content: center;">
                            <input type="text" class="form-control rounded-md" name="observacion" placeholder="Observacion..." id="observacion" value="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-center items-center " style="padding:15px;">
                        <input type="file" class="files" name="archivo" id="archivo"> &nbsp;&nbsp;&nbsp;
                        <button type="button" id="subir_excel" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duracion-700">
                            <i class="fa fa-upload" aria-hidden="true"></i> Subir
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>


<script>
    document.getElementById('subir_excel').addEventListener('click', function() {

        let observacion = document.getElementById('observacion');
        let archivo = document.getElementById('archivo');
        if (observacion.value.trim() == "") {
            observacion.style.border = "1px solid red";
            alertas('error', 'Error...', 'Agrege una descripción')
            return;
        } else {
            observacion.style.border = "1px solid #d2d6de"
        }


        var form = $('#form')[0];
        var data = new FormData(form);
        $.ajax({
            enctype: 'multipart/form-data',
            type: "post",
            url: `{{ route('insumos.proformaInsumos.subirExcel') }}`,
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
                console.log("🚀 ~ file: index.blade.php ~ line 175 ~ subirExcel ~ ", datahtml)
                Swal.close();
                let hola = "Como estas ${datahtml}"
                alertas(datahtml.status, `${datahtml.status.toUpperCase()}...`, datahtml.msj)
                if (datahtml.status == 'success') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500)
                }
            },
            error: function(datahtml) {
                alert("ocurrio un error");
            }
        });
    })
</script>