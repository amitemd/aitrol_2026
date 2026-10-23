@extends('layouts.app-template')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style type="text/css">
  .color_rojo {
    font-size: 15pt;
    font-weight: bold;
    color: red;
  }


  .conten {
    position: fixed;
    bottom: 10px;
  }

  /*notificacion de barra*/
  .notEmail {
    display: none;
    position: relative;
    width: 320px;
    height: 66px;
    margin-bottom: 5px;
    background-color: #ffffff;
    padding: 5px;
    border: 1px solid rgba(0, 0, 0, 0.5);
    box-sizing: border-box;
    overflow: hidden;
    -webkit-border-radius: 0 5px 5px 0;
    border-radius: 0 5px 5px 0;

    -webkit-box-shadow: 9px 5px 7px 0 rgba(0, 0, 0, 0.3);
    box-shadow: 9px 5px 7px 0 rgba(0, 0, 0, 0.3);

    animation-name: slideIn;
    animation-duration: 1s;
    animation-timing-function: ease-out;
    animation-iteration-count: 1;
    opacity: 1;
    animation-delay: 0s;
  }

  .mensaje_not {
    float: left;
    width: 60%;
    height: 10px;
    text-align: center;
    font-size: 15px;
    margin-top: 16px;
    font-weight: bold;
  }

  .notEmail>img {
    float: right;
    height: 100%;
    background-size: contain;

  }

  @keyframes slideIn {
    0% {
      margin-left: -500px;
      opacity: 0;
    }

    100% {
      margin-left: 0px;
      opacity: 1;
    }
  }
</style>
<div class="content-wrapper cnt_wrapper">
  <!-- Content Header (Page header) -->
  <div>
    <div style="padding: 10px; background-color: #f5f5f5; border-radius: 5px; margin-bottom: 10px;">
      @php
      $empresa = \Sis_medico\Empresa::find(Session::get('id_empresa'));
      @endphp
      @if($empresa->logo!=null)
      <div>
        <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
      </div>
      @endif
      <div>
        <span class="color_rojo">
          @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
        </span>
      </div>
    </div>

    <section class="content">
      @yield('action-content')
    </section>
  </div>

</div>
@endsection