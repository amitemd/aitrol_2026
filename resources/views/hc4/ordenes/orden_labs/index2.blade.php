<style>
    .hc4-lab-order-card {
        overflow: hidden;
        margin: 0 0 18px;
        border: 1px solid #19a4b0 !important;
        border-radius: 16px !important;
        background: #fff !important;
        box-shadow: 0 8px 22px rgba(5, 84, 126, .10);
    }

    .hc4-lab-order-card > .hc4-lab-order-summary {
        display: grid !important;
        grid-template-columns: minmax(220px, 1fr) minmax(220px, 1fr) auto;
        align-items: center;
        gap: 18px;
        min-height: 68px;
        padding: 12px 18px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    .hc4-lab-order-summary__date,
    .hc4-lab-order-summary__doctor {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        min-width: 0;
        font-size: 14px;
        line-height: 1.4;
    }

    .hc4-lab-order-summary__date { grid-column: 1; grid-row: 1; }

    .hc4-lab-order-summary__doctor {
        grid-column: 2;
        grid-row: 1;
        text-align: center;
    }

    .hc4-lab-order-summary__tools {
        position: static !important;
        top: auto !important;
        right: auto !important;
        grid-column: 3;
        grid-row: 1;
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .hc4-lab-order-summary__id {
        padding: 7px 11px;
        border-radius: 8px;
        background: rgba(0, 61, 105, .28);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .hc4-lab-order-card .hc4-lab-order-summary__toggle {
        width: 38px;
        height: 38px;
        margin: 0 !important;
        border: 1px solid rgba(255, 255, 255, .4);
        border-radius: 9px;
        background: rgba(255, 255, 255, .13);
        color: #fff;
    }

    .hc4-lab-order-card > .box-body {
        padding: 18px !important;
        background: #fff !important;
    }

    .hc4-lab-order-detail {
        overflow: hidden;
        margin: 0 !important;
        border: 1px solid #25a7b4 !important;
        border-radius: 14px !important;
        box-shadow: 0 6px 18px rgba(5, 84, 126, .08);
    }

    .hc4-lab-order-detail > .hc4-lab-order-detail__header {
        display: grid !important;
        grid-template-columns: auto minmax(220px, 1fr) auto;
        align-items: center;
        gap: 14px;
        min-height: 66px;
        padding: 10px 16px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff;
    }

    .hc4-lab-order-detail__title {
        grid-column: 2;
        grid-row: 1;
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
    }

    .hc4-lab-order-detail__action {
        position: static !important;
        grid-column: 1;
        grid-row: 1;
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        min-width: 0;
    }
    .hc4-lab-order-detail__action--right {
        grid-column: 3;
        justify-content: flex-end;
        text-align: right;
    }

    #area_trabajo .hc4-lab-order-detail .hc4-lab-order-detail__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: auto !important;
        height: auto !important;
        min-height: 40px;
        margin: 0 !important;
        padding: 8px 13px;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        border-radius: 9px !important;
        background: rgba(255, 255, 255, .13) !important;
        color: #fff !important;
        box-shadow: none !important;
        font-size: 13px;
        line-height: 1.2 !important;
        text-decoration: none !important;
        white-space: nowrap;
        cursor: pointer;
    }

    #area_trabajo .hc4-lab-order-detail .hc4-lab-order-detail__button:hover,
    #area_trabajo .hc4-lab-order-detail .hc4-lab-order-detail__button:focus {
        background: rgba(255, 255, 255, .24) !important;
        color: #fff !important;
        text-decoration: none !important;
        outline: none !important;
    }

    #area_trabajo .hc4-lab-order-detail .hc4-lab-order-detail__button > i {
        position: static !important;
        inset: auto !important;
        display: inline-block !important;
        flex: 0 0 auto;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: currentColor !important;
        font-size: 15px !important;
        line-height: 1 !important;
    }

    .hc4-lab-order-detail > .box-body { padding: 16px !important; }

    .hc4-lab-order-view {
        overflow: hidden;
        border: 1px solid #d9e3ea;
        border-radius: 10px;
        background: #fff;
    }

    .hc4-lab-order-view__count {
        padding: 9px 12px;
        border-bottom: 1px solid #d9e3ea;
        background: #f2f8fa;
        color: #405466;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
    }

    .hc4-lab-order-view__grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-auto-flow: dense;
        align-items: start;
        gap: 10px;
        padding: 10px;
    }

    .hc4-lab-order-view__group {
        overflow: hidden;
        min-width: 0;
        border: 1px solid #dfe7ec;
        border-radius: 8px;
        background: #fff;
    }

    .hc4-lab-order-view__group--wide { grid-column: 1 / -1; }

    .hc4-lab-order-view__group-title {
        margin: 0;
        padding: 7px 10px;
        background: var(--group-bg);
        color: var(--group-text);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.25;
        text-align: center;
        text-transform: uppercase;
    }

    .hc4-lab-order-view__group--wide .hc4-lab-order-view__items {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .hc4-lab-order-view__exam {
        min-width: 0;
        padding: 7px 10px;
        border-bottom: 1px solid #edf1f3;
        color: #4b5260;
        font-size: 12px;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .hc4-lab-order-view__exam:last-child { border-bottom: 0; }

    @media (max-width: 900px) {
        .hc4-lab-order-card > .hc4-lab-order-summary {
            grid-template-columns: 1fr auto;
        }
        .hc4-lab-order-summary__doctor {
            grid-column: 1 / -1;
            grid-row: 2;
            text-align: left;
        }
        .hc4-lab-order-summary__tools { grid-column: 2; grid-row: 1; }
        .hc4-lab-order-detail > .hc4-lab-order-detail__header {
            grid-template-columns: 1fr 1fr;
        }
        .hc4-lab-order-detail__title {
            grid-column: 1 / -1;
            grid-row: 1;
        }
        .hc4-lab-order-detail__action { grid-column: 1; grid-row: 2; }
        .hc4-lab-order-detail__action--right { grid-column: 2; grid-row: 2; }
        .hc4-lab-order-view__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .hc4-lab-order-view__group--wide .hc4-lab-order-view__items { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 560px) {
        .hc4-lab-order-card > .hc4-lab-order-summary,
        .hc4-lab-order-detail > .hc4-lab-order-detail__header {
            display: flex !important;
            flex-direction: column;
            align-items: stretch;
        }
        .hc4-lab-order-summary__doctor,
        .hc4-lab-order-detail__title { text-align: left; }
        .hc4-lab-order-summary__tools { justify-content: flex-start; }
        .hc4-lab-order-detail__action--right { text-align: left; }
        #area_trabajo .hc4-lab-order-detail .hc4-lab-order-detail__button { width: 100% !important; }
        .hc4-lab-order-view__grid,
        .hc4-lab-order-view__group--wide .hc4-lab-order-view__items { grid-template-columns: 1fr; }
    }
</style>

@foreach($ordenes as $orden)
	<div class="col-md-12">
	    @php
			$xdoctor = null;
			if(!is_null($orden->fecha_orden)){
				$fecha_r = Date('Y-m-d', strtotime($orden->fecha_orden));
			}else{
				$fecha_r = Date('Y-m-d', strtotime($orden->created_at));
			}
			$ordenColapsada = $fecha_r != date('Y-m-d');

			if($orden->id_doctor_ieced != ""){
				$xdoctor = DB::table('users as us')->where('us.id', $orden->id_doctor_ieced)->first();
			}

   	  	@endphp
   	  	@php
            $fecha = substr($orden->fecha_orden,0,10);
            $invert = explode( '-',$fecha);
            $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];
		@endphp

        <div class="box hc4-lab-order-card @if($ordenColapsada) collapsed-box @endif">
		<div class="hc4-lab-order-summary">
			<div class="hc4-lab-order-summary__date">
                            @if(!is_null($orden->fecha_orden))
                                @php
		                         $dia =  Date('N',strtotime($orden->fecha_orden));
		                         $mes =  Date('n',strtotime($orden->fecha_orden));
		                        @endphp

		                        <b>
		                        @if($dia == '1') @lang('hc4.monday')
                                 @elseif($dia == '2') @lang('hc4.tuesday')
                                 @elseif($dia == '3') @lang('hc4.wednesday')
                                 @elseif($dia == '4') @lang('hc4.thursday')
                                 @elseif($dia == '5') @lang('hc4.friday')
                                 @elseif($dia == '6') @lang('hc4.saturday')
                                 @elseif($dia == '7') @lang('hc4.sunday')
                                @endif
                                 {{substr($orden->fecha_orden,8,2)}} @lang('hc4.of')
                                @if($mes == '1') @lang('hc4.january')
	                                 @elseif($mes == '2') @lang('hc4.february')
	                                 @elseif($mes == '3') @lang('hc4.march')
	                                 @elseif($mes == '4') @lang('hc4.april')
	                                 @elseif($mes == '5') @lang('hc4.may')
	                                 @elseif($mes == '6') @lang('hc4.june')
	                                 @elseif($mes == '7') @lang('hc4.july')
	                                 @elseif($mes == '8') @lang('hc4.august')
	                                 @elseif($mes == '9') @lang('hc4.september')
	                                 @elseif($mes == '10') @lang('hc4.october')
	                                 @elseif($mes == '11') @lang('hc4.november')
	                                 @elseif($mes == '12') @lang('hc4.december')
                                @endif
                                @lang('hc4.of') {{substr($orden->fecha_orden,0,4)}}</b>
                            @endif
                    </div>
                    <div class="hc4-lab-order-summary__doctor">
						<strong>Dr (a):</strong>
						@if(!is_null($xdoctor) && !is_null($xdoctor->nombre1))
							{{$xdoctor->nombre1}} {{$xdoctor->apellido1}}
						@else
							<span>No asignado</span>
						@endif
                    </div>
                    <div class="hc4-lab-order-summary__tools">
						@if(!is_null($orden->id))
							<span class="hc4-lab-order-summary__id">Orden #{{$orden->id}}</span>
						@endif
						<button type="button" class="btn btn-sm hc4-lab-order-summary__toggle" data-widget="collapse" title="Mostrar u ocultar orden" aria-expanded="{{$ordenColapsada ? 'false' : 'true'}}">
                            <i class="fa {{$ordenColapsada ? 'fa-plus' : 'fa-minus'}}"></i>
						</button>
					</div>

		</div>
		<div class="box-body" style="background: white;">
			<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
	                <div class="box hc4-lab-order-detail">

	                    <div class="hc4-lab-order-detail__header">
						<div class="hc4-lab-order-detail__action">
							<button type="button" class="btn hc4-lab-order-detail__button" onclick="editar_orden_lab({{$orden->id}},{{$orden->estado}});">
								<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								<span>@lang('hc4.editar')</span>
							</button>
						</div>
						<div class="hc4-lab-order-detail__title">
							@lang('hc4.detalleOrden') {{optional($orden->seguro)->nombre ?? 'PARTICULAR'}}
						</div>
						<div class="hc4-lab-order-detail__action hc4-lab-order-detail__action--right">
							<a class="btn hc4-lab-order-detail__button" @if(optional($orden->seguro)->tipo=='0') href="{{url('orden/descargar')}}/{{$orden->id}}" @else href="{{url('cotizador_p/orden/imprimir')}}/{{$orden->id}}" @endif target="_blank">
								<i class="glyphicon glyphicon-download-alt" aria-hidden="true"></i>
								<span>@lang('hc4.descargarOrden')</span>
							</a>
							@if(optional($orden->seguro)->tipo=='0')
								<a class="btn hc4-lab-order-detail__button" href="{{route('formulario.10A',['id' => $orden->id])}}" target="_blank">
									<i class="glyphicon glyphicon-download-alt" aria-hidden="true"></i>
									<span>@lang('hc4.ordenNuevoFormato')</span>
								</a>
							@endif
						</div>
					</div>
		                <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="xorden{{$orden->id}}">
			   	  			<div class="col-md-12" style="padding: 1px;">
			   	  			    <div class="row">
			   	  			    	<div class="col-md-8">
					                    @if(!is_null($fecha_invert))
					                      <span style="font-family: 'Helvetica general';font-size: 12px">@lang('hc4.fecha'):</span>
					                      <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
					                      </label>
					                    @endif
			                        </div>
			                    </div>
			                </div>
			                <div class="col-md-12" style="padding: 1px;">
			                	<div class="row">
			                	    <div class="col-md-8">
				   	  					<div>
				   	  						@if(!is_null($xdoctor))
												<span style="font-family: 'Helvetica general'; font-size: 12px">@lang('hc4.doctorSolicitante'):</span>
												<label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$xdoctor->apellido1}} {{$xdoctor->apellido2}}
												      {{$xdoctor->nombre1}} {{$xdoctor->nombre2}}
			                                      </b>
			                                    </label>
			                                @endif
								        </div>
			                        </div>
			                        <div class="col-md-4">
			                        	@if(!is_null($orden->seguro))
							                <span style="font-family: 'Helvetica general';font-size: 12px">@lang('hc4.seguro'):</span>
						                    <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
						                      <b>
						                        {{$orden->seguro->nombre}}
						                      </b>
						                    </label>
					                    @endif
			                        </div>
			                    </div>
			                </div>
			                <div class="col-12" style="padding-top: 5px"></div>
			                <div class="col-12" style="padding-top: 5px">
                            @php
                                $paletaAgrupadores = [
                                    ['fondo' => '#dbeafe', 'texto' => '#1e3a5f'],
                                    ['fondo' => '#dcfce7', 'texto' => '#285943'],
                                    ['fondo' => '#fef3c7', 'texto' => '#6b4f16'],
                                    ['fondo' => '#fce7f3', 'texto' => '#6b3150'],
                                    ['fondo' => '#ede9fe', 'texto' => '#493d72'],
                                    ['fondo' => '#cffafe', 'texto' => '#245766'],
                                    ['fondo' => '#ffedd5', 'texto' => '#6b4630'],
                                    ['fondo' => '#e2e8f0', 'texto' => '#34445a'],
                                ];
                                $examenes_labs= array();
			                		if($orden->seguro->tipo=='0'){
										$detalles = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->get();
			                		}else{


										$detalles = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)
											->join('examen as e','e.id','examen_detalle.id_examen')
											->orderBy('e.id_agrupador')
											->get();

										$examenes_labs = DB::table('examen_detalle as ed')
											->where('id_examen_orden',$orden->id)
											->join('examen as e','e.id','ed.id_examen')
											->join('examen_agrupador as agrupador','agrupador.id','e.id_agrupador')
											->select('e.id_agrupador','e.descripcion','e.nombre','e.valor','e.id as ex_id','agrupador.nombre as lnombre')
											->orderBy('e.id_agrupador')
											->orderBy('e.descripcion')
											->get();
                                }

                                $gruposVisualizacion = collect($examenes_labs)
                                    ->groupBy('id_agrupador')
                                    ->map(function ($examenesGrupo) use ($paletaAgrupadores) {
                                        $primero = $examenesGrupo->first();
                                        $nombreNormalizado = strtoupper(\Illuminate\Support\Str::ascii(trim((string) $primero->lnombre)));
                                        $indiceColor = abs((int) $primero->id_agrupador) % count($paletaAgrupadores);

                                        return [
                                            'id' => $primero->id_agrupador,
                                            'nombre' => $primero->lnombre,
                                            'otros' => $nombreNormalizado === 'OTROS',
                                            'cantidad' => $examenesGrupo->count(),
                                            'extenso' => $examenesGrupo->count() > 12,
                                            'color' => $paletaAgrupadores[$indiceColor],
                                            'examenes' => $examenesGrupo->sortBy('descripcion')->values(),
                                        ];
                                    })
                                    ->sort(function ($grupoA, $grupoB) {
                                        if ($grupoA['otros'] !== $grupoB['otros']) {
                                            return $grupoA['otros'] ? 1 : -1;
                                        }
                                        if ($grupoA['cantidad'] !== $grupoB['cantidad']) {
                                            return $grupoA['cantidad'] <=> $grupoB['cantidad'];
                                        }
                                        return strcasecmp((string) $grupoA['nombre'], (string) $grupoB['nombre']);
                                    })
                                    ->values();
                            @endphp

								@if($orden->seguro->tipo=='0')

								@else
									<div class="hc4-lab-order-view">
                                        <div class="hc4-lab-order-view__count">
                                            @lang('hc4.cantidad'): {{$orden->cantidad}}
                                        </div>
                                        <div class="hc4-lab-order-view__grid">
                                            @foreach($gruposVisualizacion as $grupo)
                                                <section class="hc4-lab-order-view__group @if($grupo['extenso']) hc4-lab-order-view__group--wide @endif"
                                                         style="--group-bg:{{$grupo['color']['fondo']}};--group-text:{{$grupo['color']['texto']}};">
                                                    <h4 class="hc4-lab-order-view__group-title">{{$grupo['nombre']}}</h4>
                                                    <div class="hc4-lab-order-view__items">
                                                        @foreach($grupo['examenes'] as $examen)
                                                            <div class="hc4-lab-order-view__exam">{{$examen->descripcion ?: $examen->nombre}}</div>
                                                        @endforeach
                                                    </div>
                                                </section>
                                            @endforeach
                                        </div>
									</div>
								@endif

			                </div>

				</div>
	             	</div>
            	</div>
   	  		</div>
   	    </div>
	</div>
@endforeach

<script type="text/javascript">

	//Funcion Editar Orden de Procedimiento Endoscopico
	@if($editar!='0')
		editar_orden_lab({{$editar}},'-1');
	@endif

    function editar_orden_lab(id_orden,estado){

    	if(estado=='-1'){
    		$.ajax({
				type: "GET",
				url: "{{url('hc4/orden/laboratorio/doctor/editar')}}/"+id_orden,
				data: "",
				datatype: "html",
				success: function(datahtml){
				   $("#xorden"+id_orden).html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
    	}else{

			alert({!! json_encode(trans('hc4.ordenProcesoNoModificar')) !!});

		}
    }

</script>
