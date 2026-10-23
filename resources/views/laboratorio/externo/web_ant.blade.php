<!DOCTYPE HTML>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('css/css_web/style.css')}}" type="text/css" media="all" />
<script type="text/javascript" src="{{asset('js/js_web/jquery.js')}}"></script>

<script src="{{ asset ("/bower_components/jquery/dist/jquery.min.js")}}"></script>	
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<style type="text/css">
	
	.pl-title{
		font-size: 14px !important;
	}
	.wrap{
		width: 90%;
	}
	.xdiv{
		padding: 0 !important;
	}
	.xvmenu{
		width: 95%;
	}

	
</style>
</head>
<body>
	@php
		$promo1=100;$promo2=155;$promo3=205;$promo4 = 0; $promo5=0;
		if(!is_null($covid_local)){
			$promo4= $covid_local->valor;
		}
		if(!is_null($covid_domicilio)){
			$promo5= $covid_domicilio->valor;
		}
	@endphp
	<!--div class="header">

	</div-->

	<script type="text/javascript">
	$(".navigation li").hover(
	  function () {
	    $(this).addClass("nav-hover");	
	  },
	  function () {
	    $(this).removeClass("nav-hover");
	  }
	);
	</script>
	<style type="text/css">
		
	</style>

	<div class="modal fade" id="orden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    	<div id="contenido_modal">
	    		
	    	</div>
	    </div>
	  </div>
	</div>
	

	<div class="blue-bar">
		<div class="wrap">
			<div class="host-content">
				<div class="row">
					<div class="col-md-6"><h2 class="h2-subheading">PREVENCIÓN</h2></div>
					<div class="col-md-5 col-xs-12" style="background-color: white;color: black;border: 2px solid;">
						<div class="col-md-row">
							<div class="col-md-6 col-xs-6"  style="padding: 10px;"><label style="font-size: 18px;"> Pagar Número de Orden</label></div>
							<div class="col-md-4 col-xs-4"  style="padding: 10px;"><input class="form-group" type="text" name="id_orden" id="id_orden" value="0" style="width: 100%"></div>
							<div class="col-md-1  col-xs-1" style="padding: 10px;">
								<button type="button" id="buscar" class="btn btn-warning btn-xs" onclick="buscar_orden()"><span class="glyphicon glyphicon-search"></span></button>
							</div>
						</div>	
					</div>
					<div class="col-md-12"><h2 class="h2-subheading">Tu salud al cuidado de los mejores</h2></div>	
				</div>
	    						
				<div class="host-main">
	 				<div class="plans-columns-wrp">
	      				<div class="server_loc_tabs">
				            <ul class="country_specific_tabs">
				               		<li country="US" style="padding-left: 20px !important;">Planes Preventivos ante el covid 19</li>
				                   
				            </ul>
	        			</div>
						<div class="grids-hosting" id="divx">
							<div class="row">
							    <div class="xdiv col-md-3">	
							       	<ul class="plan-list us_plan xvmenu"  id="ul1">
								        <li class="p-name2"><span class="pl-title">PAQUETE PREVENTIVO <br>BÁSICO<b></b></span></li>
								        <li class="p-feat">$ {{$promo1}}</li>
								        <li class="p-feat1">Toma de muestra a domicilio</li>
								        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
								        <li class="p-feat">+ Hemograma, PCR, Grupo Sanguineo<br><br><br></li>
								        <li class="p-feat"><br><br>
								        </li> 
								        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('1');return false;">Seleccionar</a></li>
									</ul>
								</div>	
							    <div class="xdiv col-md-3">	
							        <ul class="plan-list us_plan xvmenu"  id="ul2">
								        <li class="p-name2"><span class="pl-title">PAQUETE PREVENTIVO <br>COMPLETO<b></b></span></li>
								        <li class="p-feat">$ {{$promo2}}</li>
								        <li class="p-feat1">Toma de muestra a domicilio</li>
								        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
								        <li class="p-feat">+ Hemograma, PCR, Dimero - D, <br>Ferritina, LDH, Glucosa, <BR>Úrea, Creatinina, AST, ALT, Grupo Sanguineo
								        </li>
								        <li class="p-feat"><br><br>
								        </li>
								        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('2');return false;">Seleccionar</a></li>
									</ul>
								</div>	
							    <div class="xdiv col-md-3">	
							        <ul class="plan-list us_plan xvmenu"  id="ul3">
								        <li class="p-name2"><span class="pl-title">COMPLETO +<br> CONSULTA ESPECIALIZADA<b></b></span></li>
								        <li class="p-feat">$ {{$promo3}}</li>
								        <li class="p-feat1">Toma de muestra a domicilio</li>
								        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
								        <li class="p-feat">+ Hemograma, PCR, Dimero - D, Ferritina, LDH,<br>Glucosa, Úrea, Creatinina, AST, ALT, Grupo Sanguineo
								        </li>
								        <li class="p-feat">+Consulta en línea con Neumología<br>Interpretación de pruebas
								        </li>
								        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('3');return false;">Seleccionar</a></li>
									</ul> 
								</div>	 
								<div class="xdiv col-md-3">	
									<ul class="plan-list us_plan xvmenu"  id="ul4">
								        <li class="p-name2"><span class="pl-title">PRUEBA RÁPIDA COVID-19<br>(IGG-IGM)<b></b></span></li>
								        <li class="p-feat">Toma de muestra el Local:</li>
								        <li class="p-feat">$ {{$promo4}}</li>
								        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('4');return false;">Seleccionar</a></li>
								        <li class="p-feat" style="padding-top: 7px;"></li>
								        <li class="p-feat">Toma de muestra a Domicilio:</li>
								        <li class="p-feat">$ {{$promo5}}</li>
								        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('4');return false;">Seleccionar</a></li>
									</ul> 
								</div>	 
							</div>
							<ul class="plan-list us_plan" style="width: 60%;display: none;" id="ulx">
							</ul>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	    $(document).ready(function(){
	        display_preferred_server_location("us");
	        var left= ($(".plans-columns-wrp").width()/2 )-($(".server_loc_tabs").width()/2);
	        $(".server_loc_tabs").css('left', left + 'px');
	        $(".server_loc_tabs li").click( function(){
	            $(".server_loc_tabs li").removeClass('sel');
	            $(this).addClass('sel');
	            $('.sel_tab').remove();
	            $(this).append("<div class='sel_tab'></div>");
	            $('#plans-container input[type=radio]').removeAttr("checked");
	        })

	        $('.country_specific_tabs li').each(function() {
	            add_event_to_tabs($(this).attr('country'));
	        });

	    });

	    function buscar_orden(){
	    	var id_orden = $('#id_orden').val();
	      	$.ajax({
	          type: 'get',
	          url:"{{ url('laboratorio/externo/web/promo/buscar/numero') }}/"+id_orden, 
	          
	          success: function(xdata){
	          	console.log(xdata);
	          	$('#contenido_modal').empty().html(xdata);  
	          	$("#orden").modal();
	          },
	          error: function(xdata){
	          	console.log(xdata);
	          }
	        });
	    }  	
	      
	

	    function add_event_to_tabs(country){
	        $(".tab_" + country).click( function(){
	            var sel_tab_left=($(this).outerWidth()/2 );
	            $(".server_loc_tabs li .sel_tab").css('left', sel_tab_left + 'px');
	            var location = $(this).attr('country').toLowerCase();
	            $('.plan-list').hide();
	            $('.' + location + '_plan').show();
	        })
	    }

	    function display_preferred_server_location(location){
	        $('.plan-list').hide();
	        $(".tab_" + location.toUpperCase()).addClass('sel').append("<div class='sel_tab'></div>");
	        $('.' + location + '_plan').show();
	        $('#' + location.toUpperCase() + '_plan_check').attr('checked', 'checked');
	    }

	    function ingreso_orden(id){
	    	$.ajax({
	            type: 'get',
	            url: "{{ url('laboratorio/externo/web/promo')}}/"+id, //lab_externo.promo
	                       
	            success: function(data){
	            	if(id =='1'){
	            		$('#ul2').css("display","none");
	            		$('#ul3').css("display","none");
	            		$('#ul4').css("display","none");
	            		$('#ul5').css("display","none");
	            		$('#ulx').css("display","inline-block");	
	            	};
	            	if(id =='2'){
	            		$('#ul1').css("display","none");
	            		$('#ul3').css("display","none");
	            		$('#ul4').css("display","none");
	            		$('#ul5').css("display","none");
	            		$('#ulx').css("display","inline-block");	
	            	};
	            	if(id =='3'){
	            		$('#ul2').css("display","none");
	            		$('#ul1').css("display","none");
	            		$('#ul4').css("display","none");
	            		$('#ul5').css("display","none");
	            		$('#ulx').css("display","inline-block");	
	            	};
	            	if(id =='4'){
	            		$('#ul2').css("display","none");
	            		$('#ul1').css("display","none");
	            		$('#ul3').css("display","none");
	            		$('#ul5').css("display","none");
	            		$('#ulx').css("display","inline-block");	
	            	};
	            	if(id =='5'){
	            		$('#ul2').css("display","none");
	            		$('#ul1').css("display","none");
	            		$('#ul4').css("display","none");
	            		$('#ul3').css("display","none");
	            		$('#ulx').css("display","inline-block");	
	            	};

	                $('#ulx').empty().html(data);
	            	
	            }    
	        }); 
	    }

	</script>

	<script type="text/javascript">
	  function pagar_orden(){   
	    var email = $('#email').val();
	    var texto = '';
	    if(email==''){
	        texto = "Ingrese el email";
	    }
	    if(texto != ''){
	        $("#err").text(texto);
	        $(".alerta_correcto").fadeIn(1000);
	        $(".alerta_correcto").fadeOut(10000);    
	    }
	    
	    if (texto=='') {        
	        $.ajax({

	            type: "post",
	                url: "{{route('lab_externo.pagar_orden')}}",
	                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	                datatype: "json",
	                data: $("#guardar_mail").serialize(),
	                success: function(datahtml){
	                    console.log(datahtml);
	                    if(datahtml=='ok'){
	                        $(".alerta_ok").fadeIn(1000);
	                        $(".alerta_ok").fadeOut(20000);
	                        $("#bingresa").css("display","none");
	                        $("#brefresca").css("display","inline-block");
	                        $("#brefresca").css("display","inline-block");
	                    }  

	                },
	                error: function(datahtml){

	                  console.log(JSON.parse(datahtml.responseText).email[0]);
	                  var err = JSON.parse(datahtml.responseText).email[0];
	                  if(err!=null){
	                    $("#err").text(err);
	                    $(".alerta_correcto").fadeIn(1000);
	                    $(".alerta_correcto").fadeOut(10000);
	                  }


	                }
	        });
	       
	      }  
	    }
	  
	</script>

	
	


	<script type="text/javascript">
		function openModal(modalId){
			$('#modal_pago_1').show();
		}
		function closeModal(modalId){
			$('#modal_pago_1').hide();
		}


	</script>
	
	<style>
		.modal-payment-wrapper{
			position:fixed;
			width:100%;
			top:0px;
			left:0px;
			height:100%;
			background-color:rgba(0, 0, 0, 0.9);
			text-align:center;
			overflow:auto;
		}		
		.modal-payment-wrapper .modal-payment{
			background:#FFFFFF;
			height:1700px;
			padding:0px;
		}		
		.modal-payment-wrapper .modal-payment .modal-title{
			text-align: right;
			padding: 15px;
			background:#DDD;
			border-bottom: solid 1px #CCC;
		}			
		.modal-payment-wrapper .modal-payment .modal-title .closex{
			width:20px;
			height:auto;
			cursor:pointer;
		}
		.modal-payment-wrapper .modal-payment .modal-title .closex:hover{
			opacity:0.5;
		}	
		.modal-payment-wrapper .modal-payment iframe{
			width:100%;
			border:none;
			height:1601px;
		}
	</style>
</body>
</html>
