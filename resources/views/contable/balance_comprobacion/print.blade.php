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
      padding-bottom: 15px;

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
      padding: 1px;
      padding-left:1px;
      font-family: 'Arial';
      font-size: 0.7em;
      font-weight: normal;
    }

    .details_title_border_right{
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color:#FFF;
      padding: 1px;
      padding-right:1px;
      font-family: 'Arial';
      font-size: 0.7em;
      font-weight: normal;
    }

    .details_title{
      background: #3d7ba8;
      color:#FFF;      
      padding: 1px;
      font-family: 'Arial';
      font-size: 0.7em;
      font-weight: normal;
    }
  </style>
  @if(count($balance)>0)

  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <div style="text-align: center">
              <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa">
            </div>
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$empresa->id}}<br/>
            Nombre Comercial: {{$empresa->nombrecomercial}}<br/>
            Teléfono: {{$empresa->telefono1}}<br/>
            Dir.Matriz: {{$empresa->direccion}}<br/>
            <br/>
          </div>
        </div>
        <!-- /.box -->
      </div>
    </div> 
      <div class="row">
        <div class="table-responsive col-md-12">

          <div style="text-align: left; font-size:0.8em">
            Periodo: {{$fecha_d}} a {{$fecha_h}}<br/>
            <br/>
          </div>

          <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
            <thead> 
              <tr class='well-dark'> 
                <th width="10%" ><div class="details_title_border_left">Cuenta</div></th>
                <th width="40%" ><div class="details_title">Detalle</div></th>
                <th width="10%" style="text-align: center"><div class="details_title">Deudor</div></th>
                <th width="10%" style="text-align: center"><div class="details_title">Acreedor</div></th>
                <th width="10%" style="text-align: center"><div class="details_title">Deudor</div></th>
                <th width="10%" style="text-align: center"><div class="details_title">Acreedor</div></th>
                <th width="10%" style="text-align: center"><div class="details_title">Deudor</div></th>
                <th width="10%" style="text-align: center"><div class="details_title_border_right">Acreedor</div></th>
              </tr>
            </thead>
            <tbody id="detalle_productos" >
               
                @php
                 $saldo = 0; $i=0;
                @endphp
                @foreach($balance as $value) 
                @php 
                  if(trim($value['cuenta'])=='2.01.07.05.01' ){$value['haber'] += $participacion; $balance_ant[$i]['haber'] += $participacionant;}
                  // if(trim($value['cuenta'])=='2.01.07.05.01' ){}

                  $acum_debe = 0;   $acum_haber = 0;    $saldo_debe = 0;   $saldo_haber = 0;    $saldo = 0;
                  $acum_debe = $balance_ant[$i]['debe'] + $value['debe'];
                  $acum_haber = $balance_ant[$i]['haber'] + $value['haber'];
                  $saldo = $acum_debe - $acum_haber;
                  if($saldo > 0){
                    $saldo_debe = $saldo;
                    $saldo_haber = 0;
                  }else{
                    $saldo_debe = 0;
                    $saldo_haber = (-1)*$saldo;
                  }
                @endphp
                  <tr class="round">
                    <td style="font-size: 12px">{{$value['cuenta']}}</td> 
                    <td style="font-size: 12px;">{{$value['nombre']}} </td>
                    
                    <td style="font-size: 11px;text-align: right; @if($value['debe'] < 0) color:red; @endif" ><p>{{number_format($balance_ant[$i]['debe'],2)}}</p></td>
                    <td style="font-size: 11px;text-align: right; @if($value['haber'] < 0) color:red; @endif" ><p>{{number_format($balance_ant[$i]['haber'],2)}}</p></td>

                    <td style="font-size: 11px;text-align: right; @if($value['debe'] < 0) color:red; @endif" ><p>{{number_format($value['debe'],2)}}</p></td>
                    <td style="font-size: 11px;text-align: right; @if($value['haber'] < 0) color:red; @endif" ><p>{{number_format($value['haber'],2)}}</p></td>
                    
                    <td style="font-size: 11px;text-align: right; @if($value['haber'] < 0) color:red; @endif" ><p>{{number_format($saldo_debe,2)}}</p></td>
                    <td style="font-size: 11px;text-align: right; @if($value['haber'] < 0) color:red; @endif" ><p>{{number_format($saldo_haber,2)}}</p></td>
                  </tr>
                  @php
                    $i++;
                  @endphp
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
  </div>
  <br>
  <br>
  @endif