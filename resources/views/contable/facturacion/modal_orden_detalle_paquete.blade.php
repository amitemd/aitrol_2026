<style type="text/css">
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

</style>
<div class="modal-content">
      <div class="box-header">
            <br>
            <div class="col-md-9">
              <h3 class="box-title"><b>DETALLE PAQUETE</b></h3>
            </div>
            <div class="col-md-1 text-right">
            </div>
            <div class="col-md-2">
              <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
            </div>
      </div>
      <div style="text-align: left" class="modal-header">
        <div class="box-header">
          <h3 class="box-title"><b>Orden # {{$id}} </b></h3>
        </div>
      </div>
      @foreach ($detalles as $value)
          <div class="box-header">
            <div class="col-md-6">
              {{$value->descripcion}}
            </div>
            <div class="col-md-3">
            </div>
            <div class="col-md-2">
              {{$value->total}}
            </div>
            <!--@if($id_usuario == '0922794102')
              <div class="col-md-1">
                <a id="compl_proc" class="btn btn-primary btn-sm" href="{{route('completa_proceso.cierrecaja',['id_prod' => $value->id_producto,'prec_prod' => $value->total,'id_orden' => $value->id_orden])}}" >CP</a>
              </div>
            @else
              <div class="col-md-1">
              </div>
            @endif-->
          </div>

          @if(!is_null($value->id_producto))
            
            @php
              $paquetes = $orden->paquetes_detalles->where('id_producto',$value->id_producto);
            @endphp

            @if(!is_null($paquetes))

              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="blue" aria-describedby="example2_info">
                  <thead>
                  <tr class="well-dark">
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">GRUPO</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">CANTIDAD</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">DETALLE</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">VALOR</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">IVA</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">TOTAL</th>
                  </tr>
                  </thead>
                  <tbody>
                     
                      @foreach ($paquetes as $value2)

                      @php

                        $grup_prod = null;

                        $prod_paq = Sis_medico\Ct_productos_paquete::where('id',$value2->id_producto_paquete)->where('estado','1')->first();
                        
                        if(!is_null($prod_paq)){
                          $grup_prod = Sis_medico\Ct_productos::where('id',$prod_paq->id_producto )->first();
                        } 
                        
                      @endphp
                       
                      <tr>
                        <td>
                          @if(!is_null($grup_prod))
                            @if($grup_prod->grupo == '1')
                              Insumos
                            @elseif($grup_prod->grupo == '2')
                              Medicamentos
                            @elseif($grup_prod->grupo == '4')
                              Procedimientos
                            @elseif($grup_prod->grupo == '3')
                              Servicios
                            @elseif($grup_prod->grupo == '5')
                              Otros
                            @elseif($grup_prod->grupo == '6')
                              Honorario
                            @endif
                          @endif
                        </td>
                        <td>@if(!is_null($prod_paq)){{$prod_paq->cantidad}}@endif</td>
                        <td>@if(!is_null($value2)){{$value2->descripcion}}@endif</td>
                        <td>@if(!is_null($value2)){{$value2->precio}}@endif</td>
                        <td>0.00</td>
                        <td>@if(!is_null($value2)){{($prod_paq->cantidad)*($value2->precio)}}@endif</td>
                      </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>

            @endif

          @endif
         
      @endforeach

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
      </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
  
  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : false,
    'autoWidth'   : false
  })

</script>


