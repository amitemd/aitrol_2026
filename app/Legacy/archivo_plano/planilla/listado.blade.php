<div class="table-responsive col-md-12" style="min-height: 210px;">
  <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
    <thead>
      <tr role="row">
        <th width="10">Sel.</th>
        <th width="10">Fecha</th>
        <th width="10">Convenio</th>
        <th width="10">Tipo</th>
        <th width="10">Cant.</th>
        <th width="10">Valor</th>
        <th width="20">Resultados(%)</th> 
        <th width="10">Resultados</th>               
        <th width="10">Acción</th>
      </tr>
    </thead>
    <tbody>

      @foreach ($ordenes as $value)
        <tr role="row" >
          <td style="font-size: 11px;"><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_orden('{{$value->id}}','{{$ap->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
          <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
          <td style="font-size: 11px;">{{$value->seguro->nombre}} / {{$value->empresa->nombre_corto}}</td>
          <td>
            @if($value->protocolo!=null)
              <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->protocolo->pre_post}}</span>
            @endif
          </td>
          <td>{{$value->cantidad}}</td>
          <td>{{$value->total_valor}}</td>
          <td>
            <div class="progress progress">
              <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                <span id="sp{{$value->id}}" style="color: black;"></span>
              </div>
            </div>
          </td>
          <td>
            <div class="col-md-12" style="padding-left: 0px">
              <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});">
                <span style=" font-size: 10px">Resultados</span>
              </button>
            </div> 
          </td>
          <td>
            <div class="col-md-10" style="padding-left: 0px">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
              <span >Orden</span> 
              </a>   
            </div>  
          </td>        
        </tr>
      
    @endforeach
    </tbody>
  </table>
</div>

<script type="text/javascript">

  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach
    
    $('#example2_labs').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  });