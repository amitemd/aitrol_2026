@extends('contable.caja_banco.base')
@section('action-content')

<style type="text/css">
  .separator{
    width:100%;
    height:30px;
    clear: both;
  }
</style>


  <script type="text/javascript">
    
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8) {
            return true;
        }
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }
    function goBack() {
      window.history.back();
    }

    function obtener_detalle_grupo(){

      var valor= $("#grupo_plan_cuenta").val();

        $.ajax({
          type: 'post',
          url:"{{route('caja_banco.detallegrupo')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {'opcion': valor},
          success: function(data){

            if(data.value!='no'){

              if(valor!=0){
                
                $("#detalle_grupo").empty();
                $.each(data,function(key, registro) {
                  $("#detalle_grupo").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                });
            
              }else{
                  $("#detalle_grupo").empty();
              }

            }
          },
          error: function(data){
            console.log(data);
          }

        })

    }
  </script>
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="../banco">Banco</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Editar Banco</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
          <form id="enviar_caja_banco" class="form-vertical" role="form" method="POST" action="{{route('banco_clientes.update',[$sucursal->id])}}">
            {{ csrf_field() }}
            
            <div class="separator"></div>
            <div class="box-body col-xs-24">
              <div class="form-group  col-xs-6">
                  <label for="nombre" class="col-md-4 texto">Nombre:</label>
                  <div class="col-md-7">
                     <input id="nombre" name="nombre" type="text" class="form-control" value="{{$sucursal->nombre}}">
                  </div>
              </div>
              <div class="form-group col-md-6">
                  <label for="estado" class="col-md-4 texto">Estado</label>
                  <div class="col-md-7">
                    <select id="estado" name="estado" class="form-control" required>
                      <!-- <option>Seleccione...</option> -->
                      <option {{ $sucursal->estado == 1 ? 'selected' : ''}} value="1">activo</option>
                      <option {{ $sucursal->estado == 0 ? 'selected' : ''}} value="0">inactivo</option>
                    </select>
                  </div>
              </div>
              <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit"  class="btn btn-default btn-gray btn_add">
                      <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                    </button>
                </div>
              </div>
            </div>
          </form>
        </div>
    </div>
  </section>
     
@endsection
