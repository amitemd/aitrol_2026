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

</style>

<div style="border-radius: 8px;" id="det_proc">
  <div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
      <div class="col-md-12">
        <div class="table-responsive col-md-12">
          <table cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
            <thead>
              <tr style="color:White;background-color:#006699;font-weight:bold;">
                <th>N.</th>
                @if($j_seguro == 5)
                 <th>CLASIFICADOR</th>
                @endif
                <th>TIPO</th>
                <th>CODIGO</th>
                <th>DESCRIPCION</th>
                <th>CANTIDAD</th>
                <th>VALOR</th>
                <th>IVA</th>
              </tr>
            </thead>
            <tbody>
              @php
                $valor = 0;
                $valor_a = 0;
                $contador = 0;
              @endphp
              @foreach ($proc as $value)
                @php
                  $contador++;
                  $tip_examen = Sis_medico\Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                  $valor_nivel = Sis_medico\ApProcedimientoNivel::where('id_procedimiento',$value->id)->where('cod_conv',$id_nivel)->first();
                  if(!is_null($valor_nivel)){
                    $valor =round(($valor_nivel->uvr1*$valor_nivel->prc1),2);
                    $valor_a =round(($valor_nivel->uvr1a*$valor_nivel->prc1a),2);
                  }
                  else{
                  $valor = $value->valor;
                  }
                  $tipo_an = 'AN';
                  if($value->orden == '3'){
                    $valor =$valor/2;
                  }

                  $iva = $value->iva;
                  if($fecha == null){
                      $iva = $value->iva;
                  }
        
                  if($fecha >= '2024-04-01'){

                      $iva = $value->iva_nuevo;    
                  }

                @endphp
                <tr  role="row" class="odd">
                  <td>{{$contador}}</td>
                  @if($j_seguro == 5)
                  <td>
                      @if(!is_null($tip_examen))
                       {{$tip_examen->clasificado}}
                      @endif
                  </td>
                  @endif
                  <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                  <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                  <td>@if(!is_null($value->descripcion)){{$value->descripcion}}@endif</td>
                  <td>@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td> 
                  <td>{{$valor}}</td> 
                  <td>{{$iva}}</td>               
                </tr>
                @if($value->orden == 1)
                  @php
                   $contador++;
                  @endphp
                  <tr>
                    <td>{{$contador}}</td>
                    @if($j_seguro == 5)
                     <td>SA19-84</td>
                    @endif
                    <td>{{$tipo_an}}</td>
                    <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                    <td>@if(!is_null($value->descripcion)){{$value->descripcion}}@endif</td>
                    <td>
                      @if(!is_null($value->cantidad))
                        {{$value->cantidad}}
                      @endif
                    </td> 
                    <td>{{$valor_a}}</td> 
                    <td>{{$iva}}</td>               
                  </tr>
                @endif
              @endforeach
              
            </tbody>
          </table>
        </div> 
      </div> 
    </div>
  </div>
</div>





