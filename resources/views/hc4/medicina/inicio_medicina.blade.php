<style type="text/css">

  .boton-1{
    font-size: 10px ;
    width: 20%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .boton-2{
    font-size: 10px ;
    width: 60%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .color{
    font-size: 12px; 
    color: #004AC1; 
   }
   .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px #004AC1 !important;
   }
</style>
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px;">	  

  <div class="container-fluid" id="detalle" style="padding-left: 0px; padding-right: 0px;">      
     <div class="row" style="padding-left: 0px;padding-right: 0px;">
        <div class="col-md-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">
            <div class="row" >  
              <div class="col-md-6 col-6" style="padding: 10px;"> 
                  <h1 style="font-size: 15px; margin:0;">
                    <img style="width: 49px;" src="{{asset('/')}}hc4/img/med2_blanco.png"> 
                    <b>CREAR / EDITAR MEDICINAS</b>
                  </h1>
              </div>
              <div class="col-md-2 col-3" style="padding: 10px;">
                <a class="btn btn-danger" onclick="crear_medicina();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white; width: 100%; height: 100%">
                  <span class="glyphicon glyphicon-list-alt">&nbsp;Crear Medicina</span>
                </a>
              </div>
              <!--<div class="col-md-2 col-3" style="padding: 10px;">
                <a class="btn btn-danger" onclick="editar_medicina();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white; width: 100%; height: 100%">  
                  <span class="glyphicon glyphicon-list-alt">&nbsp;Editar Medicinas</span>
                </a>
              </div>-->
            </div>
        </div>
    </div>
    <!--<div class="box box" style="border-radius: 8px;" id="area_trabajo">
    </div>-->  
  </div>
 
<script>
   
  function crear_medicina(){
    $.ajax({
      type: "GET",
      url: "{{route('crear.medicina_hc4')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#detalle").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
     });
  }

  function editar_medicina(){
    $.ajax({
      type: "GET",
      url: "{{route('editar.medicina_hc4')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#detalle").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
     });
  }

</script>  
</section>




