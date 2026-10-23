@extends('insumos.proforma.base')
@section('action-content')


<div style="background-color: white; border-radius: 15px; padding: 20px;">
   <div class="flex justify-between items-center place-items-center" >
      <h3 class="text-3xl font-semibold uppercase ">Proforma </h3>
      <a href="{{ route('insumos.proformaInsumos.index') }}">
         <button class="bg-red-500 hover:bg-red-700 text-white font-semibold duration-500 transition-all py-2 px-4 rounded">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp; Regresar
         </button>
      </a>
   </div>
   <br>
   <hr>
   <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
      <div>
         <div style="width:100%">
            <label for="">Cliente</label>
         </div>
         @if(isset($proforma->cliente))
         <p>{{$proforma->cliente->nombre}}</p>
         @endif
      </div>
      <div style="grid-column-start: 1; grid-column-end: 3;">
         <label for="">Descripcion</label>
         <p>{{ $proforma->descripcion }}</p>
      </div>
      <a href="{{route('insumos.proformaInsumos.excelproforma',['id_proforma'=>$id_proforma])}}" target="_blank">
         <button class="bg-green-500 hover:bg-green-700 text-white font-semibold duration-500 transition-all py-2 px-4 rounded">
            <span class="glyphicon glyphicon-download-alt"></span> &nbsp;
            Descargar Excel
         </button>
      </a>
   </div>
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
                  <th>Lote</th>
                  <th>Bodega</th>
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
                  <td class="text-left">{{$detalle->inv_serie->producto->codigo}}</td>
                  <td class="text-left">{{$detalle->inv_serie->producto->nombre}}</td>
                  <td class="text-left">{{$detalle->lote}}</td>
                  <td class="text-left">{{$detalle->inv_serie->bodega->nombre}}</td>
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
</div>

@endsection