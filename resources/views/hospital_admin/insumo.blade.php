@extends('hospital_admin.base')
@section('action-content')
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
     
    .boton-proce{
      font-size: 15px ;
      width: 20%;
      background-color: #004AC1; 
      color: white;
      text-align: center;
      height: 35px;
      padding-left: 5px;
      padding-right: 5px;
      padding-bottom: 0px;
      padding-top: 7px; 
      margin-bottom: 5px; 
    } 
     
    .parent{
     height: 462px;
    }
    .titulo{
    font-family: 'Helvetica general' !important;
    font-family: 'Monserrat-Bold' !important;
    border-bottom:  solid 1px #004AC1 !important;
   }

</style>

<div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-2" style=" text-align: center;">
        <b style="">FARMACIA</b>
        </div>
        <div class="col-md-10" style="border-bottom-style: dashed; border-bottom-width: 3px; margin-bottom: 12px; opacity: : 0.7;">
        </div>
      </div>
    </div>
  
  </div>

<section style=" margin-left: 4px;margin-right: 4px;">  

<!---CUADRO DE OPCIONES DE FARMACIA--->
<div class="box-body" style="border: 2px solid #004AC1; border-radius: 8px;">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-2" style="margin-top: 20px;">
        <b style=" color: #004AC1">LISTA DE PRODUCTOS</b>
      </div>
      <div class="col-md-10">
        <div class="col-md-12" >
          <div class="row">

              <div class="col-md-3"  >
              <button class="btn btm-primary boton-proce" style="; border-radius: 10px; width: 200px;" id="boton" onclick="guardar_procedimiento();">
                Marcas
              </button>
              </div>

              <div class="col-md-3">              
              <button class="btn btm-primary boton-proce" style=" border-radius: 10px; width: 200px;" id="boton" onclick="guardar_procedimiento();">
                Tipos de productos
              </button>
              </div>

              <div class="col-md-3">
              <button class="btn btm-primary boton-proce" style=" border-radius: 10px; width: 200px;" id="boton" onclick="location.href='{{route('hospital_admin.agregarprodu')}}'">
                Agregar productos
              </button>
              </div>

              <div class="col-md-3">
              <button class="btn btm-primary boton-proce" style=" border-radius: 10px; width: 200px;" id="boton">Ingreso de Productos
              </button>
              </div>

          </div>
        </div>
        <div class="col-md-12"  >
          <div class="row">
            <div class="col-md-1"></div>
            <center>
              <div class="col-md-3">
              <button class="btn btm-primary boton-proce" style="; border-radius: 10px; width: 200px;" id="boton" onclick="guardar_procedimiento();">
                Pedidos Realizados
              </button>
         </div>
          <div class="col-md-3">
            <button class="btn btm-primary boton-proce" style=" border-radius: 10px; width: 200px;" id="boton" onclick="guardar_procedimiento();">En Transito
            </button>
          </div>
          <div class="col-md-3">
            <button class="btn btm-primary boton-proce" style=" border-radius: 10px; width: 200px;" id="boton" onclick="guardar_procedimiento();">Descargar Reporte
            </button>
          </div>
            </center>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px;">      
    <div class="col-md-12" style=" margin-top: 5px; padding: 8px; border-radius: 30px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">   
      <form method="POST" id="form_buscador" action="">
        {{ csrf_field() }}
        <div class="row">

          <div class="col-md-12 col-sm-12 col-12"> 
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-5">
                    <h1 class="col-md-10" style="font-size: 15px; color: white"><img src="{{asset('/')}}hc4/img/Busqueda_Medicamentos.png" style="width: 30px; text-align:right;">
                      <b style="">RESULTADOS DE LA B&Uacute;SQUEDA
                      </b>
                    </h1> 
                </div>
                <div class="col-md-5" style="margin-top: 12px">
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" required maxlength="30" placeholder="CÓDIGO" style="text-align: center;">
                    </div>
                    <div class="col-md-6">
                      <input required maxlength="30" placeholder="NOMBRE" style="text-align: center;">
                    </div>
                  </div>
                </div>
                <div class="col-md-2" style="margin-top: 12px">
                  <button type="button" style=" font-family: Monserrat Medium; background-color: #004AC1; font-family: Monserrat Medium; margin: 2px;color: white; text-align: center;border-radius: 30px;">
                    <img src="{{asset('/')}}hc4/img/busqueda.png" style="width: 20px; text-align:right;"> &nbsp;&nbsp;<b style="">BUSCAR</b>
                  </button>
                </div>
            </div>
            </div>
             
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="box-body" style = "border: 2px solid #004AC1;">
    <div id="area_cambiar2" class="col-md-12">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="margin-right: 850px;">
              <thead>
                <tr role="row">
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Código</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Marca</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Nombre</th>
                  <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Cantidad</th>
                  <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Stock</th>
                  <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="" >Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr role="row" class="odd">
                  <td style="">Anthony Chilán</td>
                  <td style="">Desarrollo</td>
                  <td style="">Activado</td> 
                  <td style="">3 Paquetes</td> 
                  <td style="">Disponibidad</td> 
                  <td><a type="button" class="btn btn-primary" style="">Ver</a></td>
                </tr>
                <tr role="row" class="odd">
                  <td style="">Miguel Poveda</td>
                  <td style="">Desarrollo</td>
                  <td style="">Activado</td> 
                  <td style="">2 Paquetes</td> 
                  <td style="">Disponibidad</td>  
                  <td><a type="button" style="" class="btn btn-primary">Ver</a></td>
                </tr>
                <tr role="row" class="odd">
                  <td style="">Fausto Javier</td>
                  <td style="">Desarrollo</td>
                  <td style="">Activado</td> 
                  <td style="">3 Paquetes</td> 
                  <td style="">Disponibidad</td> 
                  <td><a type="button" style="" class="btn btn-primary">Ver</a></td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

  <script type="text/javascript">
  $("#fecha").change(function(){
    alert($("#fecha").val());
  });
  function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscador_paciente_fecha();
      };
  }
  function cambio_fecha(){
    alert('cambio');
  }


</script>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>


</section>

@endsection
