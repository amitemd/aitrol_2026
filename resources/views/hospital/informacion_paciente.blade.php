<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --color: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    }

    .emitido {
        background-color: #00a65a;
        font-weight: 600;
        color: #ffffff;
    }

    .pendiente {
        background-color: #f39c12;
        font-weight: 600;
        color: #ffffff;
    }

    .anulado {
        background-color: #dd4b39;
        font-weight: 600;
        color: #ffffff;
    }

    .swal2-icon.swal2-warning {
        font-size: 37px !important;
    }

    .swal2-icon-content {
        font-size: 102px;
    }

    .swal2-warning {
        margin: 16px !important;
    }

    .contenedor-valores {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 5px 20px;
        width: 100%;
        text-align: center;
    }

    .contenedor-valores>p {
        margin: 0px;
        font-weight: 600;
        color: white;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(20em, 1fr));
        gap: 20px;
        margin-top: 0px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }
</style>
<div style="border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Informacion Paciente</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">

        </div>
    </div>
    <br><br>
    <div class="w-full">
        <button type="button" onclick="info_tipo('{{$paciente->id}}','1')" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-2 py-2 transition-all duration-300  hover:bg-green-700"> Consultas</button>
        <button type="button" onclick="info_tipo('{{$paciente->id}}','2')" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-2 py-2 transition-all duration-300  hover:bg-green-700"> Procedimientos</button>
        <button type="button" onclick="info_tipo('{{$paciente->id}}','3')" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-2 py-2 transition-all duration-300  hover:bg-green-700"> Laboratorio</button>
        <button type="button" onclick="info_tipo('{{$paciente->id}}','4')" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-2 py-2 transition-all duration-300  hover:bg-green-700"> Doc. Externas</button>
        <button type="button" onclick="info_tipo('{{$paciente->id}}','5')" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-2 py-2 transition-all duration-300  hover:bg-green-700"> Ordenes Proc.</button>
    </div>

    <div class="w-full" id="det_info">

    </div>
</div>

<script>
    function info_tipo(id, idtipo) {
        let detalles = $('#det_info')
        $.ajax({
            url: "{{url('bd/infoTipo')}}/" + id + '/' + idtipo,
            type: "GET",
            datatype: 'json',
            beforeSend: function() {
                detalles.empty()
                detalles.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
            },
            success: function(data) {
                detalles.empty()
                detalles.append(data)
                return;
            },
            error: function(data) {
                // alertas(data.status, data.status, data.msj)
            }
        })
    }
</script>