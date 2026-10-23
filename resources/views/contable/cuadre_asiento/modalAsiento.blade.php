 <!-- Modal -->
 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
     <div class="modal-dialog w-[60%]" role="document">
         <div class="modal-content" style="border-radius: 0.25rem;">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Posibles asientos de anulacion</h4>
             </div>
             <div class="modal-body">
                 <div class="max-h-[40vh] overflow-scroll">
                     <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                         <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                             <tr>
                                 <th  scope="col" class="px-6 py-3 w-[10%]">
                                     Id asiento
                                 </th>
                                 <th scope="col" class="px-6 py-3 w-[60%]">
                                     Observacion
                                 </th>
                                 <th scope="col" class="px-6 py-3 w-[30%]">
                                     Valor
                                 </th>
                             </tr>
                         </thead>
                         <tbody id="cuerpo_asiento">

                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>