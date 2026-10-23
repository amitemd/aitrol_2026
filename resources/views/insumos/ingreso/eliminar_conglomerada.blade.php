
@if($eliminar !=  null)
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title"><b>No se puede eliminar la orden conglomerada ya fue procesado por contabilidad<b></h3>
            </div>
        </div>
    </div>
</section>
@else
<section class="content">
    <center>
        <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-12">
                @php 
                    $pedido = Sis_medico\Pedido::find($id);
                @endphp
              <h3 class="box-title" style="color: red;text-transform: uppercase;">{{trans('winsumos.esta_seguro_eliminar') }} @if(!is_null($pedido)) @if($pedido->orden_conglomerada == 1) Conglomerada @else Pedido @endif @endif ?</h3>
              <br/>
              <h3 class="box-title">{{trans('winsumos.paraelimpedidoingreseclave')}}</h3>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;"> 
            <form id="frm_eliminar" class="form-vertical" role="form" method="POST" >
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$id}}">
                <div class="box-body col-xs-24">
                    <!--Codigo-->
                    <div class="form-group col-xs-12{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-5 control-label" style="text-align: right;">Clave</label>
                        <div class="col-md-3">
                            <input id="password" type="password" class="form-control" name="password"  required autofocus >
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="button" onclick="eliminar_pedido()" class="btn btn-primary">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </center>
</section>

<script type="text/javascript">
    function eliminar_pedido(){
        $.ajax({
          type: 'post',
        //   url:"{{route('ingreso.eliminar_clave')}}", // hc4/HistoriaPacienteController->ingreso
          url:"{{route('ingreso.eliminar_clave_force')}}", // hc4/HistoriaPacienteController->ingreso
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_eliminar").serialize(),
          success: function(data){
            if(data == 'no'){
                alert(`{{trans('winsumos.lacontraesincorrec')}}`);
            }
            else{
                setTimeout(function() {
                    swal.fire({
                        title: "Mensaje: ",
                        text:  `{{trans('winsumos.elimina_correcto')}}`,
                        type: "success",
                    }).then((result) => {
                        if(result.value){
                            window.location.href = "{{ route('codigo.barra')}}";
                        }
                    });
                }, 1000);
            }
            console.log(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }
    // Splitter Atx 24 Pin, Fuente
</script>
@endif
