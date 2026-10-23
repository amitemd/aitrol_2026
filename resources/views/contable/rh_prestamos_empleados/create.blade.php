@extends('contable.rh_prestamos_empleados.base')
@section('action-content')

<style type="text/css">

  .separator{
    width:100%;
    height:30px;
    clear: both;
  }

  .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
  }

</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="../empleados">Prestamos a Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo Prestamo</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Fichero de Prestamo</h3>
            </div>
            <div class="col-md-1 text-right">
                <button id="crear_rol_pago" onclick="crear_rol()" class="btn btn-primary btn-gray">
                   Guardar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
            <form class="form-vertical"  id="crear_prestamo" role="form" method="POST" autocomplete="off">
                {{ csrf_field() }}
                <div class="separator"></div>
                <!--Monto Prestamo-->
                <div class="form-group col-xs-6 cl_monto_prestamo">
                    <label for="monto_prestamo" class="col-md-4 texto">Monto Prestamo:</label>
                    <div class="col-md-10">
                        <input id="monto_prestamo" type="text" class="form-control" name="monto_prestamo" value="{{ old('monto_prestamo') }}" onkeypress="return isNumberKey(event)"  onblur="checkformat(this);">
                        <span class="help-block">
                           <strong id="str_monto_prestamo"></strong>
                        </span>
                    </div>
                </div>
                <!--Tipo Rol -->
                <div class="form-group  col-xs-6 cl_tipo_rol">
                  <label for="tipo_rol" class="col-md-4 texto">Cobrar en Tipo Rol:</label>
                  <div class="col-md-10">
                    <select id="tipo_rol" name="tipo_rol" class="form-control">
                      <option>Seleccione...</option>
                      @foreach($ct_tipo_rol as $value)
                        <option value="{{$value->id}}">{{$value->descripcion}}</option>
                      @endforeach 
                    </select>
                    <span class="help-block">
                        <strong id="str_tipo_rol"></strong>
                    </span>
                  </div>
                </div>
            </form>
        </div>
    </div>
   
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) { 


      var test = entry.value;

      if (!isNaN(test)) {
          entry.value=parseFloat(entry.value).toFixed(2);
      }
      
      if (isNaN(entry.value) == true){      
          entry.value='0.00';        
      }
      if (test < 0) {
 
          entry.value = '0.00';
      }
    }

    function goBack() {
      window.history.back();
    }

</script>

</section>
@endsection
