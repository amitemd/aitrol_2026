<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
@include('hc4.partials.catalog_list_styles')
<section class="content v3-clinical-list">
  <div class="v3-clinical-list__card">
    {{ csrf_field() }}
    <header class="v3-clinical-list__head">
      <div class="v3-clinical-list__title">
        <div class="v3-clinical-list__icon"><i class="fas fa-flask" aria-hidden="true"></i></div>
        <div><div class="v3-clinical-list__eyebrow">Laboratorio clínico · V3</div><h3>Resultados de laboratorio</h3><p>Consulta la disponibilidad y descarga los resultados de laboratorio generados hoy.</p></div>
      </div>
      <div class="v3-clinical-list__actions">
        @php $crm = Auth::user()->id; @endphp
        @if($crm == '1307189140' || $crm == '9666666666')
          <button type="button" onclick="ver_grafico();" id="agenda_semana" class="v3-clinical-list__button"><i class="fas fa-chart-bar" aria-hidden="true"></i>Ver estadísticas</button>
        @endif
      </div>
    </header>
    <div id="laboratory_orders_panel">
      <div id="div_grafico" class="v3-clinical-list__table-wrap" style="min-height:210px">
          <table id="example2" class="v3-clinical-list__table" role="grid" aria-describedby="example2_info">
            <thead>
              <tr>
                <th class="color titulo">Fecha</th>
                <th class="color titulo">Nombres</th>
                <th class="color titulo">Seguro/Convenio</th>
                <th class="color titulo">Tipo</th>
                <th class="color titulo">Creada</th>
                <th class="color titulo">Modificada</th>
                <th class="color titulo">Cantidad</th>
                <!--<th class="color titulo">Valor</th>-->
                <th class="color titulo">Ex&aacutemenes Ingresados(%)</th>
                <th class="color titulo">Resultados</th>
                <!--<th class="color titulo">Ver</th> -->
              </tr>
            </thead>
            <tbody>
              @forelse ($ordenes_lab as $value)
              <tr role="row">
                <td class="color">{{substr($value->fecha_orden,0,10)}}</td>
                <td class="color">
                    {{$value->papellido1}}
                  @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)')
                  @else
                    {{ $value->papellido2 }}
                  @endif
                    {{$value->pnombre1}}
                  @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)')
                  @else
                    {{ $value->pnombre2 }}
                  @endif </td>
                <td class="color">{{$value->snombre}}/{{$value->nombre_corto}}</td>
                <td class="color">
                  @if($value->estado=='-1')
                    <span style="color: red;">COTIZ.</span>
                  @else
                    {{$value->pre_post}}
                  @endif
                </td>
                <td class="color">{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
                <td class="color">{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                <td class="color">{{$value->cantidad}}</td>
                <!--<td class="color">{{$value->total_valor}}</td>-->
                <td>
                  <div class="progress progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                      <span id="sp{{$value->id}}" style="color: black;"></span>
                    </div>
                  </div>
                </td>
                <td>
                  @if($value->estado=='1')
                    <div>
                      <div class="btn-group">
                        <button type="button" class="v3-clinical-list__action v3-clinical-list__action--view"
                        onclick="descargar({{$value->id}});"><i class="fas fa-file-medical-alt" aria-hidden="true"></i><span>Resultados</span>
                        </button>
                        <!--<button type="button" class="btn btn-info boton-2 dropdown-toggle"
                         data-toggle="dropdown" aria-expanded="false">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="background-color: #004AC1;padding: 2px;min-width: 100px;">
                          <li ><a  href="{{ route('resultados.imprimir3',['id_orden' => $value->id]) }}" >Formato Gastro</a></li>
                        </ul>-->
                      </div>
                    </div>
                  @endif
                </td>
                <!--<td>
                  @if($value->stipo!='0')
                    <div class="col-md-12">
                      <div class="btn-group" >
                          <button type="button" class="btn btn-info boton-2" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span >Cotización</span></button>
                          <button type="button" class="btn btn-info boton-2 dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu cot" role="menu" style="background-color: #004AC1;padding: 2px;min-width: 100px;">
                            <li ><a style="width: 100%; height: 100%"  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank">Formato Gastro</a></li>
                          </ul>
                      </div>
                    </div>
                  @else
                    <div class="col-12" >
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a style="width: 100%; height: 100%" href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-info boton-2">
                      <span >Orden</span>
                      </a>
                    </div>
                  @endif
                </td>-->
                <!--<td>
                  @if($value->id_empresa != null && $value->id_empresa != '9999999999')
                    @if($value->stipo!='0')

                      <div class="form-group col-md-6" style="padding: 2px;">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">

                      <a target="_blank"  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" class="btn btn-info boton-2" style="padding: 0px;">
                      <span class="fa fa-download"></span> Orden
                      </a>

                    </div>
                    @else
                      <div class="form-group col-md-6" style="padding: 5px;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <a href="{{ route('orden.detalle', ['id' => $value->id, 'dir' => 'sup']) }}" class="btn btn-info boton-2" style="padding: 0px;">
                        <span class="fa fa-download"></span> Orden
                        </a>

                      </div>
                    @endif
                  @endif
                </td> -->
              </tr>
              @empty
              <tr>
                <td colspan="9" class="v3-clinical-list__empty">
                  <i class="fas fa-flask" aria-hidden="true"></i>
                  No existen resultados de laboratorio para la fecha seleccionada.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      <footer class="v3-clinical-list__footer"><span>Total de registros</span><strong>{{$ordenes_lab->count()}}</strong></footer>
    </div>
  </div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  var peticionesAjax = [];  // Almacena referencias a las solicitudes AJAX

   $(document).ready(function($){
      @foreach ($ordenes_lab as $value)
    
      var xhr = $.ajax({
          type: 'get',
          url:"{{ route('barra.progress_imprimir',['id_orden' => $value->id]) }}",

          success: function(data){
            if(data.cant_par==0){
              var pct = 0;
            }else{

              var pct = data.certificados/data.cant_par*100;
              //alert(pct);


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

      // Almacena la referencia al objeto XMLHttpRequest en el array
      peticionesAjax.push(xhr);
   
    @endforeach
  });

  // Función para detener todas las peticiones AJAX pendientes
  function detenerPeticionesAjax() {
      for (var i = 0; i < peticionesAjax.length; i++) {
          peticionesAjax[i].abort();  // Cancela la solicitud AJAX
      }
      
      // Limpia el array de peticiones
      peticionesAjax = [];
  }

  function descargar(id_or) {

      var cert = $('#sp'+id_or).text();
      //alert(id_or);

      if(cert=='0%'){
        alert("Sin Exámenes Ingresados");
      }else{
        window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');
      }
    }

</script>
<script type="text/javascript">
  function ver_grafico() {

    detenerPeticionesAjax();

    let load_var = '<div class="text-center color" style="padding:45px 15px"><i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i><br><br>Cargando estadísticas...</div>';
    $("#div_grafico").html(load_var);

    $.ajax({
      type: 'post',
      url: "{{route('hc4/laboratorio.grafico')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      data: '',
      datatype: "html",
      success: function(datahtml) {

        $("#div_grafico").html(datahtml);
      },
      error: function(xhr) {
        if (typeof mostrarErrorAmbulatorio === 'function') {
          mostrarErrorAmbulatorio(xhr, 'cargar las estadísticas de laboratorio');
        } else {
          alert('No se pudieron cargar las estadísticas de laboratorio.');
        }
      }
    })
  }

  function buscar_ordenes_lab() {
    $.ajax({
      type: 'post',
      url: "{{route('obtener.ordenes_lab_nuevo')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#form_ordenes_lab").serialize(),
      success: function(data) {
        $("#area_trabajo").html(data);
      },
      error: function(xhr) {
        if (typeof mostrarErrorAmbulatorio === 'function') {
          mostrarErrorAmbulatorio(xhr, 'buscar las órdenes de laboratorio');
        } else {
          alert('No se pudieron buscar las órdenes de laboratorio.');
        }
      }
    });
  }
</script>
