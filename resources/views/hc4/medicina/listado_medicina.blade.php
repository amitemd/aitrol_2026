<style type="text/css">

  .table-hover>tbody>tr:hover{
    background-color: #ccffff;
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
<section class="content" style="padding-left: 0px; padding-right: 0px;padding-top: 0px;">
  <div class="col-12" style="font-family: Helvetica;color: white; margin-top: 5px; margin-bottom: 0px; padding: 10px;  background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1);border-radius: 5px;">
    <form method="POST" id="form">
      {{ csrf_field() }}
      <div class="row">  
        <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 5px"> 
          <h1 style="font-size: 15px; margin:0;margin-top: 15px">
           <b>LISTADO DE MEDICINAS</b>
          </h1>
        </div>
        <div class="col-md-5 col-12" style="padding-right: 0px;right: 0px; top: 10px">
            <div class="row">
              <div class="col-2"style="padding-right: 5px" >
                <h1 style="font-size: 15px; margin:0;margin-top: 15px">
                  <span>Nombre</span> 
                </h1>
              </div>
              <div class="col-4" style="padding-right: 5px">
                <div class="input-group">
                   <input value="@if($nombre!=''){{$nombre}}@endif" type="text" class="form-control input-sm" name="nombre" id="nombre" placeholder="medicina " style="text-transform:uppercase;" onkeypress="enviar_enter(event);">
                </div>
              </div>
              <div class="col-4" style="padding-right: 5px">
                <a class="btn btn-info" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;" onclick="buscar_medicina()"> <i class="fa fa-search" aria-hidden="true">
                </i> 
                &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;
                </a>
              </div>
            </div>
        </div> 
        <div class="col-md-3 col-12" style="padding: 10px;">
          <div class="row">
            <div class="col-6"style="padding-right: 5px" >
              <a class="btn btn-danger" onclick="crear_medicina();" style="color:white; background-color:#004AC1 ;  border-radius: 5px; border: 2px solid white; width: 100%; height: 100%">
                <span class="glyphicon glyphicon-plus">&nbsp;Crear Medicina</span>
              </a>
            </div>
            <div class="col-6"style="padding-right: 5px" >
              <a class="btn btn-danger" onclick="crear_generico();" style="color:white; background-color:#004AC1 ;  border-radius: 5px; border: 2px solid white; width: 100%; height: 100%">
                <span class="glyphicon glyphicon-list-alt">&nbsp;Genéricos</span>
              </a>
            </div>
          </div>
        </div>
      </div>
     </form>
    </div>
    <div class="box-body" style="border: 2px solid #004AC1;border-radius: 3px;">
      <div class="col-md-12">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          <div class="table-responsive col-md-12 col-xs-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
              <thead style="">
                <tr style=" ">
                <th class="color titulo">Nombre</th>
                <th class="color titulo">Dosis</th>
                <th class="color titulo">Cantidad</th>
                <th class="color titulo">Genérico</th>
                <th class="color titulo">Acci&oacute;n</th>
                </tr>
              </thead>
              <tbody>
                @foreach($medicinas as $medicina)
                <tr>
                  <td class="color">{{$medicina->nombre}}</td>
                  <td class="color">{{$medicina->dosis}}</td>
                  <td class="color">{{$medicina->cantidad}}</td>
                  <td class="color">  
                    @php 
                      $medicina_principio = DB::table('medicina_principio')->where('id_medicina',$medicina->id)->get(); 
                    @endphp 
                    @foreach($medicina_principio as $md)    
                      <span class="label label">@if(!is_null($genericos->where('id',$md->id_principio_activo)->first())){{$genericos->where('id',$md->id_principio_activo)->first()->nombre}}@endif
                      </span>
                    @endforeach
                  </td>
                  <td class="color">
                    <a class="btn btn-info boton-2" style="color: white; width: 100%; height: 100%" onclick="editar_medicina({{$medicina->id}});">
                      <span style="font-size: 15px">
                        &nbsp;Editar
                      </span>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
   
      </div>
    </div>
</section>

<script type="text/javascript">

  //Funcion que permite Crear la medicina
  function crear_medicina(){
    $.ajax({
      type: "GET",
      url: "{{route('crear.medicina_hc4')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#area_trabajo").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
     });
  }

   //Funcion que permite Crear Generico
  function crear_generico(){
    $.ajax({
      type: "GET",
      url: "{{route('listar.medicina_generica.hc4')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#area_trabajo").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
     });
  }

  //Funcion que permite Editar la medicina
  function editar_medicina(id){
           $.ajax({
           type: "GET",
           url: "{{route('edit.medicina')}}/"+id, 
           data: "",
           datatype: "html",
           success: function(datahtml){
            $("#area_trabajo").html(datahtml);
           },
           error:  function(){
           alert('error al cargar');
           }
        });
  }


  //Funcion que permite buscar la medicina
  function buscar_medicina(){
         $.ajax({
            type:'post',
            url:"{{route('medicina.buscar_hc4')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
              $("#area_trabajo").html(data);
              console.log(data);
            },
            error:  function(){
                alert('error al cargar');
            }
        }); 
  }

</script>

<script type="text/javascript">
    $('#retlista_crea_edita').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('agregar_edit.medicina')}}",
          success: function(data){
            $("#info").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
      
    });

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
</script> 


<script type="text/javascript">
  function enviar_enter(e){
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscar_medicina();
      };
  }
</script>  


