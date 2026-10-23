@extends('contable.rol_anticipo_quincena.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Anticipo Quincena</li>
      </ol>
    </nav>
    <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">CALCULO ANTICIPO QUINCENA EMPLEADOS POR EMPRESA</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="calculo_anticipo" action="{{route('anticipos_quincena.reporte')}}"> 
          {{ csrf_field() }}
          
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id_empresa">Empresa: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
                <option value="">Seleccione...</option> 
                @foreach($empresas as $value)
                  <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
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
          <div class="form-group col-md-1 col-xs-1">
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
          <br>
          <div class="form-group col-md-1 col-xs-1">
            <label class="texto" for="valor_porcent">% Quincena:</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4" style="padding-left: 15px;">
            <input class="form-control" type="text" id="valor_porcent" name="valor_porcent"  placeholder="Ingrese Porcentaje..."  value=""/>
          </div>
          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;"> 
            <button type="button" onclick="calculo_anticipo_empleado();" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Calculo Anticipo
            </button>
            <button type="button" onclick="buscar_anticipos();" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
            <button type="button" onclick="descargar_reporte();" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
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
    
    function calculo_anticipo_empleado(){
        
        var formulario = document.forms["calculo_anticipo"];

        var id_emp = formulario.id_empresa.value;
        var anio = formulario.year.value;
        var id_mes = formulario.mes.value;
        
        var porcent_valor = formulario.valor_porcent.value;
        
        //Mensaje 
        var msj = "";

        if(id_emp == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
        }

        if(porcent_valor == ""){
          msj = msj + "Por favor, Ingrese el valor de Porcentaje Quincena<br/>";
        }

        if(porcent_valor > 100){
          msj = msj + "El valor de Porcentaje Quincena no puede ser mayor al 100%<br/>";
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
        url:"{{route('anticipos_quincena.valor')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#calculo_anticipo").serialize(),
        success: function(data){
          $("#area_trabajo").html(data);
          //console.log(data);
        },
        error: function(data){
          console.log(data);
        }
      });
    
    }

    function buscar_anticipos(){

      var formulario = document.forms["calculo_anticipo"];
      var id_emp = formulario.id_empresa.value;
      var anio = formulario.year.value;
      var id_mes = formulario.mes.value;

      var msj = "";

        if(id_emp == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(anio == ""){
          msj = msj + "Por favor, Seleccione el Año<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "Por favor, Seleccione el Mes<br/>";
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
          url:"{{route('anticipos_quincena.buscar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#calculo_anticipo").serialize(),
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
      $( "#calculo_anticipo" ).submit(); // Click on the checkbox
    }

    /*function reporte_anticipo_empleado(){

      var formulario = document.forms["calculo_anticipo"];

      var id_mes = formulario.mes.value;
      var id_emp = formulario.id_empresa.value;
      var porcent_valor = formulario.valor_porcent.value;

       
      var msj = "";

      if(id_mes == ""){
        msj = msj + "Por favor, Seleccione la Quincena del Mes<br/>";
      }

      if(id_emp == ""){
        msj = msj + "Por favor, Seleccione la Empresa<br/>";
      }

      if(porcent_valor == ""){
        msj = msj + "Por favor, Ingrese el valor de Porcentaje Quincena<br/>";
      }

      if(porcent_valor > 100){
        msj = msj + "El valor de Porcentaje Quincena no puede ser mayor al 100%<br/>";
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
        url:"{{route('anticipos_quincena.reporte')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: $("#calculo_anticipo").serialize(),
        success: function(datahtml){
          console.log(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });

    
    }*/


  </script>
  </section>
@endsection
