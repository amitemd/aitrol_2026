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
      padding: 4px;


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
  </style>
  {{--@if(count($balance)>0)--}}

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
            Periodo: {{$fecha_desde}} a {{$fecha_hasta}}<br/>
            <br/>
          </div>

          <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
            <thead>
              <tr>
                <th style="font-size: 16px"><div class="details_title_border_left">CUENTA</div></th>
                <th style="font-size: 16px"><div class="details_title">DETALLE</div></th>
                <th style="font-size: 16px"><div class="details_title_border_right">SALDO</div></th>
              </tr>
            </thead>
            <tbody id="detalle_productos" >
            @php
                $band = 0;      $acum_mas = 0;      $acum_menos = 0;
            @endphp 
            @if(isset($data))
            @foreach($data as $value)
                @php
                   
                    if($value['signo']=='+'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp
                @if($value['signo']=='-' and $band==0)
                    @php
                        $band = 1;
                    @endphp
                    <tr> 
                        <td colspan="2" style="font-size: 16px;font-weight: bold">TOTAL</td>
                        <td style="font-size: 16px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                @endif
                  <tr>
                    <td style="font-size: 16px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 16px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 16px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>
                @endforeach
                @endif
                <tr> 
                    <td colspan="2" style="font-size: 16px;font-weight: bold">TOTAL</td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr> 
                    <td colspan="2" style="font-size: 16px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
  </div>
  <br>
  <br>
  {{--@endif--}}