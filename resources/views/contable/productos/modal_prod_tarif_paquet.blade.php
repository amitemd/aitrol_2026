<style type="text/css">

    ul.ui-autocomplete {
        z-index: 1100;
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
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .info_nomina{
      width: 69%;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .datos_nomina
    {
      font-size: 0.8em;
    }

    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    #rol_pago{
      width: 100%;
      margin-bottom: 10px;
    }


    .info_nomina .col-xs-8 {
        padding-left:10px;
        font-size: 0.9em;
    }
    .info_nomina .round{
        padding-top:10px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .modal-body .form-group {
        margin-bottom: 0px;
    }

    .h3.modal_h3{
        font-family: 'BrixSansBlack';
        font-size: 8pt;
        display: block;
        background: #3d7ba8;
        color: #FFF;
        text-align: center;
        padding: 3px;
        margin-bottom: 5px;
        padding: 7px;
        font-size: 1em;
        margin-bottom: 15px;
    }
    .h3.modal_h3_2{
        margin-top: -20px !important;
        margin-bottom: 25px !important;
        padding: 7px;
        font-size: 1em;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }

    .separator1{
      width:100%;
      height:5px;
      clear: both;
    }

    
    /* Nuevo CSS*/

    .mLabel{
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 10px;
    }

    .texto {
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 15px;
    }

    .color_texto{
      color:#FFFFFF;
    }

    .head-title{
      background-color: #4682B4;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .t9{
      font-size: 0.9rem;
    }

    .well-dark{
      background-color: #cccccc;
    }

    .control-label{
        padding: 0;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px;
    }
    
    .table>tbody>tr>td{
    padding: 2px;
    }

    hr {
    
     height: 1px;
     background-color: black;
    }

</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;"><b>DETALLE PAQUETE TARIFARIO </b></h4>
  <div class="modal-body">
  <div class="row">
    <form id="guardar_tarif_paquete" method="post">
        {{ csrf_field() }}
        <input  name="id_prod" id="id_prod" type="text" class="hidden" value="@if(!is_null($id_producto)){{$id_producto}}@endif">
        <input  name="id_prod_paq" id="id_prod_paq" type="text" class="hidden" value="@if(!is_null($id_paquete)){{$id_paquete}}@endif">
        @php
          $prod = Sis_medico\Ct_productos::where('id',$id_producto)->where('estado_tabla','1')->first();
          $prod_paq = Sis_medico\Ct_productos_paquete::where('id',$id_prod_paq)->where('estado','1')->first();
        @endphp
        <div class="box-header">
          <div class="form-group col-md-12">
            <div class="form-group col-md-8">
              <label for="descrip_prod" class="col-md-2 control-label">Producto:</label>
              <div class="col-md-7">
                {{$prod->nombre}}
              </div>
            </div>
          </div>
          <div class="form-group col-md-12">
            <div class="form-group col-md-8">
              <label for="paquete" class="col-md-2 control-label">Descripción:</label>
              <div class="col-md-10">
                {{$prod_paq->nombre}}
              </div>
            </div>
          </div>
          <div class="form-group col-md-12">
            <div class="form-group col-md-8">
              <label for="cantidad" class="col-md-2 control-label">Cantidad:</label>
              <div class="col-md-7">
                {{$prod_paq->cantidad}}
              </div>
            </div>
          </div>
          <div class="form-group col-md-12">
            <div class="form-group col-md-8">
              <label for="PvP" class="col-md-2 control-label">Precio:</label>
              <div class="col-md-7">
                {{$prod_paq->precio}}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-10">
            <h3></h3>
          </div>
          <div class="col-md-2">
            <div class="col-md-7">
            <a id="crear_product_tar" class="btn btn-success btn-xs" data-remote="{{route('modal_crear_tarifario.productos',['id_prod_paq' => $id_prod_paq,'id_producto' => $id_producto,'id_paquete' => $id_paquete,'cantidad' => $prod_paq->cantidad])}}" 
              data-toggle="modal" data-target="#producto_tarifario" ><span>Crear Registro</span> 
            </a>
            </div>
          </div>
        </div>
        <div class="box-body">
          <div id="recarga_prod_tarif">
          </div>    
        </div> 
    </form>
  </div>
  <br><br>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
  </div>
</div>
</div>

    <!--<div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
          <center>
            <div class="col-md-6 col-md-offset-2">
              <img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 30%; display: none;">
              <button id="enviar_datos" type="button" class="btn btn-primary" onclick="registra_tarifario_paquete()">
                  Guarda
              </button>
            </div>
          </center>
    </div>-->
      
<script type="text/javascript">

  $(document).ready(function(){
    
    carga_tabla_producto_tarifario();

  });

  //Carga la Tabla de Producto Tarifario 
  function carga_tabla_producto_tarifario()
  {
    var id_prod = $("#id_prod").val();
    var id_paq = $("#id_prod_paq").val();
    
    $.ajax({
        type:"GET",
        url:"{{route('recarga_prod_tarifario.index')}}/" +id_prod +"/"+ id_paq,
        data: "",
        datatype: "html",
        success:function(data){
            $('#recarga_prod_tarif').html(data);
        },
        error:function(){
           alert('error al cargar');
        }
    });

  }
  
  
  function elimina_producto_tarifario_paquete(id_pr_paq){

    Swal.fire({
      title: '¿Desea Eliminar Producto Tarifario Paquete?',
      text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      
      if(result.isConfirmed){

        $.ajax({
          type: 'get',
          url: "{{ route('anula_producto_tarifario.paquete')}}",
          datatype: 'json',
          data: {'id_pr_paq': id_pr_paq},
          success: function(data)
          {
            swal({
              title: "Producto Tarifario Paquete Eliminado",
              icon: "success",
              type: 'success',
              buttons: true,
            })

            carga_tabla_producto_tarifario();
          
          },
          error: function(data) {
            console.log(data);
          }
        
        });
      }

    })
  
  }


  //Recarga Tabla Producto Tarifario




  //Registra Tarifario Producto Paquetes
  /*function registra_tarifario_paquete(){

    var id_paquet = $("#id_prod_paq").val();
    alert(id_paquet);
    var filas = $("#tbl_tarifario_producto").find("tr"); 
    for(i=0; i<filas.length; i++){ 
      var celdas = $(filas[i]).find("td"); 
      id_prod_tar =  parseFloat($($(celdas[0]).children("input")[0]).val());
      id_seg = parseFloat($($(celdas[1]).children("input")[0]).val());
      id_nivel = parseFloat($($(celdas[2]).children("input")[0]).val());
      valor_tar = parseFloat($($(celdas[3]).children("input")[0]).val());

      registro_tarifario(id_prod_tar,id_seg,id_nivel,valor_tar,id_paquet);
    
    }
  
  }*/

  /*function registro_tarifario(id_prod_tar,id_seg,id_nivel,valor_tar,id_paquet){
    
    $.ajax({
          type: 'post',
          url:"{{route('store_producto_tarifario.paquete')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {'id_prod_tar': id_prod_tar,
                 'id_seg': id_seg,
                 'id_nivel': id_nivel,
                 'valor_tar': valor_tar,
                 'id_paquet': id_paquet,
                },
          success: function(data){
            console.log(data);
          },
          error: function(data){
            console.log(data);
          }
      });
  }*/ 

</script>












  