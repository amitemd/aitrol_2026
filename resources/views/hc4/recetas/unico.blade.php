<div id="" >  
 <div class="col-md-12" >
  @if(!is_null($hist_recetas))
  @php $receta = Sis_medico\hc_receta::find($hist_recetas->id);  @endphp
  <div class="row">
        <div class="col-md-6">
          <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
          <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #004AC1; ">
              @php $rdetalles = $receta->detalles; @endphp
              @if(!is_null($receta))
                @foreach($rdetalles as $detalle)
                  @php $genericos = $detalle->medicina->genericos; @endphp
                  <p>{{$detalle->nombre}} @foreach($genericos as $gen) ( {{$gen->generico->nombre}} ) @endforeach : {{$detalle->cantidad}} {{ $detalle->detalle_cantidad}}</p>
                @endforeach
                @if(!is_null($hist_recetas->rp))
                <p><?php echo $hist_recetas->rp?>
                </p>
                @endif 
              @endif
          </div>
        </div>
        <div class="col-md-6" >
            <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
            <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
              @php $rdetalles = $receta->detalles; @endphp
              @if(!is_null($receta))
                @foreach($rdetalles as $detalle)
                  @php $genericos = $detalle->medicina->genericos; @endphp
                  <p>{{$detalle->nombre}}: {{$detalle->dosis}}</p>
                @endforeach
                @if(!is_null($hist_recetas->prescripcion))
                  <p><?php echo $hist_recetas->prescripcion?></p>
                @endif
              @endif
            </div>
        </div>
  </div>
  @endif
</div>
</div>