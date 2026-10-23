<style>
    .btn-guardar-modal {
        background-color: rgba(0, 166, 90, 0.2);
    }

    .btn-guardar-modal:hover {
        background-color: rgba(0, 166, 90, 1);
        color: white;
    }

    .btn-cancelar-modal {
        background-color: rgba(221, 75, 57, 0.2);
    }

    .btn-cancelar-modal:hover {
        background-color: rgba(221, 75, 57, 1);
        color: white;
    }

    .btn-modal {
        border-radius: 10px;
        padding: 5px 40px;
        color: white;
        border: 1px solid transparent;
        color: black;
        font-weight: 500;
        transition: all .3s ease-in-out;
    }
</style>
<div style="padding: 10px 20px;">
    <div style="text-align: center;">
        <p style="font-size: 20px; font-weight: 600">Comentarios de la Orden</p>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
        <textarea style="border-radius: 10px; padding: 10px;" name="observacion" id="observacion" cols="60" rows="5">{{ $orden->comentario }}</textarea>
    </div>
    <div style="display:flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
        <button onclick="guardar()" class="btn-modal btn-guardar-modal" type="button">Guardar</button>
        <button id="cerrar" class="btn-modal btn-cancelar-modal" type="button">Cancelar</button>
    </div>  
</div>

<script>
    function guardar() {
        $.ajax({
            url: `{{ route('laboratorio.orden.comentarios_guardar') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                observacion: $('#observacion').val(),
                id_orden: `{{ $orden->id }}`
            },
            success: function(data) {
                if (data.status == "error") {
                    alertas("error", "Error", data.msj)
                    return;
                }
                Swal.fire({ position: 'center', icon: 'success', title: data.msj, showConfirmButton: false, timer: 2000 })
                
                setTimeout(() => {
                    $('#comentarios_orden').click();
                }, 2000)
            },
            error: function(data) {
                alertas("error", "Error", "Ocurrio un error");
                return;
            }
        })
    }
</script>


<script>
    document.getElementById('cerrar').addEventListener('click', () => {
        $('#comentarios_orden').click()
    });
</script>
