<!-- split buttons box -->
@extends('disponibilidad.base')
@section('action-content')

<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
    
    }
</style>

 <section class="content">
  <div class="box">
    <div class="box-header">  

        <h4><b>{{$hospital->nombre_hospital}}</b></h4>
      <div class="col-md-12" style="text-align: right;">
                            <a type="button" href="{{route('disponibilidad.disponibilidad_menu')}}" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-arrow-left">Regresar</span>
                            </a>
                         </div>
                         
      </div>
       <div class="box-body">
           <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <table id="example2" class="table table-bordered table-hover dataTable" >
              <tbody>
               @foreach($sala as $sala)
                  <div class="col-md-4" style="padding: 5px;">
                      <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                          <a class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center" onClick="agenda_sala({{$sala->id}});">{{$sala->nombre_sala}}
                          </a>
                      </div>
                  </div>
              @endforeach
                <div class="col-md-12" id="agenda"></div>
              </tbody>

            </table>
     
          <div class="col-md-12" id="agendas_todas"></div>
        </div>
      </div>
        
  </div>
   
  <!-- /.box-body -->
    </section>
    

    <!-- /.content -->
    

  <script type="text/javascript">
    $(function () {
     
     agenda_salas_todas();
     

    });

  
  
 </script> 

  <script type="text/javascript">
         function agenda_sala(id){
            console.log(id);
            $.ajax({
        
            type: "GET",
            url: "{{ url('disponibilidad/sala_agenda')}}/"+id,
            datatype: "html",
            success: function(datahtml){

                $("#agenda").html(datahtml);

            },
            error:  function(){
              alert('error al cargar');
            }
          });
        }

        function agenda_salas_todas(){
          //alert("ok");
          
            $.ajax({
        
            type: "get",
            url: "{{ route('disponibilidad.salas_todas',['id_hospital'=> $hospital->id])}}",
            datatype: "html",
            success: function(datahtml){
                  console.log(datahtml);
                $("#agendas_todas").html(datahtml);

            },
            error:  function(){
              alert('error al cargar');
            }
          });
        }
         
    </script> 

@endsection

