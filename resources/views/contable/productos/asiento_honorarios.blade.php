<style type="text/css">
    .modal-body .form-group {
        margin-bottom: 0px;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-header">
    <button type="button" class="close" id="cerrar_modal{{$c_tipo->id}}" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">GENERAR ASIENTO {{ $c_tipo->nombre }}</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <form method="POST" id="form_asiento_honorarios{{$c_tipo->id}}" >
            {{ csrf_field() }}
            @php
            $contador=0;
            @endphp
            <div class="row">
               
                <div class="col-md-4">
                    <label>Debe</label>
                    <input type="hidden" name="id" id="id" value="{{$planilla->id}}">
                    <input type="hidden" name="tipo" id="tipo" value="{{$c_tipo->id}}">
                </div>
                <div class="col-md-4">
                    <select class="form-control select2" required name="debe">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option @if($c->id=="5.1.01.06") selected="selected" @endif value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_debe" value="{{$valor}}" onKeyPress="return soloNumeros(event)">
                </div>
                <div class="col-md-4">
                    <label> Haber </label>
                </div>
                <div class="col-md-4">
                    <select class="form-control select2" required name="haber">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option @if($c->id=="1.01.03.01.02") selected="selected" @endif value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_haber" value="{{$valor}}" onKeyPress="return soloNumeros(event)">
                </div>
              

                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-primary" type="button" onclick="guardar_asiento_honorarios('{{$c_tipo->id}}')"> Guardar </button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
</div>
<script>
    $('.select2').select2({
        tags: false
    });

    function soloNumeros(e) {
        var key = window.Event ? e.which : e.keyCode
        return (key >= 48 && key <= 57)
    }

    function guardar_asiento_honorarios(id){
        //alert("hola");
        $.ajax({
            type: 'post',
            url: "{{route('productos.guardar_asiento_honorarios')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_asiento_honorarios"+id).serialize(),
            success: function(data){
                if(data.mensaje == 'ok'){
                    $("#cerrar_modal"+id).click();
                }
                else{
                    alert(data.mensaje);
                }
              
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }
</script>