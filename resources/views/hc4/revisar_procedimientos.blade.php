<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="row" style="margin: 0;">  
  <div class="col-lg-6 col-6"><h4>Revisar Procedimientos Desde: {{$desde}} Hasta: {{$hasta}}</h4></div>
  <div class="col-lg-6 col-6 color" style="padding-left: 15px;font-size: 15px">Total de Registros: {{$procedimientos->count()}}</div>
</div>

<table  id="example2" class="table" cellspacing="0" width="100%" style="font-size: 12px;">
  <thead style="">
    <tr style=" ">
      <th scope="col" class="color titulo" >Fecha / Hora</th>
      <th scope="col" class="color titulo" >Cédula</th>
      <th scope="col" class="color titulo" >Apellidos</th>
      <th scope="col" class="color titulo" >Nombres</th>
      <th scope="col" class="color titulo" >Edad</th>
      <th scope="col" class="color titulo" >Seguro</th>
      <th scope="col" class="color titulo" >Procedimientos</th> 
      <th scope="col" class="color titulo" >Amb/hosp</th>
      <th scope="col" class="color titulo" >Estado</th>
      <th scope="col" class="color titulo" >Revisado</th>
    </tr>
  </thead>
  <tbody>

    @foreach($procedimientos as $procedimiento)
       @php
          $edad= 0;
            if ($procedimiento->paciente->fecha_nacimiento != null) {
                $edad = Carbon\Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
            }
        @endphp
      <tr>
        <td class="color">{{$procedimiento->fechaini}}</td>
        <td class="color">{{$procedimiento->id_paciente}}</td>
        <td class="color">{{$procedimiento->paciente->apellido1}} {{$procedimiento->paciente->apellido2}}</td>
        <td class="color">{{$procedimiento->paciente->nombre1}} {{$procedimiento->paciente->nombre2}}</td>
        <td class="color">{{$edad}}</td>
        <td class="color">{{$procedimiento->seguro->nombre}}</td>
        <td class="color">{{$procedimiento->procedimiento->nombre}}</td>
        <td class="color">@if($procedimiento->est_amb_hos == 0) AMBULATORIO @else HOSPITALIZADO @endif</td>
        <td class="color">@if($procedimiento->estado_cita=='0') POR CONFIRMAR @elseif($procedimiento->estado_cita=='1') CONFIRMADO @elseif($procedimiento->estado_cita=='2') REAGENDAR @elseif($procedimiento->estado_cita=='3') SUSPENDIDO @elseif($procedimiento->estado_cita=='4') PRE-ADMISIONADO @endif</td>
        <td class="color">
          <select id="revisado{{$procedimiento->id}}" name="revisado{{$procedimiento->id}}" onclick="revisado_si_no(this);">
            <option @if($procedimiento->revisado=='0') selected @endif value="0">NO</option>
            <option @if($procedimiento->revisado=='1') selected @endif value="1">SI</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="color" ><b style="color: green;">Diagnóstico</b></td>
        <td class="color" colspan="3"><input style="width: 100%" type="text" name="diagnostico{{$procedimiento->id}}" id="diagnostico{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->diagnostico_proc}}"></td>
        <td class="color"><b style="color: green;">Observacion</b></td>
        <td class="color" colspan="3"><input style="width: 100%" type="text" name="observacion{{$procedimiento->id}}" id="observacion{{$procedimiento->id}}" onchange="revisar_formulario({{$procedimiento->id}})" maxlength="200" value="{{$procedimiento->observacion_proc}}"></td> 
        <td class="color" colspan="1"><button class="btn btn-success btn-sm" onclick="revisar_formulario({{$procedimiento->id}})"><span class="glyphicon glyphicon-floppy-saved"></span></button></td>
        <td class="color" colspan="1"><span style="color: red"> SUPER: </span><input id="sup{{$procedimiento->id}}" name="sup{{$procedimiento->id}}" type="checkbox" class="flat-red" @if($procedimiento->supervisa_robles) checked @endif ><span style="color: purple"> CRM: </span><input id="crm{{$procedimiento->id}}" name="crm{{$procedimiento->id}}" type="checkbox" class="flat-purple" @if($procedimiento->solo_robles) checked @endif ></button></td>   
      </tr>
  @endforeach
  </tbody>

</table>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  
  $('input[type="checkbox"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  });

  $('input[type="checkbox"].flat-purple').iCheck({
    checkboxClass: 'icheckbox_flat-purple',
    radioClass   : 'iradio_flat-purple'
  });

  $('input[type="checkbox"].flat-red').on('ifChecked', function(event){

    //console.log(this.name,this.name.substring(3));
    cambiar_supervision(this.name.substring(3));
    //cotizador_crear_id(this.name.substring(2));
  }); 

  $('input[type="checkbox"].flat-purple').on('ifChecked', function(event){

    //console.log(this.name,this.name.substring(3));
    cambiar_crm(this.name.substring(3));
    //cotizador_crear_id(this.name.substring(2));
  }); 

</script>    

<script type="text/javascript">
  function revisado_si_no(val){
    $.ajax({
      type: 'get',
      url:"{{url('revision/cargar')}}/"+val.id+"/"+val.value,
      datatype: 'html',

      success: function(datahtml){
        //console.log(datahtml);
        
        
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }
  function revisar_formulario(val){
    //alert($('#diagnostico'+val).val());
    $.ajax({
      type: 'post',
      url:"{{route('hc4_revisar.formulario')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'html',
      data: { id: val, diagnostico: $('#diagnostico'+val).val(), observacion: $('#observacion'+val).val()},
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }
  function cambiar_supervision(id){
    
    $.ajax({
      type: 'post',
      url:"{{url('revision/cambiar_supervision')}}/1/"+id,
      data: {'_token': '{{ csrf_token() }}'},
      datatype: 'html',
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }
  function cambiar_crm(id){
    //alert($('#diagnostico'+val).val());
    $.ajax({
      type: 'post',
      url:"{{url('revision/cambiar_crm')}}/1/"+id,
      data: {'_token': '{{ csrf_token() }}'},
      datatype: 'html',
      success: function(datahtml){
        //console.log(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    }); 
  }

</script>


