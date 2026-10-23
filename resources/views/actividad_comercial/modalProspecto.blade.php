 <!-- Modal -->
 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
     <div class="modal-dialog" role="document">
         <div class="modal-content" style="border-radius: 0.25rem;">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Creacion prospecto</h4>
             </div>
             <div class="modal-body">
                 <form action="" id="formProspecto">

                     <div class="grid md:grid-cols-2 gap-4">
                         <div class="mb-2">
                             <label for="id_cliente" class="block mb-2 text-sm font-medium text-gray-900">Cedula/Ruc</label>
                             <input type="text" name="id_cliente" id="id_cliente" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                         <div class="mb-2">
                             <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900">Nombre</label>
                             <input type="text" name="nombre" id="nombre" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                         <div class="mb-2">
                             <label for="direccion_prospecto" class="block mb-2 text-sm font-medium text-gray-900">Direccion</label>
                             <input type="text" name="direccion_prospecto" id="direccion_prospecto" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                         <div class="mb-2">
                             <label for="ciudad_prospecto" class="block mb-2 text-sm font-medium text-gray-900">Ciudad</label>
                             <input type="text" name="ciudad_prospecto" id="ciudad_prospecto" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                         <div class="mb-2">
                             <label for="correo" class="block mb-2 text-sm font-medium text-gray-900">Correo</label>
                             <input type="email" name="correo" id="correo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                         <div class="mb-2">
                             <label for="telefono" class="block mb-2 text-sm font-medium text-gray-900">Telefono</label>
                             <input type="text" name="telefono" id="telefono" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         </div>
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="text-red-500 font-semibold" data-dismiss="modal">Close</button>
                 <button onclick="storeProspecto()" type="button" class="ml-2 text-white bg-blue-500 hover:bg-blue-700 transition-all duration-500 px-3 py-2 rounded font-semibold">
                     Guardar
                 </button>
             </div>
         </div>
     </div>
 </div>

 <script>
     const storeProspecto = () => {
         $.ajax({
             type: "POST",
             url: `{{ route('actividad_comercial.storeProspecto') }}`,
             dataType: "json",
             headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             data: $("#formProspecto").serialize(),
             success: function(data) {
                 if (data.status == "success") {
                     Swal.fire({
                         icon: 'success',
                         title: 'Prospecto creado',
                         showConfirmButton: false,
                         timer: 1500
                     })
                 }else{
                    Swal.fire({
                         icon: 'error',
                         title: 'Error al crear el prospecto',
                         text: data.message,
                     })
                 }
             },
             error: function(data) {

             }
         })
     }
 </script>