<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    @media screen and (max-width: 1500px) {
      label#peri.control-label {
        font-size: 13px;
      } 
    }
    
    .table>tbody>tr>td, .table>tbody>tr>th {
      padding: 0.4% ;
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

    .mce-edit-focus,
    .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
      background-color: white !important;
    }
   
    .centered{
      text-align: center;
    }

    .select2-selection__choice{
     background-color: red !important;
     border-color: red !important;
    }
  
</style>

<section class="content" style=" margin-left: 4px;margin-right: 4px;padding-left: 0px;padding-right: 0px;padding-top: 5px;">
  <div class="container-fluid" style="padding-left: 0px; padding-right: 0px">
    <div class="row">
      @php
        $xfechaatencion = (substr($xhistoria->fecha_atencion,0,10));
      @endphp 
      <div class="col-12" style="padding-left: 0px; padding-right: 0px">
        <input type="hidden" name="id_paciente" id="id_paciente" value="{{$paciente->id}}">
        <input type="hidden" name="id_receta" value="{{$hc_receta->id}}">
        <div class="row">
          <div class="col-md-6">
            <div class="col-md-12">
                <div>
                    <label style="font-family: 'Helvetica general'; " for="inputid" class="control-label">Fecha:</label>
                </div>
            </div>
            <div class="col-md-12">
              <input id="fecha_atencion" type="date" onchange="obtener_fecha();" name="fecha_atencion" class="form-control input-sm" 
                @if(!is_null($xfechaatencion)) 
                  value='{{$xfechaatencion}}' 
                @endif>
            </div>
          </div>
          <div class="col-md-6">
            <div class="col-md-12">
              <label style="font-family: 'Helvetica general'; " for="inputid" class="control-label">Doctor(a):
              </label>
            </div>
            <div class="col-md-12">
              <select name="id_doctor_receta" id="id_doctor_receta" required onchange="obtener_iddoctor();">
                  <option value="">Seleccione ...</option>
                  @foreach($doctores as $value)
                      <option value="{{$value->id}}">
                        {{$value->apellido1}} {{$value->apellido2}} 
                        {{$value->nombre1}} {{$value->nombre2}}
                      </option>
                  @endforeach
              </select>
            </div>
          </div>
        </div>
        <br>
        <label style="font-family: 'Helvetica general'; padding-left: 30px" for="inputid" class="control-label">Medicina</label>
        <div class="col-12" style="padding-left: 30px; padding-right: 30px">
          <div class="row"> 
            <div class="col-md-8">
              <input style="margin-bottom: 6px" value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre">
            </div>
            <div class="col-md-4">  
              <button id="limpiar" class="btn btn-primary" style="background-color: #3c8dbc"
                onClick="buscar_nombre_medicina()">
                <span class="fa fa-plus"></span> Agregar
              </button>
            </div>
          </div>
        </div>
        <div style="font-family: 'Helvetica general';" class="col-md-2">Alergias:</div>
        <div class="col-md-10">
            @if($alergiasxpac->count()==0) 
              <b>NO TIENE </b>
            @else 
              @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
              @endforeach 
            @endif
        </div>
        <div id="index">
          <br>
          <br>
        </div>
        <form id="final_receta" method="POST">
          <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
          <input type="hidden" name="id_receta" value="{{$hc_receta->id}}">
          <input type="hidden" name="id_doct" id="id_doct">
          <input type="hidden" name="fech_aten" id="fech_aten">
          <div class="col-md-12" id="actualiza">
            <div class="row">
              <br>
              <div class="col-md-6">
                <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                  <div id="trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                    <?php if(!is_null($hc_receta)): ?>
                      <?php echo $hc_receta->rp ?>
                    <?php endif; ?>
                  </div>
                <input type="hidden" name="rp" id="rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>">
              </div>
              <div class="col-md-6" >
                <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                    <div id="tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                      <?php if(!is_null($hc_receta)): ?>        
                        <?php echo $hc_receta->prescripcion ?>
                      <?php endif; ?>                              
                    </div> 
                <input type="hidden" name="prescripcion" id="prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>"> 
              </div>
            </div>
            <br>
            <div class="centered">
              <button type="button" class="btn btn-primary" onclick="ingresar_recetas(<?php echo e($hc_receta->id); ?>)" style="background-color: #3c8dbc;">
                <span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
              </button>
            </div>                                 
          </div>
        </form>
      </div> 
    </div>
  </div>
  
<script type="text/javascript">
  tinymce.init({
    selector: '#trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>',
    inline: true,
    menubar: false,
    content_style: ".mce-content-body {font-size:14px;}",
    //readonly: 1,
        
      setup: function (editor){
          editor.on('init', function (e) {
             var ed = tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>');
              $("#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
          });
      },
      
      init_instance_callback: function (editor){
          editor.on('Change', function (e) {
              var ed = tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>');
              $("#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
              cambiar_receta_2(); 
            
          }); 
      }
  });

  tinymce.init({
    selector: '#tprescripcion{{$hc_receta->id}}<?php echo e(date('his')); ?>',
    inline: true,
    menubar: false,
    content_style: ".mce-content-body {font-size:14px;}",
    //readonly: 1,
      
      setup: function (editor){
            editor.on('init', function (e){
               var ed = tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>');
                $("#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
      },
       
      init_instance_callback: function (editor){
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>');
                $("#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
      }
  });

  
  //nueva actualiza receta
   function ingresar_recetas(id){
         $.ajax({
            type: 'post',
            url:"{{route('historiaclin_paciente.receta_act')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#final_receta").serialize(),
            success: function(data){
              //cambiar_receta_2();
              //alert("!! RECETA ACTUALIZADA !!");
              $("#receta"+id).html(data);
              
            },
            error:  function(){
                alert('error al cargar');
            }
        }); 
    }
  
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
              }
            })
        },
    minLength:2,
  });

  function buscar_nombre_medicina(){
      $.ajax({
        type: 'post',
        url:"{{route('receta.buscar_nombre2')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#nombre_generico"),
          
          success: function(data){
            
            if(data!='0'){
              if(data.dieta == 1 ){
                var dosis = data.dosis;
                if(null == data.dosis){
                  dosis = '';
                }
                anterior = tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent();
                tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior+ data.value +': \n' +dosis);
                $('#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                  cambiar_receta_2();
                }
                if(data.dieta == 0){
                  Crear_detalle(data);
                }
                  $('#nombre_generico').val(''); 
                }                
            },
            error: function(data){
            }
      })
  } 

    
  $("#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").change( function(){
    cambiar_receta_2();
  });
  
  $("#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").change( function(){
    cambiar_receta_2();
  });

  function cambiar_receta_2(){
    $.ajax({
      type: 'post',
      url:"{{route('update_receta_2.recetahc4')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#final_receta").serialize(),
      success: function(data){
        //alert("guardado")
        //console.log(data);
      },
      error: function(data){
        //console.log(data);
      }
    })
  }

  function obtener_iddoctor(){
    var id_doc = document.getElementById("id_doctor_receta").value;
    document.getElementById("id_doct").value = id_doc;
    cambiar_fecha_iddoctor();
  }

  function obtener_fecha(){
    var fech = document.getElementById("fecha_atencion").value;
    document.getElementById("fech_aten").value = fech;
    cambiar_fecha_iddoctor();
  }

  function cambiar_fecha_iddoctor(){
    $.ajax({
      type: 'post',
      url:"{{route('update_fecha.iddoctor')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#final_receta").serialize(),
      success: function(data){
        
      },
      error: function(data){
        
      }
    })
  }
 
  @if(!is_null($hc_receta))
  function Crear_detalle(med){
    var js_cedula = document.getElementById("id_paciente").value;
      $.ajax({
        type: 'get',
        url:"{{url('receta_detalle/crear_detalle')}}"+"/"+{{$hc_receta->id}}+"/"+med.id+"/"+js_cedula, 
        datatype: 'json',
        success: function(data){
          
          if(data == 1){
            if(med.genericos == null){
              anterior2 = tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent();
                var keywords = ['cie10-receta'];
                var resultado = "";
                var pos = -1;
                keywords.forEach(function(element){
                pos = anterior2.search(element.toString());
                  if(pos!=-1){
                    resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                  }
                });

                //En caso de que no exista.
                if(pos === -1 && resultado === ""){
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                    $('#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                }else{
                    var dosis = med.dosis;
                    if(null == med.dosis){
                      dosis = '';
                    }
                    pos = pos-12;
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2.substr(0, pos) +'\n'+ med.value +':  ' +med.cantidad +anterior2.substr(pos));
                    $('#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                }
                //fin de receta
                //anterior = $('#prescripcion').val();
                anterior = tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent();
                //console.log(anterior);
                //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                var dosis = med.dosis;
                if(null == med.dosis){
                  dosis = '';
                } 
                tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);

                $('#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                cambiar_receta_2(); 
            }else{
                //anterior2 = $('#rp').val();
                anterior2 = tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent();
                //codigo cie10 de posicion de receta
                var keywords = ['cie10-receta'];
                var resultado = "";
                var pos = -1;

                keywords.forEach(function(element) {
                    //En caso de existir se asigna la posición en pos
                    pos = anterior2.search(element.toString());
                    //Si existe
                    if(pos!=-1){
                        resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                    }

                });

                //En caso de que no exista.
                if(pos === -1 && resultado === ""){
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                    $('#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                }else{
                    pos = pos-12;
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
                    $('#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                }
                //fin de receta cie10
                //anterior = $('#prescripcion').val();
                anterior = tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent();
                //$('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
                if(dosis == 'null'){
                  dosis = '';
                }
                tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                $('#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                cambiar_receta_2(); 
            }
            }else{
                $('#index').empty().html(data);
                //var contenido = data;
                //var texto = contenido.replace(/<[^>]*>?/g,'');
                //alert(texto);
                //var texto = $(contenido).text();
                //alert(texto);
            }
            //console.log(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
  }
  @endif

</script>
</section>