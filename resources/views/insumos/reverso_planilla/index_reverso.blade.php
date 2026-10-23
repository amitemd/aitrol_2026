@extends('insumos.reverso_planilla.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <form method="POST" id="reverso" action="" target="_blank">
                    {{ csrf_field() }}
                    <h3>
                    Reverso de Planillas
                    </h3>
                    <div class="box-body">
                        <div class="form-group col-xs-3{{ $errors->has('id_cab_mov') ? ' has-error' : '' }}">
                            <label for="id_cab_mov" class="col-md-10 control-label">Ingrese Id Cab movimientos</label>
                            <input type="int" name="id_cab_mov" size="5" class="form-control" value=" " required maxlength="10">
                        </div>
                    </div>
                            
                    
                    <div class="form-group">
                        
                        <div class="col-md-6 col-md-offset-4">
                            <button type="button" class="btn btn-danger" onclick=reversar()> Reversar </button>
                        </div>

                    </div>   

                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script type="text/javascript">

    
        function reversar(){
            $.ajax({
              type: 'post',
              url:"{{route('insumos.reverso_planilla')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              type: 'POST',
              datatype: 'json',
              data: $('#reverso').serialize(),
              beforeSend: function(){
                Swal.fire({
                    text: "Cargando...",
                    imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
              },
              success: function(data){
                if(data== 'Cabecera Reversada Correctamente'){
                    swal('Success', data, 'success');
                }else{
                    swal('error', data, 'error');
                }
               
                 
              },
              error: function(data){
                swal('error', data, 'error');
              }
          })
        }
    


    
</script>
</section>
@endsection