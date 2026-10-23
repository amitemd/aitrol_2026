@extends('laboratorio.encuesta_laboratorio.pregunta_labs.base')
@section('action-content')

 
    <!-- Main content -->
    <section class="content">


      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista Resultados de Encuesta</h3>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <!--AQUI VA EL BUSCADOR-->
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          
          
              @foreach($encuesta as $value)
                    
                <div class="box box-success">
              <div class="box-header with-border">
                <h4>{{trans('laboratorio.encuesta-')}} N°{{$value->id}}</h4>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Fecha</label>
                  <div class="col-sm-8">
                      {{date("Y-m-d",strtotime($value->created_at))}}
                    </div>
                  </div>
              </div>
                  <div class="box-body">
                   
                       
                              <br>
                                <table border="2">
                                  <thead>
                                    <tr role="row">
                                      <th width="20%" style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">Criterios de Evaluacion</th>
                                      <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY BUENO</th>
                                      <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">BUENO</th>
                                      <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">NI BUENO NI MALO </th>
                                      <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MALO</th>
                                      <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY MALO</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                              @foreach($value->complementos as $dato2)
                                @if($dato2->id_grupo != 4 && $dato2->id_grupo != 2 )
                                  <tr role="row">
                                    <td style="align-content: center">{{$dato2->pregunta->nombre}}</td>
                                    <td align="center">@if($dato2->valor=='5') x @else &nbsp;@endif</td>
                                    <td align="center">@if($dato2->valor=='4') x @else &nbsp;@endif</td>
                                    <td align="center">@if($dato2->valor=='3') x @else &nbsp;@endif</td>
                                    <td align="center">@if($dato2->valor=='2') x @else &nbsp;@endif</td>
                                    <td align="center">@if($dato2->valor=='1') x @else &nbsp;@endif</td>
                                    
                                  </tr>
                                  @endif
                                  @endforeach
                            </tbody>
                          </table>
              
                        @foreach($value->complementos as $dato1)
                        @if($dato1->id_grupo ==2)
                        <div class="col-md-12">
                          <br>
                          
                            <div class="col-md-6" style="vertical-align: middle; border-color: black; font-size: 15px;">{{$dato1->pregunta->nombre}}</div>
                            <div class="col-md-6" style="text-align: center; border-color: black;vertical-align: middle;font-size: 15px;">{{$dato1->calificacion}} puntos</div>
                        </div>
                        @endif
                       
                        @if($dato1->id_grupo ==4)
                              <div class="col-md-12">
                                <br>
                                    <div class="col-md-6" style="vertical-align: middle; border-color: black; font-size: 15px;">{{$dato1->pregunta->nombre}}</div>
                                    <div class="col-md-6" style="text-align: center; border-color: black;vertical-align: middle;font-size: 15px;">{{$dato1->valor}}</div>
                          
                              </div> 
                        @endif 
                        @endforeach 
                  </div>
                </div>
              @endforeach 
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

  <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    $('#fecha').datetimepicker({
            format: 'YYYY-MM-DD',
            
            
            defaultDate: '',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY-MM-DD',
            
            
            defaultDate: '',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


   function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

</script>  

@endsection