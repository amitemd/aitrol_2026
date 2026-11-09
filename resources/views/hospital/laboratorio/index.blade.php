@extends('layouts.app-template-h')
@section('content')



@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style type="text/css">
  .laboratory-page {
    --lab-navy: #073b66;
    --lab-blue: #0878ba;
    --lab-cyan: #17a9bd;
    --lab-purple: #7457b8;
    --lab-ink: #17344e;
    --lab-muted: #6a8092;
    --lab-line: #d6e6ee;
    color: var(--lab-ink);
  }

  .laboratory-page, .laboratory-page * { box-sizing: border-box; }

  .laboratory-heading {
    position: relative;
    display: flex;
    min-height: 116px;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    overflow: hidden;
    padding: 1.55rem 1.75rem;
    border-radius: 18px;
    background: linear-gradient(118deg, #073b66, #0878ba 60%, #1598a2);
    box-shadow: 0 15px 36px rgba(7,59,102,.14);
    color: #fff;
  }

  .laboratory-heading::after {
    content: '';
    position: absolute;
    width: 210px;
    height: 210px;
    right: -65px;
    top: -120px;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 50%;
  }

  .laboratory-heading-main {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .laboratory-heading-icon {
    display: inline-flex;
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 16px;
    background: rgba(255,255,255,.14);
    font-size: 1.65rem;
  }

  .laboratory-heading-label {
    display: block;
    margin-bottom: .25rem;
    color: #bceaf0;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .11em;
    text-transform: uppercase;
  }

  .laboratory-heading h1 { margin: 0; color: #fff; font-size: 1.55rem; font-weight: 800; }
  .laboratory-heading p { margin: .35rem 0 0; color: rgba(255,255,255,.82); font-size: .8rem; }

  .laboratory-heading-count {
    position: relative;
    z-index: 1;
    min-width: 135px;
    padding: .8rem 1rem;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 13px;
    background: rgba(255,255,255,.12);
    text-align: center;
  }

  .laboratory-heading-count strong { display: block; color: #fff; font-size: 1.45rem; line-height: 1; }
  .laboratory-heading-count span { display: block; margin-top: .35rem; color: #d2f0f2; font-size: .65rem; font-weight: 700; text-transform: uppercase; }

  .laboratory-card {
    margin-top: 1.15rem;
    overflow: hidden;
    border: 1px solid var(--lab-line) !important;
    border-radius: 16px !important;
    background: #fff;
    box-shadow: 0 10px 28px rgba(7,59,102,.07) !important;
  }

  .laboratory-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--lab-line);
    background: linear-gradient(100deg,#f8fcfe,#eaf7fa);
  }

  .laboratory-card-title { display: flex; align-items: center; gap: .75rem; }
  .laboratory-card-title-icon {
    display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center;
    border-radius: 10px; background: #dff2f7; color: var(--lab-blue); font-size: .95rem;
  }
  .laboratory-card-title h2 { margin: 0; color: var(--lab-navy); font-size: 1.02rem; font-weight: 800; }
  .laboratory-card-title p { margin: .15rem 0 0; color: var(--lab-muted); font-size: .72rem; }
  .laboratory-card-tool {
    display: inline-flex; width: 34px; height: 34px; align-items: center; justify-content: center;
    padding: 0 !important; border: 1px solid var(--lab-line) !important; border-radius: 9px !important;
    background: #fff !important; color: var(--lab-muted) !important;
  }

  .laboratory-filter { padding: 1.2rem 1.25rem; background: #fbfdfe; }
  .laboratory-filter-grid {
    display: grid;
    grid-template-columns: repeat(2,minmax(150px,.85fr)) minmax(190px,.9fr) minmax(240px,1.2fr) auto;
    gap: 1rem;
    align-items: end;
  }
  .laboratory-field { min-width: 0; margin: 0 !important; }
  .laboratory-page .laboratory-field label {
    display: block; margin: 0 0 .45rem; color: var(--lab-ink); font-size: .72rem; font-weight: 800;
  }
  .laboratory-input-wrap { position: relative; }
  .laboratory-input-wrap > i {
    position: absolute; z-index: 2; left: .8rem; top: 50%; color: #88a0b2;
    font-size: .82rem; transform: translateY(-50%); pointer-events: none;
  }
  .laboratory-page .laboratory-input-wrap .form-control {
    width: 100%; height: 42px; padding: .55rem .75rem .55rem 2.25rem;
    border: 1px solid #ccdde7; border-radius: 9px; background: #fff; color: var(--lab-ink);
    font-size: .8rem; box-shadow: none;
  }
  .laboratory-page .laboratory-input-wrap .form-control:focus {
    border-color: var(--lab-cyan); box-shadow: 0 0 0 3px rgba(23,169,189,.13);
  }
  .laboratory-search-btn {
    display: inline-flex; min-width: 118px; min-height: 42px; align-items: center; justify-content: center;
    gap: .5rem; padding: .65rem 1rem !important; border: 1px solid var(--lab-blue) !important;
    border-radius: 9px !important; background: var(--lab-blue) !important; color: #fff !important;
    font-size: .76rem !important; font-weight: 800 !important;
  }

  .laboratory-results-body { padding: 1rem !important; }
  .laboratory-table-wrap { overflow-x: auto; border: 1px solid var(--lab-line); border-radius: 11px; }
  .laboratory-page .laboratory-table { min-width: 1180px; margin: 0; }
  .laboratory-page .laboratory-table thead th {
    padding: .85rem .7rem; border: 0; border-bottom: 1px solid var(--lab-line);
    background: #edf7fa; color: var(--lab-navy); font-size: .65rem; font-weight: 800;
    letter-spacing: .035em; text-transform: uppercase; white-space: nowrap;
  }
  .laboratory-page .laboratory-table tbody td {
    padding: .8rem .7rem !important; border-color: #e7eff3; color: var(--lab-ink);
    font-size: .72rem !important; vertical-align: middle;
  }
  .laboratory-page .laboratory-table tbody tr:hover { background: #f7fbfd; }
  .laboratory-page .label { display: inline-flex; margin: 2px; padding: .35rem .5rem; border-radius: 999px; font-size: .62rem !important; }
  .laboratory-page .progress { overflow: hidden; min-width: 85px; border-radius: 999px; background: #e7eef2; }
  .laboratory-page .progress-bar { border-radius: 999px; }

  .laboratory-empty-state { padding: 3.25rem 1rem !important; text-align: center; }
  .laboratory-empty-icon {
    display: inline-flex; width: 62px; height: 62px; align-items: center; justify-content: center;
    margin-bottom: .85rem; border-radius: 17px; background: #e5f4f8; color: var(--lab-blue); font-size: 1.65rem;
  }
  .laboratory-empty-state strong { display: block; color: var(--lab-navy); font-size: .9rem; }
  .laboratory-empty-state small { display: block; margin-top: .35rem; color: var(--lab-muted); font-size: .72rem; }

  .laboratory-pagination {
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    padding: .85rem .2rem 0; color: var(--lab-muted); font-size: .72rem;
  }
  .laboratory-pagination .pagination { margin: 0; }
  .laboratory-page .hovers:hover { cursor: pointer; }
  .laboratory-page .cot > li > a:hover { background-color: #00acd6 !important; }

  @media (max-width: 1100px) {
    .laboratory-filter-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .laboratory-filter-grid .laboratory-field:last-child { grid-column: span 2; }
    .laboratory-search-btn { width: 100%; }
  }
  @media (max-width: 600px) {
    .laboratory-heading { align-items: flex-start; padding: 1.25rem; }
    .laboratory-heading-count { display: none; }
    .laboratory-heading-icon { width: 48px; height: 48px; flex-basis: 48px; }
    .laboratory-heading h1 { font-size: 1.25rem; }
    .laboratory-filter-grid { grid-template-columns: 1fr; }
    .laboratory-filter-grid .laboratory-field:last-child { grid-column: auto; }
    .laboratory-pagination { align-items: flex-start; flex-direction: column; }
  }
</style>

<main class="content laboratory-page">
  <section class="laboratory-heading" aria-labelledby="laboratory-page-title">
    <div class="laboratory-heading-main">
      <span class="laboratory-heading-icon"><i class="fa fa-flask" aria-hidden="true"></i></span>
      <div>
        <span class="laboratory-heading-label">Diagnóstico clínico</span>
        <h1 id="laboratory-page-title">{{trans('translab.OrdenesdeLaboratorio')}}</h1>
        <p>Consulta las órdenes, revisa su avance y accede a los resultados disponibles.</p>
      </div>
    </div>
    <div class="laboratory-heading-count">
      <strong>{{ $ordenes->total() }}</strong>
      <span>órdenes encontradas</span>
    </div>
  </section>

  <div class="card laboratory-card">
    <div class="laboratory-card-header">
      <div class="laboratory-card-title">
        <span class="laboratory-card-title-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
        <div>
          <h2>{{trans('translab.ListadodeOrdenesdeLaboratorio')}}</h2>
          <p>Utiliza uno o varios criterios para localizar las órdenes.</p>
        </div>
      </div>
      <button type="button" class="btn btn-card-tool laboratory-card-tool" data-widget="collapse" aria-label="Contraer filtros"><i class="fa fa-minus"></i></button>
    </div>
    <div class="card-body laboratory-filter">
      <form method="POST" action="{{route('hospital_laboratorio.index')}}">
        {{ csrf_field() }}
        <div class="laboratory-filter-grid">
          
            <div class="form-group laboratory-field">
              <label for="fecha">{{trans('translab.Desde')}}</label>
              <div class="input-group date laboratory-input-wrap">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <input type="text"  data-input="true" class="form-control input-xs flatpickr-basic active" name="fecha" id="fecha" value="{{$fecha}}" autocomplete="off">
              </div>
            </div>
            <div class="form-group laboratory-field">
              <label for="fecha_hasta">{{trans('translab.Hasta')}}</label>
              <div class="input-group date laboratory-input-wrap">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <input type="text"  data-input="true" class="form-control input-xs flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}" autocomplete="off">
              </div>
            </div>
            <div class="form-group laboratory-field">
              <label for="seguro">{{trans('translab.Seguro')}}</label>
              <div class="laboratory-input-wrap">
                <i class="fa fa-shield" aria-hidden="true"></i>
                <select class="form-control form-control-sm" id="seguro" name="seguro">
                  <option value="">{{trans('translab.TODOS')}}</option>
                  @foreach ($seguros as $value)
                    <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group laboratory-field">
              <label for="nombres">{{trans('translab.Paciente')}}</label>
              <div class="laboratory-input-wrap">
                <i class="fa fa-user-o" aria-hidden="true"></i>
                <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" placeholder="{{trans('translab.NombresApellidos')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="{{$nombres}}">
              </div>
            </div>
            <div class="form-group laboratory-field">
              <button type="submit" class="btn btn-primary btn-xs laboratory-search-btn" id="boton_buscar">
                <i class="fa fa-search" aria-hidden="true"></i>
                {{trans('translab.Buscar')}}
              </button>
            </div>  
        </div>
       </form>  
    </div>  
  </div>  
  <div class="card laboratory-card">
    <div class="laboratory-card-header">
      <div class="laboratory-card-title">
        <span class="laboratory-card-title-icon"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
        <div>
          <h2>Resultados de la consulta</h2>
          <p>Seguimiento de muestras, valores y disponibilidad de resultados.</p>
        </div>
      </div>
      <span class="badge badge-light">{{ $ordenes->total() }} registros</span>
    </div>
    <div class="card-body laboratory-results-body">
      <div id="muestra" class="laboratory-table-wrap">
        <table class="table table-hover laboratory-table">
          <thead class="table-primary">
            <tr >
              <th width="5">{{trans('translab.Id')}}</th>
              <th width="5">{{trans('translab.Fecha')}}</th>
              <th width="5">{{trans('translab.Muestra')}}</th>
              <th width="10">{{trans('translab.Nombres')}}</th>
              <th width="5">{{trans('translab.Convenio')}}</th>
              <th width="5">{{trans('translab.Tipo')}}</th>
              <th width="5">{{trans('translab.Creada')}}</th>
              <th width="5">{{trans('translab.Modific.')}}</th>
              <th width="5">{{trans('translab.Cant.')}}</th>
              <th width="5">{{trans('translab.Valor')}}</th>
              <th width="5">{{trans('translab.Resultados(%)')}}</th>
              <th width="10">{{trans('translab.Resultados')}}</th> 
              <!--th width="20">Compro.</th>                
              <th width="10">Acción</th-->
            </tr>
          </thead>
          <tbody>
            @php
                $agrup2 = session()->get('a_orden'); 

              @endphp
              @foreach ($ordenes as $value)
                @php 
                 $user = Sis_medico\User::find($value->id_usuariocrea);
                 $pac = DB::table('paciente')->where('id',$value->id_paciente)->first();
                @endphp
                <tr role="row">
                  <td>   
                    {{$value->id}}
                  </td>
                  <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
                  <td>{{$value->toma_muestra}}</td>
                  <td style="font-size: 11px;">{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                  <td style="font-size: 11px;">{{$value->snombre}} / {{$value->nombre_corto}}</td>
                  <td>
                    @if($value->estado=='-1')
                      @if($value->stipo!='0')
                        <span class="label pull-right bg-red" style="font-size: 10px">{{trans('translab.CotizacionAbrev')}}</span>
                      @else
                        <span class="label pull-right bg-red" style="font-size: 10px">{{$value->pre_post}}-{{trans('translab.Pendiente')}}</span>
                      @endif 
                    @else  
                      @if($value->estado_pago =='1')
                        <span class="label pull-right bg-green" style="font-size: 10px">{{trans('translab.PagadoAbrev')}} @if($value->pago_online=='1'){{trans('translab.Online')}} @endif</span>
                      @endif
                      @if($value->pre_post!=null)
                        <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->pre_post}}</span>
                      @endif
                    @endif
                  </td>
                  <td style="font-size: 11px;@if($user->id_tipo_usuario =='3') color: red; @endif">{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
                  <td style="font-size: 11px;">{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                  <td>{{$value->cantidad}}</td>
                  <td>{{$value->total_valor}}</td>
                  <td>
                    <p class="card-text mb-50" id="sp{{$value->id}}"> </p>
                    <div class="progress mt-25" style="height: 6px;">
                      <div role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="70" class="" style="width: 70%;"  id="td{{$value->id}}">
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($value->estado=='1')
                    <div class="col-md-12" style="padding-left: 0px">
                      <button type="button" class="btn btn-success btn-sm" id="result{{$value->id}}" onclick="descargar({{$value->id}});">    <span style=" font-size: 10px">{{trans('translab.Resultados')}}</span>
                      </button>
                    </div>
                    @endif  
                  </td>
                  <!--td>
                    @if($value->stipo!='0')
                      <div class="col-md-12" style="padding-left: 0px"> 
                        <div class="btn-group" >
                            <button type="button" class="btn btn-info btn-xs" 
                            onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span style=" font-size: 9px">COTIZ</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Formato Gastro</span></a></li>
                              <li ><a  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Orden</span></a></li>
                              <li ><a  href="{{ route('pdf_tributario', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Comprobante</span></a></li>
                              <li ><a  href="{{ route('pdf_cotizacion', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Recibo de Cobro</span></a></li>
                               <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                    @else
                    <div class="col-md-12" style="padding-left: 0px"> 
                        <div class="btn-group" >
                            <button type="button" class="btn btn-info btn-xs"
                            onclick="window.open('{{ route('orden.descargar', ['id' => $value->id]) }}','_blank');"
                            ><span style=" font-size: 9px">Orden</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                    
                      @php 
                        $agendas = Sis_medico\Examen_Orden_Agenda::where('id_orden',$value->id)->get();
                      @endphp 
                      @if($agendas->count()=='1') 
                      <div class="col-md-3" style="padding-left: 0px">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('agenda.edit2', ['agenda' => $agendas->first()->id_agenda, 'doctor' => '4444444444']) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-calendar"></span>
                        </a>   
                      </div>
                      @endif
                        
                    @endif   
                  </td-->
                  <!--td>
                    
                    @if($value->stipo!='0')
                      @if($value->realizado=='0' && $value->estado_pago =='0')
                      <div class="col-md-3" style="padding: 3px;">
                        
                        <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                        
                        <span class="glyphicon glyphicon-edit"></span> 
                        </a>  
                      </div>
                      @endif  

                    @else
                      @if($value->realizado=='0')
                        <div class="col-md-3" style="padding: 3px;">
                          
                          <a href="{{ route('orden.edit', ['orden' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                          <span class="glyphicon glyphicon-edit"></span>
                          </a>  
                        </div>
                      @endif  
                        
                    @endif 
                    @if($value->realizado=='0' && $value->estado_pago =='0')
                        <div class="col-md-2" style="padding: 3px;">
                          
                          <form method="POST" action="{{ route('orden.eliminar',['id' => $value->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-block btn-danger btn-xs" onclick="return confirm('¿Eliminar esta orden?');">
                              <span class="glyphicon glyphicon-trash"></span>
                            </button>
                          </form>
                        </div>
                    @endif
                   
                    @if($value->stipo!='0')
                      @if($value->estado=='1')
                        @if($value->estado_pago =='0')
                          <div class="col-md-3" style="padding: 1px;">
                            <a data-toggle="modal" data-target="#pago" data-remote="{{route('modal.pago_paciente', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-danger btn-xs">Pago
                            </a>
                          </div>
                        @endif
                      @endif
                    @endif
                    
                    @if(in_array($rolUsuario, array(1)) == true)
                      @if($value->estado_pago =='1')
                        @if($pac->id == $pac->id_usuario)
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reseteo_clave" data-remote="{{route('paciente_reseteo_clave', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span ></span>R
                          </a>  
                        </div>
                        @endif 
                      @endif
                    @endif
                    @if($value->estado_pago =='1')
                      <div class="col-md-3" style="padding: 3px;">
                        <a data-toggle="modal" data-target="#reenvio_email" data-remote="{{route('paciente_reenvio_email', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-warning  btn-xs">
                          <span class="glyphicon glyphicon-envelope"></span>
                        </a>
                      </div>
                    @endif
                    
                    @if($value->stipo!='0')
                      @if($value->estado=='1')
                        
                          @if($value->fecha_envio!=null)
                            <div class="col-md-4 ">
                              <a class="btn btn-primary btn-xs" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->comprobante, 'id_empresa' => '0993075000001', 'tipo' => 'pdf']) }}">RIDE</a>
                            </div>
                          @else
                          @if($value->fecha_orden < '2021-06-01  00:00:00')
                          <div class="col-md-4 ">
                            <button class="btn btn-danger btn-xs" onclick="emitir_sri('{{$value->id}}')">SRI</button>
                          </div> 
                          @endif
                           
                          @endif
                        
                      @endif
                    @endif
                  </td-->          
                </tr>
              @endforeach
              @if($ordenes->count() === 0)
                <tr>
                  <td colspan="12" class="laboratory-empty-state">
                    <span class="laboratory-empty-icon"><i class="fa fa-flask" aria-hidden="true"></i></span>
                    <strong>No se encontraron órdenes de laboratorio</strong>
                    <small>Modifica el periodo o los criterios de búsqueda e inténtalo nuevamente.</small>
                  </td>
                </tr>
              @endif

          </tbody>
        </table>
      </div>
      <div class="laboratory-pagination">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
            {{trans('translab.Mostrando')}} {{ $ordenes->firstItem() ?: 0 }} / {{ $ordenes->lastItem() ?: 0 }} {{trans('translab.de')}} {{$ordenes->total()}} {{trans('translab.registros')}}
          </div>
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro', 'facturadas']))->links()}}
          </div>
      </div>

    </div>  
  </div>
</main>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">

  $('#pago').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reenvio_email').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#pago_online').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#gestionar_orden').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#modal_agrupada').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });


  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id_orden' => $value->id]) }}",
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar bg-danger");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar bg-warning");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar bg-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha_hasta}}',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">{{trans('translab.Ordenes')}}</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 
   $('#modal_datosfacturas').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("{{trans('translab.SinExamenesIngresados')}}");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }
  function emitir_sri(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/info_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 
  function sendInvoice(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('contable/cierre_caja/recibo/')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datosrecibo').empty().html(data);
        $('#modalrecibo').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });  
  }

  function ver_pendientes(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('examenes_pendientes')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        
        $('#div_examenes_pendientes').empty().html(data);
        $('#modal_examenes').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 

  function factura_agrupada(){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/modal_factura_agrupada')}}",
      datatype: 'json',
      success: function(data){
        $('#datos_agrupada').empty().html(data);
        $('#modal_agrupada').modal();
      },
      error: function(data){
        //console.log(data);
      }
    }); 

  }

  function eliminar(){
      //alert("hola");
      $.ajax({
            type: 'post',
            url: "{{ url('facturacion_labs/factura_agrup/eliminar_sesion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
      success: function(data){
          //alert("sucess");
          if(data=='ok'){
            factura_agrupada();
          };
            
      },
      error:  function(){
        alert("{{trans('translab.ErrorCargar')}}");
      }
    });
  }

  function guarda_sesion_factura(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn"+id_orden).css("background-color", "green"); 
        }else{
          alert("{{trans('translab.NoAgregarOrden')}}");
        }   
      },
      error: function(data){
        alert("{{trans('translab.ErrorAgregarOrden')}}");
      }
    }); 
  }
  
  function guarda_sesion_factura_contab(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura/contabilidad')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn_contab"+id_orden).css("background-color", "green"); 
        }  
      },
      error: function(data){
        alert("{{trans('translab.ErrorAgregarOrden')}}");
      }
    }); 
  }

  function datos_factura_agrupada(){
      //alert(cuenta);
      $.ajax({
            type: 'get',
            url: "{{ url('facturacion_labs/datos_factura_agrupada')}}",
            datatype: 'json',
      success: function(datahtml){
          //alert("sucess");
          $("#datos_factura_agrup").empty().html(datahtml);
            
      },
      error:  function(){
        alert("{{trans('translab.ErrorCargar')}}");
      }
    });
    }
  
</script>  

@endsection
