<style type="text/css">
  .boton-buscar{
    font-size: 14px ;
    width: 70%;
    height: 35px;
    background-color: #004AC1;
    color: white;
    text-align: center;

  }


tbody>tr:hover{
  background-color: #8CAFFF;
}

</style>
<section class="content">
  <div class="box ">
    <div class="box-header">

        <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
          <div class="col-12" style="background-color: #004AC1; padding: 10px">
              <label class="box-title" style="color: white; font-size: 20px">Lista de Procedimientos Completos
              </label>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6" style="padding: 1px;">
                <form method="POST" id="proc_plantilla" >
                  <div class="row">
                      {{ csrf_field() }}
                      <div class="col-md-5">
                        <label for="proc_com" class="control-label col-md-6" style="font-size: 14px"><span style="font-family: 'Helvetica general';">Buscar Procedimiento</span>
                        </label>
                      </div>
                    <div class="col-md-5" style="width: 50px">
                        <select class="form-control select2"  name="proc_com" style="width: 100%;">
                              <option value="">Todos</option>
                          @foreach($proc_completo as $value)
                            <option @if($value->id == $procedimiento_completo) selected @endif value="{{$value->id}}">{{$value->nombre_general}}
                            </option>
                          @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 " >
                      <button style="background-color: #004AC1" type="button"  onclick="buscar_plantilla_proc()" class="btn btn-info " id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </div>
                </div>
              </form>
              </div>
            <div class="col-md-2 ">
                <button  type="button" class="btn btn-success " onclick="crear_plantilla_proc()" >
                  <span class="glyphicon glyphicon-plus">
                    Crear Procedimiento
                  </span>
                </button>
            </div>
          </div>
        </div>
    </div>

    <div class="box-body">
      <div id="plantillas_proc" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
            <div class="table-responsive col-md-12" >
            <table id="example2" class="table table-bordered dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                    <tr >
                      <th  width="15%"><span style="font-family: 'Helvetica general';">Nombre</span></th>
                      <th  width="50%"><span style="font-family: 'Helvetica general';">Hallazgo</span></th>
                      <th  width="5%"><span style="font-family: 'Helvetica general';">Grupo</span></th>
                      <th  width="5%"><span style="font-family: 'Helvetica general';">Record</span></th>
                      <th  width="5%"><span style="font-family: 'Helvetica general';">Estado</span></th>
                    </tr>
                  </thead>
                   <tbody >
                  @foreach ($tecnicas_quirurgicas as $value)
                      <tr  onclick="editar_plantilla({{$value->id}});" >
                        <td style="font-size: 12px;">{{ $value->nombre_completo}}</td>
                        <td style="font-size: 12px;"><?php echo $value->tecnica_quirurgica; ?></td>
                        <td style="font-size: 12px;">@if(!is_null($value->id_grupo_procedimiento)){{ $value->grupo_procedimiento->nombre }}@endif</td>
                        <!--<td>{{ $value->precio_compra }}</td>-->
                        <td style="text-align: center;vertical-align: middle;">@if($value->estado_anestesia == 0){{"No"}}@endif @if($value->estado_anestesia == 1){{"Si"}}@endif</td>
                        <td style="text-align: center;vertical-align: middle;">@if($value->estado == 0){{"Inactivo"}}@endif @if($value->estado == 1){{"Activo"}}@endif</td>
                    </tr>
                  @endforeach
                  </tbody>
            </table>
            <label style="padding-left: 15px;font-size: 16px">Total de Registros: {{$tecnicas_quirurgicas->count()}}</label>
            </div>
          </div>
      </div>
    </div>
</section>

 <script type="text/javascript">

    $(document).ready(function(){

      $('.select2').select2({
        tags: false
      });

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });


      $(".breadcrumb").append('<li class="active">Procedimientos</li>');

    });


     function buscar_plantilla_proc(){
      //alert("entro");
        $.ajax({
          type: 'post',
          url:"{{route('hc4/plantilla_proc.search')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#proc_plantilla").serialize(),
          success: function(data){
            //console.log(data);
            $("#area_trabajo").html(data);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

        function editar_plantilla(id){
          //alert("yes");
      $.ajax({
      type: "GET",
      url: "{{route('hc4/plantilla_proc.edit')}}/"+id,
      data: "",
      datatype: "json",
      success: function(data){
        //console.log(data);
        $("#area_trabajo").html(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function crear_plantilla_proc(){
      //alert("entro");
        $.ajax({
          type: 'post',
          url:"{{route('hc4/plantilla_proc.create')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          //data: $("#proc_plantilla").serialize(),
          success: function(data){
            //console.log(data);
            $("#area_trabajo").html(data);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

  </script>