<!DOCTYPE html>
<html lang="en">
<head>
  
    <title>Anticipos 1era Quincena</title>
    <style type="text/css">

	    #principal{
	      width:100px;
	    }

	    @page { margin-top:150px;margin-bottom:100px; }

	    #footer1 { position: fixed; left: 0px; bottom: -120px; right: 0px; height: 130px;}
	    #footer2 { position: fixed; left: 950px; bottom: -120px; right: 0px; height: 130px;}
	    #footer3 { position: fixed; left: 800px; bottom: -120px; right: 0px; height: 130px;}
	    #footer4 { position: fixed; left: 1250px; bottom: -120px; right: 0px; height: 130px;}
      
     
	    #page_pdf{
	      /*width:800px;*/
	     /*width: 49%;*/
	      /*margin: 0 0;*/
	      /*float: left;*/
	      /*padding-right: 20px;*/
	      /*border-right: solid 1px;*/
	    }

	    #page_pdf2{
	      /*width: 49%;*/
	      /*float: left;*/
	      width:800px;
	      padding-left: 20px;
	      
	    }

    
	    #factura_head,#factura_cliente,#factura_detalle{
	      width: 100%;
	      /*margin-bottom: 10px;*/
	    }

	    #factura_head{
	      margin-top: -80px; 
	    }


	    .info_empresa{
	      width: 50%;
	      text-align: center;
	    }

	    .separator1{
	      width:100%;
	      height:15px;
	      clear: both;
	    }

	    .separator{
	      width:100%;
	      height:10px;
	      clear: both;
	    }

	    .round{
	      border-radius: 10px;
	      border: 1px solid #3d7ba8;
	      overflow: hidden;
	      padding-bottom: 5px;
	    }

	    .round2{
	      border-radius: 15px;
	      border: 3px solid #3d7ba8;
	      padding-bottom: 15px;
	    }

	    .h3{
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

	    .info_rol{
	      width: 69%;
	    }

	    .datos_rol
	    {
	      font-size: 0.8em;
	    }

        .mLabel{
	      width:20%;
	      display: inline-block;
	      vertical-align: top;
	      font-weight: bold;
	      padding-left:0px;
	      font-size: 20px;

	    }
	    .mValue{
	      width:65%;
	      display: inline-block;
	      vertical-align: top;
	      padding-left:0px;
	      font-size: 20px;
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
	      padding: 2px;
	    }

	    table th{
	       text-align: left;
	       color:#3d7ba8;
	       font-size: 1em;
	    }

	    #detalle_rol tr:nth-child(even) {
	      background: #ededed;
	      border-radius: 10px;
	      border: 1px solid #3d7ba8;
	      overflow: hidden;
	      padding-bottom: 15px;

	    }

	    *{
	      font-family:'Arial' !important;
	    }

	    .details_title_border_left{
	      background: #888;
	      border-top-left-radius: 10px;
	      color:#FFF;
	      padding: 10px;
	      padding-left:10px;
	    }

	    .details_title_border_right{
	      background: #888;
	      border-top-right-radius: 10px;
	      color:#FFF;
	      padding: 10px;
	      padding-right:3px;
	    }

	    .details_title{
	      background: #888;
	      color:#FFF;      
	      padding: 10px;
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

	    .totals_label2{
	      font-size: 0.7em;
	      font-weight: bold;
	      font-family: 'Arial';
	    }

	    /* Nuevo CSS*/
	    .texto {
	      color: #777;
	      font-size: 0.9rem;
	      margin-bottom: 0;
	      line-height: 15px;
	    }


	    .color_texto{
	      color:#FFF;
	    }

	    .head-title{
	      background-color: #888;
	      margin-left: 0px;
	      margin-right: 0px;
	      height: 30px;
	      line-height: 30px;
	      color: #cccccc;
	      text-align: center;
	    }

	    .head-title1{
	      background-color: #888;
	      margin-left: 0px;
	      margin-right: 0px;
	      height: 30px;
	      line-height: 10px;
	      color: #cccccc;
	      text-align: center;
	    }

	    .dobra{
	       background-color: #D4D0C8;
	    }

	    .info_empresa{
	      width: 50%;
	      text-align: center;
	    }

	    .info_factura{
	      width: 60%;
	    }

	</style>  
  
</head>
<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
    <div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
		<div style="width:100%;border-right:1px solid dashed;">
			<table id="factura_head">
		      <tr>
		        @if(!is_null($empresa->logo))
		        <td class="info_empresa">
		          <div style="text-align: left">
		            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:300px;height: 100px">
		          </div>
		        </td>
		        @endif
		        <td class="info_factura">
		          <div style="text-align: right">
		            <label style="font-size: 15px"><b>{{$empresa->nombrecomercial}}</b></label><br/>
		            <label style="font-size: 14px">CALLE:{{$empresa->direccion}}</label><br/>
		            <label style="font-size: 15px">RUC:<b>{{$empresa->id}}</b></label></br>
		          </div>
		        </td>
		      </tr>
            </table>
            <div class="row head-title">
              <div class="col-md-12">
                <label class="color_texto" for="title">ANTICIPOS 1ERA QUINCENA EMPLEADOS</label>
              </div>
            </div>
            <div class="col-md-12">
            	@php
                   $nomb_mes = "";
                    if($ult_val_anticip->mes == '1'){
                        $nomb_mes = 'Enero';
                    }elseif($ult_val_anticip->mes == '2'){
                        $nomb_mes = 'Febrero';
                    }elseif($ult_val_anticip->mes == '3'){
                        $nomb_mes = 'Marzo';
                    }elseif($ult_val_anticip->mes == '4'){
                        $nomb_mes = 'Abril';
                    }elseif($ult_val_anticip->mes == '5'){
                        $nomb_mes = 'Mayo';
                    }elseif($ult_val_anticip->mes == '6'){
                        $nomb_mes = 'Junio';
                    }elseif($ult_val_anticip->mes == '7'){
                        $nomb_mes = 'Julio';
                    }elseif($ult_val_anticip->mes == '8'){
                        $nomb_mes = 'Agosto';
                    }elseif($ult_val_anticip->mes == '9'){
                        $nomb_mes = 'Septiembre';
                    }elseif($ult_val_anticip->mes == '10'){
                        $nomb_mes = 'Octubre';
                    }elseif($ult_val_anticip->mes == '11'){
                        $nomb_mes = 'Noviembre';
                    }elseif($ult_val_anticip->mes == '12'){
                        $nomb_mes = 'Diciembre';
                    } 
                @endphp
                <table class="datos_rol">
                	<tr>
                		<td>
	                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
	                        <div class="mLabel">
	                          Año:
	                        </div>
	                        <div class="mValue">
	                           @if(!is_null($ult_val_anticip->anio))
		                        {{$ult_val_anticip->anio}}
		                       @endif
	                        </div>
	                      </div>
                        </td>
                        <td>
	                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
	                        <div class="mLabel">
	                          Mes:
	                        </div>
	                        <div class="mValue">
	                         @if(!is_null($nomb_mes))
		                       {{$nomb_mes}}
	                         @endif
	                        </div>
	                      </div>
                        </td>
                        <td>
	                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
	                        <div class="mLabel">
	                          Fecha:
	                        </div>
	                        <div class="mValue">
	                            @if(!is_null($ult_val_anticip->fecha_creacion))
	                              {{$ult_val_anticip->fecha_creacion}}
                                @endif
	                        </div>
	                      </div>
                        </td>
		            </tr>
	            </table>
	            <div class="separator"></div> 
	            <div class="separator"></div> 
            </div>
            <div class="row head-title">
              <div class="col-md-12">
                <label class="color_texto" for="title">COMO FUE PAGADO EL ANTICIPO</label>
              </div>
            </div>
            @php
               $tip_pago = Sis_medico\Ct_Rh_Tipo_Pago::where('id',$ult_val_anticip->id_tipo_pago)->where('estado','1')->first();
               $inf_banco = Sis_medico\Ct_Bancos::where('id',$ult_val_anticip->banco)->where('estado','1')->first();
               $inf_caja_ban = Sis_medico\Ct_Caja_Banco::where('cuenta_mayor',$ult_val_anticip->cuenta_saliente)->where('estado','1')->first();
            @endphp
            <div class="col-md-12">
            	<!--Acreditacion-->
            	@if($ult_val_anticip->id_tipo_pago == 1) 
            	    <table class="datos_rol">
            	    	<tr>
                            <td>
		                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          Tipo de Pago:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($tip_pago))
				                         {{$tip_pago->tipo}}
				                        @endif
			                        </div>
		                        </div>
                            </td>
                            <td>
			                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                         N# Cuenta:
			                        </div>
			                        <div class="mValue">
			                         @if(!is_null($ult_val_anticip->numero_cuenta))
			                           {{$ult_val_anticip->numero_cuenta}}
			                         @endif
			                        </div>
			                    </div>
                            </td>
                        </tr>
			            <tr>
			            	<td>
		                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          Banco:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($inf_banco))
			                              {{$inf_banco->nombre}}
			                            @endif
			                        </div>
		                        </div>
                            </td>
                            <td>
			                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                         Cuenta Saliente:
			                        </div>
			                        <div class="mValue">
			                          @if(!is_null($inf_caja_ban))
			                            {{$inf_caja_ban->nombre}}
			                          @endif
			                        </div>
			                    </div>
                            </td>
                        </tr>
		            </table>
                @endif
	            <!--Efectivo-->
	            @if($ult_val_anticip->id_tipo_pago == 2)
		            <table class="datos_rol">
		            	<tr>
			                <td>
		                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          Tipo de Pago:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($tip_pago))
				                         {{$tip_pago->tipo}}
				                        @endif
			                        </div>
		                        </div>
                            </td>
                            <td>
			                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          Cuenta Saliente:
			                        </div>
			                        <div class="mValue">
			                          @if(!is_null($inf_caja_ban))
			                            {{$inf_caja_ban->nombre}}
			                          @endif
			                        </div>
			                    </div>
                            </td>    
                        </tr>
			        </table> 
                @endif
	            <!--CHEQUE-->
	            @if($ult_val_anticip->id_tipo_pago == 3)
	                <table class="datos_rol">
                        <tr>
                            <td>
		                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          Tipo de Pago:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($tip_pago))
		                                  {{$tip_pago->tipo}}
		                                @endif
			                        </div>
		                        </div>
                            </td>
                            <td>
			                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                         Fecha Cheque:
			                        </div>
			                        <div class="mValue">
			                          @if(!is_null($ult_val_anticip->fecha_cheque))
		                                {{$ult_val_anticip->fecha_cheque}}
		                              @endif
			                        </div>
			                    </div>
                            </td>
                        </tr>
			            <tr>
			            	<td>
		                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                          N# Cheque:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($ult_val_anticip->num_cheque))
		                                   {{$ult_val_anticip->num_cheque}}
		                                @endif
			                        </div>
		                        </div>
                            </td>
                            <td>
			                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
			                        <div class="mLabel">
			                         Cuenta Saliente:
			                        </div>
			                        <div class="mValue">
			                            @if(!is_null($inf_caja_ban))
		                                  {{$inf_caja_ban->nombre}}
		                                @endif
			                        </div>
			                    </div>
                            </td>
                        </tr>		                
                    </table>
                @endif
            </div>
            <table id="anticipo_quincena" border="0" cellpadding="0" cellpadding="0">
	            <thead>
		          <tr>
		          	
		          	<th width="3%" style="font-size: 16px;"><div class="details_title_border_left">#</div></th>
		            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">NOMBRE</div></th>
		            <th width="30%" style="font-size: 16px;"><div class="details_title">CARGO</div></th>
		            <th style="font-size: 16px;"><div class="details_title">SUELDO</div></th>
		            <th style="font-size: 16px;"><div class="details_title_border_right">VALOR ANTICIPO</div></th>
		          </tr>
	            </thead>
	            <tbody id="detalle_anticipo">
				@php $sumaTotal = 0;  $count=1; @endphp
	            	@foreach ($inf_val_anticip as $value)
	            	    @php
						$sumaTotal+=$value->valor_anticipo;
						$inf_nomina = null; 
                          if(!is_null($value->id_user)){
                            $inf_nomina = Sis_medico\Ct_Nomina::where('id_user',$value->id_user)->where('id_empresa',$empresa->id)->orderBy('updated_at', 'desc')->first();
                            $user = Sis_medico\User::find($value->id_user);
							$sueldo_mensual = 0;
							if ($inf_nomina->jornada != '0') {
								$horas_mes = $inf_nomina->cantidad_horas * $inf_nomina->dias_laborables;
								$sueldo_mensual = $inf_nomina->sueldo_neto * $horas_mes / 240;
							} else {
								$sueldo_mensual = $inf_nomina->sueldo_neto;
							}
							$sueldo_mensual = round($sueldo_mensual, 2);

                          }
                        @endphp
                        
		                <tr class="round">
		                	<td style="font-size: 18px">{{$count}}</td>
		               	    <td style="font-size: 18px">
		               	      @if(!is_null($inf_nomina))
                                <!-- {{$inf_nomina->nombres}} -->
                                {{$user->apellido1}}  {{$user->apellido2}}  {{$user->nombre1}}  {{$user->nombre2}}
                              @endif
		               	    </td>
		               	    <td style="font-size: 18px">
		               	      @if(!is_null($inf_nomina))
                                {{$inf_nomina->cargo}}
                              @endif
		               	    </td>
		               	    <td style="font-size: 18px; text-align: right">
		               	      
                                $ {{$sueldo_mensual}}
                              	
		               	    </td>
		               	    <td style="font-size: 18px; text-align: right">
		               	      @if(!is_null($value->valor_anticipo))
                                $ {{$value->valor_anticipo}}
                              @endif
		               	    </td>
		                </tr>
		                @php $count++; @endphp
	                @endforeach
					<tr class="round2">
							<td></td>
							<td></td>
							<td></td>
							<td class="totals_label2" style="font-size: 21px; text-align: right">TOTAL</td>
							<td class="totals_label2" style="font-size: 21px; padding-rigth: 60px; text-align: right">
							@if(!is_null($value->valor_anticipo))
							$ {{number_format($sumaTotal,2,'.','')}}
											@endif
							
						</td>
					</tr>
	            </tbody>
            </table>

            <div id="footer1">
		        <div style="font-size: 14px;width: 25%;">
		          <p>
		            <hr style="width: 60%;margin-left: 100pt;margin:0 auto">
		            <label class="control-label" style="margin-left: 48pt;font-family: 'Helvetica general';font-size: 19px;">FIRMA RESPONSABLE
		            </label>
		          </p>
		        </div>
            </div>
	        <div id="footer2">
		        <div style="font-size: 14px;width: 65%;">
		          <p>
		            <hr style="width: 60%;margin-left: 280pt;margin:0 auto">
		            <label class="control-label" style="margin-left: 65pt;font-family: 'Helvetica general';font-size: 19px;">RECIBÍ CONFORME
		            </label>
		          </p>
		        </div>
		    </div>
        </div>
    </div>
</body>
</html>

