@extends('consultam.base_condoc')
@section('action-content')

<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
</style>

<!--MODAL DE COBERTURA DE SALUD-->
<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog modal-lg" role="document"   id="frame_ventana">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/{{$historia->id_paciente}}" ></iframe> 
    </div>
  </div>
</div>

<div class="modal fade" id="favoritesModal2_prin" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog modal-lg" role="document"   id="frame_ventana2">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://coresalud.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/{{$historia->id_usuario}}" ></iframe> 
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
        <div class="col-md-6 col-sm-6 col-xs-6">
          <h3 class="box-title">@if($historia->proc_consul=='0') Consulta Externa @else Procedimientos Médicos @endif - @if($historia->tipo=='0') Seguros Públicos @elseif($historia->tipo=='1') Seguros Privados @else Particular @endif </h3>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2">    
            <a  data-toggle="modal" data-target="#favoritesModal2">
              <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-globe"></span> {{trans('econsultam.CoberturaS.Pública')}}</button>
            </a>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2" style="text-align: center;">
            <a  class="btn btn-primary btn-sm"  href="{{ route('controldoc.imprimirpdf_resumen', ['id' => $historia->id_agenda]) }}" ><span class="glyphicon glyphicon-print"></span> {{trans('econsultam.Resumen')}}</a>
        </div>  
        <div class="col-md-2 col-sm-2 col-xs-2" style="text-align: center;">
            @php
              $agenda_scan = \Sis_medico\AgendaScan::where('id_agenda', $historia->id_agenda)->first();
            @endphp

            @if(!is_null($agenda_scan))
              <a href="{{route('controldoc.descarga_scan2', ['id' => $historia->id_agenda])}}" class="btn btn-success">{{trans('econsultam.DescargarArchivo')}}</a>
            @endif
            
        </div>  
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
                <td  ><b>{{trans('econsultam.Paciente')}}</b></td>
                <td colspan="2">{{$historia->nombre1}} {{$historia->nombre2}} {{$historia->apellido1}} {{$historia->apellido2}}</td>
                <td  ><b>{{trans('econsultam.FechadeNacimiento')}}</b></td>
                <td >{{$historia->fecha_nacimiento}}</td>
                <td><b>{{trans('econsultam.Edad')}}</b></td>
              </tr>
              <tr >  
                <td ><b>{{trans('econsultam.Seguro')}}</b></td>
                <td >{{$historia->nombre}} @if($historia->sbnombre!="")- {{$historia->sbnombre}} @endif</td>
                <td ><b>Fecha</b></td>
                <td @if($historia->proc_consul=='0') colspan="3" @endif>{{$historia->fechaini}}</td>
                @if($historia->proc_consul=='1')
                <td ><b>{{trans('econsultam.Procedimientos')}}</b></td>
                <td colspan="2">{{$procs}}</td>
                @endif
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      </div> 
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="table-responsive col-md-12">
          <table class="table table-bordered table-hover dataTable" >
            <thead style="text-align: center; font-size: 11px;">
              <th > #</th>
              <!--th width="5%"> Dep.Entrega</th-->
              <th> {{trans('econsultam.Documentaciónrequerida')}}</th>
              <th> {{trans('econsultam.Ch.List')}}</th>
              <th> {{trans('econsultam.P.Entrega')}}</th>
              <th> {{trans('econsultam.P.Recibe')}}</th> 
              <th> {{trans('econsultam.Fecha/HoraEntrega')}}</th>
              <th> {{trans('econsultam.Archivo')}}</th> 
            </thead>
            <tbody style="font-size: 12px;">
              <td colspan="7" style="text-align: center; font-size: 10px;"><b>{{trans('econsultam.ADMISIÓNDELPACIENTE')}}</b></td>
              @php $i=0; @endphp
              @foreach($documentos as $documento)
                @php 
                  $archivo = DB::table('archivo_historico as ah')->where('ah.id_documento',$documento->id)->where('ah.id_historia',$hcid)->join('paciente as ue','ue.id','ah.id_usuario_entrega')->join('users as ur','ur.id','ah.id_usuario_recibe')->select('ah.*','ue.nombre1 as uenombre1','ue.apellido1 as ueapellido1','ur.nombre1 as urnombre1','ur.apellido1 as urapellido1')->first(); 
                  $copia_ced = Sis_medico\Paciente_Biopsia::where('id_paciente',$historia->id_paciente)->where('estado',3)->first();
                  @endphp
                @if($documento->est_doc_tarea=='0')  
                    @php $i++; @endphp
                    <tr>
                      <td>{{$i}}</td>
                      <!--td>{{$documento->tnombre}}</td-->  
                      <td>@if($documento->id==3 || $documento->id==21 )<a href="#" data-toggle="modal" data-target=@if($documento->id==3)"#favoritesModal2"@else"#favoritesModal2_prin"@endif>{{$documento->nombre}}</a>@elseif(!is_null($documento->link))<a href="{{$documento->link}}" target="_blank">{{$documento->nombre}}</a>@else{{$documento->nombre}}@endif</td>
                      <td style="text-align: center;"><input id="ch{{$documento->id}}" type="checkbox" class="flat-green" @if(!is_null($archivo)) @if($archivo->estado=='1')checked @endif @endif></td>
                      <td>@if(!is_null($archivo)){{$archivo->ueapellido1}}@endif</td>
                      <td>@if(!is_null($archivo)){{$archivo->urapellido1}}@endif</td>
                      <td>@if(!is_null($archivo)){{$archivo->fecha_entrega}}@endif</td>
                     @if(!is_null($archivo))
                        @if($documento->msp>'0' && $documento->msp<'4')
                        <td>@if(!is_null($archivo))<a target="_blank" href="{{asset('/')}}{{$archivo->ruta}}{{$archivo->archivo}}" alt="pdf"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a>@endif</td>
                        @elseif($documento->msp=='4')
                          <td>@if(!is_null($copia_ced))<a target="_blank" href="{{route('paciente.ver_copia_cedula',['cedula' => $historia->id_paciente])}}" alt="pdf"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a>@endif</td>
                        @else
                          @if($documento->sistema==1)
                            @if($documento->formato != null)
                        <td><a href="{{route('controldoc.imprimirpdf',[ 'ahid' => $archivo->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a></td>
                        @else
                        <td></td>  
                        @endif
                          @endif 
                        @endif
                      @endif 
                      <td>@if(!is_null($archivo))@if($documento->id==3)  @endif  @endif</td>   
                    </tr> 
                @endif      
              @endforeach  
            </tbody>
          </table>
        </div>
      </div> 
      
    
      
    </div>
  </div>

</section>



<!-- iCheck 1.0.1 -->
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">

//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green',
    });

    $('input[type="checkbox"].flat-green').iCheck('disable');

$(document).ready(function () {

    edad2();
    @foreach($documentos as $documento)
      $('input[type="checkbox"]#ch{{$documento->id}}').on('ifToggled', function(){
      
      <?php /*location.href ="{{route('controldoc.crea_doc',['hcid' => $hcid, 'id_doc' => $documento->id, 'url' => $url_doctor, 'unix' => $unix])}}"; */ ?>
    });
    @endforeach 

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





</script> 
@endsection