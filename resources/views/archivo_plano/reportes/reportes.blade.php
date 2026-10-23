@extends('archivo_plano.archivo.base')
@section('action-content')
<style >
    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
    }

    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
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
        _width: 470px !important;
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

    /*.oculto {
      display: none;
    }*/

    .oculto {
       opacity: 0;
    }

    /*.oculto {
        visibility: hidden;
    }*/
    
</style>
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">Generación de Reportes</h3>
		        </div>
		    </div>
		</div>
        <div class="box-body">
            <form method="POST" action="{{route('planilla.reportes_excel')}}">
            {{ csrf_field() }}
	  		<div class="row" >
                <div class="form-group col-md-4 ">
                    <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                    <div class="col-md-7">
                        <input id="mes_plano"  type="text" class="form-control input-sm" name="mes_plano" value="{{$mes_plano}}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="seguro" class="col-md-4 control-label">Seguro:</label>
                        <div class="col-md-7">
                            <select id="seguro" name="seguro" class="form-control input-sm" >
                                <option value="">Seleccione</option>  
                                @foreach($seguro as $value)
                                    <option  @if($seg == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-4 ">
                    <label for="id_tipo_seguro" class="col-md-4 control-label">Tipo Seguro:</label>
                    <div class="col-md-7">
                        <select id="id_tipo_seguro" name="id_tipo_seguro" class="form-control input-sm">
                            <option value="5" @if($tipo_seg== "5") selected="selected" @endif>HIJO DE 7 - 17 AÑOS</option>
                            <option value="9" @if($tipo_seg== "9") selected="selected" @endif>SSC</option>
                            <option value="2" @if($tipo_seg== "2") selected="selected" @endif>ACTIVO (CONYUGE)</option>
                            <option value="7" @if($tipo_seg== "7") selected="selected" @endif>JUBILADO CAMPESINO</option>
                            <option value="6" @if($tipo_seg== "6") selected="selected" @endif>JUBILADO</option>
                            <option value="8" @if($tipo_seg== "8") selected="selected" @endif>MONTEPIO</option>
                            <option value="4" @if($tipo_seg== "4") selected="selected" @endif>HIJO DE 2 - 6 AÑOS</option>
                            <option value="3" @if($tipo_seg== "3") selected="selected" @endif>HIJO DE 0 - 1 AÑO</option>
                            <option value="1" @if($tipo_seg== "1") selected="selected" @endif>ACTIVO</option>
                            <option value="10" @if($tipo_seg== "10") selected="selected" @endif>TODOS</option>
                        </select>
                    </div>
                </div>
                
                <!--<div class="form-group col-md-4 ">
                        <label for="id_cobertura_comp" class="col-md-4 control-label">Cobertura Compartida:</label>
                        <div class="col-md-7">
                            <select id="id_cobertura_comp" name="id_cobertura_comp" class="form-control input-sm" >
                                <option value="">NINGUNO</option>
                                @foreach($seguros_publicos as $value)
                                    @if($value->id == '3' || $value->id == '6')
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                </div>-->
                
                <div class="form-group col-md-4 ">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                        <div class="col-md-7">
                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                @foreach($empresas as $value)
                                    
                                     <option value="{{$value->id}}" @if($empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                                    
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-2">
                    <div class="btn-group">
                        
                        <button id="reporte_agrupado1" class="btn btn-default oculto" type="submit" formaction="{{route('planilla.reporte_agrupado')}}">Agrupado</button>
                        
                        <button id="report_consolidado1" class="btn btn-default oculto" type="submit" formaction="{{route('planilla.reporte_consolidado_iess')}}">Reporte Consolidado GENERAL</button>
                        
                        <button id="report_campesino1" class="btn btn-default oculto" type="submit" formaction="{{route('planilla.reporte_consolidado_campesino')}}">Reporte Consolidado CAMPESINO</button>

                        <button id="report_issfa1" class="btn btn-default form-control oculto" type="submit" formaction="{{route('ap_planilla.reporte_cobertura_issfa')}}">Reporte Cons. Cob.Comp. ISSFA</button>

                        <button id="report_isspol1" class="btn btn-default form-control oculto" type="submit" formaction="{{route('ap_planilla.reporte_cobertura_isspol')}}">Reporte Cons. Cob.Comp.ISSPOL</button>

                        <button id="report_seg_priv" class="btn btn-default form-control oculto" type="submit" formaction="{{route('ap_planilla.reporte_seguros_privados')}}">Reporte Seg. Privado</button>

                        <button id="report_hon_cirujano" class="btn btn-default form-control oculto" type="submit" formaction="{{route('ap_planilla.honorario_medico')}}">Reporte Honorarios Cirujano</button>

                        <button id="report_hon_anestesiologo" class="btn btn-default form-control oculto" type="submit" formaction="{{route('planilla.reportes_excel')}}">Reporte Honorarios Anestesiologo</button>

                        <button id="reporte_biopsias" class="btn btn-default form-control oculto" type="submit" formaction="{{route('ap_planilla.reporte_biopsias')}}">Reporte Biopsias</button>
			
			<button id="nuevo_reporte_consolidado_general" class="btn btn-default form-control oculto" type="submit" formaction="{{route('nuevoArchivoPlano.nuevo_reporte_consolidado_general')}}">Nuevo Reporte Consolidado General</button>
                            <button id="iva10porc_nuevo_reporte_consolidado_general" class="btn btn-default form-control oculto" type="submit" formaction="{{route('nuevoArchivoPlano.iva_10porc_nuevo_reporte_consolidado_general')}}"> Iva 10% Nuevo Reporte Consolidado General</button>
			
			<button id="reporte_consolidado_general_v2023" class="btn btn-default form-control oculto" type="submit" formaction="{{route('nuevoArchivoPlano.reporte_consolidado_general_v2023')}}"> Reporte Consolidado Version 2023 </button>
                        <button id="nuevo_reporte_consolidado_campesino" class="btn btn-default form-control oculto" type="submit" formaction="{{route('nuevoArchivoPlano.nuevo_reporte_consolidado_campesino')}}">Nuevo Reporte Consolidado Campesino</button>
                        <button type="button" class="btn btn-info">Reportes</button>
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                              <a class="btn btn-default" onclick="existe_data_agrupado()"><span>Agrupado</span></a>
                            </li>
                            <li>
                              <a class="btn btn-default" onclick="existe_data_consolidado_general()">Reporte Consolidado GENERAL</a>
                            </li>
                            <li>
                              <a class="btn btn-default" onclick="existe_data_campesino()">Reporte Consolidado CAMPESINO</a>
                            </li>
                            <li>
                              <a class="btn btn-default" onclick="existe_data_issfa()">Reporte Cons. Cob.Comp. ISSFA</a>
                            </li>
                            <li>
                               <a class="btn btn-default" onclick="existe_data_isspol()">Reporte Cons. Cob.Comp.ISSPOL</a>
                            </li>
                            <li>
                               <a class="btn btn-default" onclick="existe_data_seg_privado()">Reporte Seg. Privado</a>
                            </li>
                            <li>
                               <a class="btn btn-default" onclick="existe_data_cirujano()">Reporte Honorarios Cirujano</a>
                            </li>
                            <li>
                               <a class="btn btn-default" onclick="existe_data_anestesiologo()">Reporte Honorarios Anestesiologo</a>
                            </li>
                            <li>
                               <a class="btn btn-default" onclick="existe_biopsias()">Reporte Biopsias</a>
                            </li>
                            <li>
                                <button class="btn btn-default" type="submit" formaction="{{route('nuevoArchivoPlano.nuevo_reporte_consolidado_general')}}">Nuevo Reporte Consolidado General</button>
                            </li>
                            <li>
                                    <button class="btn btn-default" type="submit" formaction="{{route('nuevoArchivoPlano.iva_10porc_nuevo_reporte_consolidado_general')}}"> Iva 10% Nuevo Reporte Consolidado General</button>
                                </li>
                                <li>
                            <button class="btn btn-default" type="submit" formaction="{{route('nuevoArchivoPlano.reporte_consolidado_general_v2023')}}">Reporte Consolidado Version 2023</button>
                            </li>
                            <li>
                              <button class="btn btn-default" type="submit" formaction="{{route('nuevoArchivoPlano.nuevo_reporte_consolidado_campesino')}}">Nuevo Reporte Consolidado Campesino</button>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group col-md-2 ">                     
                        <div class="col-md-6">
                          <button type="submit" formaction="{{ route('planilla.reportes')}}" class="btn btn-primary" id="boton_buscar"><span class="glyphicon glyphicon-search" > Buscar</span></button>
                        </div>
                </div>
                <!--div class="form-group col-md-1 ">                     
                        <div class="col-md-7">
                            <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-download-alt"> Exportar</span></button>
                        </div>
                </div-->
                <!--div class="form-group col-md-4 ">
                        <label for="tipo_reporte" class="col-md-4 control-label">Tipo de Reporte:</label>
                        <div class="col-md-7">
                            <select id="tipo_reporte" name="tipo_reporte" class="form-control input-sm" >
                                <option value="1" >Reporte Honorarios Anestesiologo</option>
                                <option value="2" >Agrupado</option> 
                            </select>
                        </div>
                </div-->
               
	  		</div>
    
            </form>

            <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead style="background-color: #4682B4">
                <tr >
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Tipo Seguro</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Cédula</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Nombre Beneficiario</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Fecha Ingreso</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Procedimiento </th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Codigo</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Descripcion Honorario</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Tipo HNR</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Total</th>
                  
                </tr>
            </thead>
            <tbody>
                
                @if(!is_null($archivo_plano))
                @foreach($archivo_plano as $value)
                <tr>
                    <td>{{$value->nom_tseg}}</td>
                    <td>{{$value->id_paciente}}</td>
                    <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}}</td>
                    <td>{{substr($value->fecha_ing,0,10)}}</td>
                    <td>{{$value->nom_procedimiento}}</td>
                    <td>{{$value->codigo}}</td>
                    <td>{{$value->descripcion}}</td>
                    <td>{{$value->clasif_porcentaje_msp}}</td>
                    <td>{{$value->total_solicitado_usd}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
            <tfoot>
            </tfoot>
        </table>
      </div>


	  	</div>
	  </div>
</section>


<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
   

    function existe_data_agrupado(){
     
        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var tip_seg = $('#id_tipo_seguro').val();

        var texto = "";
        
        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }
        if(tip_seg==""){
            texto+="Por favor, Seleccione el Tipo de Seguro.<br>";
        }

        if(texto != ''){
        
          swal("Error!",texto,"error");
            
        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_reporte.agrupado') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa,'tipo_seg': tip_seg},
                success: function(data){
                    //console.log(data);
                    if(data == "existe"){

                        //window.location = $('#reporte_agrupado1').attr('href');

                        $('#reporte_agrupado1').click();   
                        
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });
           

        }
        
    
    }

    function existe_data_consolidado_general(){
     
        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();

        //alert(empresa);

        var texto = "";
        
        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){
        
        swal("Error!",texto,"error");
            
        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_consolidado.general') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                   
                    if(data == "existe"){
                        
                        $('#report_consolidado1').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });

        }
        
    }

    function existe_data_campesino(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();

        var texto = "";

        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){
        
          swal("Error!",texto,"error");
            
        }


        if(texto==''){ 


            $.ajax({
            type: 'post',
            url:"{{ route('verifica_consolidado.campesino') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                   
                    if(data == "existe"){
                        
                        $('#report_campesino1').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });




        }

    }

    function existe_data_issfa(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();

        var texto = "";

        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){
        
          swal("Error!",texto,"error");
            
        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_consolidado.issfa') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#report_issfa1').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });

        }
    }

    function existe_data_isspol(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();

        var texto = "";

        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){
        
          swal("Error!",texto,"error");
        
        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_consolidado.isspol') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#report_isspol1').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });

        }

    }

    function  existe_data_seg_privado(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();


        var texto = "";

        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){

          swal("Error!",texto,"error");

        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_seguro.privado') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#report_seg_priv').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });


        }

    }

    function existe_data_cirujano(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();

        var texto = "";

        if(mes_plano==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }

        if(texto != ''){

          swal("Error!",texto,"error");

        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_honorario.cirujano') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#report_hon_cirujano').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });


        }

    }

    function existe_data_anestesiologo(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var tipo_seg = $('#id_tipo_seguro').val();

        //alert(tipo_seg);

        var texto = "";

        if(mes_plano ==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro ==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa ==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }
        if(tipo_seg ==""){
            texto+="Por favor, Seleccione el Tipo de Seguro.<br>";
        }

        if(texto != ''){

          swal("Error!",texto,"error");

        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('verifica_honorario.anestesiologo') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa,'tipo_seg': tipo_seg},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#report_hon_anestesiologo').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });



        }


    
    
    }

    function existe_biopsias(){

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var tipo_seg = $('#id_tipo_seguro').val();

        //alert(tipo_seg);

        var texto = "";

        if(mes_plano ==""){
            texto+="Por favor, Ingrese el Mes de Plano.<br>";
        }
        if(seguro ==""){
            texto+="Por favor, Seleccione el Seguro.<br>";
        }
        if(empresa ==""){
            texto+="Por favor, Seleccione la Empresa.<br>";
        }
        if(tipo_seg ==""){
            texto+="Por favor, Seleccione el Tipo de Seguro.<br>";
        }

        if(texto != ''){

          swal("Error!",texto,"error");

        }

        if(texto==''){ 

            $.ajax({
            type: 'post',
            url:"{{ route('ap_archivo.verifica_biopsia') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'mes_plano': mes_plano,'seguro': seguro,'empresa': empresa,'tipo_seg': tipo_seg},
                success: function(data){
                
                    if(data == "existe"){
                        
                        $('#reporte_biopsias').click();   
                    
                    }
                    if(data == "no_existe"){
                        swal({
                            title: "No existen registros a mostrar",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });



        }


    
    
    }

    


    /*$("#mes_plano").autocomplete({
        source: function( request, response ) {
            
            $.ajax({
                url:"{{route('search.mes_plano')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    console.log(data);

                }
            })
        },
        minLength: 2,
    } );*/
    
    
    /*$(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });*/

    $('#example2').DataTable({
      'language': {
        'emptyTable': '<span class="label label-primary" style="font-size:14px;">No se encontraron registros.</span>'
      },
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
    
</script>
@endsection