<!DOCTYPE html>
<html lang="en">
<head>
  
    <title>Prestamos vs Utilidades</title>
    <style type="text/css">

	    #principal{
	      width:100px;
	    }

	    @page { margin-top:250px;margin-bottom:100px; }

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
                <label class="color_texto" for="title">PRESTAMOS VS UTILIDADES</label>
              </div>
            </div>
            <div class="col-md-12">
            	@php
                   $nomb_mes = "";
                    if($mes == '1'){
                        $nomb_mes = 'Enero';
                    }elseif($mes == '2'){
                        $nomb_mes = 'Febrero';
                    }elseif($mes == '3'){
                        $nomb_mes = 'Marzo';
                    }elseif($mes == '4'){
                        $nomb_mes = 'Abril';
                    }elseif($mes == '5'){
                        $nomb_mes = 'Mayo';
                    }elseif($mes == '6'){
                        $nomb_mes = 'Junio';
                    }elseif($mes == '7'){
                        $nomb_mes = 'Julio';
                    }elseif($mes == '8'){
                        $nomb_mes = 'Agosto';
                    }elseif($mes == '9'){
                        $nomb_mes = 'Septiembre';
                    }elseif($mes == '10'){
                        $nomb_mes = 'Octubre';
                    }elseif($mes == '11'){
                        $nomb_mes = 'Noviembre';
                    }elseif($mes == '12'){
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
	                          {{$anio}}
	                        </div>
	                      </div>
                        </td>
                        <td>
	                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
	                        <div class="mLabel">
	                          Mes:
	                        </div>
	                        <div class="mValue">
	                         {{$nomb_mes}}
	                        </div>
	                      </div>
                        </td>
                        <td>
	                      <!--div class="row" style="padding-bottom: 0px;margin-bottom:0px">
	                        <div class="mLabel">
	                          Fecha:
	                        </div>
	                        <div class="mValue">
	                         
	                        </div>
	                      </div-->
                        </td>
		            </tr>
	            </table>
	            <div class="separator"></div> 
	            <div class="separator"></div> 
            </div>
           
            <table id="anticipo_quincena" border="0" cellpadding="0" cellpadding="0">
	            <thead>
		          <tr>
		            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">NOMBRE</div></th>
		            <th width="30%" style="font-size: 16px;"><div class="details_title">CARGO</div></th>
		            <th style="font-size: 16px;"><div class="details_title">MONTO PRESTAMO</div></th>
		            <th style="font-size: 16px;"><div class="details_title_border_right">VALOR UTILIDAD</div></th>
		          </tr>
	            </thead>
				@php 
					$i=0; 
					$total_monto1=0;
					$total_monto2 =0;
				@endphp
	            <tbody id="detalle_anticipo">
					@foreach($prestamos as $value)
						@php
						$prest_utili = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $value->id_empl)->where('pres_sal','1')->where('id_prestamo', $value->id)->first();
						@endphp
						@if(!is_null($prest_utili))
						@php $total_monto1 +=$value->monto_prestamo;  @endphp
			                <tr class="round">
			               	    <td style="font-size: 18px">
			               	      {{$value->nombres}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      {{$value->cargo}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      {{$value->monto_prestamo}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      @if(!is_null($prest_utili))@php$total_monto2 +=$prest_utili->total; @endphp {{$prest_utili->total}} @endif
			               	    </td>
			                </tr>
			            @endif
					@endforeach

					@foreach($saldos as $s)
						@php
						$prest_utili_s = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $s->id_empl)->where('pres_sal','2')->where('id_saldo', $s->id)->first();
						@endphp
						@if(!is_null($prest_utili_s))
						@php $total_monto1 += $s->saldo_inicial; @endphp
			                <tr class="round">
			               	    <td style="font-size: 18px">
			               	      {{$s->nombres}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      {{$s->cargo}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      {{$s->saldo_inicial}}
			               	    </td>
			               	    <td style="font-size: 18px">
			               	      @if(!is_null($prest_utili_s))@php $total_monto2 +=$prest_utili_s->total; @endphp {{$prest_utili_s->total}} @endif
			               	    </td>
			                </tr>
			            @endif
					@endforeach
					<tr>
						<td></td>
						<td style="font-size: 18px; font-weight: bold;">TOTAL: </td>
						<td style="font-size: 18px; font-weight: bold;" > {{$total_monto1}} </td>
						<td style="font-size: 18px; font-weight: bold"> {{$total_monto2}} </td>
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

