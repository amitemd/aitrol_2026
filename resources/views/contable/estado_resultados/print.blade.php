<style>
  #page_pdf {
    width: 95%;
    margin: 15px auto 10px auto;
  }

  #factura_head,
  #factura_cliente,
  #factura_detalle {
    width: 100%;
    max-height: auto;
    margin-bottom: 10px;
  }

  #detalle_productos tr:nth-child(even) {
    background: #ededed;
    border-radius: 10px;
    border: 1px solid #3d7ba8;
    overflow: hidden;
    padding-bottom: 15px;

  }

  #detalle_totales span {
    font-family: 'BrixSansBlack';
    text-align: right;
  }

  @page {
    margin: 10px 70px;
  }

  /* #footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 90px; } */

  .logo_factura {
    width: 25%;
  }

  .info_empresa {
    width: 50%;
    text-align: center;
  }

  .info_factura {
    width: 31%;
  }

  .info_cliente {
    width: 69%;
  }

  .textright {
    padding-left: 3;
  }


  .h3 {
    font-family: 'BrixSansBlack';
    font-size: 12pt;
    display: block;
    background: #3d7ba8;
    color: #FFF;
    text-align: center;
    padding: 3px;
    margin-bottom: 5px;
  }

  .round {
    border-radius: 10px;
    border: 1px solid #3d7ba8;
    overflow: hidden;
    padding-bottom: 15px;
  }

  table {
    border-collapse: collapse;
    font-size: 12pt;
    font-family: 'arial';
    width: 100%;
  }


  table tr:nth-child(odd) {
    background: #FFF;
  }

  table td {
    padding: 4px;


  }

  table th {
    text-align: left;
    color: #3d7ba8;
    font-size: 1em;
  }

  .datos_cliente {
    font-size: 0.8em;
  }

  .datos_cliente label {
    width: 75px;
    display: inline-block;
  }

  .lab {
    font-size: 18px;
    font-family: 'arial';
  }

  * {
    font-family: 'Arial' !important;
  }

  .mLabel {
    width: 20%;
    display: inline-block;
    vertical-align: top;
    font-weight: bold;
    padding-left: 15px;
    font-size: 0.9em;

  }

  td,
  th {
    font-size: 16px;
  }

  .mValue {
    width: 79%;
    display: inline-block;
    vertical-align: top;
    padding-left: 7px;
    font-size: 0.9em;
  }

  .totals_wrapper {
    width: 100%;
  }

  .totals_label {
    display: inline-block;
    vertical-align: top;
    width: 85%;
    text-align: right;
    font-size: 0.7em;
    font-weight: bold;
    font-family: 'Arial';
  }

  .totals_value {
    display: inline-block;
    vertical-align: top;
    width: 14%;
    text-align: right;
    font-size: 0.7em;
    font-weight: normal;
    font-family: 'Arial';
  }

  .totals_separator {
    width: 100%;
    height: 1px;
    clear: both;
  }

  .separator {
    width: 100%;
    height: 60px;
    clear: both;
  }

  .details_title_border_left {
    background: #3d7ba8;
    border-top-left-radius: 10px;
    color: #FFF;
    padding: 10px;
    padding-left: 10px;
  }

  .details_title_border_right {
    background: #3d7ba8;
    border-top-right-radius: 10px;
    color: #FFF;
    padding: 10px;
    padding-right: 3px;
  }

  .details_title {
    background: #3d7ba8;
    color: #FFF;
    padding: 10px;
  }

  .saltopagina {
    page-break-after: always;
  }
</style>
{{--@if(count($balance)>0)--}}

<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <div style="text-align: center">
            @if(!is_null($empresa->logo))
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" alt="Logo Image" style="width:230px;height:100px" id="logo_empresa">
            @endif
          </div>
        </div>
        <div style="text-align: center; font-size:0.8em">
          R.U.C.: {{$empresa->id}}<br />
          Nombre Comercial: {{$empresa->nombrecomercial}}<br />

          <span style="font-size: 1em; font-weight: bold;">ESTADO DE RESULTADOS</span>

        </div>
      </div>
      <!-- /.box -->
    </div>
  </div>
  <div class="row">
    <div class="table-responsive col-md-12">

      <div style="text-align: left; font-size:0.8em">
        Periodo: {{$fecha_desde}} a {{$fecha_hasta}}<br />
        <br />
      </div>

      <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px">
              <div class="details_title_border_left">CUENTA</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title">DETALLE</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title_border_right">SALDO</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_productos">
          {{--INGRESOS--}}
          @php
          //dd($ingresos);
          $saldo = 0; $toting = 0;
          @endphp
          @foreach($ingresos as $value)
          @php
          $cont = 0; $esp = "";
          $cont = substr_count($value['cuenta'],".");
          if($cont > 3){$cont = 3;}
          if($cont<>0){$esp = str_repeat("&nbsp;",($cont*2)); }
            //if($value['cuenta']=="4") {$toting = $value['saldo'];}
            @endphp;
            @if($value['saldo'] <> 0)
              <tr class="round">
                <td style="font-size: 16px">{{$value['cuenta']}}</td>
                <td style="font-size: 16px;@if($cont < 3) font-weight: bold; @endif">{{ $esp . $value['nombre']}} </td>
                <td style="font-size: 16px;text-align: right; @if($value['saldo'] < 0) color:red; @endif">$ {{ number_format($value['saldo'],2) }}</td>
              </tr>
              @endif
              @endforeach
              {{--COSTOS--}}
              @php
              $saldo = 0; $totgas = 0;
              @endphp
              @foreach($costos as $value)
              @php
              $cont = 0; $esp = "";
              $cont = substr_count($value['cuenta'],".");
              if($cont > 3){$cont = 3;}
              if($cont<>0){ $esp = str_repeat("&nbsp;",($cont*2)); }
                //if($value['cuenta']=="5") {$totgas = $value['saldo'];}
                @endphp;
                @if($value['saldo']>0)
                <tr class="round">
                  <td style="font-size: 16px">{{$value['cuenta']}}</td>
                  <td style="font-size: 16px;@if($cont < 3) font-weight: bold; @endif">{{ $esp . $value['nombre']}} </td>
                  <td style="font-size: 16px;text-align: right; @if($value['saldo'] < 0) color:red; @endif">$ {{ number_format($value['saldo'],2) }}</td>
                </tr>
                @endif
                @endforeach
                @php
                $utilidad = ($toting-$totgas)
                @endphp
                {{-- GASTOS --}}
                @php
                $saldo = 0; $totgast = 0;
                @endphp
                @foreach($gastos as $value)
                @php
                $cont = 0; $esp = "";
                $cont = substr_count($value['cuenta'],".");
                if($cont > 3){$cont = 3;}
                if($cont<>0){ $esp = str_repeat("&nbsp;",($cont*2)); }
                  //if($value['cuenta']=="6") {$totgast = $value['saldo'];}
                  @endphp;
                  @if($value['saldo']>0)
                  <tr class="round">
                    <td style="font-size: 16px">{{$value['cuenta']}}</td>
                    <td style="font-size: 16px;@if($cont < 3) font-weight: bold; @endif">{{ $esp . $value['nombre']}} </td>
                    <td style="font-size: 16px;text-align: right; @if($value['saldo'] < 0) color:red; @endif">$ {{ number_format($value['saldo'],2) }}</td>
                  </tr>
                  @endif
                  @endforeach
                  @php
                  @endphp
                  <tr>
                    <td style="font-size: 16px;" colspan="2"><strong>UTILIDAD / PERDIDA DEL PERIODO:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($totpyg < 0) color:red; @endif">{{number_format($totpyg,2)}}</td>
                  </tr>
                  <tr>

                    <td style="font-size: 16px;" colspan="2"><strong>15% PARTICIPACION A TRABAJADORES:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($totpyg < 0) color:red; @endif">{{number_format($participacion,2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 16px;" colspan="2"><strong>UTILIDAD CONTABLE:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($totpyg < 0) color:red; @endif">{{number_format($totalf,2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 16px;" colspan="2"><strong>UTILIDAD GRAVABLE:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($total_gravable < 0) color:red; @endif">{{number_format($total_gravable,2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 16px;" colspan="2"><strong>IMPUESTO GENERADO:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($renta_acumulada < 0) color:red; @endif">{{number_format($renta_acumulada,2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 16px;" colspan="2"><strong>UTILIDAD NETA:</strong></td>
                    <td style="font-size: 16px;text-align: right; font-weight: bold; @if($totpyg < 0) color:red; @endif">{{number_format(($totalf -$renta_acumulada),2)}}</td>
                  </tr>
        </tbody>
      </table>
    </div>
    <br><br>
    <!-- <div style="width: 100% !important;">
      <div style="width: 48% !important;">
        @if(!is_null($empresa->id_representante))
        @if($empresa->persona_nat_jur == '2')
        <div style="float:left">
          <label><b>{{trans('contableM.RepresentanteLegal')}}</b></label><br>
          @if(is_null($empresa->tipo_representante))
          <label>{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}
          </label><br>
          <label>C.I. {{$empresa->id_representante}}</label><br>
          @else
          <label>{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} <br> EN REPRESENTACION DE {{$empresa->empresa_representante}}
          </label>
          @endif
        </div>
        @endif
        @endif
      </div>

      <div style="width: 48% !important;">
        <div style="float:right">
          @if(!is_null($empresa->id_contador))
          <label><b>{{trans('contableM.ContadoraGeneral')}}</b></label> <br>

          <label>{{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}</label> <br>


          <label>C.I. {{$empresa->id_contador}}</label> <br>

          <label>Registro # {{$empresa->num_registro_contador}}</label> <br>
          @endif
        </div>
      </div>
    </div> -->
    <div style="width: 100% !important;">
      <table width="100%">
        <thead>
          <tr>
            <th width="50%">@if(!is_null($empresa->id_representante)) @if($empresa->persona_nat_jur == '2')<b>{{trans('contableM.RepresentanteLegal')}}@endif @endif</b>
            </th>
            <th width="50%" style="text-align: right;">@if(!is_null($empresa->id_contador))<b>{{trans('contableM.ContadoraGeneral')}}</b>@endif
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              @if(!is_null($empresa->id_representante))
              @if($empresa->persona_nat_jur == '2')
              @if(is_null($empresa->tipo_representante))
              {{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}
              <br>
              C.I. {{$empresa->id_representante}}<br>
              @else
              {{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} <br> EN REPRESENTACION DE {{$empresa->empresa_representante}}

              @endif
              @endif
              @endif
            </td>
            <td style="text-align: right;">
              @if(!is_null($empresa->id_contador))
              @if(!is_null($empresa->id_contador))
              {{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}} <br>
              C.I. {{$empresa->id_contador}} <br>
              @if ($empresa->num_registro_contador != '')
              Registro # {{$empresa->num_registro_contador}} <br>
              @endif
              @endif
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<br>
<br>
{{--@endif--}}