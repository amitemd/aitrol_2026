@extends('layouts.app-template-h')
@section('content')



@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th {
      padding: 1px;
  } 
  .dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
  .form-group{
    margin-bottom: 2px;
  }
  .hovers:hover{
    cursor: pointer;
  }
</style>

<div class="content">
  <section class="content-header">
    <div class="row">
        <div class="col-md-9 col-sm-9">
            <h3>
              {{trans('translab.OrdenesdeLaboratorio')}}
            </h3>
        </div>
        <div class="col-3">
          
        </div>
    </div>
  </section>
  <div class="card card-primary">
    <div class="card-header with-border">
      <h3 class="card-title">{{trans('translab.ListadodeOrdenesdeLaboratorio')}}</h3>
      <div class="card-tools pull-right">
        <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{route('hospital_laboratorio.index')}}">
        {{ csrf_field() }}
        <div class="row">
          
            <div class="form-group col-md-2">
              <label class="col-form-label-sm"><b>{{trans('translab.Desde')}}</b></label>
              <!--input type="text" class="form-control form-control-sm" id="f_nacimiento" name="f_nacimiento"--> 
              <div class="input-group date">
                <input type="text"  data-input="true" class="form-control input-xs flatpickr-basic active" name="fecha" id="fecha" value="{{$fecha}}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </div>   
              </div>
            </div>
            <div class="form-group col-md-2">
              <label class="col-form-label-sm"><b>{{trans('translab.Hasta')}}</b></label>
              <!--input type="text" class="form-control form-control-sm" id="f_nacimiento" name="f_nacimiento"--> 
              <div class="input-group date">
                <input type="text"  data-input="true" class="form-control input-xs flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </div>   
              </div>
            </div>
            <div class="form-group col-md-2">
              <label class="col-form-label-sm"><b>{{trans('translab.Seguro')}}</b></label>
              <select class="form-control form-control-sm" id="seguro" name="seguro">
                <option value="">{{trans('translab.TODOS')}}</option>
                @foreach ($seguros as $value)
                  <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach  
              </select>
            </div> 
            <div class="form-group col-md-2">
              <label class="col-form-label-sm"><b>{{trans('translab.Paciente')}}</b></label>
              <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="{{$nombres}}">
            </div> 
            <div class="form-group col-md-2"><br>
              <button type="submit" class="btn btn-primary btn-xs" id="boton_buscar">
                {{trans('translab.Buscar')}}
              </button>
            </div>  
        </div>
       </form>  
    </div>  
  </div>  
  <div class="card card-primary">
   
    <div class="card-body">
      <div id="muestra" class="card-body table-responsive">       
        <table class="table table-hover">
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
                        <span class="label pull-right bg-red" style="font-size: 10px">Cotiz.</span>
                      @else
                        <span class="label pull-right bg-red" style="font-size: 10px">{{$value->pre_post}}-Pendiente</span>
                      @endif 
                    @else  
                      @if($value->estado_pago =='1')
                        <span class="label pull-right bg-green" style="font-size: 10px">Pag. @if($value->pago_online=='1')Online @endif</span>
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
                        <a href="{{ route('agenda.edit2', ['id' => $agendas->first()->id_agenda, 'doctor' => '4444444444']) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
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
                          
                          <a href="{{ route('orden.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                          <span class="glyphicon glyphicon-edit"></span>
                          </a>  
                        </div>
                      @endif  
                        
                    @endif 
                    @if($value->realizado=='0' && $value->estado_pago =='0')
                        <div class="col-md-2" style="padding: 3px;">
                          
                          <a href="{{ route('orden.eliminar',['id' => $value->id]) }}" class="btn btn-block btn-danger btn-xs" >
                          <span class="glyphicon glyphicon-trash"></span>
                          </a>  
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
            
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="col-md-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('translab.Mostrando')}} {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}}  / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} {{trans('translab.registros')}}</div>
        </div>
        <div class="col-md-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro', 'facturadas']))->links()}}
          </div>
        </div>
      </div>

    </div>  
  </div>  

  
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
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
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

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

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
      alert("Sin Exámenes Ingresados");
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
        alert('error al cargar');
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
          alert("No se puede agregar la orden, seguro o nivel diferente");
        }   
      },
      error: function(data){
        alert("Error no se pudo agregar orden");
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
        alert("Error no se pudo agregar orden");
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
        alert('error al cargar');
      }
    });
    }
  
</script>  

@endsection