@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}
</style>
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-md-12">
          <h3 class="box-title">Reporte Anual Órdenes de Exámenes de Laboratorio 2021</h3>
        </div>
        <div class="col-md-12">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="table-responsive">
            </div>
          </div>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="1%" >Metodo</th>
                <th width="10%" >Enero</th>
                <th width="10%" >Febrero</th>
                <th width="10%" >Marzo</th>
                <th width="10%" >Abril</th>
                <th width="10%" >Mayo</th>
                <th width="10%" >Junio</th>
                <th width="10%" >Julio</th>
                <th width="10%" >Agosto</th>
                <th width="10%" >Septiembre</th>
                <th width="10%" >Octubre</th>
                <th width="10%" >Noviembre</th>
                <th width="10%" >Diciembre</th>
                <th width="20%" >Total</th>
              </tr>
            </thead>
            <tbody>
              @php
                $formas = \Sis_medico\Ct_Tipo_Pago::all();
                $t_forma[1] = 0;
                $t_forma[2] = 0;
                $t_forma[3] = 0;
                $t_forma[4] = 0;
                $t_forma[5] = 0;
                $t_forma[6] = 0;
                $t_forma[7] = 0;
                $t_forma[8] = 0;
                $t_forma[9] = 0;
                $t_forma[10] = 0;
                $t_forma[11] = 0;
                $t_forma[12] = 0;
              @endphp
              @foreach($formas as $f_pago)
              <tr>
                <td>{{$f_pago->nombre}}</td>
                @php
                  $s_forma = 0;
                @endphp
                @for($i = 1; $i<= 12; $i++)
                  @php
                    $detalle = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                        ->where('mes', $i)
                                                        ->where('examen_orden.estado', '1')
                                                        ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                        ->where('seguros.tipo', '<>', '0')
                                                        ->join('examen_detalle_forma_pago', 'examen_detalle_forma_pago.id_examen_orden', '=', 'examen_orden.id')
                                                        ->where('examen_detalle_forma_pago.id_tipo_pago', $f_pago->id)
                                                        ->select(DB::raw('SUM(examen_detalle_forma_pago.valor) as total'))
                                                        ->first();
                    $total = $detalle->total;
                    $rolUsuario = Auth::user()->id_tipo_usuario;
                    if($rolUsuario == 1){

                      /*$cantidad = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                          ->where('mes', $i)
                                                          ->where('examen_orden.estado', '1')
                                                          ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                          ->where('seguros.tipo', '<>', '0')
                                                          ->join('examen_detalle_forma_pago', 'examen_detalle_forma_pago.id_examen_orden', '=', 'examen_orden.id')
                                                          ->where('examen_detalle_forma_pago.id_tipo_pago', $f_pago->id)
                                                          ->select('examen_orden.id as id')
                                                          ->get();

                      $cantidad_2 = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                          ->where('mes', $i)
                                                          ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                          ->where('seguros.tipo', '<>', '0')
                                                          ->where('examen_orden.estado', '1')
                                                          ->where('examen_orden.id_forma_de_pago', '1')
                                                          ->select('examen_orden.id as id')
                                                          ->get();




                      foreach($cantidad as $value){
                        echo $value->id.'<br>';
                      }
                      foreach($cantidad_2 as $value){
                        echo $value->id.'<br>';
                      }
                      dd('frena');*/

                    }



                    if($f_pago->id == 1 && $i == 1){
                      $detalle_2 = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                        ->where('mes', $i)
                                                        ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                        ->where('seguros.tipo', '<>', '0')
                                                        ->where('examen_orden.estado', '1')
                                                        ->where('examen_orden.id_forma_de_pago', 1)
                                                        ->leftjoin('examen_detalle_forma_pago', 'examen_detalle_forma_pago.id_examen_orden', '=', 'examen_orden.id')
                                                          ->whereNull('examen_detalle_forma_pago.id_examen_orden')
                                                        ->select(DB::raw('SUM(examen_orden.valor) as total'))
                                                        ->first();

                      $total = $total+$detalle_2->total;
                      if($rolUsuario == 1){

                        $detalle_3 = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                          ->where('mes', $i)
                                                          ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                          ->where('seguros.tipo', '<>', '0')
                                                          ->where('examen_orden.estado', '1')
                                                          ->whereNull('examen_orden.id_forma_de_pago')
                                                          ->leftjoin('examen_detalle_forma_pago', 'examen_detalle_forma_pago.id_examen_orden', '=', 'examen_orden.id')
                                                          ->whereNull('examen_detalle_forma_pago.id_examen_orden')
                                                          ->select(DB::raw('SUM(examen_orden.valor) as total'))
                                                          ->first();


                        $total = $total+$detalle_3->total;
                      }
                    }
                    if($f_pago->id == 6 && $i == 1){
                      $detalle_2 = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                        ->where('mes', $i)
                                                        ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                        ->where('seguros.tipo', '<>', '0')
                                                        ->where('examen_orden.estado', '1')
                                                        ->where('examen_orden.id_forma_de_pago', 2)
                                                        ->select(DB::raw('SUM(examen_orden.valor) as total'))
                                                        ->first();

                      $total = $total+$detalle_2->total;
                    }
                    if($f_pago->id == 4 && $i == 1){
                      $detalle_2 = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                        ->where('mes', $i)
                                                        ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                        ->where('seguros.tipo', '<>', '0')
                                                        ->where('examen_orden.estado', '1')
                                                        ->where('examen_orden.id_forma_de_pago', 3)
                                                        ->select(DB::raw('SUM(examen_orden.valor) as total'))
                                                        ->first();

                      $total = $total+$detalle_2->total;
                    }
                    $s_forma = $s_forma+$total;
                    $t_forma[$i] =  $t_forma[$i] + $total;

                  @endphp
                  <td style="text-align:right;">$ {{number_format(round($total, 2),2)}}</td>

                @endfor
                  <td style="text-align:right;">$ {{number_format(round($s_forma, 2),2)}}</td>
              </tr>
              @endforeach
              <tr>
                @php
                  $s_forma = 0;
                @endphp
                <td>Seguros Publicos</td>
                @for($i = 1; $i<= 12; $i++)
                  @php
                    $detalle = \Sis_medico\Examen_Orden::where('anio', '2021')
                                                        ->where('mes', $i)
                                                        ->where('examen_orden.estado', '1')
                                                        ->join('seguros', 'examen_orden.id_seguro', '=', 'seguros.id')
                                                        ->where('seguros.tipo',  '0')
                                                        ->select(DB::raw('SUM(examen_orden.total_nivel2) as total'))
                                                        ->first();
                    $total = $detalle->total;
                    $s_forma = $s_forma+$total;
                    $t_forma[$i] =  $t_forma[$i] + $total;
                  @endphp
                  <td style="text-align:right;">$ {{number_format(round($total, 2),2)}}</td>
                @endfor

                  <td style="text-align:right;">$ {{number_format(round($s_forma, 2),2)}}</td>
              </tr>
            </tbody>

            <tfoot>
              <tr>
                <td>Total:</td>

                @php
                  $s_forma = 0;
                @endphp
                @foreach($t_forma as $key => $value)
                  @php
                    $s_forma = $s_forma+$value;
                  @endphp
                  <td style="text-align:right;">{{number_format(round($value, 2),2)}}</td>
                @endforeach
                <td style="text-align:right;">$ {{number_format(round($s_forma, 2),2)}}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '',

            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',


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
