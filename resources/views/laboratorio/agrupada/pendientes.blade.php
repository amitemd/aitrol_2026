  @extends('laboratorio.agrupada.base')
  @section('action-content')
  <section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-md-6">
            <h3 class="box-title">{{trans('laboratorio.facturas_agrupadas')}} {{trans('laboratorio.pendientes')}}</h3>
          </div>
        </div>


        <div class="box-body" id="index_privada">
          <form id="frm_privada">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="id_cab" name="id_cab" value="{{$id_cab}}">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="table-responsive col-md-12" style="min-height: 210px;">
                  <table id="example6" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                    <thead>

                      <tr>
                        <th>Nº</th>
                        <th>{{trans('laboratorio.orden')}}</th>
                        <th>{{trans('laboratorio.cedula')}}</th>
                        <th>{{trans('laboratorio.paciente')}}</th>
                        <th>{{trans('laboratorio.seguro')}}</th>
                        <th>{{trans('laboratorio.cantidad')}}</th>
                        <th>{{trans('laboratorio.total')}}</th>
                        <th>{{trans('laboratorio.resultado')}}(%)</th>
                        <th>R (%)</th>
                        <th>{{trans('laboratorio.fechaOrden')}}</th>
                        <th>{{trans('laboratorio.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody id="body_tabla">
                      @php

                      $x=0;
                      $total=0;

                      @endphp

                      @foreach($ordenes as $orden)
                      @php
                      $total = $total + $orden->total_valor;
                      $forma_pago = Sis_medico\Examen_Detalle_Forma_Pago::where('id_examen_orden', $orden->id)->get();
                      //echo ($forma_pago);
                      @endphp
                      <tr>
                        @php $x++; @endphp
                        <td>{{$x}}</td>
                        <td>{{$orden->id}}</td>
                        <td>{{$orden->id_paciente}}</td>
                        <td>{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}</td>
                        <td>{{$orden->seguro->nombre}}</td>
                        <td>{{$orden->cantidad}}</td>
                        <td>{{$orden->total_valor}}</td>
                        <td>
                          <div class="progress progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$orden->id}}">
                              <span id="sp{{$orden->id}}" style="color: black;"></span>
                            </div>
                          </div>
                        </td>
                        <td id="sps{{$orden->id}}"></td>
                        <td>{{$orden->fecha_orden}}</td>
                        <td><a onclick="agregar_publicas({{$orden->id}})" class="btn btn-success btn-xs">{{trans('laboratorio.agregar')}}</a></td>
                      </tr>
                      @endforeach
                      @if($x > 0)
                      <tr>
                        <td style="display: none;"><?php echo count($ordenes) + 1; ?> </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold;">{{trans('laboratorio.total')}}</td>
                        <td style="font-weight: bold;"><?php echo  $total; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
  </section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">

  $('#example6').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : false,
    'autoWidth'   : false,
  });



  function alertasPersonalizadas(icon, title, text){
   Swal.fire({
    icon: `${icon}`,
    title: `${title}`,
    text: `${text}`,
    showConfirmButton: false,
    timer: 1500
  })
 }


 $(document).ready(function($){
     // let cont=0;
     @foreach($ordenes as $orden)
     @php
     $examen_orden = Sis_medico\Examen_Orden::where('id',$orden->id)->get();
     //dd($examen_orden);
     @endphp

     @foreach ($examen_orden as $value)
     $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",
      success: function(data){
        //console.log("Hola")
        if(data.cant_par==0){
          var pct = 0;  
        }else{
          var pct = (data.certificados/data.cant_par)*100;  
        }
          //console.log(data)
          $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
          $('#sp{{$value->id}}').text(Math.round(pct)+"%");
          let thView = document.getElementById('sps{{$value->id}}');
          let pcts = Math.round(pct);
          thView.innerHTML = `${pcts}`
          if(pct < 10){
            $('#td{{$value->id}}').addClass("progress-bar-danger");
            $('#result{{$value->id}}').removeClass("btn-success");
            $('#result{{$value->id}}').addClass("btn-danger");
          }else if(pct >=10 && pct<90){
            $('#td{{$value->id}}').addClass("progress-bar-warning");
            $('#result{{$value->id}}').removeClass("btn-success");
            $('#result{{$value->id}}').addClass("btn-warning");  
          }else{
            $('#td{{$value->id}}').addClass("progress-bar-success");
          }

        },

        error: function(data){
          console.log("error")
        }
      });

     @endforeach
     @endforeach

   });



    function agregar_publicas(id_orden) {
      let id_cab = document.getElementById('id_cab').value;
      console.log(id_cab);

      $.ajax({
        type: 'get',
        url: "{{url('resultados/ingresar/pendintes/publicas/individual')}}/" + id_cab + "/" + id_orden,
        datatype: 'json',
        beforeSend: function() {
          // setting a timeout
          // $('#modal_espera').modal();
        },
        success: function(data) {
          if (data > 0) {
            alertasPersonalizadas('success', 'Exito', 'Se ha agregado la factura');
            setTimeout(function() {
              location.reload();
            }, 1500);
          }


         },
         error: function(data){


         }
       });

}

</script>
@endsection
