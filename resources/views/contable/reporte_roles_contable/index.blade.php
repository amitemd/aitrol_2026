@extends('contable.reporte_roles_contable.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reporte Rol Pago</li>
      </ol>
    </nav>
    <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">REPORTE ROL PAGO POR EMPRESA CONTABLE</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="rol_pag_cont"  action="{{route('reporte_datos_contable.rol')}}"> 
          {{ csrf_field() }}
          @php 
            $id_empresa = Session::get('id_empresa');
          @endphp
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id_empresa">Empresa: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
                <option value="">Seleccione...</option> 
                @foreach($empresas as $value)
                  <option @if($value->id == $id_empresa) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="year">Año</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select id="year" name="year" class="form-control">
              <option value="">Seleccione...</option>  
              <?php
                for($i=2019;$i<=2030;$i++)
                {
                 echo "<option value='".$i."'>".$i."</option>";
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="mes">Mes</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select id="mes" name="mes" class="form-control">
              <option value="">Seleccione...</option>  
              <?php    
                $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                  for ($i=1; $i<=12; $i++) {
                  
                    echo '<option value="'.$i.'">'.$Meses[($i)-1].'</option>';
                  }
              ?>
            </select>
          </div>
          <div class="form-group col-md-6 col-xs-9"> 
            <button type="button" onclick="roles_empleados();" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar Roles de Pago
            </button>
            <button type="button" onclick="descargar_reporte();"  class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Rol Pago
            </button>
          </div> 
        </form> 
      </div>
      <div class="box box" style="border-radius: 8px;" id="area_trabajo">
      </div>
    </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>

  <script type="text/javascript">

    function roles_empleados(){

        var formulario = document.forms["rol_pag_cont"];

        var id_emp = formulario.id_empresa.value;
        var id_anio = formulario.year.value;
        var id_mes = formulario.mes.value;

        //Mensaje 
        var msj = "";

        if(id_emp == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(id_anio == ""){
          msj = msj + "Por favor, Seleccione el año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el mes<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
        type: 'post',
        url:"{{route('buscador_roles_contable.pago')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#rol_pag_cont").serialize(),
        success: function(data){
          $("#area_trabajo").html(data);
          //console.log(data);
        },
        error: function(data){
          console.log(data);
        }
      });

    }

    function descargar_reporte() {
      $( "#rol_pag_cont" ).submit(); 
    }
  
  </script>

  
  </section>
@endsection