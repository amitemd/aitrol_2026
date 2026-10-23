  @extends('laboratorio.agrupada.base')
  @section('action-content')
  <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
  <section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-md-6">
            <h3 class="box-title">{{trans('laboratorio.factura_agrupada')}} - {{trans('laboratorio.publica')}}</h3>
          </div>

        </div>
      </div>
      <form method="POST" action="{{route('factura_agrupada.editar_publicas_buscador', ['id'=> $id_cab])}} ">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="cedula" class="col-md-4 control-label">{{trans('laboratorio.cedula')}}:</label>
              <div class="col-md-8">
                <input id="cedula" maxlength="13" type="text" class="form-control input-sm" name="cedula" placeholder="{{trans('laboratorio.cedula')}}" value="{{$cedula}}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="id_orden" class="col-md-4 control-label">{{trans('laboratorio.orden')}}:</label>
              <div class="col-md-8">
                <input id="id_orden" maxlength="11" type="number" class="form-control input-sm" name="id_orden" value="{{$id_orden}}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="usuario" class="col-md-4 control-label">{{trans('laboratorio.nombres_apellidos')}}:</label>
              <div class="col-md-8">
                <input id="usuario" maxlength="100" type="text" class="form-control input-sm" value="{{$nombre}}" name="nombre" placeholder="{{trans('laboratorio.nombres_apellidos')}}">
              </div>
            </div>
          </div>
        </div>

        <div style="padding-left: 0px!important;padding-right: 0px!important; width: 9%!important;" class="form-group col-md-2 ">
          <div class="col-md-2">
            <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> {{trans('laboratorio.buscar')}}</span></button>
          </div>
        </div>
      </form>



      <div style="text-align: center;">
        <div style="text-align: center;">
          @if($afectadas>0)
          <div id="pendientes" style="display: block;padding-left: 0px!important; width: 9%!important;" class="form-group col-md-2 ">
            <div class="col-md-2">
              <a target="_blank" id="pendientes" type="button" class="btn btn-danger" href="{{route('factura_agrupada.mostrar_pendientes_publicas',['id_cab'=>$id_cab])}}">{{trans('laboratorio.pendientes')}}</a>
            </div>
          </div>
          @endif

          <div style="padding-left: 0px!important; width:10.2%!important;" class="form-group col-md-2 ">
            <div class="col-md-2">
              <a id="pendientes" type="button" class="btn btn-primary" href="{{route('factura_agrupada.resultados_pendientes_publicas',['id_cab'=>$id_cab])}}"><i class="fa fa-download" aria-hidden="true"></i> {{trans('laboratorio.pendientes')}}</a>
            </div>
          </div>

          <div id="elim_todas_pendientes" style="padding-left: 0px!important; width:0%!important; display: none;" class="form-group col-md-2 ">
            <div class="col-md-2">
              <a type="button" class="btn btn-danger" onclick="eliminar_todas_pendientes()"><i class="fa fa-trash-o" aria-hidden="true"></i> {{trans('laboratorio.quitarexamenessinresultados')}}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12" style="min-height: 210px;">
              <table id="example5" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                <thead>
                  <input type="hidden" name="id_cab" value="{{$id_cab}}">
                  <tr>
                    <th>Nº</th>
                    <th>{{trans('laboratorio.orden')}}</th>
                    <th>{{trans('laboratorio.cedula')}}</th>
                    <th>{{trans('laboratorio.paciente')}}</th>
                    <th>{{trans('laboratorio.seguro')}}</th>
                    <th>{{trans('laboratorio.cantidad')}}</th>
                    <th>{{trans('laboratorio.total')}}</th>
                    <th>{{trans('laboratorio.fechaOrden')}}</th>
                    <th>{{trans('laboratorio.resultado')}}(%)</th>
                    <th>R(%)</th>
                    <th>{{trans('laboratorio.acción')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $x=0;
                  $totales =0;

                  @endphp
                  @if(!is_null($detalle))
                  @foreach($ordenes as $orden)
                  @php

                  $examen_orden = Sis_medico\Examen_Orden::where('id',$orden->id_examen_orden)->get();
                  //dd($examen_orden);
                  @endphp
                  @foreach($examen_orden as $ord)
                  <?php $totales = $totales + $ord->total_nivel2; ?>
                  <tr>
                    @php $x++; @endphp
                    <td>{{$x}}</td>
                    <td>{{$ord->id}}</td>
                    <td>{{$ord->paciente->id}}</td>
                    <td>{{$ord->paciente->apellido1}} {{$ord->paciente->apellido2}} {{$ord->paciente->nombre1}} {{$ord->paciente->nombre2}}</td>
                    <td>{{$ord->seguro->nombre}}</td>
                    <td>{{$ord->cantidad}}</td>
                    <td>{{$ord->total_valor}}</td>
                    <td>{{substr($ord->fecha_orden,0,10)}}</td>
                    <td>
                      <div class="progress progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$ord->id}}">
                          <span id="sp{{$ord->id}}" style="color: black;"></span>
                        </div>
                      </div>
                    </td>
                    <td id="sps{{$ord->id}}"></td>
                    <td><a onclick="confEliminacion('{{$ord->id}}','{{$id_cab}}');" class="btn btn-danger btn-xs">{{trans('dtraduccion.Eliminar')}} <span class="glyphicon glyphicon-trash"></span></a></td>
                  </tr>
                  @endforeach
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
                    <td style="font-weight: bold;"><?php echo $totales; ?> </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  @endif
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php //dd($ordenes)
  ?>
  <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
  <script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

  <script type="text/javascript">
    $('#example5').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false,
    })


    function eliminar_todas_pendientes() {
      Swal.fire({
        title: `{{trans('laboratorio.eliminar')}}`,
        text: `{{trans('laboratorio.estasegurodeeliminar')}}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `{{trans('laboratorio.sieliminar')}}`,
      }).then((result) => {
        if (result.isConfirmed) {
          busca_elimina_pendiente();
        }
      })
    }


 @php $contador=0; @endphp
    function busca_elimina_pendiente(){
    @if( is_null($ordenes))
    @else
     @foreach($ordenes as $orden)
     @php
     $examen_orden = Sis_medico\Examen_Orden::where('id',$orden->id_examen_orden)->get();
     
     @endphp

     @foreach ($examen_orden as $value)
     $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",
      success: function(data){

          let thView = document.getElementById('sps{{$value->id}}').innerHTML;
          let id= "{{$value->id}}";
          
          //thView.innerHTML = `${pcts}`

         // console.log("id: " +id + "%:"+ thView)
          if(thView < 100){
            <?php $contador++; ?>
             eliminar_orden_hilo("{{$value->id}}", "{{$id_cab}}");
          }
          
      },

      error: function(data){
      }
    });

     @endforeach
     @endforeach
    @endif
     if({{$contador}}>0){
     alertasPersonalizadas('warning','Advertencia','Se han eliminado las factura con problemas');
      setTimeout(function(){ location.reload(); }, 1500);

      }else{
       console.log("No se  han encontrado")
     }
    }



    function eliminar_orden(id_orden, id_cab) {
      $.ajax({
        type: 'get',
        url: "{{url('humanlabs/eliminar_orden_privada')}}/" + id_orden + '/' + id_cab,
        datatype: 'json',
        success: function(data) {
          if (data == 'ok') {
            alertasPersonalizadas('success', 'Exito', 'Se elimino la factura');
            setTimeout(function(){ location.reload(); }, 1500);
          }
        },
        error: function(data) {
          alertasPersonalizadas('error', 'Error...!', 'Ocurrio un error');
        }
      });
    }

    function eliminar_orden_hilo(id_orden, id_cab) {
      $.ajax({
        type: 'get',
        url: "{{url('humanlabs/eliminar_orden_privada')}}/" + id_orden + '/' + id_cab,
        datatype: 'json',
        success: function(data) {
          if (data == 'ok') {

          }
        },
        error: function(data) {

        }
      });
    }


    @php $cont=0; @endphp
    $(document).ready(function($){
    let contar=0;
    @if( is_null($ordenes))

    @else
     @foreach($ordenes as $orden)
     @php
     $examen_orden = Sis_medico\Examen_Orden::where('id',$orden->id_examen_orden)->get();
     
     @endphp

     @foreach ($examen_orden as $value)
     $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",
      success: function(data){
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

          //console.log(pcts)
          let thViews = document.getElementById('sps{{$value->id}}').innerHTML;

          // if (pcts < 100) {
          //   contar++;
          //   console.log("entra");
          // }

          // if(contar == 1){
          //   alertasPersonalizadas('warning','Advertencia','Se han encontrado  facturas con problemas');
          //   let btn_todas_pendiente = document.getElementById('elim_todas_pendientes');
          //   btn_todas_pendiente.style.display ="block";
          // }

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
      }
    });

     @endforeach
     @endforeach
    @endif
    //console.log(thViews)
    //  if(contar>0){
    //   alertasPersonalizadas('warning','Advertencia','Se han encontrado  facturas con problemas');
    //   //setTimeout(function(){ location.reload(); }, 1500);
    //  // let btn_todas_pendiente = document.getElementById('elim_todas_pendientes');
    //   //btn_todas_pendiente.style.display ="block!important";
    //   console.log(contar)


    // }else{
    //  console.log("No se  han encontrado")
    // }

  });

    function alertasPersonalizadas(icon, title, text){
     Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      text: `${text}`,
      showConfirmButton: false,
      timer: 2000
    })
   }
   function confEliminacion(id_orden, id_cab){
    Swal.fire({
      title: 'Esta seguro que desea eliminar?',
      text: "Esta acción es irreversible",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, eliminar'
    }).then((result) => {
      if (result.isConfirmed) {
       eliminar_orden(id_orden, id_cab);
     }
   })
  }
  </script>
  @endsection
