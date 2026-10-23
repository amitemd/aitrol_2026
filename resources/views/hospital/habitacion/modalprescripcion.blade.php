<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="myModalDoctor">{{trans('hospitalizacion.Evoluci&oacute;nMedica')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <form action="{{route('hospital.guardarm')}}" method="POST" id="formulario">
    <div class="modal-body">
      {{ csrf_field() }}
      <div class="form-group row">
        <label  class="col-sm-3 col-form-label">{{trans('hospitalizacion.Paciente:')}}</label>
        <div class="col-sm-9">
          <input type="text" class="form-control paciente" name="pacienteid" id="pacienteid" disabled value="{{$pacienteid->apellido1}} {{$pacienteid->apellido2}} {{$pacienteid->nombre1}} {{$pacienteid->nombre2}}">
          <input type="hidden" name="id_paciente" id="id_paciente" value="{{$pacienteid->id}}">
        </div>
      </div>

      <div class="form-group row">
        <div  class="col-sm-9">
          <input class="form-control" onchange="agregar_descripcion()" id="buscarnombre" name="buscarnombre"  type="text" placeholder="medicamento" style="z-index:999999 !important">
        </div>
        <button type="button" onclick="crear();" class="btn btn-primary col-md-2 col-sm-2 col-12">
          <span class="fa fa-plus"></span>{{trans('hospitalizacion.Agregar')}}
        </button>
      </div>

      <div class="form-group">
        <label>{{trans('hospitalizacion.Evoluci&oacute;n')}}</label>
        <input type="text" class="form-control" id="evolucion_dr" name="evolucion_dr"></input>
      </div>
      
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>{{trans('hospitalizacion.Rp')}}</label>
          <textarea class="form-control" name="medicamento" id="medicamento" rows="4"></textarea>
        </div>
        <div class="form-group col-md-6">
          <label >{{trans('hospitalizacion.Prescripcion')}}</label>
          <textarea class="form-control" name="descripcion" id="descripcion" rows="4"></textarea>
        </div>
      </div>

    </div>
    
    <div class="modal-footer">
      <button type="submit" class="btn btn-secondary" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
      <button type="" class="btn btn-danger">{{trans('hospitalizacion.Guardar')}}</button>
    </div>

  </form>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
console.log("init...");
console.log("{{route('hospital.autocompletarmodal22')}}");  

$("#buscarnombre").autocomplete({
  source: function( request, response ){
    $.ajax({
      method:'GET',
      url: "{{route('hospital.autocompletarmodal22')}}",
      dataType: "json",
      data: { term: request.term },
      success: function( data ) {
      response(data);
      $('#descripcion').html;
      }
    });
  },
  minLength: 1,
  change: function( event, ui ){
   //$( ".buscarnombre").autocomplete( "enable" );
   //$( ".medicamento").autocomplete( "enable" );
   //alert($('#buscarnombre').val());
   //$('#medicamento').append($('#buscarnombre').val()+'<br/>');
  }
});

  function crear(){ 

    $('#medicamento').append($('#buscarnombre').val()+'\r\n');
    $('#descripcion').append($('#buscarnombre').val()+'\r\n');

  }
  function agregar_descripcion(){
        $.ajax({
            type: 'post',
            url:"{{route('hospital.agregar_descripcion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'medicamento':$("#buscarnombre").val()},
            success: function(data){
            //console.log(data);
            if(data.value != 'no resultados'){
              $('#descripcion').append(data+'<br/>');

            }

            },
            error: function(data){
                console.log(data);
            }
        })
  }
</script>

<style>
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box; 
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }
</style>