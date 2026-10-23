@extends('auditoria_hc_admision.admision.base')
@section('action-content')
<!--MODAL DE COBERTURA DE SALUD-->
<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog modal-lg" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="" ></iframe>
    </div>
  </div>
</div>

<div class="modal fade" id="favoritesModal2_prin" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog modal-lg" role="document"   id="frame_ventana2">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="" ></iframe>
    </div>
  </div>
</div>
<!--MODAL DE SUBIR COBERTURA-->
<div class="modal fade" id="Subir" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog " role="document" >
    <div class="modal-content"  id="imprimir3">
    </div>
  </div>
</div>




  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

    <!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-4">
          <h3 class="box-title">@if($historia->proc_consul=='0') Consulta Externa @else Procedimientos Médicos @endif - @if($historia->tipo=='0') Seguros Públicos @elseif($historia->tipo=='1') Seguros Privados @else Particular @endif </h3>
        </div>
        <!--div class="col-md-3 col-sm-3 col-xs-3">
            <a  data-toggle="modal" data-target="#favoritesModal2">
              <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-globe"></span> Cobertura S.Pública</button>
            </a>
        </div-->
        <!--08082018-->
        @php
          $paciente = DB::table('paciente')->where('id',$historia->id_paciente)->first();
          if(($paciente->alergias == null )||($paciente->alergias == "No" )||($paciente->alergias == "" )||($paciente->alergias == "NO")||($paciente->alergias == "no" )||($paciente->alergias == "nO" )){
              $dato_alergia =  1;
          }
          else
          {
              $dato_alergia =  2;
          }
        @endphp
        @if($historia->proc_consul=='1')
        <div class="form-group col-md-2 col-sm-2 col-xs-2">
            <a class="btn btn-primary btn-sm"  id="imprimir_etiquetas" target="_blank" href="{{ route('admision.etiqueta2', ['id' => $historia->id_agenda, 'seguro' => $historia->id_seguro, 'alergia' => $dato_alergia]) }}" ><span class="glyphicon glyphicon-print"></span> Generar Etiqueta</a>
        </div>
        @endif
        <div class="col-md-1 col-sm-1 col-xs-1" style="text-align: center;">
            <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirpdf_resumen', ['id' => $historia->id_agenda]) }}" ><span class="glyphicon glyphicon-print"></span> Resumen</a>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2" style="text-align: center;">
            <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirdatos_paciente', ['id' => $historia->id_paciente]) }}" target="_blank" ><span class="glyphicon glyphicon-print"></span> Datos Paciente</a>
        </div>
        @php $sala = Sis_medico\Sala::find($historia->idsala) @endphp
        @if($sala->hospital->nombre_hospital!='HOSPITAL')
        <div class="col-md-2 col-sm-2 col-xs-2">
          <a  href="{{url('controldoc/admision')}}/{{$hcid}}/{{$url_doctor}}/{{$unix}}"><button type="button" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-chevron-right"></span> Continuar</button></a>
        </div>
        @else
        <div class="col-md-2 col-sm-2 col-xs-2">
          <a  href="{{route('cuarto.agenda_hospital',['id_sala' => $historia->idsala])}}"><button type="button" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-chevron-right"></span> Continuar</button></a>
        </div>
        @endif
      </div>
    </div>

    <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="{{$historia->fecha_nacimiento}}">
    <div class="box-body">
      <div class="col-md-12">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="table-responsive col-md-12">
          <table class="table table-bordered table-hover dataTable" >
            <tbody style="font-size: 12px;">
              <tr id="t1">
                <td  ><b>Paciente</b></td>
                <td colspan="2">{{$historia->id_paciente}} - {{$historia->nombre1}} {{$historia->nombre2}} {{$historia->apellido1}} {{$historia->apellido2}}</td>
                <td  ><b>Fecha de Nacimiento</b></td>
                <td >{{$historia->fecha_nacimiento}}</td>
                <td><b>Edad</b></td>
              </tr>
              <tr >
                <td ><b>Seguro</b></td>
                <td >{{$historia->nombre}} @if($historia->sbnombre!="")- {{$historia->sbnombre}} @endif</td>
                <td ><b>Fecha</b></td>
                <td @if($historia->proc_consul=='0') colspan="3" @endif>{{$historia->fechaini}}</td>
                @if($historia->proc_consul=='1')
                <td ><b>Procedimientos</b></td>
                <td colspan="2">{{$procs}}</td>
                @endif
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      </div>
      <br>
      <br>

      <div class="col-md-12">
        @php
          $agenda_scan = \Sis_medico\AgendaScan::where('id_agenda', $historia->id_agenda)->first();
        @endphp
        <a  href="{{url('controldoc/agenda/archivo')}}/{{$historia->id_agenda}}" data-toggle="modal" data-target="#Subir">
          <button class="btn btn-success btn-xs">@if(is_null($agenda_scan))Subir @else Actualizar @endif Documentos Escaneados</button>
        </a>
      </div>
      <br>
      <br>

      <form id="modificar_fecha">
        @if(!is_null($protocolo))
        <div class="col-md-3">
          <div class="col-md-6">
            <label>Fecha Imprimible</label>
          </div>
          <div class="col-md-6">

            <input type="hidden" name="id" value="{{$protocolo->id}}">

            <input type="text" name="fecha" id="fecha" value="@if($protocolo->fecha != null){{$protocolo->fecha}}@else{{substr($historia->fechaini, 0, -9)}}@endif" class="form-control pull-right input-sm" required onchange="cambio_fecha()">
          </div>
        </div>
      </form>

      <form id="mod_seg_emp">
        <div class="col-md-6">
          <div class="col-md-3">
            <label>Seguro</label>
          </div>
          <div class="col-md-3">
            <input type="hidden" name="id_hcproc" value="{{$protocolo->id_hcproc}}">
            <select id="id_seguro" name="id_seguro" class="form-control input-sm" onchange="cambio_seg_emp()" >
              @foreach($seguros as $value)
              <option @if($protocolo->id_seguro == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label>Empresa</label>
          </div>
          <div class="col-md-3">
            <select id="id_empresa" name="id_empresa" class="form-control input-sm" onchange="cambio_seg_emp()">
              @foreach($empresas as $value)
              <option @if($protocolo->id_empresa == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
      </form>


      <div id="index_tb" class="col-md-12">

      </div>



    </div>
  </div>

</section>



<!-- iCheck 1.0.1 -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">

//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

$(document).ready(function () {


    edad2();
    findex_tb();

    var ventana_ancho = $(window).width();


        if(ventana_ancho > "962" ){
            var nuevovalor = ventana_ancho * 0.8;
        }
        else
        {
            var nuevovalor = ventana_ancho * 0.9;
        }
        $("#frame_ventana").width(nuevovalor);
        $("#frame_ventana2").width(nuevovalor);

    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
    });

    $("#fecha").on("dp.change", function (e) {
      cambio_fecha();
    });

    $("#id_tipo_seguro").on("dp.change", function (e) {
      cambio_seg_emp();
    });

    $("#id_empresa").on("dp.change", function (e) {
      cambio_seg_emp();
    });
});

 $('#favoritesModal2').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

 $('#favoritesModal2_prin').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

 $('#Subir').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });



 function edad2()
{

    var nacimiento = document.getElementById("fecha_nacimiento").value;
    var edad = calcularEdad(nacimiento);

    if(isNaN(edad))
    {
      var row = document.getElementById("t1");
      var x = row.insertCell(5);
      x.innerHTML = "0";
    }
    else
    {
      var row = document.getElementById("t1");
      var x = row.insertCell(5);
      x.innerHTML = edad + " años";
    }
}

var findex_tb = function ()
{

    $.ajax({
        type: 'get',
        url:'{{ route('controldoc.control_tb',['hcid' => $hcid, 'proc_consul' => $proc_consul, 'tipo' => $tipo])}}',
        success: function(data){
            $('#index_tb').empty().html(data);
        }
    })

}

function cambio_fecha(){
      $.ajax({
          type: 'post',
          url:'{{route("auditoria_hc_foto.fecha_convenios")}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#modificar_fecha").serialize(),
          success: function(data){
              //alert('valio');
              //console.log('{{route("hc_foto.fecha_convenios")}}');
              console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      })
}

function cambio_seg_emp(){
      $.ajax({
          type: 'post',
          url:'{{route("auditoria_controldoc.seguro_empresa")}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#mod_seg_emp").serialize(),
          success: function(data){
              //alert('valio');
              //console.log('{{route("hc_foto.fecha_convenios")}}');
              console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      })
}


</script>
@endsection
