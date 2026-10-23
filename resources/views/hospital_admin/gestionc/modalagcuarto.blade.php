<style type="text/css">
    .input1{
      display: none;
    }
    .input2{
      display: none;
    }
    .input3{
      display: none;
    }
    p{
      font-size: 12px;
    }
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
</style>

<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Crear Habitaciones</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <form action="{{route('hospital_admin.aghabitaciones')}}" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="modal-body">

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Tipo de habitación</label>
          <select class="select form-control" name="id_tipo" onchange="cargarinput(this.value);" required>
            <option value="">TIPO...</option>
            @foreach($tipo as $value)
            <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
          <label >Piso</label>
          <select class="select form-control" name="id_piso" required>
            @foreach($nombre_piso as $value)
            <option value="{{$value->id}}">{{$value->nombre_piso}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
          <label>Numeración</label>
          <input class="form-control" required autofocus type="number" onchange="return validarRango(this);" id="numhabitacion" name="numhabitacion" placeholder="N: De Habitación">
        </div>
      </div>

      <div class="col-md-12" style="margin-bottom: 10px;">
        <div class="row">
        <!--AREA DE LA IMAGEN-->
          <div class="col-md-6 col-sm-6 col-12" >
            <div id="imagen">
              
            </div>
          </div>
          <div class="col-md-6">
            <div class="col-md-12">
            <!--NUMERO DE LA CAMA-1 Y ESTADO DE LA CAMA-1-->
              <div class="input1">
                <div class="row">
                  <!--NUMERO DE LA CAMA-1-->
                  <div class="col-md-6" id="cama1">
                      <input minlength ="2" maxlength ="4" class="form-control form-control-sm" type="text" name="codigo" placeholder="N: DE CAMA 01">
                  </div>
                  <!--ESTADO DE LA CAMA-1-->
                  <div class="col-md-6">
                    <select class="select form-control form-control-sm" name="estado_uno" required>
                      <option value="">ESTADO...</option>
                      <option value="1">LIBRE</option>
                      <option value="2">PREPARACION</option>
                      <option value="3">OCUPADA</option>
                      <option value="4">NO DISPONIBLE</option>
                    </select>
                  </div>
                </div>
                  
              </div>
            </div>
            <div class="col-md-12" style="top: 10px;">
              <!--NUMERO DE LA CAMA-2 Y ESTADO DE LA CAMA-2-->
              <div class="input2">
                <div class="row">
                  <!--NUMERO DE LA CAMA-2-->
                  <div class="col-md-6" id="cama2">
                    <input minlength ="2" maxlength ="4" class="form-control form-control-sm" type="text" name="codigodos" placeholder="N: DE CAMA 02">
                  </div>
                  <!--ESTADO DE LA CAMA-2-->
                  <div class="col-md-6">
                    <select class="select form-control form-control-sm" name="estado_dos">
                      <option value="">ESTADO...</option>
                      <option value="1">LIBRE</option>
                      <option value="2">PREPARACION</option>
                      <option value="3">OCUPADA</option>
                      <option value="4">NO DISPONIBLE</option>
                    </select>
                  </div>
                </div>
                
              </div>
            </div>
            <div class="col-md-12" style="top: 20px;">
            <!--NUMERO DE LA CAMA-3 Y ESTADO DE LA CAMA-3-->
              <div class=" input3">
                <div class="row">
                  <!--NUMERO DE LA CAMA-3-->
                  <div class="col-md-6" id="cama3">
                    <input minlength ="2" maxlength ="4" class="form-control form-control-sm" type="text" name="codigotres" placeholder="N: DE CAMA 03">
                  </div>
                  <div class="col-md-6">
                    <!--ESTADO DE LA CAMA-3-->
                    <select class="select form-control form-control-sm" name="estado_tres">
                      <option value="">ESTADO...</option>
                      <option value="1">LIBRE</option>
                      <option value="2">PREPARACION</option>
                      <option value="3">OCUPADA</option>
                      <option value="4">NO DISPONIBLE</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-trash-alt"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary active"><i class="fas fa-plus"></i> Agregar</button>
    </div>
  </form>
</div>
<script type="text/javascript">
  function cargarinput(valor){
        if(valor==1){
              $('.input1').show('slow');
              $('.input2').hide('slow');
              $('.input3').hide('slow');
              $('#imagen').removeClass();
              $('#imagen').addClass("imagend");             
        } 
        else if(valor==2){
              $('.input1').show('slow');
              $('.input2').show('slow');
              $('.input3').hide('slow');
              $('#imagen').removeClass();
              $('#imagen').addClass("imagend1");
        }
        else if(valor==3){
              $('.input1').show('slow');
              $('.input2').hide('slow');
              $('.input3').hide('slow');
              $('#imagen').removeClass();
              $('#imagen').addClass("imagend2");
        }
        else if(valor==4){
              $('.input1').show('slow');
              $('.input2').show('slow');
              $('.input3').show('slow');
              $('#imagen').removeClass();
              $('#imagen').addClass("imagend3");
        }
        else if(valor==5){
              $('.input1').show('slow');
              $('.input2').hide('slow');
              $('.input3').hide('slow');
              $('#imagen').removeClass();
              $('#imagen').addClass("imagend4");
        }
  }
</script>
<!--SCRIPT PARA VALIDAR EL RAGO DE NUMERO DE LA HABITACION CON 4 DIGITO--->
<script type="text/javascript">
	   function validarRango(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 || numero>9999){
            alert("solo 4 digitos");
            $('#numhabitacion').val("");
            return false;
        }
        return true;
      }
</script>
