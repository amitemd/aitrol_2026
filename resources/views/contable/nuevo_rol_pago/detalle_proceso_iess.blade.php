<div class="modal-header">
    <div class="col-md-10"><h3>Detalle</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
    <div class="row">
    @foreach($detalles as $detalle)
        <div class="col-md-6">
            {{$detalle->numero}}-{{$detalle->cedula}}-{{$detalle->nombres}}-{{$detalle->detalle}}-{{$detalle->nut}}-{{$detalle->valor}}
        </div>    
        <div class="col-md-6">
            {{$detalle->detalle_resultado}}
        </div>  
        <div class="col-md-12"></div>      
    @endforeach 
    </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
    
    

</script>