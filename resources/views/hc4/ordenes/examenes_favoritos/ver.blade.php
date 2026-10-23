  <div class="box-header">
    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
      <div class="col-12" style="background-color: #004AC1; padding: 10px">
         <label class="box-title" style="color: white; font-size: 20px">Nombre del Perfil: {{$protocolo->nombre}}</label>
      </div> 
    </div>
  </div>
  <input type="hidden" name="xid" id="xid" value="{{$protocolo->id}}">
  <div class="box-body"> 
    <br>
    <div id="xlistado">  
      <div id="xbuscador_fav">
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
      </div>       

      
    </div>  
  </div>
   
