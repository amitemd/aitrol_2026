@extends('financiero.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}"> 
<!-- Ventana modal editar -->
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
<style>
.content-wrapper, .right-side
  {
    background-color: #ecf0f5 !important;
  }
p.s1 {
  margin-left:  10px;
  font-size:    14px;
  font-weight:  bold;
} 
p.s2 {
  margin-left:  20px;
  font-size:    12px;
  font-weight:  bold;
} 
p.s3 {
  margin-left:  30px;
  font-size:    10px;
  font-weight:  bold;
} 
p.s4 {
  margin-left:  40px;
  font-size:    10px;
} 
p.t1 { 
  font-size:    14px;
  font-weight:  bold;
} 
p.t2 { 
  font-size:    12px;
  font-weight:  bold;
} 
p.t3 { 
  font-size:    10px;
}
.table-condensed>thead>tr>th>td, .table-condensed>tbody>tr>th>td, .table-condensed>tfoot>tr>th>td, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td {
  padding: 0.5px;
  line-height: 1;
}
</style>
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Financiero</a></li>
      
      <li class="breadcrumb-item"><a href="../">Proyección Financiera II</a></li> 
    </ol>
  </nav>
  <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">BUSCADOR</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('financiero.proyeccionfinanciera2_index') }}" >
        {{ csrf_field() }}

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="{{$fecha_desde}}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}"  autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label" >Mostrar cuentas de detalle</label>
              <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value=""  @if(@$cuentas_detalle=="1") checked @endif>
          </div>

          {{-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
              <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(@$mostrar_detalles=="1") checked @endif>
          </div> --}}

          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;"> 
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
            <button type="button" class="btn btn-primary" id="btn_imprimir" name="btn_imprimir">
                  <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
            </button> 
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
            </button> 
          </div> 

        </form>
      </div>

      @if(count($ingresos)>0 || count($costos)>0 || count($gastos)>0)
      <div id="example2_wrapper" class="form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid"> 
              <!-- /.box-header -->
              <div class="box-body">
                <div class="col-md-1">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
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
                  <h4 style="text-align: center;">Proyección Financiera II</h4>
                  <h4 style="text-align: center;">Periodo {{$fecha_desde}} - {{$fecha_hasta}}</h4>
                </div>
                <div class="col-md-4">
                  {{-- <dl>   
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd> 
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>   
                  </dl> --}}
                </div> 
                

              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="table-responsive col-md-6">
          <div class="content">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Años</th>
                  
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Utilidad del ejercicio</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total part trabaj</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Variación X</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Variación Y</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">UAIMP RENT</th>
                </tr>
              </thead>
              <tbody> 
                <tr>
                  <td colspan="3"><strong></strong></td> 
                </tr>
                @php $anios=0; @endphp
                @foreach($fechagrupo as $fecha)
                    @php $anios++; $fechault=$fecha; @endphp
                  @endforeach
                @php
                  $acum_mas=0;$agastos=0;$acostos=0;$avgastos=0;$avcostos=0;
                  $cont = 0;  $esp = ""; $index=0; $ind=0; $axy=0; $ax2=0; $ay2=0;

                // dd($liquidez);
                @endphp
                @foreach($liquidez as $data)
                    @foreach($data as $value)
                @php
                 //dd($data);


                @endphp
                   
                  <tr>
                    <td>{{$fechagrupo[$index]}}</td>
                  
                    @if ($anios==1)
                    <td style="font-size: 10px;text-align: right; @if($value['utilidad'] < 0) color:red; @endif" >{{number_format($value['utilidad'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['trabajadores'] < 0) color:red; @endif" >{{number_format($value['trabajadores'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variacionx'] < 0) color:red; @endif" >{{($value['variacionx'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variaciony'] < 0) color:red; @endif" >{{($value['variaciony'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['renta'] < 0) color:red; @endif" >{{($value['renta'])}}</td>
                    @else
                    
                    <td style="font-size: 10px;text-align: right; @if($value['utilidad'] < 0) color:red; @endif" >{{number_format($value['utilidad'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['trabajadores'] < 0) color:red; @endif" >{{number_format($value['trabajadores'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variacionx'] < 0) color:red; @endif" >{{($value['variacionx'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variaciony'] < 0) color:red; @endif" >{{($value['variaciony'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['renta'] < 0) color:red; @endif" >{{($value['renta'])}}</td>
                    @endif
                  </tr>
               
                  @php $acostos+=$value['autilidad']; $agastos+=$value['atrabajo']; $avcostos+=$value['vutilidades']; $avgastos+=$value['vutilidades'];  @endphp
                    @endforeach
                    @php $index++; $numanios=$index;  @endphp
                @endforeach 
                <tr>
                        <td style="font-size: 10px;font-weight: bold;">TOTAL</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acostos < 0) color:red; @endif" >{{number_format($acostos,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($agastos < 0) color:red; @endif" >{{number_format($agastos,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($avcostos < 0) color:red; @endif" >{{number_format(($avcostos/$numanios),2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($avgastos < 0) color:red; @endif" >{{number_format(($avgastos/$numanios),2)}}</td>
                    </tr>
                  
                <tr> 
                  <td colspan="3">&nbsp;</td>  
                </tr>

  
           
             
              </tbody>
            </table>
          </div> 
            
          </div>
          <div class="table-responsive col-md-6">
          <div class="content">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Periodo</th>
                  
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">XY</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">X2</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Y2</th>
                  
                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><strong></strong></td> 
                </tr>
                @php $anios=0; @endphp
                @foreach($fechagrupo as $fecha)
                    @php $anios++; $fechault=$fecha; @endphp
                  @endforeach
                @php
                
                  $cont = 0;  $esp = ""; $index=0; $ind=0;
                  
                  //dd($liquidez);
                @endphp
                @foreach($liquidez as $data) 
                    @foreach($data as $value) 
                @php
                //dd($value['cuenta']);
                    @$axy+=$value['xy'];
                    @$ax2+=$value['x2'];
                    @$ay2+=$value['y2'];
                @endphp
                  <tr>
                    <td>{{$fechagrupo[$index]}}</td>
                    
                    @if ($anios==1)
                    <td style="font-size: 10px;text-align: right; @if($value['xy'] < 0) color:red; @endif" >{{($value['xy'])}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['x2'] < 0) color:red; @endif" >{{($value['x2'])}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['y2'] < 0) color:red; @endif" >{{($value['y2'])}}</td>
                    @else
                    
                    <td style="font-size: 10px;text-align: right; @if($value['xy'] < 0) color:red; @endif" >{{($value['xy'])}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['x2'] < 0) color:red; @endif" >{{($value['x2'])}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['y2'] < 0) color:red; @endif" >{{($value['y2'])}}</td>
                    
                    
                    @endif
                  </tr>
                    
                    @endforeach
                @php $index++; $numanios=$index;  @endphp
                @endforeach
                <tr>
                        <td style="font-size: 10px;font-weight: bold;">TOTAL</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acostos < 0) color:red; @endif" >{{number_format($axy,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($agastos < 0) color:red; @endif" >{{number_format($ax2,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($avcostos < 0) color:red; @endif" >{{number_format(($ay2),2)}}</td>
                      
                    </tr>
                <tr> 
                  <td colspan=""></td>  
                </tr>

  
           
             
              </tbody>
            </table>

            
          </div> 
            
          </div>
          <div class="table-responsive col-md-3">
          <div class="content" style="min-height: 100px;">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Codigo: activate to sort column ascending">Resultados</th>
                  
                </tr>
              </thead>
                    @php $px=$acostos/$numanios; $py=$agastos/$numanios; @endphp
              <tbody>
                <tr>
                    <td style="font-size: 10px;font-weight: bold;">PROMEDIO X</td>
                    <td style=" ">{{number_format($px,2)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10px;font-weight: bold;">PROMEDIO Y</td>
                    <td style=" ">{{number_format($py,2)}}</td>
                </tr>
              </tbody>
              </table>
              </div> 
          </div> 
          <div class="table-responsive col-md-3">
          <div class="content" style="min-height: 100px;">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Codigo: activate to sort column ascending">Resultados</th>
                  
                </tr>
              </thead>
              @if ($numanios>1)  @php $numanios-=1; @endphp @endif
              @php
                $div2=$ax2-(($acostos*$acostos)/$numanios);
                if ($div2){
                  $formulab=(($axy-(($acostos*$agastos)/$numanios))/($div2)); 
                }else{
                  $formulab=0;
                }
                
                $formulaa=$py-($formulab*$px); 
              @endphp
              @if ($numanios>1) @php $numanios+=1;@endphp  @endif

              <tbody>
                <tr>
                    <td style="font-size: 10px;font-weight: bold;">FÓRMULA B</td>
                    <td style=" ">{{number_format($formulab,2)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10px;font-weight: bold;">FÓRMULA A</td>
                    <td style=" ">{{number_format($formulaa,2)}}</td>
                </tr>
              </tbody>
              </table>
              </div> 
          </div> 
          <div class="table-responsive col-md-6">
            
          </div> 

          <div class="table-responsive col-md-6" style="clear:both;">
          <div class="content">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
              <tr class='well-dark'>
              <th width="10%" class="" style="text-align: center;" tabindex="0" aria-controls="example2" rowspan="1" colspan="6" aria-label="Codigo: activate to sort column ascending">AJUSTE</th>
              </tr>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Periodo</th>
                  
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Utilidad del ejercicio</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Regresión</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total part trabaj</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Error</th>
                  
                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><strong></strong></td> 
                </tr>
                @php $anios=0;; @endphp
                @foreach($fechagrupo as $fecha)
                    @php $anios++; $fechault=$fecha; @endphp
                  @endforeach
                @php
                  $acum_mas=0;$agastos=0;$acostos=0;$avgastos=0;$avcostos=0;
                  $cont = 0;  $esp = ""; $index=0; $ind=0;$aregresion=0; $aerror=0;
                  
                  //dd($liquidez);
                @endphp
                @foreach($liquidez as $data) 
                    @foreach($data as $value) 
                @php
                //dd($value['cuenta']);
                
                 $regresion=0;
                
                @endphp
                  <tr>
                    <td>{{$fechagrupo[$index]}}</td>
                    
                    @if ($anios==1)
                    @php $regresion=($value['utilidad']*$formulab)+$formulaa; $error=$value['trabajadores']-$regresion;  $aerror+=$error; @endphp
                    <td style="font-size: 10px;text-align: right; @if($value['utilidad'] < 0) color:red; @endif" >{{number_format($value['utilidad'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variacionx'] < 0) color:red; @endif" >{{($value['variacionx'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($regresion < 0) color:red; @endif" >{{number_format($regresion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['trabajadores'] < 0) color:red; @endif" >{{number_format($value['trabajadores'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($error < 0) color:red; @endif" >{{number_format($error,2)}}</td>

                    @else
                    
                    @php $regresion=($value['utilidad']*$formulab)+$formulaa; $error=$value['trabajadores']-$regresion;  $aerror+=$error; @endphp
                    <td style="font-size: 10px;text-align: right; @if($value['utilidad'] < 0) color:red; @endif" >{{number_format($value['utilidad'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['variacionx'] < 0) color:red; @endif" >{{($value['variacionx'].' %')}}</td>
                    <td style="font-size: 10px;text-align: right; @if($regresion < 0) color:red; @endif" >{{number_format($regresion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['trabajadores'] < 0) color:red; @endif" >{{number_format($value['trabajadores'],2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($error < 0) color:red; @endif" >{{number_format($error,2)}}</td>
                    
                    @endif
                  </tr>
                    @php $acostos+=$value['autilidad']; $agastos+=$value['vtrabajo']; $avcostos+=$value['vutilidades']; $avgastos+=$value['vutilidades']; $ultiing=$value['autilidad']; @endphp
                    @endforeach
                @php $index++;  @endphp
                @endforeach 
                @php $aerror=($aerror/$numanios)  @endphp
                <tr>
                        <td style="font-size: 10px;font-weight: bold;">TOTAL</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acostos < 0) color:red; @endif" >{{number_format($acostos,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if(($avcostos/$numanios) < 0) color:red; @endif" >{{number_format($avcostos/$numanios,2)}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($agastos < 0) color:red; @endif" ></td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($avgastos < 0) color:red; @endif" ></td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($aerror < 0) color:red; @endif" >{{number_format($aerror,2)}}</td>
                    </tr>
                  
                <tr> 
                  <td colspan="3">&nbsp;</td>  
                </tr>

  
           
             
              </tbody>
            </table>
          </div> 
            
          </div>
          <div class="table-responsive col-md-6">
          <div class="content">
            
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
              <tr class='well-dark'>
              <th width="10%" class="" style="text-align: center;" tabindex="0" aria-controls="example2" rowspan="1" colspan="6" aria-label="Codigo: activate to sort column ascending">PRONÓSTICO</th>
              </tr>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Periodo</th>
                  
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Utilidad del ejercicio</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total part trabaj</th>
                  <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ajuste</th>
                  
                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><strong></strong></td> 
                </tr>
                @php $anios=0;
                   
                  $proyecciongrupo=array();
                  for ($i=$fechault+1; $i <= ($fechault+4) ; $i++) { 
                    array_push($proyecciongrupo,(int)$i);
                } 
                //dd($proyecciongrupo);
               
              
                
                $cont = 0;  $esp = ""; $index=0; $ind=0;
                  
                
                @endphp
                @foreach($proyecciongrupo as $data) 
                @if ($cont==0)          
                  @php      
                  if($numanios!=0 && $avcostos!=0 ) {$inProyeccion=round($value['utilidad']+($value['utilidad']/($avcostos/$numanios)),2);} else {$inProyeccion=0;}
                  //$inProyeccion=($value['utilidad']+($value['cosutilidadtos']/($avcostos/$numanios)));
                  $egProyeccion=($formulab*$inProyeccion)+$formulaa;
                  $promedioe=$egProyeccion+$aerror;
                  @endphp
                @else
                @php  
                  if($numanios!=0 && $avcostos!=0 ) {$inProyeccion=round($inProyeccion+($inProyeccion/($avcostos/$numanios)),2);} else {$inProyeccion=0;}
                  //$inProyeccion=($inProyeccion+($inProyeccion/($avcostos/$numanios)));
                  $egProyeccion=($formulab*$inProyeccion)+$formulaa;
                  $promedioe=$egProyeccion+$aerror;

                  @endphp
                @endif
                  <tr>
                    <td>{{$data}}</td>
                    
                    @if ($anios==1)
                    <td style="font-size: 10px;text-align: right; @if($inProyeccion < 0) color:red; @endif" >{{number_format($inProyeccion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($egProyeccion < 0) color:red; @endif" >{{number_format($egProyeccion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($promedioe < 0) color:red; @endif" >{{number_format($promedioe,2)}}</td>
                    @else
                    
                    <td style="font-size: 10px;text-align: right; @if($inProyeccion < 0) color:red; @endif" >{{number_format($inProyeccion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($egProyeccion < 0) color:red; @endif" >{{number_format($egProyeccion,2)}}</td>
                    <td style="font-size: 10px;text-align: right; @if($promedioe < 0) color:red; @endif" >{{number_format($promedioe,2)}}</td>
                    
                    
                    @endif
                  </tr>
                    @php $cont++;  @endphp
                    @endforeach
               
                
                <tr> 
                  <td colspan="3">&nbsp;</td>  
                </tr>

  
           
             
              </tbody>
            </table>
          </div> 
       
        
        </div>

        <div class="table-responsive col-md-6">
          <div class="content">
                <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
          </div>
        

      
      <div class="table-responsive col-md-6">
          <div class="content">
                <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
          </div>
          </div>

      </div>
      @endif
      
  </div>
  </section>
  <form method="POST" id="print_reporte_master" action="{{ route('proyeccionfinanciera.resultado') }}" target="_blank">
    {{ csrf_field() }}
    <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
    <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
    <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
    <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="{{@$mostrar_detalles}}">
    <input type="hidden" name="exportar" id="exportar" value="0">
    <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>
  @php $acostosf=($acostos);  $agastosf=($agastos); @endphp
  <!-- /.content -->
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

    $(document).ready(function(){

      // $('#example2').DataTable({
      //   'paging'      : false,
      //   'lengthChange': false,
      //   'searching'   : false,
      //   'ordering'    : true,
      //   'info'        : false,
      //   'autoWidth'   : false
      // });

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    });

    $(function () {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY',
            //defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM',
            //defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
            //buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            //buscar();
        });
        $( "#btn_imprimir").click(function() {
          $("#filfecha_desde").val($("#fecha_desde").val());
          $("#filfecha_hasta").val($("#fecha_hasta").val());
          // $("#filcuentas_detalle").val($("#cuentas_detalle").val()); alert($("#cuentas_detalle").val());
          if($("#cuentas_detalle").prop("checked")){
            $("#filcuentas_detalle").val(1);
          }else{
            $("#filcuentas_detalle").val("");
          }
          // $("#filmostrar_detalles").val($("#mostrar_detalles").val());  
          $("#exportar").val(0);  
          $( "#print_reporte_master" ).submit();
        });
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function verifica_fechas(){ 
    var fecha_desde = Date($("#fecha_desde").val() + '01-01');
    var fecha_hasta = Date($("#fecha_hasta").val() + '-30');
    if(Date.parse(fecha_desde) > Date.parse(fecha_hasta)){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    } 
  }
  $( "#btn_exportar").click(function() { 
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        if($("#cuentas_detalle").prop("checked")){
          $("#filcuentas_detalle").val(1);
        }else{
          $("#filcuentas_detalle").val("");
        }
         //alert($("#cuentas_detalle").prop("checked"));
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());  
        $("#exportar").val(1);  
        $("#print_reporte_master" ).submit();
    });

    var myDoughnutChart = document.getElementById('donutChart').getContext('2d');

    var donutData= {
      labels: [
          'UTILIDAD DEL EJERCICIO', 
          '15% PARTICiPACION A TRABAJADORES', 
 
      ],
      datasets: [
        {
          data: [@php echo $acostosf; @endphp,@php echo $agastosf; @endphp],
          backgroundColor : ['#f56954', '#3c8dbc'],
        }
      ]
    }
var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    var donutChart = new Chart(myDoughnutChart, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions      
    })

    var dataFecha=[];    

    
    var areaChartData = {
      labels  : dataFecha,
      datasets: [
        {
          label               : 'Total',
          borderColor         : 'rgba(60,141,188,0.8)',         
          data                : [
            @foreach($liquidez as $valueD) 
              @foreach($valueD as $value) 
                
                {x:"{{$value['utilidad']}}",y:"{{$value['trabajadores']}}"},
              @endforeach
            @endforeach
          ],
        } 
      ]
    }

    var lineChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
       
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
                           type: 'linear', // MANDATORY TO SHOW YOUR POINTS! (THIS IS THE IMPORTANT BIT) 
                           display: true, // mandatory
                           scaleLabel: {
                                display: true, // mandatory
                                labelString: 'Your label' // optional 
                           },
                      }], 
        yAxes: [{ // and your y axis customization as you see fit...
                        display: true,
                        scaleLabel: {
                             display: true,
                             labelString: 'Count'
                        }
                    }],
      }
    }

     // window.myPie = new Chart(ctx2, myDoughnutChart);
      var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    //var lineChartOptions = jQuery.extend(true, {}, areaChartOptions)
    var lineChartData = jQuery.extend(true, {}, areaChartData)
 

    var lineChart = new Chart(lineChartCanvas, { 
      type: 'line',
      data: lineChartData, 
      options: lineChartOptions
    })




</script>
@endsection
