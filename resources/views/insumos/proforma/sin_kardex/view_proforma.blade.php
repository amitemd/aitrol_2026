@extends('insumos.proforma.base')
@section('action-content')


<div style="background-color: white; border-radius: 15px; padding: 20px;">
   <div class="flex justify-between items-center place-items-center">
      @if($proforma->tipo == 1)
      <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
         PROFORMA <br> Nº {{ $proforma->secuencia }}
      </h3>
      @endif

      @if($proforma->tipo == 2)
      <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
         NOTA ENTREGA <br> Nº {{ $proforma->secuencia }}
      </h3>
      @endif

      @if($proforma->tipo == 3)
      <h3 class="text-2xl font-semibold text-gray-800 leading-tight" style="font-weight: bold; text-transform: uppercase; text-decoration: double;">
         PROFORMA PUBLICA <br> Nº {{ $proforma->secuencia }}
      </h3>
      @endif
      <a href="{{ route('insumos.proformaInsumos.index') }}">
         <button class="bg-red-500 hover:bg-red-700 text-white font-semibold duration-500 transition-all py-2 px-4 rounded">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp; Regresar
         </button>
      </a>
   </div>
   <br>
   <hr>
   <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
         <div style="width:100%">
            <label for="">Cliente</label>
         </div>
         @if(isset($proforma->cliente))
         <p>{{$proforma->cliente->nombre}}</p>
         @endif
      </div>

      @if($proforma->tipo == 3)

      <div class="">
         <label for="">Tipo necesidad:</label>
         <p>{{ $proforma->tipo_necesidad }}</p>
      </div>

      <div class="">
         <label for="">Codigo CPC:</label>
         <p>{{ $proforma->codigo_cpc }} </p>
      </div>

      <div class="">
         <label for="">Cod. de contratacion:</label>
         <p>{{ $proforma->cod_contratacion }}</p>
      </div>

      @endif
      <div>
         <label for="">Descripcion</label>
         <p>{{ $proforma->descripcion }}</p>
      </div>

   </div>
   <a href="{{route('insumos.proformaInsumos.excelproforma',['id_proforma'=>$id_proforma])}}" style="margin-top: 10px;" target="_blank">
      <button class="bg-green-500 hover:bg-green-700 text-white font-semibold duration-500 transition-all py-2 px-4 rounded">
         <span class="glyphicon glyphicon-download-alt"></span> &nbsp;
         Descargar Excel
      </button>
   </a>
   <div style="width: 100%; margin-top: 20px;">
      <div style="text-align: center; font-size: 12px; overflow:scroll">
         <table class="table table-bordered table-striped table-hover">
            <thead>
               <tr class="bg-blue-500 text-white">
                  <th colspan='10' class="text-center text-lg font-semibold">Detalles</th>
               </tr>
               <tr>
                  <th>Cod. Producto</th>
                  <th>Nombre</th>
                  <th>Cantidad</th>
                  <th>Precio</th>
                  <th>Descuento</th>
                  <th>Iva</th>
                  <th>Total</th>
               </tr>
            </thead>
            <tbody id="detalles">
               @foreach($detalles as $detalle)
               <tr id='fila_{{$detalle->id}}'>
                  <td class="text-left">{{$detalle->producto->codigo}}</td>
                  <td class="text-left">{{$detalle->producto->nombre}}</td>
                  <td class="text-end">
                     <p>{{$detalle->cantidad}}</p>
                  </td>
                  <td class="text-end">
                     <p>{{$detalle->precio}}</p>
                  </td>
                  <td class="text-end">
                     <p>{{$detalle->descuento}}</p>
                  </td>
                  <td class="text-end"> <input type='checkbox' @if($detalle->iva == 1) checked @endif disabled> </td>
                  <td class="text-end">
                     <p>{{$detalle->total}}</p>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>


   <div class="grid grid-cols-1 md:grid-cols-3 mb-4 gap-3">
      @if($proforma->tipo == 3)
      <div class="">
         <div class="grid gap-2 items-center">
            <p class="font-semibold whitespace-nowrap">Forma de pago</p>
            <p>{{ $proforma->forma_pago }}</p>
         </div>
      </div>

      <div class="">
         <div class="grid gap-3   items-center">
            <p class="font-semibold whitespace-nowrap">Tiempo de entrega</p>
            <div class="flex gap-1 items-center">
               <p>{{ $proforma->tiempo_entrega }}</p>
               <p class="whitespace-nowrap font-semibold">Dias habiles</p>
            </div>
         </div>
      </div>

      <div class="">
         <div class="grid gap-2 items-center">
            <p class="font-semibold whitespace-nowrap">Garantia</p>
            <p>{{ $proforma->garantia }} </p>
         </div>
      </div>
      @endif
   </div>
</div>

@endsection