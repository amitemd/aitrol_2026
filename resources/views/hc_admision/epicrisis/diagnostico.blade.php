
@extends('historiaclinica.base')

@section('action-content')


<style type="text/css">
    
    .mce-branding{
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



<div  class="modal fade fullscreen-modal" id="Crea_Actualiza" tabindex="-1" role="dialog" aria-labelledby="Crea_ActualizaLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>  
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-triangle-left"></span> Procedimientos</button>
                    </a>  
                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><b>Paciente:</b></td>
                            <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                            <td><b>Edad:</b></td>
                            <td><span id="edad"></span></td>
                            <td><b>Seguro:</b></td>
                            <td>{{$seguro->nombre}}</td>
                        </tr>
                        <tr>
                            <td><b>Procedimientos:</b></td>
                            <td colspan="3">{{$procedimientos_completo->find($hc_procedimiento->id_procedimiento_completo)->nombre_general}}</td>
                        </tr>                        
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        <button class="w3-bar-item w3-button tablink w3-red"onclick="location.href = '{{route('epicrisis.diagnostico',['id' => $id])}}' ">DIAGNOSTICO</button>
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('anestesiologia.mostrar',['id' => $id])}}' ">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('protocolo.mostrar',['id' => $id])}}'">PROTOCOLO</button>  
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('evolucion.evolucion',['id' => $id])}}'">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('epicrisis.mostrar',['id' => $id])}}'">EPICRISIS</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('hc_video.mostrar',['id' => $id])}}'">VIDEO</button>
                    </div>
                    <br>    
                    
  
                    <div class="col-md-12" id="tab4" >

                        <input type="hidden" name="hcid" id="hcid" value="{{$hca->hcid}}">
                        <input type="hidden" name="hc_id_procedimiento" id="hc_id_procedimiento" value="{{$id}}">
                        
                       
                        <div class="form-group col-md-12 {{ $errors->has('cie10') ? ' has-error' : '' }}" style="padding-right: 0px;">
                            <label for="cie10" class="col-md-12 control-label"><b>DIAGNÓSTICO INGRESO/EGRESO</b></label>
                            <div class="col-md-12">
                                <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico">
                                <span class="help-block">
                                    <strong id="str_cie10"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group col-md-3 {{ $errors->has('codigo') ? ' has-error' : '' }}" id="cambio5">
                            <label for="codigo" class="col-md-3 control-label">Código</label>
                            <div class="col-md-9">
                                <input id="codigo" type="text" class="form-control input-sm"  name="codigo" value="{{old('codigo')}}" required readonly>
                                <span class="help-block">
                                    <strong id="str_id_ing"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group col-md-6" >
                            <label for="cie10_in_pre" class="col-md-6 control-label">INGRESO/EGRESO</label>
                            <div class="col-md-6">
                                <select class="form-control" name="in_eg" id="in_eg" onchange="habilita_agregar();">
                                    <option value="">Seleccione ...</option>
                                    <option value="INGRESO">INGRESO</option>
                                    <option value="EGRESO">EGRESO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6" >
                            <label for="cie10_in_pre" class="col-md-6 control-label">PRESUNTIVO/DEFINITIVO</label>
                            <div class="col-md-6">
                                <select class="form-control" name="pre_def" id="pre_def" onchange="habilita_agregar();">
                                    <option value="">Seleccione ...</option>
                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button class="btn btn-success" id="ag_ingreso" disabled>
                                <span class="glyphicon glyphicon-plus"> Agregar</span>
                            </button>
                        </div>

                        <div class="table-responsive col-md-12">
                            <table class="table table-striped" >
                                <thead>
                                    <th width="5%">Código</th>
                                    <th width="30%">Descripción</th>
                                    <th width="10%">Ingreso/Egreso</th> 
                                    <th width="10%">Presuntivo/Definitivo</th>      
                                </thead>
                                <tbody>
                                @if($hc_cie10!='[]')
                                @foreach($hc_cie10 as $value)
                                    <tr>
                                        <td>{{$value->cie10}}</td>
                                        <td>{{$c10_arr[$value->id]}}</td>
                                        <td>{{$value->ingreso_egreso}}</td>
                                        <td>{{$value->presuntivo_definitivo}}</td>
                                        
                                    </tr>
                                @endforeach
                                @endif    
                                </tbody>
                            </table>
                        </div>

                            
                        
                    </div>

                    



                </div>
            </div>
        </div>  

        
      
 
    </div>
</div>


<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script>
 
    $(document).ready(function() {

        //Initialize Select2 Elements
        $('.select2').select2();

        



        
  
  

tinymce.init({

    selector: '#pronostico',
    height: 100,
    menubar: false,

    
    setup:function(ed) {
        ed.on('change', function(e) {
            tinyMCE.triggerSave();
            
        });
        /*ed.on('init', function() 
        {
            this.execCommand("fontName", false, "tahoma");
            this.execCommand("fontSize", false, "12px");

        });*/    


    }
  });

tinymce.init({

    selector: '#condicion',
    height: 100,
    menubar: false,
    
    setup:function(ed) {
        ed.on('change', function(e) {
            tinyMCE.triggerSave();
            
        });
        /*ed.on('init', function() 
        {
            this.execCommand("fontName", false, "tahoma");
            this.execCommand("fontSize", false, "12px");
        });  */  


    }
  });

tinymce.init({

    selector: '#resumen',
    height: 100,
    menubar: false,
    
    setup:function(ed) {
        ed.on('change', function(e) {
            tinyMCE.triggerSave();
            
        });
        ed.on('init', function() 
        {
            this.execCommand("fontName", false, "tahoma");
            this.execCommand("fontSize", false, "12px");
        });    


    }
  });

 tinymce.init({

        selector: '#hallazgo',
        height: 100,
        menubar: false,
    
        setup:function(ed) {
            ed.on('change', function(e) {
                tinyMCE.triggerSave();

            
            });
            ed.on('init', function() 
            {
                this.execCommand("fontName", false, "tahoma");
                this.execCommand("fontSize", false, "12px");
            });
        }
    });


        

        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');
        $(".breadcrumb").append('<li class="active">Atención</li>');    

        
                
        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ "años";
                
        $('#edad').text( edad );

    


    });



  $("#cie10").autocomplete({
        source: function( request, response ) {
                
            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
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

    $("#cie10").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);
                //alert(data.id);
                habilita_agregar();
                }
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });

    function habilita_agregar() {
        var codigo = document.getElementById('codigo').value;
        var tipo = document.getElementById('pre_def').value;
        var tipo2 = document.getElementById('in_eg').value;
        if(codigo!='' && tipo!='' && tipo2!=''){

            $( "#ag_ingreso" ).prop( "disabled", false );

        }else{

            $( "#ag_ingreso" ).prop( "disabled", true ); 

        }
    } 

    $( "#ag_ingreso" ).click(function() {
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': $("#hcid").val(), 'hc_id_procedimiento': $("#hc_id_procedimiento").val(), 'in_eg': $("#in_eg").val() },
            success: function(data){
                //alert("ok");
                console.log(data);
                location.reload();
               
                
            },
            error: function(data){
                    //console.log(data);
                }
        })
    });



    

</script>

@include('sweet::alert')
@endsection

