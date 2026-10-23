@extends('layouts.app-template')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="content-wrapper cnt_wrapper">
  <!-- Content Header (Page header) -->
  <div class="p-4">
    @php
    $empresa = \Sis_medico\Empresa::find(Session::get('id_empresa'));
    @endphp

    <!-- Contenedor para imagen y nombre -->
    <div class="flex items-center space-x-4 ml-3 my-2">
      @if($empresa && $empresa->logo != null)
      <img src="{{ asset('/logo') . '/' . $empresa->logo }}" class="w-24 h-10 object-contain">
      @endif

      <span class="text-red-600 font-bold text-lg">
        @if(isset($empresa))
        {{ $empresa->id }} {{ $empresa->nombrecomercial }}
        @else
        GASTROCLINICA
        @endif
      </span>
    </div>

    <!-- Contenido adicional -->
    <section class="content">
      @yield('action-content')
    </section>
  </div>
</div>
@endsection