<style type="text/css">
	

	table{
		font-size: 13px;		
	}
</style>

@php 
	$mes_txt = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
@endphp

<div class="box box-success">
  <div class="box-header with-border">
    <center><h3 class="box-title" id="titulo1" ></h3></center>  
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 	

    <div class="col-md-12">
      <div class="row">  
        <div class="col-lg-6 col-sm-12">
          <div id="example2_wrapper" >
            <center>
              <div class="row">
                <div class="table-responsive">
                  
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" stordenes_valores_totalesyle="font-size: 12px;">
                    <thead>
                      <tr role="row" style="background-color: #009999; color: white;">
                        <th >Doctor</th>
                        <th >Ordenes</th>
                        <th >Valor ($)</th>   
                        <th >Comision ($)</th>                               
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($or_aniomes_doctor as $value)
                      <tr>
                          <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                          <td style="text-align: right;">{{$value->cantidad}}</td>
                          <td style="text-align: right;"></td>
                          <td style="text-align: right;"></td>
                      </tr>
                      @endforeach                  
                    </tbody>
                  </table>
                </div>
              </div>
            </center>
          </div>
           
        </div> 
        <div class="col-lg-6 col-sm-12">
            <canvas id="canvas_datos" ></canvas>
        </div> 
      </div>
    </div>
  </div>
</div>  

<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>

<script type="text/javascript">
	$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

	var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: '',
          backgroundColor: [
            @foreach($or_aniomes_doctor as $value)
            	'{{$value->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor as $value)
          	 '{{$value->apellido1}} {{$value->nombre1}}',
          @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var item_arr = data.labels[tooltipItem.index].split(':');
                    var label = item_arr[0];
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]);
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo1').text('Producción de Procedimientos por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $');
    var ctx = document.getElementById('canvas_datos').getContext('2d');

      window.myPie = new Chart(ctx, config);

    function refrescar(){
      location.reload();
    }

    $(document).ready(function(){
        document.getElementById("xboton").innerHTML = "<button type='button' class='btn btn-success btn-sm col-md-offset-9' onclick='refrescar();''><i class='glyphicon glyphicon-arrow-left'> </i> Regresar</button>";
    }); 
</script>