@extends('contable.balance_general.base')
@section('action-content')

@php
$exp_p= "";
$exp_r= "";
$cuenta_p = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PARTICIPACION_15');
if(!is_null($cuenta_p)){
$exp_p = explode('.', $cuenta_p->secuencial_mostrar);
}
$cuenta_r = \Sis_medico\Ct_Configuraciones::obtener_cuenta('IMPUESTO_RENTA_PAGAR_BALANCE');
if(!is_null($cuenta_r)){
$exp_r = explode('.', $cuenta_r->secuencial_mostrar);
}
@endphp
<style>
  /* #maincontainer {
    width:100%;
    height: 100%;
  }

  #leftcolumn {
    float:left;
    display:inline-block;
    width: 100px;
    height: 100%;
  }

  #contentwrapper {
    float:left;
    display:inline-block;
    width: -moz-calc(100% - 100px);
    width: -webkit-calc(100% - 100px);
    width: calc(100% - 100px);
    height: 100%;
  }

p.s1 {
  margin-left: 20px;
}


p.s2 {
  margin-left: 40px;
}
</style>

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style>
  .glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -webkit-animation: spin2 .7s infinite linear;
  }

  @-webkit-keyframes spin2 {
    from {
      -webkit-transform: rotate(0deg);
    }

    to {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spin {
    from {
      transform: scale(1) rotate(0deg);
    }

    to {
      transform: scale(1) rotate(360deg);
    }
  }

  input.error {
    border: 1px solid red !important;
  }

  select.error {
    border: 1px solid red !important;
  }

  label.error {

    color: red !important;

  }



  .table>tbody>tr>td,
  .table>tbody>tr>th {
    padding: 0.4%;
    font-size: 12px;
  }

  .icheckbox_flat-green.checked.disabled {
    background-position: -22px 0 !important;
    cursor: default;
  }

  */ p.s1 {
    margin-left: 10px;
    font-size: 14px;
    font-weight: bold;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 60px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }

  .hover :hover {
    color: green;
    cursor: pointer;
    font-weight: bold;
  }
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="../">Balance General</a></li>
    </ol>
  </nav>
  <div class="box">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <form method="POST" action="{{ route('libro_mayor.index') }}" style="display:none" target="_blank">
      {{ csrf_field() }}
      <input type="text" name="fecha" id="fechan">
      <input type="text" name="fecha_hasta" id="fecha_hastan">
      <input type="text" name="cuenta" id="cuentan">
      <input type="text" id="cuenta_hasta" name="cuenta_hasta">
      <input type="submit" id="guardar">
    </form>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('balance_general.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if(isset($fecha_desde) and $fecha_desde != '01/01/2010') {{@$fecha_desde}}  @endif" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{@$fecha_hasta}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label">Mostrar cuentas de detalle</label>
          <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value="1" @if(@$cuentas_detalle=="1" ) checked @endif>
        </div>
        {{-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(@$mostrar_detalles=="1") checked @endif>
        </div> --}}

        <div class="form-group col-md-9 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          <button type="button" class="btn btn-primary" id="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportarxml">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar XLS
          </button>
        </div>
      </form>
    </div>
    <input type="hidden" id="id_empresa" value="{{$empresa->id}}">
    <!-- /.box-body -->
    @if(count($activos)>0 or count($pasivos)>0 or count($patrimonio)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="izq col-md-1 clearfix">
                <dl class="izquierda">
                  @if(!is_null($empresa->logo))
                  <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                  @endif
                  {{-- <dd class="clearfix titulos">&nbsp; {{$empresa->nombrecomercial}}</dd>
                  <dd>&nbsp; {{$empresa->id}}</dd> --}}
                </dl>
              </div>
              <div class="col-md-3">
                <dl>
                  <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                  <dd>&nbsp; {{$empresa->id}}</dd>
                </dl>
              </div>
              <div class="col-md-4">
                <h4 style="text-align: center;">Estado de situaci&oacute;n financiera</h4>
                @if($periodo_desde != '01 de Enero de 2010')
                <h5 style="text-align: center;">Del {{$periodo_desde}} <br> al {{$periodo_hasta}}</h5>
                @else
                <h5 style="text-align: center;">AL {{$periodo_hasta}}</h5>
                @endif
              </div>
              <div class="col-md-4">
                {{-- <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                </dl> --}}
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <div class="row">
        <div class="table-responsive col-md-12">
          <div class="content ">
            <div class="izq content col-md-6">
              <table id="example2_a" class="izquierda table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark back'>
                    <th style="color: black;" width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                    <th style="color: black;" width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th>
                  </tr>
                </thead>
              <tbody id="activos">

                @php $valor100 = 0; $total_activos = 0; @endphp
                @foreach($activos as $value)
                @if(Auth::user()->id == '0922729587')
                @endif
                @php
                $cont = 0; $sangria = ""; $t = "";
                $cont = substr_count($value['cuenta'],".");
                if($cont==0){ $sangria = "class=s1"; $t = "class=t1"; $total_activos = $valor100 = $value['saldo']; }
                if($cont==1){ $sangria = "class=s2"; $t = "class=t2"; }
                if($cont==2){ $sangria = "class=s3"; $t = "class=t3"; }
                if($cont>=3){ $sangria = "class=s4"; $t = "class=t3"; }
                if($valor100 != 0){
                $porcent = number_format((($value['saldo']*100)/$valor100),2);
                }else{
                $porcent = number_format($valor100,2);
                }

                  @endphp
                  @if($value['saldo']!=0)
                  <tr>
                    <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif>
                      <p {{ $sangria }}>{{$value['cuenta']}}</p>
                    </td>
                    <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif style="@if($cont < 3) font-weight: bold; @endif">
                      <p {{ $sangria }}>{{$value['nombre']}}</p>
                    </td>
                    <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                      <p {{ $t }}>{{number_format($value['saldo'],2)}}</p>
                    </td>
                    <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                      <p {{ $t }}>{{ $porcent }} %</p>
                    </td>
                  </tr>
                  @endif
                  @endforeach
                  {{-- PASIVOS --}}
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="derecha content col-md-6">
              <table id="example2_pas" class="table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;margin-top: 0">
                <thead>
                  <tr class='well-dark'>
                    <th style="color: black;" width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                    <th style="color: black;" width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th>
                  </tr>
                </thead>
              <tbody id="pasivos">
                  @php
                  $valor100 = 0; $total_pasivos = 0;
                  @endphp
                  @foreach($pasivos as $value)
                    @php
                    $cont = 0; $sangria = ""; $t = "";
                    $cont = substr_count($value['cuenta'],".");
                    $entorno = "";
                    if($exp_p != ""){
                      foreach($exp_p as $key => $value_parti){
                        if($key == 0){
                          $entorno = $value_parti;
                        }
                        else
                        {
                          $entorno = $entorno.'.'.$value_parti;
                        }                        
                        if(trim($value['cuenta']) == $entorno ){
                          $value['saldo'] += $participacion;
                          break;
                        }
                      }
                    }else{
                      if(trim($value['cuenta'])=='2' ){$value['saldo'] += $participacion;}
                      if(trim($value['cuenta'])=='2.01' ){$value['saldo'] += $participacion;}
                      if(trim($value['cuenta'])=='2.01.07' ){$value['saldo'] += $participacion;}
                      if(trim($value['cuenta'])=='2.01.07.05' ){$value['saldo'] += $participacion;}
                      if(trim($value['cuenta'])=='2.01.07.05.01' ){$value['saldo'] += $participacion;}
                    }

                    $entorno = "";
                    if($exp_r != ""){
                      foreach($exp_r as $key => $value_parti){
                        if($key == 0){
                          $entorno = $value_parti;
                        }else{
                          $entorno = $entorno.'.'.$value_parti;
                        }
                        if(trim($value['cuenta']) == $entorno ){
                          $value['saldo'] += $impuesto_causado;
                          break;
                        }
                      }
                    }else{
                      if(trim($value['cuenta'])=='2' ){$value['saldo'] += $impuesto_causado;}
                      if(trim($value['cuenta'])=='2.01' ){$value['saldo'] += $impuesto_causado;}
                      if(trim($value['cuenta'])=='2.01.07' ){$value['saldo'] += $impuesto_causado;}
                      if(trim($value['cuenta'])=='2.01.07.01' ){$value['saldo'] += $impuesto_causado;}
                      if(trim($value['cuenta'])=='2.01.07.01.11' ){$value['saldo'] += $impuesto_causado;}
                    }
                    if($cont==0){ $sangria = "class=s1"; $t = "class=t1"; $valor100 = $total_pasivos = $value['saldo']; }
                    if($cont==1){ $sangria = "class=s2"; $t = "class=t2"; }
                    if($cont==2){ $sangria = "class=s3"; $t = "class=t3"; }
                    if($cont>=3){ $sangria = "class=s4"; $t = "class=t3"; }
                    if($valor100 != 0){
                      $porcent = number_format((($value['saldo']*100)/$valor100),2);
                    }else{
                      $porcent = number_format($valor100,2);
                    }
                    @endphp
                    @if($value['saldo']!=0)
                  <tr>
                    <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif>
                      <p {{ $sangria }}>{{$value['cuenta']}}</p>
                    </td>
                    <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif style="@if($cont < 3) font-weight: bold; @endif ">
                      <p {{ $sangria }}>{{$value['nombre']}}</p>
                    </td>
                    <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                      <p {{ $t }}>{{number_format($value['saldo'],2)}}</p>
                    </td>
                    <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                      <p {{ $t }}>{{ $porcent }} %</p>
                    </td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
              <table id="example2_pat" class="table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;margin-top: 0">
                <thead>
                  <tr class='well-dark'>
                    <th style="color: black;" width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                    <th style="color: black;" width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                    <th style="color: black;" width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th>
                  </tr>
                </thead>
              <tbody id="patrimonio">
                  {{-- PATRIMONIO --}}
                  @php
                  $valor100 = 0;
                  $total_patrimonio = 0;
                  $nsaldo = 0;
                  @endphp
                  @foreach($patrimonio as $value)
                  @php
                  $cont = 0;
                  $sangria = "";
                  $t = "";
                  $cont = substr_count($value['cuenta'],".");
                  if(trim($value['cuenta'])=='3'){
                    $value['saldo'] += $totpyg;
                  }
                  $allowed_servers = array(
                      'accounting.aitrol.com',
                      'cir.aitrol.com',
                      'drnet.aitrol.com',
                      'gastritech.aitrol.com',
                      'inttegra.aitrol.com',
                      'mdconsgroup.aitrol.com',
                      'humanlabsquito.aitrol.com',
                      'humanlabs.aitrol.com',
                      'inmodavid.aitrol.com',
                      'mercurypharm.aitrol.com',
                      '1gastromanta.aitrol.com',
                      'medifast.aitrol.com'
                  );
                  if (in_array($_SERVER['SERVER_NAME'], $allowed_servers)) {
                    if(trim($value['cuenta'])=='3.07'){
                        if($impuesto_causado==0)
                          $value['saldo']=$totpyg*-1;
                        else
                          $value['saldo'] += $totpyg;
                    }
                    if(trim($value['cuenta'])=='3.07.01' ){
                      if($totpyg > 0){
                        $value['saldo'] += $totpyg;
                      }
                      elseif($value['saldo'] <0){
                        $nsaldo=$value['saldo']; 
                        $value['saldo']=0; 
                      } 
                    }
                    if(trim($value['cuenta'])=='3.07.02' ){
                      if($impuesto_causado==0) 
                        $value['saldo']=$totpyg*-1; 
                      else{ 
                        if($totpyg < 0){ 
                          $value['saldo'] +=($nsaldo*(-1)) +($totpyg*(-1)); 
                        } 
                      } 
                    } 
                  } else{ 
                    if(trim($value['cuenta'])=='3.06' ){ 
                      $value['saldo'] +=$totpyg; 
                    } 
                    if(trim($value['cuenta'])=='3.06.01' ){ 
                      $value['saldo'] +=$totpyg; 
                    } 
                    if(trim($value['cuenta'])=='3.06.01.01' ){ 
                      $value['saldo'] +=$totpyg; 
                    } 
                    if(trim($value['cuenta'])=='3.06.01.01.01' ){ 
                      if($totpyg> 0){
                        $value['saldo'] += $totpyg;
                      }
                    } 
                    if(trim($value['cuenta'])=='3.06.01.01.02' ){ 
                      if($totpyg < 0)
                      { 
                        $value['saldo'] +=($nsaldo*(-1)) +($totpyg*(-1));
                      } 
                    } 
                  } 
                if($cont==0){ 
                  $sangria="class=s1"; 
                  $t="class=t1"; 
                  $total_patrimonio=$valor100=$value['saldo']; 
                } 
                if($cont==1){ 
                  $sangria="class=s2"; 
                  $t="class=t2"; 
                } 
                if($cont==2){$sangria="class=s3"; 
                  $t="class=t3";
                } 
                if($cont>=3){ 
                  $sangria = "class=s4"; 
                  $t = "class=t3"; 
                }
                      if($valor100 != 0){
                      $porcent = number_format((($value['saldo']*100)/$valor100),2);
                      }else{
                      $porcent = number_format($valor100,2);
                      }
                      @endphp
                      @if($value['saldo']!=0)
                      @if($value['cuenta'] == '3.06.02' or $value['cuenta'] == '3.07.02')
                      <tr>
                        <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif>
                          <p {{ $sangria }}>{{$value['cuenta']}}</p>
                        </td>
                        <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" @endif style="@if($cont < 3) font-weight: bold; @endif">
                          <p {{ $sangria }}>{{ $value['nombre']}}</p>
                        </td>
                        <td style="text-align: right; @if($value['saldo'] > 0) color:red; @endif">
                          <p {{ $t }}>{{number_format($value['saldo'],2)}}</p>
                        </td>
                        <td style="text-align: right; @if($value['saldo'] > 0) color:red; @endif">
                          <p {{ $t }}>{{ $porcent }} %</p>
                        </td>
                      </tr>
                      @else
                      @if(Auth::user()->id=='0921605895' && $value['cuenta'] == '3.07.02')
                      {
                      {{$value['cuenta']}}

                      }
                      @endif
                      <tr>
                        <td @if($cuentas_detalle==1 and $sangria=="class=s4" ) class="hover" onclick="redireccionar('{{$value['cuenta']}}');" @endif>
                          <p {{ $sangria }}>{{$value['cuenta']}}</p>
                        </td>
                        <td style="@if($cont < 3) font-weight: bold; @endif" @if($cuentas_detalle==1 and $sangria=="class=s4" ) onclick="redireccionar('{{$value['cuenta']}}');" @endif>
                          <p {{ $sangria }}>{{ $value['nombre']}}</p>
                        </td>
                        <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                          <p {{ $t }}>{{number_format($value['saldo'],2)}}</p>
                        </td>
                        <td style="text-align: right; @if($value['saldo'] < 0) color:red; @endif">
                          <p {{ $t }}>{{ $porcent }} %</p>
                        </td>
                      </tr>
                      @endif
                      @endif
                      @endforeach
                </tbody>
              </table>
            </div>
            @php $total = $total_pasivos + $total_patrimonio; @endphp
            <div class=" col-md-12">
              <table id="example2" class="table table-condensed dataTable col-md-12" role="grid" aria-describedby="example2_info" style="font-size: 10px;margin-top: 0">
                <tbody>
                  <tr class='well-dark'>
                    <td class="col-md-4">
                      <p style="font-weight: bold; text-align: center; color: black;">TOTAL ACTIVO </p>
                    </td>
                    <td class="col-md-2" style="text-align: right; font-weight: bold; color: black; @if($total_activos < 0) color:red; @endif">
                      <p {{ $t }}>{{number_format($total_activos,2)}}</p>
                    </td>
                    <td class="col-md-4">
                      <p style="font-weight: bold; text-align: center;color: black;">TOTAL PASIVO + PATRIMONIO </p>
                    </td>
                    <td class="col-md-2" style="text-align: right; font-weight: bold; color: black; @if($total < 0) color:red; @endif">
                      <p {{ $t }}>{{number_format($total,2)}}</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- @if($empresa->id=='0993069299001')
            <div class="col-md-12" style="margin-top:10%">
              &nbsp;
            </div>
            <div class="col-md-12">
              <div class="row">
                <div class=" col-md-6" >
                  <div class="row">
                    <label class="col-md-12">Representante Legal</label>


                    <label class="col-md-12">Ing. Juan Manuel de León Méndez</label>


                    <label class="col-md-12">C.I. 0930790720</label>
                  </div>
                </div>

                <div class="col-md-6" >
                  <div class="row" style="float:right">
                    <label class="col-md-12">Contadora General</label> <br>

                    <label class="col-md-12">C.P.A Paola Villon Leoro</label> <br>


                    <label class="col-md-12">C.I. 0926066317</label> <br>

                    <label class="col-md-12">Registro # 1006-12-1156487</label> <br>
                  </div>
                </div>
              </div>
            </div>
            @endif -->

            <div class="col-md-12" style="margin-top:10%">
              &nbsp;
            </div>
            <div class="col-md-12">
              <div class="row">
                <div class=" col-md-6">
                  @if(!is_null($empresa->id_representante))
                  @if($empresa->persona_nat_jur == '2')
                  <div class="row">
                    <label class="col-md-12">{{trans('contableM.RepresentanteLegal')}}</label><br />
                    @if(is_null($empresa->tipo_representante))
                    <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}</label><br />
                    <label class="col-md-12">C.I. {{$empresa->id_representante}}</label><br />
                    @else
                    <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} <br> EN REPRESENTACION DE {{$empresa->empresa_representante}}
                    </label><br />
                    <label class="col-md-12">C.I. {{$empresa->id_representante}}</label>

                    @endif
                  </div>
                  @endif
                  @endif
                </div>
                <div class="col-md-6">
                  <div class="row" style="float:right">
                    @if(!is_null($empresa->id_contador))
                    <label class="col-md-12">{{trans('contableM.ContadoraGeneral')}}</label> <br>
                    <label class="col-md-12">{{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}</label> <br>
                    <label class="col-md-12">C.I. {{$empresa->id_contador}}</label> <br>
                    <?php
                    if ($empresa->num_registro_contador != '') {
                    ?>
                      <label class="col-md-12">Registro # {{$empresa->num_registro_contador}}</label> <br>
                    <?php
                    }
                    ?>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
</section>
<form method="POST" id="print_reporte_master" action="{{ route('balance_general.show') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
  <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
  <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="{{@$mostrar_detalles}}">
  <input type="hidden" name="datosBalanceA" id="datosBalanceA">
  <input type="hidden" name="datosBalanceP" id="datosBalanceP">
  <input type="hidden" name="datosBalancePat" id="datosBalancePat">
  <input type="hidden" name="funcionN" id="funcionN" value="1">
  <input type="hidden" name="exportar" id="exportar" value="0">
  <input type="hidden" name="imprimir" id="imprimir" value="">
</form>
<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });


  $('#cuenta').on('select2:select', function(e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    //console.log("si es valida")
    let idEmpresa = document.getElementById('id_empresa').value;
    if (idEmpresa != '0992704152001') {
      console.log("entra al if")
      imprimirElemento();

    } else {
      console.log("No entra al if")
      $("#filfecha_desde").val($("#fecha_desde").val());
      $("#filfecha_hasta").val($("#fecha_hasta").val());
      $("#filcuentas_detalle").val($("#cuentas_detalle").val());
      $("#filmostrar_detalles").val($("#mostrar_detalles").val());
      $("#print_reporte_master").submit();
    }


    // console.log($("#fecha_hasta").val())
    // console.log($("#fecha_desde").val())
    //
  });

  $("#btn_exportar").click(function() {

    let array_patrimonio = [];
    let array_activos = [];
    let array_pasivos = [];

    $("#activos tr").each(function(index, fila) {
      let cuenta = $(fila).first().find("td").eq(0).find("p").text();
      let nombreCuenta = $(fila).first().find("td").eq(1).find("p").text();
      let cuentaUno = $(fila).first().find("td").eq(2).find("p").text();
      let porcentaje = $(fila).first().find("td").eq(3).find("p").text();
      array_activos.push({
        cuenta: cuenta,
        nombre: nombreCuenta,
        saldo: cuentaUno
      })
    });
    $("#pasivos tr").each(function(index, fila) {
      let cuenta = $(fila).first().find("td").eq(0).find("p").text();
      let nombreCuenta = $(fila).first().find("td").eq(1).find("p").text();
      let cuentaUno = $(fila).first().find("td").eq(2).find("p").text();
      let porcentaje = $(fila).first().find("td").eq(3).find("p").text();

      array_pasivos.push({
        cuenta: cuenta,
        nombre: nombreCuenta,
        saldo: cuentaUno
      })
    });

    $("#patrimonio tr").each(function(index, fila) {
      let cuenta = $(fila).first().find("td").eq(0).find("p").text();
      let nombreCuenta = $(fila).first().find("td").eq(1).find("p").text();
      let cuentaUno = $(fila).first().find("td").eq(2).find("p").text();
      let porcentaje = $(fila).first().find("td").eq(3).find("p").text();

      array_patrimonio.push({
        cuenta: cuenta,
        nombre: nombreCuenta,
        saldo: cuentaUno
      })
    });

    $("#datosBalanceA").val(JSON.stringify(array_activos));
    $("#datosBalanceP").val(JSON.stringify(array_pasivos));
    $("#datosBalancePat").val(JSON.stringify(array_patrimonio));
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    if ($("#cuentas_detalle").prop("checked")) {
      $("#filcuentas_detalle").val(1);
    } else {
      $("#filcuentas_detalle").val("");
    }
    //alert($("#cuentas_detalle").prop("checked"));
    $("#filmostrar_detalles").val($("#mostrar_detalles").val());
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function() {

    tinymce.init({
      selector: '#hc'
    });

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });
    $('#btn_exportarxml').click(function() {
      exportTableToExcel('example2_a', 'Balance financiero Activos');
      exportTableToExcel('example2_pas', 'Balance financiero Pasivos');
      exportTableToExcel('example2_pat', 'Balance financiero Patrimonio');
    });

  });

  $(function() {
    $('#fecha_desde').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

    //alert('{{$fecha_hasta}}');

  });

  function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    // Specify file name
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    // Create download link element
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
      var blob = new Blob(['ufeff', tableHTML], {
        type: dataType
      });
      navigator.msSaveOrOpenBlob(blob, filename);
    } else {
      // Create a link to the file
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
      // Setting the file name
      downloadLink.download = filename;
      //triggering the function
      downloadLink.click();
    }
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/

  //imprimir
  function imprimirElemento() {
    var ventana = window.open('', 'PRINT');
    var elemento = document.getElementById('example2_wrapper').innerHTML;
    //http://www.ieced.siaam.ec/sis_medico/public/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css

    ventana.document.write('<html><head><title>' + document.title + '</title>');
    ventana.document.write('<link rel="stylesheet" href="http://www.ieced.siaam.ec/sis_medico/public/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css">'); //Aquí agregué la hoja de estilos
    ventana.document.write('<link href="http://192.168.75.51/sis_medico/public/css/app-template.css" rel="stylesheet">');
    ventana.document.write('</head><body >');
    ventana.document.write(estilos()); //Aquí agregué la hoja de estilos
    ventana.document.write(elemento);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    ventana.onload = function() {
      ventana.print();
      ventana.close();
    };
    return true;
  }

  function estilos() {
    let estilos_tablas = `
      <style>

      .row{
        width:80%;
        float:left;
      }
      .col-md-8 {
        width:66.66666667%,
        float: left;

      }

      .col-md-5 {
        width:41.66666667%,
        float: left;
      }

      .col-md-6 {
        width: 50%;
        float: left;
      }
      .col-md-12 {
        width: 100%;
        position: relative;
      }
      .well-dark {
          background-color: #aaa;
          width: 100%;
          color: #000000;
      }
      .table {
          width: 100%;
      }
      table.dataTable {
          clear: both;
          margin-top: 6px !important;
          margin-bottom: 6px !important;
          max-width: none !important;
      }
    table {
        border-spacing: 0;
        border-collapse: collapse;
    }
    p.s2 {
        margin-left: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    p.s3 {
        margin-left: 30px;
        font-size: 10px;
        font-weight: bold;
    }
    p.t3 {
        font-size: 10px;
    }
    p.s4 {
      margin-left: 60px;
      font-size: 10px;
    }
    .derecha{
      float: right;
    }
    .izquierda{
      padding-right:10px;
      float: left;

    }
    .izq{
      width:45%;
    }
    th{
      color: #010101;
    }
    .titulos{
      margin-top:50px;
    }

      </style>`;
    return estilos_tablas;
  }

  function redireccionar(campo) {
    //console.log(campo);
    var fechaDesde = document.querySelector("#fecha_desde").value;
    var fechaHasta = document.querySelector('#fecha_hasta').value;
    document.getElementById('fechan').value = fechaDesde;
    document.getElementById('fecha_hastan').value = fechaHasta;
    document.getElementById('cuentan').value = campo;
    document.getElementById('cuenta_hasta').value = campo;
    $("#guardar").click();
  }
</script>
@endsection