@extends('laboratorio.orden.base')
@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    text-align: right;
}

</style>

@php $tmes = ['0', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE']; @endphp
<!-- Main content -->
<section class="content">
  <div class="box box-success">
    <div class="box-header">
      <div class="row">
        <div class="col-md-9">
          <h3 class="box-title">Comisiones DESDE {{$tmes[$mes]}} HASTA {{$tmes[$mes_hasta]}}/{{$año}}</h3>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" action="{{ route('labscomisiones.comisiones') }}" id="estad_labs">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-2">
            <label><b>Ingrese año/mes a Consultar</b></label>      
          </div>
          <div class="col-md-2">
            <select class="form-control" name="anio" onchange="buscar();">
                @php $x=2021; $anio_actual=date('Y'); @endphp 
                @for($x=2020;$x<=$anio_actual;$x++)
                <option @if($x==$año) selected @endif value="{{$x}}">{{$x}}</option>
                @endfor
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" name="mes" onchange="buscar();">
                @foreach($tmes as $key => $tm)
                  @if($key > 0)
                    <option @if($key==$mes) selected @endif value="{{$key}}">{{$tm}}</option>
                  @endif
                @endforeach
            </select>
          </div>
          <div class="col-md-1">
            <label><b>Hasta</b></label>      
          </div>
          <div class="col-md-2">
            <select class="form-control" name="mes_hasta" onchange="buscar();">
                @foreach($tmes as $key => $tm)
                  @if($key > 0)
                    <option @if($key==$mes_hasta) selected @endif value="{{$key}}">{{$tm}}</option>
                  @endif
                @endforeach
            </select>
          </div>  
        </div>
      </form>

      <div style="text-align: center">
        <div id="example1_wrapper" >
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" >
                  <thead>
                    <tr role="row">
                      <th >Doctor</th>
                      @foreach($arr_tmp as $tx)
                      <th >{{$tmes[substr($tx,5)]}}-{{substr($tx,0,4)}}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @php $rolUsuario = Auth::user()->id_tipo_usuario; @endphp
                    @foreach($arr_com as $key => $doctor)
                    <tr>
                      <td style="text-align: left;">{{$users->find($key)->apellido1}} {{$users->find($key)->apellido2}} {{$users->find($key)->nombre1}}</td>
                      @foreach($arr_tmp as $tx)
                      <td style="text-align: center;">
                        <br/>
                        @if($rolUsuario == 1)
                        @if(isset($doctor[$tx]))
                          <a href="{{route('labscomisiones.detalle_comisiones',['ames'=> $tx, 'doctor' => $key])}}">{{number_format(round($doctor[$tx],2),'2','.','')}}</a>
                        @else 
                          0
                        @endif
                        <span>/</span>
                        @endif
                        @if(isset($doctor['staff-'.$tx]))
                          <a style=color:green; href="{{route('labscomisiones.detalle_comisiones_staff',['ames'=> 'staff-'.$tx, 'doctor' => $key])}}">{{number_format(round($doctor['staff-'.$tx],2),'2','.','')}}</a>  
                        @else 
                          <a style=color:green; href="{{route('labscomisiones.detalle_comisiones_staff',['ames'=> 'staff-'.$tx, 'doctor' => $key])}}">{{number_format(0,'2','.','')}}</a> 
                        @endif
                      </td>
                      @endforeach
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>  
           

          </div>

        </div>
      </div>

      <div style="text-align: center">
        <div id="example2_wrapper" >
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
                  <thead>
                    <tr role="row">
                      <th >Código</th>
                      @foreach($arr_tmp as $tx)
                      <th >{{$tmes[substr($tx,5)]}}-{{substr($tx,0,4)}}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($arr_com2 as $key => $cod_ex)
                    @php $doc_ext = Sis_medico\Labs_doc_externos::find($key); @endphp
                    <tr>
                      <td style="text-align: left;">{{ $key }} - @if(!is_null($doc_ext)){{ $doc_ext->nombre1 }} {{ $doc_ext->apellido1 }}@endif</td>
                      @foreach($arr_tmp as $tx)
                        @if(!is_null($doc_ext))
                          @if($doc_ext->tipo == 'COMISION')
                            <td>@if(isset($cod_ex[$tx]))<a class="btn btn-success" href="{{route('labscomisiones.detalle_comisiones_externos_nuevo',['ames'=> $tx, 'doctor' => $key])}}">Descargar</a> @endif</td>
                          @endif

                          <!-- @if($doc_ext->id == 'CITISALUD')
                            <td>@if(isset($cod_ex[$tx]))<a class="btn btn-success" href="{{route('labscomisiones.detalle_comisiones_externos',['ames'=> $tx, 'doctor' => $key])}}">Ver</a>@else {{trans('laboratorio.no_data')}} @endif</td>
                          @else
                            <td>@if(isset($cod_ex[$tx]))<a href="{{route('labscomisiones.detalle_comisiones_externos',['ames'=> $tx, 'doctor' => $key])}}">{{number_format(round($cod_ex[$tx],2),'2','.','')}}</a>@else 0 @endif</td>
                          @endif -->
                        @endif
                      @endforeach
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>  
           

          </div>

        </div>
      </div>  
      
  
     


    
    </div>
  </div>  
  </section>
    <!-- /.content -->

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/hc4/chart/utils.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/chart/Chart.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
  function buscar(){
    $('#estad_labs').submit();  
  }
  var table = $('#example1').DataTable({
      dom: 'lBrtip',
      paging: false,
      buttons: [
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'COMISIONES STAFF',
          
        }
      ],
      
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },

     
      "order": [
        [1, 'desc']
      ],
      
    });
  var table = $('#example2').DataTable({
      dom: 'lBrtip',
      paging: false,
      buttons: [
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'COMISIONES EXTERNOS',
          
        }
      ],
      
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },

     
      "order": [
        [1, 'desc']
      ],
      
    });
</script>


@endsection
