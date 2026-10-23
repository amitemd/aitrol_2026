<div class="col-md-12">
	@php
			    
        if(!is_null($ordfun->fecha_orden)){
		   $fecha_r =  Date('Y-m-d',strtotime($ordfun->fecha_orden));
	    }

	    if($ordfun->id_doctor != ""){
            $xdoctor = DB::table('users as us')->where('us.id',$ordfun->id_doctor)->first();
        }

    @endphp
	@php
        $fecha = substr($ordfun->fecha_orden,0,10);
        $invert = explode( '-',$fecha);
        $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
	@endphp
	@php
            
        if(!is_null($ordfun->id)){
  				$procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $ordfun->id) ->where('id_grupo_procedimiento','18')->first();
        }

        $texto = ""; 
	    
	    if(!is_null($procedimiento_orden_tipo)){ 

		    $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $procedimiento_orden_tipo->id)->get();

            $mas = true;
      
            foreach($procedimiento_orden_proced as $value2)
            {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
					$texto = $nombre_procedimiento->nombre;
					$mas = false; 
                }else{
		  	        $texto = $texto.' + '.$nombre_procedimiento->nombre;
		  	    }

		  	}

        }
    @endphp
	
	<div class="box @if($fecha_r != date('Y-m-d')) collapsed-box  @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px;">
		<div class="box-header with-border" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">
			<div class="row">
				<div class="col-md-5">
                    @if(!is_null($ordfun->fecha_orden))
                        @php 
                         $dia =  Date('N',strtotime($ordfun->fecha_orden)); 
                         $mes =  Date('n',strtotime($ordfun->fecha_orden)); 
                        @endphp
                        <b>
                        @if($dia == '1') Lunes 
                         @elseif($dia == '2') Martes
                         @elseif($dia == '3') Miércoles 
                         @elseif($dia == '4') Jueves 
                         @elseif($dia == '5') Viernes 
                         @elseif($dia == '6') Sábado 
                         @elseif($dia == '7') Domingo 
                        @endif
                         {{substr($ordfun->fecha_orden,8,2)}} de
                        @if($mes == '1') Enero 
                             @elseif($mes == '2') Febrero 
                             @elseif($mes == '3') Marzo 
                             @elseif($mes == '4') Abril 
                             @elseif($mes == '5') Mayo 
                             @elseif($mes == '6') Junio 
                             @elseif($mes == '7') Julio 
                             @elseif($mes == '8') Agosto 
                             @elseif($mes == '9') Septiembre 
                             @elseif($mes == '10') Octubre 
                             @elseif($mes == '11') Noviembre 
                             @elseif($mes == '12') Diciembre 
                        @endif
                        del {{substr($ordfun->fecha_orden,0,4)}}</b>  
                    @endif
                </div>
                <div class="col-md-5">
                	<div>
						<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) : </span> 
						<span style="font-size: 12px">@if(!is_null($xdoctor->nombre1)){{$xdoctor->nombre1}} {{$xdoctor->apellido1}} @endif</span>
				    </div>
                </div>
                <div class="col-md-1" style="color: white"> 
	                  @if(!is_null($ordfun->id)) 
	                    {{$ordfun->id}}
	                  @endif
                </div>
                <div class="pull-right box-tools" style="padding-top: 4px;">
                	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                    <i class="fa fa-plus"></i></button>
        	    </div>
            </div>
                <div class="row">
                  <div class="col-md-12">
                  	<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimientos:</span>
				  </div>
                  <div class="col-md-12">
                    <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;" class="badge badge-primary">@if(!is_null($texto)) {{$texto}} @endif</span>
                  </div>
                </div>
        </div>
        <div class="box-body" style="background: white;">
        	<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
	            <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
						<div class="row">
		  					<div class="col-3" style="margin-right: 10px">
		    					<div class="btn" style="color: white">
				    				<a class="fa fa-pencil-square-o " onclick="editar_orden_profuncional({{$ordfun->id}},'{{$ordfun->id_paciente}}');" ><span style="font-size: 13px">&nbsp;Editar</span>
				    				</a>
			    				</div>
		    				</div>
		    				<div class="col-4" style="text-align: center"> 
		  						<span >Detalle de Orden</span>
		  					</div>
		  					<div class="col-4" style="text-align: right;padding-top: 6px">
				               <a class="btn btn-danger" onclick="descargar_orden_funcional({{$ordfun->id}});" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden</a>
				            </div>
                        </div>
				    </div>
				    <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden_func{{$ordfun->id}}">
                                            
                    </div>
                <s/div>
            </div>
        </div>
    </div>
</div>

 <script type="text/javascript">

 	editar_orden_profuncional_new('{{$ordfun->id}}','{{$ordfun->id_paciente}}');

	function editar_orden_profuncional_new(id_orden,id_paciente){
    	$.ajax({
			type: "GET",
			url: "{{route('editar.orden_procedimiento_funcional')}}/"+id_orden+'/'+id_paciente, 
			data: "",
			datatype: "html",
			success: function(datahtml){
				$("#xorden_func"+id_orden).html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});	
	}


 </script>

