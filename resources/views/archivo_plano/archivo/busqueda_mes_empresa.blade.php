<style type="text/css">
  
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .info_nomina{
      width: 69%;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .datos_nomina
    {
      font-size: 0.8em;
    }

    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    #rol_pago{
      width: 100%;
      margin-bottom: 10px;
    }


    .info_nomina .col-xs-8 {
        padding-left:10px;
        font-size: 0.9em;
    }
    .info_nomina .round{
        padding-top:10px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .modal-body .form-group {
        margin-bottom: 0px;
    }

    .h3.modal_h3{
        font-family: 'BrixSansBlack';
        font-size: 8pt;
        display: block;
        background: #3d7ba8;
        color: #FFF;
        text-align: center;
        padding: 3px;
        margin-bottom: 5px;
        padding: 7px;
        font-size: 1em;
        margin-bottom: 15px;
    }
    .h3.modal_h3_2{
        margin-top: -20px !important;
        margin-bottom: 25px !important;
        padding: 7px;
        font-size: 1em;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }

    .separator1{
      width:100%;
      height:5px;
      clear: both;
    }

    
    /* Nuevo CSS*/

    .mLabel{
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 10px;
    }

    .texto {
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 15px;
    }

    .color_texto{
      color:#FFFFFF;
    }

    .head-title{
      background-color: #4682B4;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .t9{
      font-size: 0.9rem;
    }

    .well-dark{
      background-color: #cccccc;
    }

    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
    }
</style>

<div style="border-radius: 8px;" id="det_busqueda">
  <div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
      <div class="col-md-12">
        <div class="table-responsive col-md-12">
          <table id="example2_item" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
            <thead style="background-color: #4682B4">
              <tr>
                <!--<th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N°</th>-->
                <th style="width: 8%;height:8px;color: white;text-align: center; font-size: 12px;">Fecha</th>
                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">Código de Validacion </th>
                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">Beneficiario</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Código TSNS</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Descripcion</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Clasificador</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Cantidad Total</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Precio Unitario</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Clasificador %</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Subtotal </th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Valor por Modificador </th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Valor Solicitado </th>
                <!--<th style="width: 8%;height:8px;;color: white;text-align: center;">Nombre Beneficiario</th>-->
                <!--<th style="width: 8%;height:8px;;color: white;text-align: center;">Iva Unitario</th>-->
              </tr>
            </thead>
            <tbody>
              @php 
                $contador=1;
                $x=0;
                $id_temporal=0; 
              @endphp
              @foreach($archivo_plano as $value)
                @php 
                  if($value->paciente->fecha_nacimiento == null){
                      $edad=0;
                  }else{
                      $edad=  Carbon\Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;    
                  }
                  if($value->id_paciente != $id_temporal) {
                    $id_temporal=$value->id_paciente;
                    $x++;
                  } 

                  $fech  = substr($value->fecha_ing, 0, 10);
                  $fech_inver = date("d/m/Y",strtotime($fech));
                  $descripcion = substr($value->descripcion,0,40);
                @endphp
                <tr>
                  <!--<td>{{$x}}</td>-->
                  <td>{{$fech_inver}}</td>
                  <td>{{$value->cod_deriva_msp}}</td>
                  <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}}</td>
                  <td>@if(($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M'))  &nbsp; @else {{$value->codigo}} @endif</td>
                  <td>{{$descripcion}}</td>
                  <td>@if(!is_null($value->clasificador)){{$value->clasificador}}@endif</td>
                  <td>@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                  <td>@if(!is_null($value->valor)){{$value->valor}}@endif</td>
                  <td>100</td>
                  <td>@if(!is_null($value->subtotal)){{$value->subtotal}}@endif</td>
                  <td>@if(!is_null($value->valor_porcent_clasifi)){{$value->valor_porcent_clasifi}}@endif</td>
                  <td>@if(!is_null($value->total_solicitado_usd)){{$value->total_solicitado_usd}}@endif</td>
                </tr>
                @php
                  $contador= $contador+1;
                @endphp
              @endforeach
            </tbody>
          </table>
        </div> 
      </div> 
    </div>
  </div>
</div>





