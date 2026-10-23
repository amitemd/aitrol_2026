@extends('contable.rh_prestamo_utilidades.base')
@section('action-content')


<style>
    div.dataTables_wrapper {
        width: 95%;
        margin: 0 auto;
    }

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }

    .separator1 {
        width: 100%;
        height: 5px;
        clear: both;
    }

    .head-title {
        background-color: #888;
        margin-left: 0px;
        margin-right: 0px;
        height: 30px;
        line-height: 30px;
        color: #cccccc;
        text-align: center;
    }
</style>

<div class="modal fade" id="modal_liq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="modal_liq_saldos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>


<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    Guardado Correctamente
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cruces Utilidades vs Pagos
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">CRUCES UTILIDADES VS PRESTAMOS EMPLEADOS</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="calculo_otro_anticipo" action="{{route('prestamos_empleados.asientos_guardar')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                    <label for="year" class="texto col-md-2 control-label">Año:</label>
                    <div class="col-md-9">
                        <select id="year" name="year" class="form-control">
                            <option value="">Seleccione...</option>
                            @for ($i = 2019; $i <= 2030; $i++) 
                                <option @if($i==$anio) selected @endif value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                    <label for="mes" class="texto col-md-2 control-label">Mes:</label>
                    <div class="col-md-9">
                        <select id="mes" name="mes" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php
                            $Meses = array(
                                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                            );
                            ?>

                            @for ($i = 1; $i <= 12; $i++) 
                                <option @if($mes==$i) selected @endif value="{{$i}}">{{$Meses[($i) - 1]}}</option>
                            @endfor
                            
                        </select>
                    </div>
                </div>
                <!--Fecha_Creacion-->
                <div class="form-group col-xs-4">
                    <label for="fecha_creacion" class="col-md-4 texto">Fecha:</label>
                    <div class="col-md-7">
                       
                        <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_creacion" id="fecha_creacion" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_creacion').value = '';"></i>
                            </div>   
                      </div>
                    </div>
                </div>

                
                <div class="form-group col-md-2 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                    <button type="submit"  class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Guardar
                    </button>
                </div>
                <div class="form-group col-md-12">
                    <div class="form-row">
                        <div id="contenedor">
                            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                <div class="row">
                                    <div class="table-responsive col-md-12">

                                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                            <thead>
                                                <tr class='well-dark'>
                                                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id Nomina</th>
                                                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Identificacion</th>
                                                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres</th>
                                                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha de Ingreso</th>
                                                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Area</th>
                                                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cargo</th>
                                                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto Prestamo</th>
                                                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                                                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Utilidades</th>
                                                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_otros_anticipos">
                                                @php
                                                $sumaTotal = 0;
                                                $total = 0;
                                                $total_utilidades = 0;
                                                @endphp
                                                @foreach ($prestamos as $value)
                                                @php
                                                

                                                	$user = Sis_medico\User::find($value->id_user);

                                                	$prest_utili = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $value->id_empl)->where('pres_sal','1')->where('id_prestamo', $value->id)->first();

                                                    $total += $value->saldo_total; 
                                                @endphp

                                                <tr class="well">
                                                   
                                                    <td>{{$value->id_nomina}}</td>
                                                    <td>{{$value->id_empl}}</td>
                                                    <td>{{$value->nombres}}</td>
                                                    <td>{{$value->fecha_ingreso}}</td>
                                                    <td>
                                                        @if(!is_null($value->area))
	                                                        @if($value->area == '1')
	                                                        	ADMINISTRATIVA
	                                                        @elseif($value->area == '2')
	                                                        	MEDICA
	                                                        @endif
                                                        @endif
                                                    </td>
                                                    <td>{{$value->cargo}}</td>
                                                    <td align=right>{{$value->monto_prestamo}}</td>
                                                    <td align=right>{{$value->saldo_total}}</td>
                                                    
                                                    <td>
                                                        @if(!is_null($prest_utili))
                                                            @php $total_utilidades += $prest_utili->total;  @endphp
                                                            <input type="text" name="valuti" id="valuti_{{$value->id}}" value="{{$prest_utili->total}}" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" readonly>
                                                        @else
                                                    	   <input type="text" name="valor_uti" id="valor_uti_{{$value->id}}" value="0.00" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" readonly>
                                                        @endif
                                                    </td>
                                                   
                                                    <td>
                                                        <a class="btn btn-warning btn-xs" data-remote="{{route('prestamos_empleados.modal_utilidades',['id' => $value->id])}}" data-toggle="modal" data-target="#modal_liq" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                               


                                                @foreach($saldos as $s)
                                                    @php
                                                        $prest_utili2 = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $s->id_empl)->where('pres_sal','2')->where('id_saldo', $s->id)->first();
                                                        $total +=$s->saldo_res;
                                                    @endphp
                                                <tr class="well">
                                                    <td>{{$s->id_nomina}}</td>
                                                    <td>{{$s->id_empl}}</td>
                                                    <td>{{$s->nombres}}</td>
                                                    <td>{{$s->fecha_ingreso}}</td>
                                                    <td>
                                                        @if(!is_null($s->area))
                                                            @if($s->area == '1')
                                                                ADMINISTRATIVA
                                                            @elseif($s->area == '2')
                                                                MEDICA
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{$s->cargo}}</td>
                                                    <td align=right>{{$s->saldo_inicial}}</td>
                                                    <td align=right>{{$s->saldo_res}}</td>
                                                    <td>
                                                        @if(!is_null($prest_utili2))
                                                        @php $total_utilidades += $prest_utili2->total  @endphp
                                                            <input type="text" name="val2" id="val2_{{$s->id}}" value="{{$prest_utili2->total}}" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" readonly>
                                                        @else
                                                           <input type="text" name="val_uti2" id="val_uti2_{{$s->id}}" value="0.00" autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" readonly>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-success btn-xs" data-remote="{{route('prestamos_empleados.modal_utilidades_saldos',['id' => $s->id])}}" data-toggle="modal" data-target="#modal_liq_saldos" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="text-align: right;font-weight: bold;">Total: </td>
                                                    <td style="text-align: right;font-weight: bold;">{{$total}}</td>
                                                    <td style="text-align: right;font-weight: bold;">{{$total_utilidades}}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--div class="row">
                                            <div class="col-xs-2">
                                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros {{count($prestamos)}} </div>
                                            </div>
                                        </div-->
                                    </div>
                                </div>
                                <br>
                                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                                    <a target="_blank" type="button" onclick="imprimir_pdf();" class="btn btn-primary" id="imprimir_rol">Imprimir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script type="text/javascript">

    $('#modal_liq').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    $('#modal_liq_saldos').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    $('#fecha_creacion').datetimepicker({
        format: 'YYYY/MM/DD',
        defaultDate: '{{$fecha_hoy}}',
    });

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46){
      return false;

     }
      
     return true;
    }

    function checkformat(entry) { 
        
        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){      
            entry.value='0.00';        
        }
        if (test < 0) {

            entry.value = '0.00';
        }
    
    }

    function guardar_utili(id_prestamo){

        var formulario = document.forms["form_liq"];

        var val_liq = formulario.val_liq.value;

        var msj = "";

        if(val_liq == 0){
          msj += "Por favor, Ingrese un Valor<br/>";
        }

        if(msj != ""){
                
                swal({
                      title: "Error!",
                      type: "error",
                      html: msj
                    });
                return false;
        }
        //alert('entra');
        
        var anio = $('#year').val();
        var mes = $('#mes').val();
        var valor_liqui = $('#val_liq').val();
        //console.log(anio, mes, valor_liqui);
        
        $.ajax({
          type: 'post',
          url:"{{ route('prestamos_empleados.guardar_mod') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {
            'val_liq'     : valor_liqui,
            'anio'        : anio,
            'mes'         : mes,
            'id_prestamo' : id_prestamo,
          },
            success: function(data){
            
                if(data.msj =='ok'){
                  
                  swal({
                      title: "Datos Guardado con Exito",
                      icon: "success",
                      type: 'success',
                      buttons: true,
                  })

                  $("#modal_liq").modal('hide');
                  $('body').removeClass('modal-open');
                  $('.modal-backdrop').remove();

                  //Refrescar Pagina
                  location.reload();
                
                }
                    
            },

            error: function(data){
                console.log(data);
            }
        });
    }

    function guardar_utili_saldo(id_saldo){

        var formulario = document.forms["form_liq_sal"];

        var val_liq_sal = formulario.val_liq_sal.value;

        var msj = "";

        if(val_liq_sal == 0){
          msj += "Por favor, Ingrese un Valor<br/>";
        }

        if(msj != ""){
                
                swal({
                      title: "Error!",
                      type: "error",
                      html: msj
                    });
                return false;
        }
        //alert('entra');
        
        var anio = $('#year').val();
        var mes = $('#mes').val();
        var val_liq_sal = $('#val_liq_sal').val();
        //console.log(anio, mes, val_liq_sal);
        
        $.ajax({
          type: 'post',
          url:"{{ route('prestamos_empleados.guardar_mod_saldos') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {
            'val_liq_sal'     : val_liq_sal,
            'anio'        : anio,
            'mes'         : mes,
            'id_saldo' : id_saldo,
          },
            success: function(data){
            
                if(data.msj =='ok'){
                  
                  swal({
                      title: "Datos Guardado con Exito",
                      icon: "success",
                      type: 'success',
                      buttons: true,
                  })

                  $("#modal_liq_saldos").modal('hide');
                  $('body').removeClass('modal-open');
                  $('.modal-backdrop').remove();

                  //Refrescar Pagina
                  location.reload();
                
                }
                    
            },

            error: function(data){
                console.log(data);
            }
        });
    }

    function imprimir_pdf(){
        var anio = $('#year').val();
        var mes = $('#mes').val();

        var ruta = ''+"{{url('contable/nomina/cruce/pdf')}}/"+mes+"/"+anio;  
        window.open(ruta);
    }
    </script>
</section>
@endsection