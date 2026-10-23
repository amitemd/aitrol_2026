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

<div class="box " style="border: 2px solid #004AC1; background-color: white;">
  <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
    <div class="row">
      <div class="col-md-9">
        <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/receta.png"> 
              <b>NUEVA RECETA</b>
        </h1>   
      </div>
    </div>
    @if(!is_null($paciente)) 
      <center> 
        <div class="col-12" style="padding-bottom: 10px;">
          <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
          <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                    {{$paciente->nombre1}} {{$paciente->nombre2}}
          </b>
          </h1>
        </div> 
      </center>
    @endif  
  </div>

  <div class="box-body" style="background-color: #56ABE3" >
    <div class="box" style="border: 2px solid #004AC1;border-radius: 10px;background-color: white;font-size: 13px;font-family: Helvetica;margin-bottom: 10px;margin-top: 0px;padding-left: 20px;padding-right: 20px;padding-top: 20px;padding-bottom: 20px;">
        <input type="hidden" name="id_paciente" id="id_paciente" value="{{$paciente->id}}">
        <div class="col-12" >

          <div class="col-9">
            <div class="row">
              <div class="col-6" style="text-align: right;" >
                  <label style="font-family: 'Helvetica general';" >Seguro:</label>
              </div>
              <div class="col-6">
                @if(!is_null($hc_receta->nombre_seguro))
                  {{$hc_receta->nombre_seguro}} 
                @endif 
              </div>
            </div>
          </div>

          <div class="form-group">
            <label style="font-family: 'Helvetica general';" for="inputid" class="control-label">Medicina</label>
            <div class="row"> 
              <div class="col-10">
                <input value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre">
              </div>&nbsp;&nbsp;&nbsp;
              <div class="centered">
                <button id="limpiar" class="btn btn-primary" style="background-color: #004AC1;"
                  onClick="buscar_nombre_medicina()">
                  <span class="fa fa-plus"></span> Agregar
                </button>
              </div>
            </div>
          </div> 
        </div>                        
      <div style="font-family: 'Helvetica general';" class="col-md-1">Alergias:</div>
      <div class="col-md-12" style="margin-bottom: 10px">
        @if($alergiasxpac->count()==0) 
          <b>NO TIENE </b>
        @else 
          @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
          @endforeach 
        @endif
      </div>
      <div id="index">
      </div>                
      <form id="final_receta" method="POST">
      <input type="hidden" name="id_receta" value="{{$hc_receta->id}}"> 
      <input type="hidden" name="id_paciente" value="{{$paciente->id}}"> 
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
              <div id="trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;"> 
                <?php if(!is_null($hc_receta)): ?>
                  <?php echo $hc_receta->rp ?>
                <?php endif; ?>
              </div>
              <input type="hidden" name="rp" id="rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>">
            </div>
            <div class="col-md-6" >
              <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
              <div id="tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                  <?php if(!is_null($hc_receta)): ?>        
                    <?php echo $hc_receta->prescripcion ?>
                  <?php endif; ?>
              </div> 
              <input type="hidden" name="prescripcion" id="prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>"> 
            </div>
          </div>
          <br>
          <div class="centered">
            <button type="button" class="btn btn-primary" onClick="ingresar_recetas('{{$id_paciente}}')" style="background-color: #004AC1;">
              <span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
          </div>     
        </div>
      </form>
</div>

<script type="text/javascript">

  function ingresar_recetas(){
    $.ajax({
      type: "GET",
      url: "{{route('paciente.recetas', ['id_paciente' => $paciente->id])}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        //alert("!!RECETA GUARDADA!!");
        $("#area_trabajo").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

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
               var ed = tinyMCE.get('tprescripcion{{$hc_receta->id}}<?php echo e(date('his')); ?>');
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


  $("#nombre_generico").autocomplete({
    source: function( request, response ) {
      $.ajax({
        url:"{{route('buscar_nombre.receta')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
        data: {
            term: request.term,
            seguro: {{$hc_receta->id_seguro}},
            tipo: {{$publica}}
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
        url:"{{route('buscar_nombre2.receta')}}",
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
                //console.log(data);
                Crear_detalle(data);
              }
                $('#nombre_generico').val(''); 
              }                
            },
            error: function(data){
            }
        })
  }

  function cambiar_receta_2(){
    $.ajax({
      type: 'post',
      url:"{{route('update_receta_2.receta')}}",
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


  $("#prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").change( function(){
    cambiar_receta_2();
  });
  $("#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>").change( function(){
    cambiar_receta_2();
  });

  

@if(!is_null($hc_receta))
  function Crear_detalle(med){
    var js_cedula = document.getElementById("id_paciente").value;
      $.ajax({
        type: 'get',
        url:"{{url('detalle_receta/detalle_crear')}}"+"/"+{{$hc_receta->id}}+"/"+med.id+"/"+js_cedula, 
        datatype: 'json',
        success: function(data){
          //console.log(data);
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
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+': '+med.cantidad);
                    $('#rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').val(tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').getContent());
                }else{
                    pos = pos-12;
                    tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
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

                tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior +'\n'+ med.value +':  ' +dosis);

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
                 
                  tinyMCE.get('trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior2 +'\n'+ med.value+" ("+med.genericos+")"+': ' + med.cantidad);

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
                var dosis = med.dosis;
                if(null == med.dosis){
                  dosis = '';
                }

                tinyMCE.get('tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>').setContent(anterior +'\n'+ med.value +':  ' +dosis);
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
