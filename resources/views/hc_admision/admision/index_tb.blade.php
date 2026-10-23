<style type="text/css">
  .table>tbody>tr>td{
    padding: 2px;
  }
</style>
@php 
  $paciente = Sis_medico\Paciente::find($historia->id_paciente);
  $repre_opc = Sis_medico\Paciente_Familia::where('id_paciente',$historia->id_paciente)->first();
  $copia_ced = Sis_medico\Paciente_Biopsia::where('id_paciente',$historia->id_paciente)->where('estado',3)->first();
@endphp
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="table-responsive col-md-12">
    <table class="table table-bordered table-hover dataTable" >
      <thead style="text-align: center; font-size: 11px;">
        <th width="5%"> #</th>
        <!--th width="5%"> Dep.Entrega</th-->
        <th width="35%"> Documentación requerida</th>
        <th width="5%"> Ch.List</th>
        <th width="10%"> P.Entrega</th>
        <th width="10%"> P.Recibe</th> 
        <th width="15%"> Fecha/Hora Entrega</th>
        <th width="10%"> Archivo</th> 
        <th width="10%"> Acción</th>
      </thead>
      <tbody style="font-size: 12px;">
        <td colspan="7" style="text-align: center; font-size: 10px;"><b>ADMISIÓN DEL PACIENTE</b></td>
        @php $i=0; @endphp
        @foreach($documentos as $documento)
          @php $archivo = DB::table('archivo_historico as ah')->where('ah.id_documento',$documento->id)->where('ah.id_historia',$hcid)->join('paciente as ue','ue.id','ah.id_usuario_entrega')->join('users as ur','ur.id','ah.id_usuario_recibe')->select('ah.*','ue.nombre1 as uenombre1','ue.apellido1 as ueapellido1','ur.nombre1 as urnombre1','ur.apellido1 as urapellido1')->first(); @endphp
          @if($documento->est_doc_tarea=='0')  
              @php $i++; @endphp
              <tr>
                <td>{{$i}}</td>
                <!--td>{{$documento->tnombre}}</td-->   
                <td>
                  @if($documento->msp=='1')<!--cobertura paciente-->
                    <a href="https://coberturasalud.msp.gob.ec/" target="_blank">{{$documento->nombre}}</a>
                  @elseif($documento->msp=='3') <!--cobertura principal-->
                    <a href="https://coberturasalud.msp.gob.ec/" target="_blank">{{$documento->nombre}}@if($paciente->papa_mama!=null)-{{$paciente->papa_mama}}@endif</a>
                  @elseif($documento->msp=='2')  <!-- cobertura opcional-->
                    <a href="https://coberturasalud.msp.gob.ec/" target="_blank">{{$documento->nombre}}@if(!is_null($repre_opc))-{{$repre_opc->papa_mama}}@endif</a>
                  @elseif($documento->msp=='4')  <!-- copia cedula paciente-->
                    <a href="{{route('paciente.ver_copia_cedula',['cedula' => $paciente->id])}}" target="_blank">{{$documento->nombre}}</a>  
                  @elseif(!is_null($documento->link))
                    <a href="{{$documento->link}}" target="_blank">{{$documento->nombre}}</a>
                  @else
                    {{$documento->nombre}}
                  @endif
                </td>
                <td style="text-align: center;"><input id="ch{{$documento->id}}" type="checkbox" class="flat-green" @if(!is_null($archivo)) @if($archivo->estado=='1')checked @endif @endif></td>
                <td>@if(!is_null($archivo)){{$archivo->ueapellido1}}@endif</td>
                <td>@if(!is_null($archivo)){{$archivo->urapellido1}}@endif</td>
                <td>@if(!is_null($archivo)){{$archivo->fecha_entrega}}@endif</td>
                @if(!is_null($archivo))
                  @if($documento->msp>'0' && $documento->msp<'4')
                  <td>@if($archivo->archivo!=null)<a target="_blank" href="{{asset('/hc')}}/{{$archivo->archivo}}" alt="pdf"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a>@endif</td>
                  @elseif($documento->msp=='4')
                  <td>@if(!is_null($copia_ced))<a target="_blank" href="{{route('paciente.ver_copia_cedula',['cedula' => $paciente->id])}}" alt="pdf"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a>@endif</td>
                  @else
                    @if($documento->sistema==1)
                      @if($documento->formato != null)
                  <td><a href="{{route('controldoc.imprimirpdf',[ 'ahid' => $archivo->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"> Descargar</span></a></td>
                  @else
                      <td></td>
                      @endif
                    @else
                  <td></td>  
                    @endif  
                  @endif
                @else
                  <td></td>
                @endif 
                <td>
                  @if(!is_null($archivo))
                    @if($documento->msp>'0' && $documento->msp<'4')
                    <a href="{{url('control/documentos/admision/sube/archivo')}}/{{$archivo->id}}/{{$documento->id}}" data-toggle="modal" data-target="#Subir">
                      <button class="btn btn-success btn-xs">@if($archivo->archivo==null)Subir @else Actualizar @endif</button>
                    </a>  
                    @endif  
                    @if($documento->msp=='4')
                    <a href="{{url('control/documentos/admision/sube/archivo')}}/{{$archivo->id}}/{{$documento->id}}" data-toggle="modal" data-target="#Subir">
                      <button class="btn btn-success btn-xs">@if(is_null($copia_ced))Subir @else Actualizar @endif</button>
                    </a>
                    @endif
                    @if($documento->sistema==1)
                    <a href="{{url('control/documentos/admision/sube/archivo')}}/{{$archivo->id}}/{{$documento->id}}" data-toggle="modal" data-target="#Subir">
                      <button class="btn btn-success btn-xs">@if($archivo->archivo==null)Subir @else Actualizar @endif</button>
                    </a>
                    @if($archivo->archivo!=null)<a target="_blank" href="{{asset('/hc')}}/{{$archivo->archivo}}" alt="pdf"><span class="glyphicon glyphicon-download-alt"> Ver</span></a>@endif
                    @endif
                  @endif
                    
                </td>   
              </tr> 
          @endif      
        @endforeach  
      </tbody>
    </table>
  </div>
</div> 
      

<!-- iCheck 1.0.1 -->
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">

//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })  

  $(document).ready(function () {

    @foreach($documentos as $documento)
        
      $('input[type="checkbox"]#ch{{$documento->id}}').on('ifChecked', function(){

        $.ajax({
          type: 'get',
          url:'{{route('controldoc.valida_existe',['hcid' => $hcid, 'id_doc' => $documento->id])}}',
          success: function(data){
          
            if(data=='0'){
              crea_documento({{$hcid}},{{$documento->id}});
            }else if(data=='1'){
              actualiza_documento({{$hcid}},{{$documento->id}},1); 
            }  

          }
        });

            
    
      });

      $('input[type="checkbox"]#ch{{$documento->id}}').on('ifUnchecked', function(){

        actualiza_documento({{$hcid}},{{$documento->id}},0);        
    
      });


/*$('input[type="checkbox"]#ch{{$documento->id}}').on('ifToggled', function(){

    $.ajax({
        type: 'get',
        url:'{{route('controldoc.crea_doc',['hcid' => $hcid, 'id_doc' => $documento->id])}}',
        success: function(data){
            $('#index_tb').empty().html(data);
        }
    })
    
    
});*/
    @endforeach  


});

//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green',
      
    });

    function crea_documento(hcid,id){

      $.ajax({
        type: 'get',
        url:'{{url("controldoc/admision")}}'+'/'+hcid+'/'+id, //controldoc.crea_doc
        success: function(data){
            $('#index_tb').empty().html(data);
        }
      })


    }

    function actualiza_documento(hcid,id,activa){
      $.ajax({
        type: 'get',
        url:'{{url("control/admision/actu")}}'+'/'+hcid+'/'+id+'/'+activa, //controldoc.crea_doc
        success: function(data){
            $('#index_tb').empty().html(data);
        }
      }) 
    }





 </script> 
