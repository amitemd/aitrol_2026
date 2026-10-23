
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th >Sel.</th>
              <th >Fecha</th>
              <th >Procedimiento</th> 
              <th >Detalle</th> 
            </tr>
          </thead>
          <tbody>
          @foreach($insumos as $value)
              <tr>

                  <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_insumo('{{$a_plano->id}}','{{$value->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
                  <td>{{substr($value->fecha,0,10)}}</td>
                  @php $procedimientos = $value->procedimiento->hc_procedimiento_f;  @endphp
                  <td>@foreach($procedimientos as $procedimiento) @if($procedimiento->procedimiento->grupo_procedimiento != null) {{ $procedimiento->procedimiento->nombre }} @endif @endforeach</td> 
                  <td style="text-align:left;">
                    @foreach($value->detalles as $detalle)
                      @if($detalle->check == 1 and $detalle->estado == 1)
                        @php $producto = Sis_medico\Producto::where('codigo',$detalle->codigo)->first(); @endphp
                        @if(!is_null($producto))
                        <span  @if(!is_null($producto->tipo_produc)) @if($producto->tipo_produc->nombre == 'INSUMOS RE' || $producto->tipo_produc->nombre == 'INSUMOS MEDICOS' ) style="background-color:#5DE2CA;" @endif @else style="color:red;"  @endif> {{$producto->codigo}} - {{$producto->nombre}} - <b> Precio: </b> {{$detalle->precio}} - @if(!is_null($producto->tipo_produc)) <b> {{$producto->tipo_produc->nombre}} {{$producto->tipo_produc->ap_insumo}} </b> @endif </span> <br>
                        @endif
                      @endif
                    @endforeach
                  </td>
              </tr>
          @endforeach
          
          </tbody>
        </table>
      </div>
   