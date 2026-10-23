<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="table-responsive col-12">
      <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <tbody>
       		@php  $cambia = 0; $contador = 0; @endphp	
       		@foreach($examenes_labs as $examen)
       			@if($cambia != $examen->id_examen_agrupador_labs)
       				@php $contador = 0; @endphp
       				<tr>
              	<td colspan="4" style="background-color: #ff6600;color: white;margin: 0px;padding: 0;">{{$agrupador_labs->where('id',$examen->id_examen_agrupador_labs)->first()->nombre}}</td>
              </tr>
              @php $cambia = $examen->id_examen_agrupador_labs; @endphp	
       			@endif
       			@if($contador == 0)
       			<tr >
       			@endif	
                  <td style="padding: 5px;"><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif type="checkbox" class="flat-orange"></td>
                  <td style="padding: 5px;" >{{$examen->nombre}}</td>

                  @php $contador ++; @endphp
                  @if($contador == 2) @php $contador = 0; @endphp @endif
                @if($contador == 0)   
                </tr>
                @endif
       		@endforeach

        </tbody>
      </table>
    </div>
  </div>
  <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
  <script type="text/javascript">
    $('input[type="checkbox"].flat-orange').iCheck({
      checkboxClass: 'icheckbox_flat-orange',
      radioClass   : 'iradio_flat-orange'
    }); 
    $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
      //console.log(this.name.substring(2));
      crear_examen_favorito(this.name.substring(2));
    });

    $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
      //cotizador_crear();
      eliminar_examen_favorito(this.name.substring(2));
    });
  </script>