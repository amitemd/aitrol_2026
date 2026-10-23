@extends('hc_admision.visita.base')


@section('action-content')

<style type="text/css">
    
    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    } 

    #mceu_13-body, #mceu_44-body, .mce-branding{
        display: none;
    } 


    .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }
       
        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222; 
        }
</style>


 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-10">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Nombres:</b></td><td>{{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
                                    <td><b>Apellidos:</b></td><td>{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
                                    <td><b>Identificación:</b></td><td>{{$paciente->id}}</td>
                                    <td><b>Edad:</b><span id="edad"></span></td>
                                    <td><b>Seguro:</b></td>
                                    <td>{{$historia->seguro->nombre}}</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a type="button" href="{{ URL::previous() }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Regresar</span>
            </a>

        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">  
                    <div class="box-header with-border">
                        <div class="col-md-7">
                            <h4>RECETA DEL PACIENTE</h4>    
                        </div>
                        <div class="col-md-5">
                        @if($receta != "")   
                            <a href="{{ route('hc_receta.imprime', ['id' => $receta->id, 'tipo' => '1']) }}" type="button" class="btn btn-primary btn-sm">
                                    Descargar
                            </a>
                            <a href="{{ route('hc_receta.imprime', ['id' => $receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-primary btn-sm">
                                    Descargar Membretada
                            </a>
                            <a href="{{ url('medicina') }}" type="button" class="btn btn-success btn-sm">
                                    Medicinas
                            </a>
                        @endif
                        
                        <!--a type="button" href="{{route('agenda.detalle', ['id' => $historia->id_agenda ])}}" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-user"> Historia Clinica</span>
                        </a-->
                        </div>
                    </div>

                    <input type="hidden" name="id_paciente" id="id_paciente" value="{{$paciente->id}}">
                            
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                  <label for="inputid" class="col-md-2 control-label">Medicina</label>
                                  <div class="col-md-6">
                                    <input value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre"  >
                                  </div>
                                  <div class="col-md-4">
                                        <button  type="button" id="limpiar" class="btn btn-primary">
                                                Agregar
                                        </button>
                                    </div>
                              </div>
                            </div>
                        </div>

                        <br>
                                    
                                
                        <!--form id="frm" >
                            <div class="form-group col-xs-12">
                                <div class="col-md-12">
                                    <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
                                    <span><b>Alergias:</b> </span><br>
                                </div>                                               
                                <div class="col-md-6">
                                    <textarea type="text" style="width: 98%;" onchange="guardar();" rows="4" name="alergias">{{$paciente->alergias}}</textarea>
                                </div>
                            </div>
                        </form-->
                        <div class="col-md-2"><b>Alergias: </b></div><div class="col-md-10">@if($alergiasxpac->count()==0) <b>NO TIENE </b>@else @foreach($alergiasxpac as $ale)<span class="bg-red" style="padding: 3px;border-radius: 10px;">{{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;@endforeach @endif</div>

                        <div id="index">
                            
                        </div>


                        <!--form class="form-vertical" id="form2" role="form" method="POST" action="{{ route('receta.update_crea') }}" >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">  
                            <input type="hidden" name="id_hc" value="{{ $historia->hcid }}">
                            <input type="hidden" name="id_receta" value="{{ $receta->id }}">  
                            
                            
                            <div class="form-group col-xs-6{{ $errors->has('rp') ? ' has-error' : '' }}">
                                <label for="rp" class="col-md-12 control-label">Rp</label>
                                <div class="col-md-12">
                                     <textarea id="rp"  onchange="guardar2();" name="rp" style="width: 100%; height: 200px;">@if($receta != ""){{$receta->rp}}@endif</textarea>
                                
                                    @if ($errors->has('rp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('rp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group col-xs-6{{ $errors->has('prescripcion') ? ' has-error' : '' }}">
                                <label for="prescripcion" class="col-md-12 control-label">Prescripcion</label>
                                <div class="col-md-12">
                                     <textarea onchange="guardar2();" id="prescripcion"  name="prescripcion" style="width: 100%; height: 200px;">@if($receta != ""){{$receta->prescripcion}}@endif</textarea>
                                
                                    @if ($errors->has('prescripcion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('prescripcion') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-9">
                                    <button type="button" class="btn btn-primary">
                                            Guardar
                                    </button>
                                </div>
                            </div>
                    
                        </form-->              
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>           


            

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

    $(document).ready(function() {

        index();

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        }); 
           
        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ " años";
        $('#edad').text( edad );

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');
                


    });
    $("#limpiar").click( function(){
        $('#nombre_generico').val(''); 
    });
    $("#nombre_generico").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('receta.buscar_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);
                }
            })
        },
        minLength: 2,
    } );
    
    $("#nombre_generico").change( function(){
        var variable1;
        var variable2;
        $.ajax({
            type: 'post',
            url:"{{route('receta.buscar_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre_generico"),
            success: function(data){
                if(data!='0'){
                    Crear_detalle(data);
                    //console.log(data);
                    // 12102018 NUEVO RECETA_DETALLE
                    /*variable1 =  $('#rp').val();
                    variable2 = $('#prescripcion').val(); 
                    if(data.nombre_generico != null){
                        variable1 = variable1+'\n'+ data.nombre_generico+'('+data.value+'): ';
                        variable2 = variable2+'\n'+ data.nombre_generico+'('+data.value+'): ';
                        if(data.cantidad != null){
                            variable1 = variable1+data.cantidad;
                        }
                        if(data.dosis != null){
                            variable2 = variable2+data.dosis;
                        }
                        
                    }
                    else{
                        variable1 = variable1+'\n'+ data.value+': ';
                        variable2 = variable2+'\n'+ data.value+': ';
                        if(data.cantidad != null){
                            variable1 = variable1+data.cantidad;
                        }
                        if(data.dosis != null){
                            variable2 = variable2+data.dosis;
                        }
                    }
                    
                    $('#rp').val(variable1); 
                    $('#prescripcion').val(variable2);
                    guardar2();*/
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

    function Crear_detalle(med){
        var js_cedula = document.getElementById("id_paciente").value;
        //alert(js_cedula);
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/crear_detalle')}}"+"/"+{{$receta->id}}+"/"+med.id+"/"+js_cedula, //receta.crear_detalle
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function index(){
        
        $.ajax({
          type: 'get',
          url:"{{route('receta.index_detalle',['receta' => $receta->id])}}",
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }


    function guardar(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.guardar2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form2").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    
              
    function det_delete(id) {
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/eliminar_detalle')}}/"+{{$receta->id}}+"/"+id,
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }
</script>

@include('sweet::alert')
@endsection

