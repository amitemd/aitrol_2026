<div class="form-group col-md-12">
        <div class="form-row">

          <div id="resultados">
          </div>
          <div id="contenedor">
            <form class="form-vertical" method="POST" id="form" action="{{route('ventas_viewOmni')}}">
              {{ csrf_field() }}
              <input type="hidden" id="tipo_fact" name="tipo_fact" value="1">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cita</th>
                          <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Paciente</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Doctor</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimientos</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hospital</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro</th>
                          <th><input type="checkbox" id="allItems" /></th>
                          <th>Facturado</th>

                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($procedimientos as $value)
                            @php
                            $verificar_insumo = DB::table('ct_factura_omni')->where('tipo_factura','1')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            $verificar_equipo= DB::table('ct_factura_omni')->where('tipo_factura','2')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            $verificar_honorarios= DB::table('ct_factura_omni')->where('tipo_factura','3')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            @endphp
                            <tr>
                              <td>{{date('d-m-Y',strtotime($value->fechaini))}}</td>

                              <td> {{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}} </td>
                              <td>Dr. {{$value->doctor1->nombre1}} {{$value->doctor1->apellido1}}</td>
                              <td>
                                @if(isset($value->historia_clinica))
                                @if(isset($value->historia_clinica->procedimiento))
                                @foreach($value->historia_clinica->procedimiento as $ps)
                                @if(isset($ps->hc_procedimiento_final->procedimiento))
                                {{$ps->hc_procedimiento_final->procedimiento->nombre}}+
                                @endif
                                @endforeach
                                @endif
                                @endif

                              </td>
                              <td> @if($value->omni!=null) @if($value->omni=="OM") SI  @else {{$value->omni}} @endif @elseif($value->proc_consul==4) SI @else NO @endif</td>
                              <td>
                                @if($value->proc_consul==0)
                                CONSULTA
                                @elseif($value->proc_consul==1)
                                PROCEDIMIENTO
                                @elseif($value->proc_consul==3)
                                HOSPITALIZADOS
                                @elseif($value->proc_consul==4)
                                VISITAS
                                @endif
                              </td>
                              @php 
                                $s= DB::table('seguros')->where('id',$value->seguro_final)->first();
                              @endphp
                              <td>@if(isset($s)){{$s->nombre}} @endif</td>
                              <td> <input type="checkbox" class="form-col facturar" @if($verificar_equipo!=null && $verificar_insumo!=null) disabled @endif />
                                <input type="hidden" class="facturars" value="{{$value->id}}" />
                                <input type="hidden" class="pacientes" value="{{$value->paciente->id}}">
                              </td>
                              <td>

                                @if(!is_null($verificar_insumo))

                                @php
                                $ventas= DB::table('ct_ventas')->where('id',$verificar_insumo->id_ct_ventas)->first();
                                @endphp

                                <!--<div class="col-md-12">
                              if u want whatever icon 
                              
                              <i class="fa fa-medkit mr-1 green" aria-hidden="true"></i>
                              </div>-->
                                <div class="col-md-12" style="background-color: salmon; color: white;">
                                  <span> {{$ventas->nro_comprobante}} </span>

                                </div>

                                @endif
                                @if(!is_null($verificar_equipo))
                                @php
                                //dd($verificar_equipo);
                                $ventas2= DB::table('ct_ventas')->where('id',$verificar_equipo->id_ct_ventas)->first();
                                @endphp
                                  <div class="col-md-12" style=" background-color:seagreen; color: white;">
                                    <span> {{$ventas2->nro_comprobante}} </span>

                                  </div>
                                @endif

                                @if(!is_null($verificar_honorarios))
                                @php
                                //dd($verificar_equipo);
                                $ventas2= DB::table('ct_ventas')->where('id',$verificar_honorarios->id_ct_ventas)->first();
                                @endphp
                                  <div class="col-md-12" style="background-color:slateblue; color: white;">
                                    <span> {{$ventas2->nro_comprobante}} </span>

                                  </div>
                                @endif


                              </td>
                            </tr>

                            @endforeach

                      </tbody>
                      <tfoot>
                      </tfoot>
                    </table>

                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{sizeof($procedimientos)}} registros</div>
                  </div>

                </div>

                <!--
                  <div class="col-md-3">
                    <button type="button" class="btn btn-default btn-gray btn_fact3">
                      <i class="fa fa-user-md" aria-hidden="true"></i>&nbsp;&nbsp;Honorarios Medicos
                    </button>
                  </div>-->

            </form>
          </div>
          <div class="row">
          </div>
        </div>
      </div>