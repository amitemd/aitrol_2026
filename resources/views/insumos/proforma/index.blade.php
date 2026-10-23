@extends('insumos.proforma.base')
@section('action-content')

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

<div class="modal fade bd-example-modal-lg rounded" id="modal_proforma" aria-labelledby="myModalLabelGuia" style="width: 100%;">
   <div class="modal-dialog modal-lg" style="width: 60%;">
      <div class="modal-content" id="cargar_modal" style="width: 100%;">

      </div>
   </div>
</div>

<div style="background-color: white; border-radius: 8px; padding: 15px">
   <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
      <h3 class="text-3xl font-semibold text-gray-800">Proformas y notas de pedido</h3>

      <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
         <div class="text-center md:w-auto w-full">
            <button type="button" class="truncate w-full md:w-auto bg-green-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-green-700" onclick="mostrarModalProforma()">
               <i class="fa fa-upload" aria-hidden="true"></i>
               Subir. Excel
            </button>
         </div>
         <div class="text-center md:w-auto w-full">
            <a href="{{ route('insumos.proformaInsumos.excelproforma1') }}" target="_blank">
               <button class="truncate w-full md:w-auto bg-green-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-green-700" type="button">
                  <span class="glyphicon glyphicon-download-alt"></span>
                  Excel
               </button>
            </a>
         </div>
         <div class="text-end md:w-auto w-full">
            <button type="button" onclick="crearProforma(1)" class="truncate w-full md:w-auto bg-green-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-green-700">
               <i class="fa fa-plus" aria-hidden="true"></i> Nueva nota pedido
            </button>
         </div>
         <!-- proforma -->
         <div class="text-end md:w-auto w-full">
            <a type="button" href="{{ route('insumos.proformaInsumos.sin_inventario') }}" class="truncate w-full md:w-auto bg-sky-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-sky-700 cursor-pointer">
               <i class="fa fa-plus" aria-hidden="true"></i> Proforma
            </a>
         </div>
      </div>
   </div>

   <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="mt-4">
      <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
   </div>

   <form action="{{ route('insumos.proformaInsumos.index') }}" method="GET">
      <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-2">
         <div class="mb-5">
            <label for="id" class="block mb-2 text-md font-semibold text-gray-900">Cliente</label>
            <input type="text" id="id" name="cliente" value="@if(isset($data['cliente'])){{$data['cliente']}}@endif" placeholder="Ingrese cliente..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>

         <div class="mb-5">
            <label for="descripcion" class="block mb-2 text-md font-semibold text-gray-900">Descripcion</label>
            <input type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>

         <div class="mb-5">
            <label for="id_usuariocrea" class="block mb-2 text-md font-semibold text-gray-900">Responsable</label>
            <!-- <input type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"> -->
            <select class="form-control rounded-lg" name="id_usuariocrea" id="id_usuariocrea">
               <option value="">{{trans('nuevos.seleccione')}}</option>
               @foreach($nombre as $usuario)
               @php
               $nombre_cliente = "{$usuario->nombre1} {$usuario->nombre2} {$usuario->apellido1} {$usuario->apellido2}";
               @endphp
               <option value="{{$usuario->id_usuariocrea}}" @if(isset($data['id_usuariocrea'])) @if($data['id_usuariocrea']==$usuario->id_usuariocrea) selected @endif @endif>{{$nombre_cliente}}</option>
               @endforeach
            </select>
         </div>
         <div class="mb-5">
            <label for="id_usuariomod" class="block mb-2 text-md font-semibold text-gray-900">Aprueba</label>
            <!-- <input type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"> -->
            <select class="form-control rounded-lg" name="id_usuariomod" id="id_usuariomod"">
               <option value="">{{trans('nuevos.seleccione')}}</option>
               @foreach($aprueba as $aprobado)
               @php
               $nombre_cliente = " {$aprobado->nombre1} {$aprobado->nombre2} {$aprobado->apellido1} {$aprobado->apellido2}";
               @endphp
               <option value="{{$aprobado->id_usuariomod}}" @if(isset($data['id_usuariomod'])) @if($data['id_usuariomod']==$aprobado->id_usuariomod) selected @endif @endif >{{$nombre_cliente}}</option>
               @endforeach
            </select>
         </div>
         <div class="mb-5">
            <label for="estado" class="block mb-2 text-md font-semibold text-gray-900">Estado proforma</label>
            <!-- <input type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"> -->
            <select class="form-control" name="estado" id="estado">
               <option value="" @if(isset($data['estado'])) @if($data['estado']=="" ) selected @endif @endif>Seleccione</option>
               <option value="0" @if(isset($data['estado'])) @if($data['estado']==0) selected @endif @endif>Anuladas</option>
               <option value="1" @if(isset($data['estado'])) @if($data['estado']==1) selected @endif @endif>Aprobadas</option>
               <option value="2" @if(isset($data['estado'])) @if($data['estado']==2) selected @endif @endif>Pendientes</option>
               <option value="3" @if(isset($data['estado'])) @if($data['estado']==3) selected @endif @endif>Caducadas</option>
            </select>
         </div>

         <div class="mb-5">
            <label for="tipo" class="block mb-2 text-md font-semibold text-gray-900">Tipo proforma</label>
            <!-- <input type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"> -->
            <select class="form-control" name="tipo" id="tipo">
               <option value="">Seleccione</option>
               <option value="1" @if(isset($data['tipo'])) @if($data['tipo']==1) selected @endif @endif>Proforma Privada</option>
               <option value="3" @if(isset($data['tipo'])) @if($data['tipo']==3) selected @endif @endif>Proforma Publica</option>
            </select>
         </div>

         <div class="mb-5">
            <label for="fecha_desde" class="block mb-2 text-md font-semibold text-gray-900">Desde</label>
            <input type="date" id="fecha_desde" name="fecha_desde" value="@if(isset($data['fecha_desde'])){{$data['fecha_desde']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>

         <div class="mb-5">
            <label for="fecha_hasta" class="block mb-2 text-md font-semibold text-gray-900">Hasta</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" value="@if(isset($data['fecha_desde'])){{$data['fecha_desde']}}@endif" placeholder="Ingrese descripcion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
         </div>

      </div>


      {{--
         <div class="col-md-12">
         <!-- <div class="">
            <div class="form-group col-md-1 col-xs-2">
               <label class="texto" for="id">Cliente</label>
            </div>
            <div class="form-group col-md-3 col-12 container-4">
               <input class="form-control rounded" type="text" id="id" name="cliente" value="@if(isset($data['cliente'])){{$data['cliente']}}@endif" placeholder="Ingrese cliente...">
</div>
</div> -->
<div>
   <div class="form-group col-md-1 col-xs-1">
      <label class="texto" for="descripcion">Descripcion: </label>
   </div>
   <div class="form-group col-md-3 col-12 container-4">
      <input class="form-control rounded" type="text" id="descripcion" name="descripcion" value="@if(isset($data['descripcion'])){{$data['descripcion']}}@endif" placeholder="Ingrese descripcion...">
   </div>
</div>

<div>
   <div class="form-group col-md-1 col-xs-1">
      <label class="texto" for="descripcion">Responsable: </label>
   </div>
   <div class="form-group col-md-3 col-12 container-4">
      <select class="form-control" name="id_usuariocrea" id="id_usuariocrea">
         <option value="">{{trans('nuevos.seleccione')}}</option>
         @foreach ($nombre as $usuario)
         @php
         $nombre = $usuario->nombre1."&nbsp;".$usuario->nombre2."&nbsp;".$usuario->apellido1."&nbsp;".$usuario->apellido2;
         @endphp
         <option value="{{$usuario->id_usuariocrea}}" @if(isset($data['id_usuariocrea'])) @if($data['id_usuariocrea']==$usuario->id_usuariocrea) selected @endif @endif>{{$nombre}}</option>
         @endforeach
      </select>
   </div>
</div>
</div>

<div class="col-md-12">
   <div class="form-group col-md-1 col-xs-1">
      <label class="texto" for="descripcion">Aprueba: </label>
   </div>
   <div class="form-group col-md-3 col-12 container-4">
      <select class="form-control" name="id_usuariomod" id="id_usuariomod">
         <option value="">{{trans('nuevos.seleccione')}}</option>
         @foreach ($aprueba as $aprobado)
         @php
         $nombre = $aprobado->nombre1."&nbsp;".$aprobado->nombre2."&nbsp;".$aprobado->apellido1."&nbsp;".$aprobado->apellido2;
         @endphp
         <option value="{{$aprobado->id_usuariomod}}" @if(isset($data['id_usuariomod'])) @if($data['id_usuariomod']==$aprobado->id_usuariomod) selected @endif @endif >{{$nombre}}</option>
         @endforeach
      </select>
   </div>


   <div class="form-group col-md-2 col-xs-1">
      <label class="texto" for="descripcion">Estado proforma: </label>
   </div>
   <div class="form-group col-md-2 col-12 container-4">
      <select class="form-control" name="estado" id="estado">
         <option value="" @if(isset($data['estado'])) @if($data['estado']=="" ) selected @endif @endif>Seleccione</option>
         <option value="0" @if(isset($data['estado'])) @if($data['estado']==0) selected @endif @endif>Anuladas</option>
         <option value="1" @if(isset($data['estado'])) @if($data['estado']==1) selected @endif @endif>Aprobadas</option>
         <option value="2" @if(isset($data['estado'])) @if($data['estado']==2) selected @endif @endif>Pendientes</option>
         <option value="3" @if(isset($data['estado'])) @if($data['estado']==3) selected @endif @endif>Caducadas</option>
      </select>
   </div>




   <div class="form-group col-md-2 col-xs-1">
      <label class="texto" for="descripcion">Tipo proforma: </label>
   </div>
   <select class="form-group col-md-2 col-12 container-2" name="tipo" id="tipo">
      <option value="">Seleccione</option>
      <option value="1" @if(isset($data['tipo'])) @if($data['tipo']==1) selected @endif @endif>Proforma Privada</option>
      <option value="3" @if(isset($data['tipo'])) @if($data['tipo']==3) selected @endif @endif>Proforma Publica</option>
   </select>
</div>




<div>
   <div class="form-group col-md-1 col-xs-1">
      <label for="fecha" class="col-md-12 control-label">{{trans('laboratorio.desde')}}</label>
   </div>
   <div class="form-group col-md-3 col-12 container-4">
      <input type="date" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off" value="@if(isset($data['fecha_desde'])){{$data['fecha_desde']}}@endif">
   </div>
</div>



<div>
   <div class="form-group col-md-1 col-xs-1">
      <label for="fecha_hasta" class="col-md-12 control-label">{{trans('laboratorio.hasta')}}</label>
   </div>
   <div class="form-group col-md-3 col-12 container-4">
      <input type="date" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="@if(isset($data['fecha_hasta'])){{$data['fecha_hasta']}}@endif">
   </div>
</div>
--}}


<div class="form-group col-md-3 col-12 container-4">
   <button class="bg-blue-400 py-2 px-4 transition-all duration-500  hover:bg-blue-600 text-white rounded-lg w-full md:w-auto" type="submit" id="btnBuscar">
      <i class="fa fa-search" aria-hidden="true"></i> Buscar
   </button>
</div>

</form>

@php
$fecha_desde = date('Y')."-01-01";
$fecha_hasta = date('Y-m-d');
@endphp

<input type="hidden" id="fecha_desde" value="{{$fecha_desde}}">
<input type="hidden" id="fecha_hasta" value="{{$fecha_hasta}}">

@if($empresa->inventario == 1 and $empresa->egreso_kardex == 1)
<div class="col-md-12 rounded bg-blue-400" style="padding: 0px;">
   <div class="grid grid-cols-2 md:grid-cols-4 place-items-center">
      <div class="contenedor-valores">
         <p style="font-size: 18px;" id="total_venta"></p>
         <p>Ventas</p>
      </div>

      <div class="contenedor-valores">
         <p style="font-size: 18px; white-space: nowrap;">$ {{number_format($valores["total_proforma"], 2, '.', '')}}</p>
         <p>Cotizaciones</p>
      </div>

      <div class="contenedor-valores">
         <p style="font-size: 18px;">{{$valores["cantidad_proforma"]}}</p>
         <p>#Cotizaciones</p>
      </div>

      <div class="contenedor-valores">
         <p style="font-size: 18px;" id="atendidos">0</p>
         <p>#Atendidos</p>
      </div>
   </div>
</div>
@endif

<div class="col-md-12 bg-gray-200 p-2 rounded">
   <p style="margin: 0; font-weight:bold;" class="text-gray-500">Listado de Proformas</p>
</div>


<div class="col-md-12 mt-5" style="padding: 0px;">
   <div class="grid-cards bg-white">

      @foreach($proformas as $value)
      @php
      if ($value->estado == 0){
      $estado ="red";
      $text_estado ="anulada";
      }else if ($value->estado == 1){
      $estado ="green";
      $text_estado ="emitido";
      }else if($value->estado == 3){
      $estado = "gray";
      $text_estado = "Expirado";
      }else{
      $estado ="yellow";
      $text_estado ="pendiente emitir";
      }
      @endphp
      <div class="md:max-w-md w-full border @if($value->tipo == 2) bg-yellow-50 border-yellow-50 @else border-gray-100 @endif shadow-lg rounded-lg p-4 grid place-items-end">
         <div class="w-full">
            @if(isset($value->cliente))
            <div class="border-b-4 border-blue-300">
               <h2 class="font-bold text-gray-800 text-xl">
                  {{$value->cliente->nombre}}
               </h2>
            </div>
            @endif

            <div class="mt-4">
               <p class="font-semibold text-sm">{{$value->secuencia}}</p>
            </div>
            <div class="mt-4">
               <p class="font-semibold text-sm">{{$value->descripcion}}</p>
            </div>

               <div class="mt-4">
                  <p class="font-semibold text-sm">{{$value->fecha_exp}}</p>
               </div>
               @php
               $tiposProforma = ["N/A", "Nota pedido", "Nota Entrega", "Proforma Publica", "Proforma"]
               @endphp
               <div class="mt-4">
                  <p class="font-semibold text-sm underline">{{isset($tiposProforma[$value->tipo]) ? $tiposProforma[$value->tipo] : 'N/A' }}</p>
               </div>

            <div class="mt-4 flex place-items-center item-center gap-2">
               <div class="w-[15px] h-[15px] rounded-full bg-{{$estado}}-500"></div>
               <p class="uppercase font-bold text-sm">
                  {{$text_estado}}
               </p>
            </div>

            <div class="mt-4 w-full">
               <div class="grid-buttons  place-items-center item-center gap-2">

                  @if($value->estado != 2)
                  <a class="w-full" href="{{ route('insumos.proformaInsumos.observarProforma', ['id_proforma'=> $value->id]) }}">
                     <button class="border-2 border-blue-500 text-blue-500 hover:text-white hover:bg-blue-500 transition-all duration-500 rounded w-full text-center font-bold py-1 px-2">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                     </button>
                  </a>
                  @endif
                  @if($value->tipo == 3 and $value->estado == 1)
                  <a class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500 rounded w-full" href="{{ route('insumos.nuevo_formatoproforma', ['id'=> $value->id]) }}">
                     <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                  </a>
                  @endif
                  @if($value->tipo == 4 and $value->estado == 1)
                  <a class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500 rounded w-full" href="{{ route('insumos.pdfProformaSinKardex', ['id'=> $value->id]) }}">
                     <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                  </a>
                  @endif
                  @if($value->estado == 1 and is_null($value->id_venta))
                  <button class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500  rounded w-full" onclick="eliminar(`{{$value->id}}`)">
                     <i class="fa fa-trash" aria-hidden="true"></i>
                  </button>
                  @endif

                  @if($value->estado == 2)
                  <a class="border-2 border-yellow-500 text-yellow-500 hover:text-white hover:bg-yellow-500 text-center font-bold py-1 px-2 transition-all duration-500 rounded w-full" href="{{ url('new_proforma/compras/insumos/editar') }}/{{$value->id}}">
                     <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </a>
                  <button class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500  rounded w-full" onclick="eliminar(`{{$value->id}}`)">
                     <i class="fa fa-trash" aria-hidden="true"></i>
                  </button>
                  @if(!is_null($permiso_aprobado) or Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
                  <button class="border-2 border-green-500 text-green-500 hover:text-white hover:bg-green-500 text-center font-bold py-1 px-2 transition-all duration-500 rounded w-full" type="button" onclick="aprobarProforma(`{{$value->id}}`, 1)">
                     <i class="fa fa-check" aria-hidden="true"></i>
                  </button>
                  @endif
                  @endif
               </div>
            </div>
         </div>
      </div>
      @endforeach
   </div>
</div>
<div class="row">
   <div class="col-sm-5">
      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($proformas->currentPage() - 1) * $proformas->perPage())}} / {{count($proformas) + (($proformas->currentPage() - 1) * $proformas->perPage())}} de {{$proformas->total()}} registros</div>
   </div>
   <div class="col-sm-7">
      <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
         {{ $proformas->appends(request()->query())->links()  }}
      </div>
   </div>
</div>
</div>
<script>
   window.onload = function() {
      valoresVenta()
   }
   const crearProforma = (type = 1) => {
      $.ajax({
         url: `{{ route('insumos.proformaInsumos.store') }}`,
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
         },
         datatype: 'json',
         data: {
            type
         },
         success: function(data) {
            window.location.href = `{{url('new_proforma/compras/insumos/editar/${data.id_proforma}')}}`;
         },
         error: function(data) {

         }
      })
   }

   function eliminar(id_proforma) {
      Swal.fire({
         title: 'Esta seguro?',
         text: "No se podra revertir esta proforma",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Si, Eliminar'
      }).then((result) => {
         if (result.isConfirmed) {
            $.ajax({
               url: `{{ route('insumos.proformaInsumos.delete') }}`,
               type: 'POST',
               headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
               },
               dataType: 'json',
               data: {
                  'id_proforma': id_proforma
               },
               success: function(data) {
                  if (data.status == "success") {
                     alertas("success", "Exito...", data.msj)
                     setTimeout(() => {
                        window.location.reload();
                     }, 2500);
                     return;
                  }
                  alertas("error", "Error...", data.msj)
               },
               error: function(data) {
                  alertas("error", "Error...", "Error a eliminar")
               }
            })
         }
      })
   }

   const valoresVenta = () => {
      $.ajax({
         url: `{{ url('ventas/informeNeto') }}`,
         type: "GET",
         data: {
            app: 1,
            fecha_desde: $("#fecha_desde").val(),
            fecha_hasta: $("#fecha_hasta").val(),
            id_vendedor: `{{Auth::user()->id}}`,
         },
         success: function(data) {
            data = JSON.parse(data);
            document.getElementById("total_venta").innerHTML = `$ ${parseFloat(data.ventaNeta).toFixed(2)}`;
            document.getElementById("atendidos").innerHTML = `${parseInt(data.numFacturas)}`;
         },
         error: function(data) {
            console.log(data);
         }
      })
   }



   const mostrarModalProforma = () => {
      $.ajax({
         url: `{{url('new_proforma/compras/modal')}}`,
         type: 'GET',
         data: {},
         success: function(data) {
            $('#modal_proforma').modal();
            $('#cargar_modal').empty().html(data);
         },
         error: function(data) {

         }
      })
   }


   $(document).ready(function() {
      $("#id_usuariomod").select2();
      $("#id_usuariocrea").select2();
      $("#tipo").select2();
      $("#estado").select2();



   });
</script>

@endsection