@if(count($data1)>0)
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
              </div>
              <!-- /.box-header -->
              {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">Flujo de caja comparativo</h4>
                  <h4 style="text-align: center;">Periodo {{$fecha_desde}} - {{$fecha_hasta}}</h4>
                </div>
                <div class="col-md-4">
                  <dl>   
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd> 
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>   
                  </dl>
                </div>
              </div> --}}
              <div class="box-body">
                <div class="col-md-1">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl> 
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd> 
                  </dl>
                </div>
                <div class="col-md-4"> 
                  <h4 style="text-align: center;">Flujo de caja comparativo</h4>
                  <h5 style="text-align: center;">Periodo {{$fecha_desde}} - {{$fecha_hasta}}</h5>
                </div>
                <div class="col-md-4"> 
                </div>  
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div> 
        <div class="row">
          <div class="table-responsive col-md-12">
          <div class="content"> 
          <div class="content col-md-12">
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr >
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Cuenta</th> 
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Detalle</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="text-align: center" >{{ $fecha_desde }}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" style="text-align: center" >{{ $fecha_hasta }}</th>
                  </tr>
                </thead>
                <tbody> 
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;        
                    $i = 0;         $acum_mas2 = 0;     $acum_menos2 = 0;
                    $padre = "";
                @endphp 
                @foreach($data1 as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];       $acum_mas2 += $data2[$i]['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];     $acum_menos2 += $data2[$i]['saldo'];
                    }
                @endphp
               
                @if($value['signo']=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp
                    <tr> 
                        <td colspan="2" style="font-size: 12px;font-weight: bold">TOTAL INGRESOS</td>
                        <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                        <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas2,2)}}</td>
                    </tr>
                @endif
                @if($padre != $value['padre'])
                  @php    $padre = $value['padre']    @endphp
                  <tr> 
                    <td colspan="4" style="font-size: 12px;font-weight: bold">{{$value['padre']}}</td>
                  </tr>
                @endif
                  <tr>
                    <td style="font-size: 12px;">{{$value['id_plan']}}</td>
                    <!-- <td style="font-size: 12px;">{{ strtoupper($value['padre']) }}</td> -->
                    <td style="font-size: 12px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 12px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                    <td style="font-size: 12px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($data2[$i]['saldo'],2)}}</td>
                  </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
                <tr> 
                    <td colspan="2" style="font-size: 12px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                    <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos2,2)}}</td>
                </tr>
                <tr> 
                    <td colspan="2" style="font-size: 1612pxpx;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                    <td style="font-size: 12px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos) color:red; @endif" >{{number_format($acum_mas2-$acum_menos2,2)}}</td>
                </tr>
                </tbody>
              </table>
          </div>  
            
          </div>
        </div>
      </div>
      @endif