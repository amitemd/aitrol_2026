<!DOCTYPE HTML>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('css/css_web/style.css')}}" type="text/css" media="all" />
<script type="text/javascript" src="{{asset('js/js_web/jquery.js')}}"></script>
<style type="text/css">
	
	.pl-title{
		font-size: 14px !important;
	}
</style>
</head>
<body>
	@php
	$promo1=100;
	$promo2=155;
	$promo3=205;
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
	<div class="blue-bar">
		<div class="wrap">
			<div class="host-content">
	    		<div class="row-indent">
				    <h2 class="h2-subheading">PREVENCIÓN</h2>
				</div>
				<div class="row-indent">
				    <h2 class="h2-subheading">Tu salud al cuidado de los mejores</h2>
				    
				</div>
				
				<div class="host-main">
	 				<div class="plans-columns-wrp">
	      				<div class="server_loc_tabs">
				            <ul class="country_specific_tabs">
				               		<li country="US" style="padding-left: 20px !important;">Planes Preventivos ante el covid 19</li>
				                   
				            </ul>
	        			</div>
						<div class="grids-hosting" id="divx">
					       	<ul class="plan-list us_plan " style="width: 30%;" id="ul1">
						    	<form name="hostingplan" id="hostingplan_4" method="POST">
							        <input type="hidden" name="action" value="add">
							        <input type="hidden" name="type" id="type_id" value="resellerhosting">
							        <input type="hidden" name="location" value="us">
							        <input type="hidden" id="planid_id" name="planid" value="4">
							        <input type="hidden" name="domain_name" value="">
							        <input type=hidden name="otherdomain" value="">
							        <input type=hidden name="orderid" value="">
							        <input type=hidden name="upgrade" value="">
							        <input type=hidden name="upgradeprice" value="">
							        <input type=hidden name="old_plan_name" value="">
							        <li class="p-name2"><span class="pl-title">BÁSICO<b></b></span></li>
							        <li class="p-feat">$ {{$promo1}}</li>
							        <li class="p-feat1">Toma de muestra a domicilio</li>
							        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
							        <li class="p-feat">+ Hemograma, PCR, Grupo Sanguineo<br><br><br></li>
							        <li class="p-feat"><br><br>
							        </li>
								       
							        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('1');return false;">Comprar</a></li>
						     	</form>
							</ul>
					        <ul class="plan-list us_plan " style="width: 30%;" id="ul2">
							    <form name="hostingplan" id="hostingplan_3" method="POST">
							        <input type="hidden" name="action" value="add">
							        <input type="hidden" name="type" id="type_id" value="resellerhosting">
							        <input type="hidden" name="location" value="us">
							        <input type="hidden" id="planid_id" name="planid" value="3">
							        <input type="hidden" name="domain_name" value="">
							        <input type=hidden name="otherdomain" value="">
							        <input type=hidden name="orderid" value="">
							        <input type=hidden name="upgrade" value="">
							        <input type=hidden name="upgradeprice" value="">
							        <input type=hidden name="old_plan_name" value="">
							        <li class="p-name2"><span class="pl-title">COMPLETO<b></b></span></li>
							        <li class="p-feat">$ {{$promo2}}</li>
							        <li class="p-feat1">Toma de muestra a domicilio</li>
							        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
							        <li class="p-feat">+ Hemograma, PCR, Dimero - D, Ferritina, LDH,<br>Glucosa, Úrea, Creatinina, AST, ALT, Grupo Sanguineo
							        </li>
							        <li class="p-feat"><br><br>
							        </li>
							        
							        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('2');return false;">Comprar</a></li>
							    </form>
							</ul>
					        <ul class="plan-list us_plan " style="width: 30%;" id="ul3">
							    <form name="hostingplan" id="hostingplan_6" method="POST">
							        <input type="hidden" name="action" value="add">
							        <input type="hidden" name="type" id="type_id" value="resellerhosting">
							        <input type="hidden" name="location" value="us">
							        <input type="hidden" id="planid_id" name="planid" value="6">
							        <input type="hidden" name="domain_name" value="">
							        <input type=hidden name="otherdomain" value="">
							        <input type=hidden name="orderid" value="">
							        <input type=hidden name="upgrade" value="">
							        <input type=hidden name="upgradeprice" value="">
							        <input type=hidden name="old_plan_name" value="">
							        <li class="p-name2"><span class="pl-title">COMPLETO + CONSULTA ESPECIALIZADA<b></b></span></li>
							        <li class="p-feat">$ {{$promo3}}</li>
							        <li class="p-feat1">Toma de muestra a domicilio</li>
							        <li class="p-feat1">Prueba Covid-19 (IGM-IGG)</li>
							        <li class="p-feat">+ Hemograma, PCR, Dimero - D, Ferritina, LDH,<br>Glucosa, Úrea, Creatinina, AST, ALT, Grupo Sanguineo
							        </li>
							        <li class="p-feat">+Consulta en línea con Neumología<br>Interpretación de pruebas
							        </li>
							        <li class="p-button"><a class="txt-button" onclick="ingreso_orden('3');return false;">Comprar</a></li>
							    </form>
							</ul>  
							<ul class="plan-list us_plan" style="width: 60%;display: none;" id="ulx">
							</ul>	
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
				            		$('#ulx').css("display","inline-block");	
				            	};
				            	if(id =='2'){
				            		$('#ul1').css("display","none");
				            		$('#ul3').css("display","none");
				            		$('#ulx').css("display","inline-block");	
				            	};
				            	if(id =='3'){
				            		$('#ul2').css("display","none");
				            		$('#ul1').css("display","none");
				            		$('#ulx').css("display","inline-block");	
				            	};

				                $('#ulx').empty().html(data);
				            	
				            }    
				        }); 
				    }
				</script>
			</div>
		</div>
	</div>

	<!--div class="footer">
	  <div class="wrap">	
		<div class="footer_grides">
		<div class="footer_grid">
	    	
		</div>   
		<div class="footer_grid">
	    	
	    </div>   
		<div class="footer_grid1">
	    	
		</div>
	  </div>
	  </div>
	<div class="footer-bottom">
	<div class="wrap">
	    
		</div>	
		</div>
	</div-->
</body>
</html>
