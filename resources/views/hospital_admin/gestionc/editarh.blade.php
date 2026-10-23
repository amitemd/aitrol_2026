<style type="text/css">
  
  .imagend{
      background-image: url("{{asset('/')}}hc4/img/simple_block.png");
      width: 30px;
      height: 50px;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
      object-fit: scale-down; 
    }
    .imagend1{
      background-image: url("{{asset('/')}}hc4/img/Doble_Bloqueda.png");
      width: 80px;
      height: 60px;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
      object-fit: scale-down;
    }
    .imagend2{
      background-image: url("{{asset('/')}}hc4/img/suite_bloqueada.png");
      width: 80px;
      height: 70px;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
      object-fit: scale-down;
    }
    .imagend3{
      background-image: url("{{asset('/')}}hc4/img/triple_bloqueada.png");
      width: 150px;
      height: 65px;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover; 
      object-fit: scale-down;
    }
    .imagend4{
      background-image: url("{{asset('/')}}hc4/img/ejecutiva_bloqueada.png");
      width: 80px;
      height: 85px;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
      object-fit: scale-down;
    }
    p{
      font-size: 12px;
    }
</style>
<!-- Modal content editarh-->
<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Editar Habitación</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updateh', ['id' => $habitacionid->id])}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="modal-body">
      
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="id" value="{{$habitacionid->id}}">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
              <label>Tipo de habitación</label>
              <b>@if(($habitacionid->id_tipo)==1) SIMPLE 
                  @elseif(($habitacionid->id_tipo)==2) DOBLE  
                  @elseif(($habitacionid->id_tipo)==3) SUITE  
                  @elseif(($habitacionid->id_tipo)==4) TRIPLE
                  @elseif(($habitacionid->id_tipo)==5) EJECUTIVA @endif</b>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
              <label>Piso</label>
              <b>@if(($habitacionid->id_piso)==1) PISO 1 
                  @elseif(($habitacionid->id_piso)==2) PISO 2 
                  @elseif(($habitacionid->id_piso)==3) PISO 3 @endif 
              </b>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
              <label>Numeración</label>
            
              <input id="codigo" type="text" class="form-control" name="codigo" value="{{$habitacionid->codigo}}"required autofocus>
              
              @if ($errors->has('codigo'))
              <span class="help-block">
                <strong>{{ $errors->first('codigo') }}</strong>
              </span>
              @endif
              </div>
              <!--ME TRAE EL ESTADO DE HABITACION--->
            <div class="col-md-3 col-sm-6 col-12">
              <label>Estado</label>
              <select class="select form-control" name="estado">
                <option value="">Seleccione ...</option>
                <option @if(($habitacionid->estado)==1) Selected @endif value="1">LIBRE</option>
                <option @if(($habitacionid->estado)==2) Selected @endif value="2">PREPARACION</option>
                <option @if(($habitacionid->estado)==3) Selected @endif value="3">OCUPADA</option>
                <option @if(($habitacionid->estado)==4) Selected @endif value="4">NO DISPONIBLE</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="row">
            <!--AREA DONDE SE MUESTRA LA IMAGEN-->
            <div class="col-md-6">
              <div id="imagen">
                @if(($habitacionid->id_tipo)==1)
                <img src="{{asset('/')}}hc4/img/simple_block.png" style="width: 50px;">
                @elseif(($habitacionid->id_tipo)==2)
                <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" style="width: 100px;">
                @elseif(($habitacionid->id_tipo)==3)
                <img src="{{asset('/')}}hc4/img/Suite.png" style="width: 100px;">
                @elseif(($habitacionid->id_tipo)==4)
                <img src="{{asset('/')}}hc4/img/Triple_Bloqueada.png" style="width: 125px;">
                @elseif(($habitacionid->id_tipo)==5)
                <img src="{{asset('/')}}hc4/img/Ejecutiva_Bloqueada.png" style="width: 100px;">
                @endif
              </div>
            </div>
            <!--CAMPOS PARA EDITAR SEGUN EL TIPO DE ESTADO DE LA HABITACION--->
            <div class="col-md-6 my-2">
                <div class="row">
                  <!--ME TRAE EL NÚMERO DE LA CAMA-->
                  <div class="col-md-6 col-sm-6 col-12" id="camainput">
                    @php $numero= 1; $est='estadoC';  @endphp
                    @foreach($cama as $value)
                    <input id="{{$est.$numero }}" type="text" class="form-control" name='{{$est.$numero++ }}' value="{{$value->codigo}}" required autofocus><br>
                    @endforeach
                    @if ($errors->has('estado_uno'))
                    <span class="help-block">
                      <strong>{{ $errors->first('estado_uno') }}</strong>
                    </span>
                    @endif
                  </div>
                  <!--ME TRAE EL ESTADO DE LA CAMA-->
                  <div class="col-md-6 col-sm-6 col-12">
                    @php $numeroestado= 1; @endphp
                    @foreach($cama as $value)
                    <select class="select form-control" id="estado{{$numeroestado}}" name="estado{{$numeroestado++}}" style="margin-bottom: 25px;">
                      <option @if(($value->estado)==1) Selected @endif value="1">LIBRE</option>
                      <option @if(($value->estado)==2) Selected @endif value="2">PREPARACION</option>
                      <option @if(($value->estado)==3) Selected @endif value="3">OCUPADA</option>
                      <option @if(($value->estado)==4) Selected @endif value="4">NO DISPONIBLE</option>
                    </select>
                    @endforeach
                  </div>
                </div>
            </div>
          </div>
        </div>
      
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="sumit" class="btn btn-primary active"><i class="far fa-edit"></i> Editar</button>
    </div>
  </form>
</div>
<script type="text/javascript">

  function mostar(){

  var myselect = document.getElementById("id_tipo").value;
    if(myselect.options[myselect.selectedIndex].value == 1){
      
    }else if(myselect.options[myselect.selectedIndex].value == 2){
      document.getElementById('estado1').style.display='block';
      document.getElementById('estadoC1').style.display = "block";
      document.getElementById('estado2').style.display='block';
      document.getElementById('estadoC2').style.display = "block";
        
    }
  }
  </script>