<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-md">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-xl font-semibold" id="exampleModalLabel">Observacion</h4>
            </div>
            <div class="modal-body">
                <form id="form_observacion">
                    <input name="id_actividad" type="hidden" id="id_actividad" value="">
                    <input name="estado_transaccion" type="hidden" id="estado_transaccion" value="">

                    <div class="form-group">
                        <textarea name="justificacion" rows="4" placeholder="Escribir observacion..." class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="ml-4 text-red-500  hover:bg-red-100 text-center font-bold py-2 px-4 transition-all duration-500 rounded-md" data-dismiss="modal">Close</button>
                <button type="button" onclick="guardarEstadoTipo()" class="ml-2 border-2 bg-blue-500 text-white hover:text-white hover:bg-blue-700 text-center font-bold py-2 px-4 transition-all duration-500 rounded-md">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    const guardarEstadoTipo = () => {
        $.ajax({
            type: 'POST',
            url: `{{ route('actividad_comercial.storeEstado') }}`,
            data: $('#form_observacion').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.status == 'success') {
                    return Swal.fire({
                        icon: 'success',
                        title: "Exito...",
                        text: data.message,
                    }).then(function() {
                        return location.reload();
                    })
                }

                return Swal.fire({
                    icon: data.status,
                    title: data.status.toUppercase(),
                    text: data.message,
                })

            },
            error: function(data) {
                console.log(data)
            }
        })
    }
</script>