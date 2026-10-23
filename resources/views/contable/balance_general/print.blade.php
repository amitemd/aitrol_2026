<style>


    #page_pdf{
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      margin-bottom: 10px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 1px;
      padding: 1px;
    }

    #detalle_totales span{
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura{
      width: 25%;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 31%;
    }

    .info_cliente{
      width: 69%;
    }

    .textright{
      padding-left: 3;
    }


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

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    table{
       border-collapse: collapse;
       font-size: 12pt;
       font-family: 'arial';
       width: 100%;
    }


    table tr:nth-child(odd){
       background: #FFF;
    }

    table td{
      padding: 1px;
    }

    table th{
       text-align: left;
       color:#3d7ba8;
       font-size: 1em;
    }

    .datos_cliente
    {
      font-size: 0.8em;
    }

    .datos_cliente label{
       width: 75px;
       display: inline-block;
    }

    .lab{
      font-size: 18px;
      font-family: 'arial';
    }

    *{
      font-family:'Arial' !important;
    }

    .mLabel{
      width:20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }
    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    .totals_wrapper{
      width:100%;
    }
    .totals_label{
      display: inline-block;
      vertical-align: top;
      width:85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }
    .totals_value{
      display: inline-block;
      vertical-align: top;
      width:14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }
    .totals_separator{
      width:100%;
      height:1px;
      clear: both;
    }

    .separator{
      width:100%;
      height:60px;
      clear: both;
    }

    .details_title_border_left{
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-left:10px;
    }

    .details_title_border_right{
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-right:3px;
    }

    .details_title{
      background: #3d7ba8;
      color:#FFF;
      padding: 10px;
    }

    p.s1 {
      margin-left:  10px;
      font-size:    14px;
    }
    p.s2 {
      margin-left:  20px;
      font-size:    12px;
    }
    p.s3 {
      margin-left:  30px;
      font-size:    10px;
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
  </style>
  @if(count($activos)>0)

  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <div style="text-align: center">
            @if(!is_null($empresa->logo))
              <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa">
            @endif
            </div>
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$empresa->id}}<br/>
            {{trans('contableM.nombrecomercial')}}: {{$empresa->nombrecomercial}}<br/>
            {{-- Teléfono: {{$empresa->telefono1}}<br/>
            {{trans('contableM.dirmatr')}}: {{$empresa->direccion}}<br/> --}}
            <br>
            <span style="font-size: 1em; font-weight: bold;">{{trans('contableM.ESTADODESITUACIONFINANCIERA')}}</span>
            <br/>
            <br/>
          </div>
        </div>
        <!-- /.box -->
      </div>
    </div>
      <div class="row">
        <div class="table-responsive col-md-12">

          <div style="text-align: left; font-size:0.8em">
            Periodo de {{$periodo_desde}} a {{$periodo_hasta}}
            <br/>
          </div>

          <table id="factura_detalle" border="0" cellpadding="0" width="50%">
            <thead>
              <tr>
                <th style="font-size: 12px"><div class="details_title_border_left">{{trans('contableM.Cuenta')}}</div></th>
                <th style="font-size: 12px"><div class="details_title">{{trans('contableM.detalle')}}</div></th>
                <th style="font-size: 12px"><div class="details_title_border_right">{{trans('contableM.saldo')}}</div></th>
              </tr>
            </thead>
            <tbody id="detalle_productos" >

                @php
                 $saldo = 0;
                @endphp
                @foreach($activos as $value)
                @php
                    $cont = 0;  $esp = "";
                    $cont = substr_count($value->cuenta,".");
                    if($cont > 3){$cont = 3;}
                    if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                    $sangria = "";  $t = "";
                    $cont = substr_count($value->cuenta,".");
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }

                @endphp
                @if($value->saldo!=0)
                  <tr class="round">
                  <td ><p {{ $sangria }}>{{$value->cuenta}}</p></td>
                  <td ><p {{ $sangria }}>{{ $value->nombre}} </p></td>
                  <td style="text-align: right; @if($value->saldo < 0) text-color:red; @endif" ><p {{ $t }}>{{number_format($value->saldo,2)}}</p></td>
                  </tr>
                @endif
                @endforeach
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                @foreach($pasivos as $value)
                @php
                    $cont = 0;  $esp = "";
                    $cont = substr_count($value->cuenta,".");
                    $sangria = "";  $t = "";
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                    // if(trim($value->cuenta)=='3.07'){$value->saldo = $totpyg;}
                    if(trim($value->cuenta)=='2' ){$value->saldo += $participacion;}
                    if(trim($value->cuenta)=='2.01' ){$value->saldo += $participacion;}
                    if(trim($value->cuenta)=='2.01.07' ){$value->saldo += $participacion;}
                    if(trim($value->cuenta)=='2.01.07.05' ){$value->saldo += $participacion;}
                    if(trim($value->cuenta)=='2.01.07.05.01' ){$value->saldo += $participacion;}
                    if(trim($value->cuenta)=='2.01.07.05.01.11' ){$value->saldo += $participacion;}

                @endphp
                @if($value->saldo>0)
                  <tr class="round">
                  <td ><p {{ $sangria }}>{{$value->cuenta}}</p></td>
                  <td ><p {{ $sangria }}>{{ $value->nombre}} </p></td>
                  <td style="text-align: right; @if($value->saldo < 0) text-color:red; @endif" ><p {{ $t }}>{{number_format($value->saldo,2)}}</p></td>
                  </tr>
                @endif
                @endforeach
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                @foreach($patrimonio as $value)
                @php
                    $cont = 0;  $esp = "";
                    // $cont = substr_count($value->cuenta,".");
                    // if($cont > 3){$cont = 3;}
                    // if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                    $sangria = "";  $t = "";
                    $cont = substr_count($value->cuenta,".");
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                    if(trim($value->cuenta)=='3.07'){$value->saldo = $totpyg;}
                    if(trim($value->cuenta)=='3.07.01' ){$value->saldo = $totpyg;}
                    if(trim($value->cuenta)=='3.07.02' ){$value->saldo = $totpyg;}
                @endphp
                  @if($value->saldo!=0)
                  <tr class="round">
                    <td ><p {{ $sangria }}>{{$value->cuenta}}</p></td>
                  <td style="@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value->nombre}}</p> </td>
                  <td style="text-align: right; @if($value->saldo < 0) text-color:red; @endif" ><p {{ $t }}>@if($value->cuenta != '3' ){{number_format($value->saldo,2)}}@else{{number_format(($value->saldo+$totpyg),2)}}@endif</p></td>
                  </tr>
                  @endif
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
  </div>
  <br>
  <br>
  @endif
