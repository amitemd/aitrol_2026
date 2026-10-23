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
@foreach($cuentas as $cuenta)
  @if($filcuenta!='')
  @php
    $registros = \Sis_medico\Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $filcuenta.'%')
    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
    ->whereBetween('c.fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
    ->where('c.id_empresa', $id_empresa)
    ->orderBy('fecha', 'ASC')
    ->get();
  @endphp
  @else
  @php
    $registros = \Sis_medico\Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $cuenta->id.'%')
    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
    ->whereBetween('c.fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
    ->where('c.id_empresa', $id_empresa)
    ->orderBy('fecha', 'ASC')
    ->get();
  @endphp
  @endif
  @if($registros != '[]')

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
            Cuenta: {{$cuenta->nombre}}<br/>
            Periodo: {{$fecha}} - {{$fecha_hasta}}<br/>
            <br/>
          </div>

          <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
            <thead>
              <tr>
                <th style="font-size: 16px;"><div class="details_title_border_left">FECHA</div></th>
                <th style="font-size: 16px"><div class="details_title">ASIENTO</div></th>
                <th style="font-size: 16px"><div class="details_title">CUENTA</div></th>
                <th style="font-size: 16px"><div class="details_title">DETALLE</div></th>
                <th style="font-size: 16px"><div class="details_title">DEBE</div></th>
                <th style="font-size: 16px"><div class="details_title">HABER</div></th>
                <th style="font-size: 16px"><div class="details_title_border_right">SALDO</div></th>
              </tr>
            </thead>
            <tbody id="detalle_productos" >

                @php
                 $saldo = 0;
                @endphp
                @foreach($registros as $value)
                    
                    @php
                      $saldo = ($saldo +$value->debe) - $value->haber;
                    @endphp
                    <tr class="round">
                      <td style="font-size: 16px">{{$value->fecha}}</td>
                      <td style="font-size: 16px">{{$value->cabecera->id}}</td>
                      <td style="font-size: 16px">{{$value->id_plan_cuenta}}</td>
                      <td style="font-size: 16px">Cuenta: {{$value->descripcion}} / Detalle: {{$value->cabecera->observacion}}</td>
                      <td style="font-size: 16px;text-align: right;">{{ number_format($value->debe,2) }}</td>
                      <td style="font-size: 16px;text-align: right;">{{ number_format($value->haber,2) }}</td>
                      <td style="font-size: 16px;text-align: right; @if($saldo < 0) color:red; @endif" >$ {{ number_format($saldo,2) }}</td>
                    </tr>
                    
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
  </div>
  <br>
  <br>
  @endif
@endforeach
