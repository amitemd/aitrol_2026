@extends('insumos.proforma.base')
@section('action-content')

<style>
   /* Clases para el buscador */
   .lds-ellipsis {
      display: inline-block;
      position: relative;
      width: 80px;
      height: 80px;
   }

   .lds-ellipsis div {
      position: absolute;
      top: 33px;
      width: 13px;
      height: 13px;
      border-radius: 50%;
      background: var(--colorPrimario);
      animation-timing-function: cubic-bezier(0, 1, 1, 0);
   }

   .lds-ellipsis div:nth-child(1) {
      left: 8px;
      animation: lds-ellipsis1 0.6s infinite;
   }

   .lds-ellipsis div:nth-child(2) {
      left: 8px;
      animation: lds-ellipsis2 0.6s infinite;
   }

   .lds-ellipsis div:nth-child(3) {
      left: 32px;
      animation: lds-ellipsis2 0.6s infinite;
   }

   .lds-ellipsis div:nth-child(4) {
      left: 56px;
      animation: lds-ellipsis3 0.6s infinite;
   }

   @keyframes lds-ellipsis1 {
      0% {
         transform: scale(0);
      }

      100% {
         transform: scale(1);
      }
   }

   @keyframes lds-ellipsis3 {
      0% {
         transform: scale(1);
      }

      100% {
         transform: scale(0);
      }
   }

   @keyframes lds-ellipsis2 {
      0% {
         transform: translate(0, 0);
      }

      100% {
         transform: translate(24px, 0);
      }
   }

   /* Fin de las clases para el buscador */
   .ancho {
      width: 100%;
   }

   @media (min-width: 768px) {
      .ancho {
         width: 40%;
      }
   }

   .borde {
      border-radius: 6px;
   }

   .btn-general {
      font-size: 17px;
      border-radius: 5px;
      padding: 1px 20px;
      background: transparent;
      transition: all 0.4s ease-in-out;
   }

   .btn-eliminar {
      border: 3px solid #dd4b39;
      color: #dd4b39;
   }

   .btn-editar {
      border: 3px solid #f39c12;
      color: #f39c12;
   }

   .btn-eliminar:hover {
      color: white;
      background: #dd4b39;
   }

   .btn-editar:hover {
      color: white;
      background-color: #f39c12;
   }

   .swal2-icon.swal2-warning {
      font-size: 37px !important;
   }

   .swal2-icon-content {
      font-size: 102px;
   }

   .buscador_proforma {
      display: flex;
      justify-content: center;
      gap: 10px;
   }

   @media (max-width: 666px) {
      .buscador_proforma {
         flex-direction: column;
      }
   }


   .notification {
      position: fixed;
      bottom: 50px;
      left: 50px;
      width: max-content;
      padding: 20px 15px;
      border-radius: 4px;
      background-color: #141619;
      color: #f6f5f9;
      box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
      transform: translateY(30px);
      opacity: 0;
      visibility: hidden;
      animation: fade-in 4s linear forwards;
   }

   .notification__progress {
      position: absolute;
      left: 5px;
      bottom: 5px;
      width: calc(100% - 10px);
      height: 3px;
      transform: scaleX(0);
      transform-origin: left;
      background-image: linear-gradient(to right, #539bdb, #3250bf);
      border-radius: inherit;
      animation: load 3s 0.25s linear forwards;
   }

   @keyframes fade-in {
      5% {
         opacity: 1;
         visibility: visible;
         transform: translateY(0);
      }

      90% {
         opacity: 1;
         transform: translateY(0);
      }
   }

   @keyframes load {
      to {
         transform: scaleX(1);
      }
   }
</style>

<div style="background-color: white; border-radius: 15px; padding: 20px;">
   <div style="display: flex; justify-content: space-between;">

      @if($proforma->tipo == 1)
      <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
         Nota pedido <br> Nº {{ $proforma->secuencia }}
      </h3>
      @endif

      @if($proforma->tipo == 2)
      <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
         NOTA ENTREGA <br> Nº {{ $proforma->secuencia }}
      </h3>
      @endif

      @if($proforma->tipo == 3)
      <div class="grid w-full">
         <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
            PROFORMA PUBLICA
         </h3>
         <input name="secuencia" onchange="handlePublica(this)" class="form-control borde md:w-[30%]" type="text" value="{{$proforma->secuencia }}">
      </div>
      @endif

      <a href="{{ route('insumos.proformaInsumos.index') }}">
         <button class="bg-red-500 hover:bg-red-700 text-white hover:text-white font-semibold py-2 px-4 rounded duration-300 transition-all flex gap-3 items-center">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar
         </button>
      </a>
   </div>
   <br>
   <hr>
   <hr>


   <div class="row mt-4">
      <div class="col-md-3">
         <div style="width:100%">
            <label for="">Cliente</label>
         </div>
         <select style="width: 100%;" name="id_cliente" id="id_cliente" class="form-control select2_cliente" onchange="guardarCabecera(1)">
            <option value="">Seleccione...</option>
            @if(isset($proforma->cliente))
            <option selected value="{{$proforma->cliente->identificacion}}">{{$proforma->cliente->nombre}}</option>
            @endif
         </select>
      </div>
      <div class="col-md-3">
         <label for="">Fecha Exp:</label>
         <input type="date" id="fecha_exp" class="form-control borde" value="{{ $proforma->fecha_exp }}" onchange="guardarCabecera(3)">
      </div>


      @if($proforma->tipo == 3)

      <div class="col-md-3">
         <label for="">Tipo necesidad:</label>
         <input type="text" name="tipo_necesidad" id="tipo_necesidad" class="form-control borde" value="{{ $proforma->tipo_necesidad }}" onchange="handlePublica(this)">
      </div>

      <div class="col-md-3">
         <label for="">Codigo CPC:</label>
         <input type="text" name="codigo_cpc" id="codigo_cpc" class="form-control borde" value="{{ $proforma->codigo_cpc }}" onchange="handlePublica(this)">
      </div>

      <div class="col-md-3">
         <label for="">Cod. de contratacion:</label>
         <input type="text" name="cod_contratacion" id="cod_contratacion" class="form-control borde" value="{{ $proforma->cod_contratacion }}" onchange="handlePublica(this)">
      </div>

      @endif

      <div class="col-md-12">
         <div class="">
            <label for="descripcion" class="block mb-2 text-md font-bold text-gray-900">Descripcion</label>
            <input type="text" id="descripcion" value="{{ $proforma->descripcion }}" onchange="guardarCabecera(2)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>
      </div>
   </div>
   <div style="width: 100%">
      <div style="margin-top: 10px;">
         <div style="text-align: center;">
            <p style="font-weight: bold;">Buscador</p>
         </div>
      </div>
      <div class="buscador_proforma">
         <div class="ancho">
            <p style="font-weight: 600;">Bodega</p>
            <select name="id_bodega" id="id_bodega" class="form-control borde">
               <option value="">Seleccione la bodega...</option>
               @foreach($bodegas as $bodega)
               <option value="{{$bodega->id}}">{{$bodega->nombre}}</option>
               @endforeach
            </select>
         </div>
         <div class="ancho">
            <abbr title="PUEDE BUSCAR POR: LOTE, SERIE, CODIGO PRODUCTO, NOMBRE PRODUCTO">
               <p><i class="fa fa-info-circle" aria-hidden="true"></i></p>
            </abbr>
            <input placeholder="Escriba por lo menos 3 caracteres..." type="text" id="buscador" class="form-control borde" onkeyup="buscador()">
         </div>
      </div>
   </div>

   <div style="width: 100%; margin-top: 20px;">
      <div id="contenedor_buscador" style="text-align: center; font-size: 12px;  overflow-y: scroll;scroll-behavior: smooth;">

      </div>
   </div>

   <div style="width: 100%; margin-top: 20px;">
      <div style="text-align: center; font-size: 12px; overflow-y: scroll;scroll-behavior: smooth;">
         <div class="w-full bg-blue-500 rounded text-white text-lg uppercase font-bold p-2">
            Detalles
         </div>
         <table class='table table-bordered table-striped table-hover'>
            <thead>

               <tr>
                  <th>Cod. Producto</th>
                  <th>Nombre</th>
                  <th>Lote</th>
                  <th>Bodega</th>
                  @if($proforma->tipo == 3)
                  <th>Registro Sanitario</th>
                  <th>Espef. Tecnica</th>
                  <th>Espef. Tecnica Ofertada</th>
                  @endif
                  <th>Cantidad</th>
                  <th>Precio</th>
                  <th>Descuento</th>
                  <th>Iva</th>
                  <th>Total</th>
                  <th>Accion</th>
               </tr>
            </thead>
            <tbody class="font-semibold" id="detalles">
               @php echo $detalle["table"]; @endphp
            </tbody>
         </table>
      </div>
   </div>


   <div class="">
      <div class="">
         <div class="mb-6 w-[95%] md:w-[30%]">
            <label for="diasPlazo" class="block mb-2 text-sm font-semibold text-gray-900">Dias Plazo</label>
            <input type="number" id="diasPlazo" value="{{ is_null($proforma->dias_plazo) ? 30 : $proforma->dias_plazo }}" onchange="guardarCabecera(4)" name="diasPlazo" value="30" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>
      </div>
   </div>
   <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      @if($proforma->tipo == 3)
      <div class="">
         <div class="grid gap-2 items-center">
            <p class="font-semibold whitespace-nowrap">Forma de pago</p>
            <input onchange="handlePublica(this)" type="text" name="forma_pago" value="{{ $proforma->forma_pago }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>
      </div>

      <div class="">
         <div class="grid gap-2  items-center">
            <p class="font-semibold whitespace-nowrap">Tiempo de entrega</p>
            <div class="flex gap-1 items-center">
               <input onchange="handlePublica(this)" type="number" name="tiempo_entrega" value="{{ $proforma->tiempo_entrega }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[50%] p-2.5">
               <p class="whitespace-nowrap font-semibold">Dias habiles</p>
            </div>
         </div>
      </div>

      <div class="">
         <div class="grid gap-2 items-center">
            <p class="font-semibold whitespace-nowrap">Garantia</p>
            <input onchange="handlePublica(this)" type="text" name="garantia" value="{{ $proforma->garantia }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>
      </div>

      <div class="">
         <div class="grid gap-2 items-center">
            <p class="font-semibold whitespace-nowrap">Vigencia de la oferta</p>
            <input onchange="handlePublica(this)" type="text" name="vigencia_oferta" value="{{ $proforma->vigencia_oferta }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>
      </div>
      @endif
   </div>

   @if(!is_null($permiso_aprobado) or Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
   @if($proforma->estado == 2)
   <div style="text-align: center;" class="mt-3">
      <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded capitalize transition-all duration-500 shadow-2xl" onclick="aprobarProforma(`{{$proforma->id}}`, 1)">
         <i class="fa fa-check" aria-hidden="true"></i> Aprobar
      </button>
   </div>
   @endif
   @endif

   @if($proforma->pre_aprobada == 0)
   <div style="text-align: center;" class="mt-3">
      <button type="button" class="bg-cyan-500 hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded capitalize transition-all duration-500 shadow-2xl" onclick="preaprobarProforma()">
         <i class="fa fa-check" aria-hidden="true"></i> Pre aprobar
      </button>
   </div>
   @endif

</div>
@if($proforma->tipo == 3)
<script>
   const handlePublica = (e) => {
      $.ajax({
         type: "post",
         dataType: "json",
         url: `{{ route('insumos.proformaInsumos.updatePublica') }}`,
         data: {
            column: e.name,
            value: e.value,
            id_proforma: `{{$proforma->id}}`
         },
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         success: function(data) {
            console.log(data)
         },
         error: function(data) {
            console.log(data)
         }
      })
   }

   const handleDetail = (e, id) => {
      $.ajax({
         type: "post",
         dataType: "json",
         url: `{{ route('insumos.proformaInsumos.updatePublicaDetails') }}`,
         data: {
            column: e.name,
            value: e.value,
            id: id
         },
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         success: function(data) {
            console.log(data)
         },
         error: function(data) {
            console.log(data)
         }
      })
   }
</script>
@endif

<script>
   const popUpAlert = (msj, icon) => {
      let notificacion = $('body');
      notificacion.append(`
            <div class="notification">
               <p>${msj} ${icon}</p>
               <span class="notification__progress"></span>
            </div>`)
   }

   const guardarCabecera = (tipo) => {
      let valor = '';
      if (tipo == 1) {
         valor = document.getElementById("id_cliente").value
      } else if (tipo == 2) {
         valor = document.getElementById("descripcion").value
      } else if (tipo == 3) {
         valor = document.getElementById("fecha_exp").value
      } else {
         valor = document.getElementById("diasPlazo").value
      }

      $.ajax({
         url: `{{ route('insumos.proformaInsumos.updateCabecera') }}`,
         type: 'POST',
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         data: {
            'id_proforma': `{{ $proforma->id }}`,
            'tipo': tipo,
            'valor': valor
         },
         success: function(data) {
            console.log(data)
            if (data.status == "error") {
               let notificacion = document.querySelectorAll(".notification").remove()
               popUpAlert(data.msj, '❌')
            }
         },
         error: function(data) {

         }
      })
   }

   const buscador = () => {
      let bodega = document.getElementById('id_bodega')
      let buscador = document.getElementById('buscador')

      if (buscador.value.trim().length < 3) {
         $('#contenedor_buscador').empty()
         return;
      }

      if (bodega.value == "") {
         bodega.style.border = "1px solid red";
         return;
      } else {
         bodega.style.border = "1px solid #d2d6de";
      }

      let contenedor_buscador = $('#contenedor_buscador')

      $.ajax({
         url: `{{ route('insumos.proformaInsumos.buscador') }}`,
         type: 'POST',
         dataType: 'json',
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         data: {
            'buscador': buscador.value.trim(),
            'bodega': bodega.value
         },
         beforeSend: function() {
            contenedor_buscador.empty()
            contenedor_buscador.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
         },
         success: function(data) {

            if (data.status == 'success') {
               contenedor_buscador.empty()
               contenedor_buscador.append(data.table)
               return;
            }

            let htmlCssError = `
            <div style="text-align: center;color: #e64942;">
               <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
            </div>`;
            contenedor_buscador.empty().append(htmlCssError);
         },
         error: function(data) {

         }
      })

   }

   const agregarDetalle = (id_inventario) => {

      if (validarDatos()) {
         return alertas("error", "Error..", "Faltan campos por llenar")
      }

      let especificacion_tecnica = document.getElementById('espec_tecnica_' + id_inventario).value;
      let especificacion_tecnica_ofertada = document.getElementById('espec_tecnica_ofertada_' + id_inventario).value;

      let existencia = document.getElementById('existencia_' + id_inventario).value;
      let cantidad = document.getElementById('cantidad_' + id_inventario);
      let precio = document.getElementById('precio_' + id_inventario).value
      let descuento = document.getElementById('descuento_' + id_inventario).value
      let iva = document.getElementById('iva_' + id_inventario).value


      if (cantidad.value <= 0) {
         alertas('error', "Error...", "Cantidad incorreta")
         return;
      }

      // if (parseInt(cantidad.value) > parseInt(existencia)) {
      //    alertas('error', "Error...", "La cantidad es superior a la existencia actual")
      //    return;
      // }

      let detalles = $('#detalles')

      $.ajax({
         url: `{{ route('insumos.proformaInsumos.updateDetails') }}`,
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         beforeSend: function() {
            detalles.empty()
            detalles.append(`<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>`)
         },
         data: {
            'id': `{{$proforma->id}}`,
            'id_inv_serie': id_inventario,
            'cantidad': cantidad.value,
            'precio': precio,
            'descuento': descuento,
            'iva': iva,
            'accion': 1,
            especificacion_tecnica_ofertada,
            especificacion_tecnica
         },
         success: function(data) {
            if (data.status == 'success') {
               detalles.empty()
               detalles.append(data.table)
               return;
            }
            popUpAlert(data.msj, '❌')

         },
         error: function(data) {
            console.log(data)
         }
      })
   }

   const calcularProducto = (id_inventario) => {
      let cantidad = document.getElementById('cantidad_' + id_inventario).value;
      let precio = document.getElementById('precio_' + id_inventario).value;
      let decuenta = document.getElementById('descuento_' + id_inventario).value;
      let iva = document.getElementById('iva_' + id_inventario).value
      let calcIva = ((cantidad * precio) - descuento) * iva;
      let total = ((cantidad * precio) - descuento) + calcIva;
   }

   const eliminarDetalle = (id_detalle) => {
      $.ajax({
         url: `{{ route('insumos.proformaInsumos.updateDetails') }}`,
         type: 'POST',
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },

         data: {
            'id_detalle': id_detalle,
            'accion': 0
         },
         success: function(data) {
            if (data.status == "success") {
               document.getElementById("fila_" + id_detalle).remove();
               return;
            }
            alertas("error", "Error...", data.msj.toUpperCase());
         },
         error: function(data) {

         }
      })
   }


   const editFila = (id_detalle, id_serie) => {
      let precio = document.getElementById(`precio_${id_detalle}_${id_serie}`).value;
      let descuento = document.getElementById(`descuento_${id_detalle}_${id_serie}`).value;



      let especificacion_tecnica = document.getElementById(`espec_tecnica_${id_detalle}_${id_serie}`);
      let especificacion_tecnica_ofertada = document.getElementById(`espec_tecnica_ofertada_${id_detalle}_${id_serie}`);

      especificacion_tecnica = especificacion_tecnica ? especificacion_tecnica.value : "";
      especificacion_tecnica_ofertada = especificacion_tecnica_ofertada ? especificacion_tecnica_ofertada.value : "";

      let detalles = $('#detalles')

      precio = precio == '' ? 0 : precio;
      descuento = descuento == '' ? 0 : descuento;

      $.ajax({
         url: `{{ route('insumos.proformaInsumos.editarFila') }}`,
         type: 'POST',
         headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
         },
         data: {
            id_detalle,
            precio,
            descuento,
            "espec_tecnica": especificacion_tecnica,
            "espec_tecnica_ofertada": especificacion_tecnica_ofertada
         },
         success: function(data) {
            if (data.status == 'success') {
               detalles.empty()
               detalles.append(data.table)
               popUpAlert(data.msj, '✔')
               return;
            }

            let htmlCssError = `
            <div style="text-align: center;color: #e64942;">
               <h4 style="font-weight: 600;"><i class="fa fa-times-circle" aria-hidden="true"></i> ${data.msj}</h4>
            </div>`;
            detalles.empty().append(htmlCssError);
            popUpAlert(data.msj, '❌')
            return;
         },
         error: function(data) {

         }
      })
   }

   /*const preaprobarProforma = () => {
      $.ajax({
         type: "post",
         url: `{{ route('insumos.proformaInsumos.preaprobar') }}`,
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         data: {
            id_proforma: `{{ $proforma->id }}`
         },
         success: function(data) {
            console.log(data)
         },
         error: function(data) {
            console.log(data)
         }
      })
   }*/
   const preaprobarProforma = () => {
   $.ajax({
      type: "post",
      url: `{{ route('insumos.proformaInsumos.preaprobar') }}`,
      headers: {
         'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      data: {
         id_proforma: `{{ $proforma->id }}`
      },
      success: function(data) {
         if (data.status === "success") {
            Swal.fire({
               icon: 'success',
               title: data.msj,
               showConfirmButton: false,
               timer: 1500
            }).then(() => {
               window.location.href = "{{ route('insumos.proformaInsumos.index') }}";
            });
         } else {
            Swal.fire({
               icon: 'error',
               title: 'Error',
               text: data.msj
            });
         }
      },
      error: function(data) {
         Swal.fire({
            icon: 'error',
            title: 'Error en el servidor',
            text: 'No se pudo completar la acción'
         });
         console.error(data);
      }
   });
};

</script>

<script>
   window.onload = function() {
      setTimeout(() => {
         $(".select2_cliente").trigger("changue");
      }, 1500);
   }

   $('.select2_cliente').select2({
      tags: false
   });
   $('.select2_cliente').select2({
      placeholder: `Seleccione un cliente...`,
      allowClear: true,
      minimumInputLength: 3,
      cache: true,
      ajax: {
         url: `{{ route('insumos.proformaInsumos.buscadorClientes') }}`,
         data: function(params) {
            var query = {
               search: params.term,
               type: 'public'
            }
            return query;
         },
         processResults: function(data) {
            return {
               results: data
            };
         }
      }
   });
</script>

@endsection