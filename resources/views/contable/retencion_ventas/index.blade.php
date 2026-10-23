@extends('contable.retenciones.base')
@section('action-content')
<style type="text/css">
       .container-4{
              overflow: hidden;
              width: 300px;
              vertical-align: middle;
              white-space: nowrap;
        }
        .container-4 input#nombre_proveedor{
              width: 200px;
              height: 40px;
              background: #fff;
              border-radius: 5px;
              font-size: 10pt;
              float: left;
              color: black;
              border-color: #ececed;
              padding-left: 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
        }
        .container-4 input#nombre_proveedor::-webkit-input-placeholder {
          color: #65737e;
        }
        
        .container-4 input#nombre_proveedor:-moz-placeholder { /* Firefox 18- */
          color: #65737e;  
        }
        
        .container-4 input#nombre_proveedor::-moz-placeholder {  /* Firefox 19+ */
          color: #65737e;  
        }
        
        .container-4 input#nombre_proveedor:-ms-input-placeholder {  
          color: #65737e;  
        }
        .container-4 button.icon{
          -webkit-border-top-right-radius: 5px;
          -webkit-border-bottom-right-radius: 5px;
          -moz-border-radius-topright: 5px;
          -moz-border-radius-bottomright: 5px;
          border-top-right-radius: 5px;
          border-bottom-right-radius: 5px;
          border: none;
          background: #232833;
          height: 40px;
          width: 50px;
          color: #4f5b66;
          opacity: 0;
          font-size: 12px;
        
          -webkit-transition: all .55s ease;
          -moz-transition: all .55s ease;
          -ms-transition: all .55s ease;
          -o-transition: all .55s ease;
          transition: all .55s ease;
        }
        .container-4:hover button.icon, .container-4:active button.icon, .container-4:focus button.icon{
          outline: none;
          opacity: 1;
          margin-left: -50px;
        }
      
        .container-4:hover button.icon:hover{
          background: white;
        } 
        .container-4 input#buscar_secuencia::-webkit-input-placeholder {
          color: #65737e;
        }
        
        .container-4 input#buscar_secuencia:-moz-placeholder { /* Firefox 18- */
          color: #65737e;  
        }
        
        .container-4 input#buscar_secuencia::-moz-placeholder {  /* Firefox 19+ */
          color: #65737e;  
        }
        
        .container-4 input#buscar_secuencia:-ms-input-placeholder {  
          color: #65737e;  
        }           
        .container-4 input#buscar_secuencia{
              width: 200px;
              height: 40px;
              background: #fff;
              border-radius: 5px;
              font-size: 10pt;
              float: left;
              color: black;
              border-color: #ececed;
              padding-left: 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
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
</style>
<div class="content">

        <div class="box">
         <form id="buscador_form" method="post">
            <div class="box-header with-border size_text" style="color: black; font-family: 'Helvetica general3'; ">
                <div class="form-group col-md-12 cabecera">
                    <label class="color_texto size_text" for="title">BUSCADOR RETENCIONES</label>
                </div>
              <div class="form-group col-md-12">
                  <div class="form-row">
                    <div class="form-group col-md-2 col-xs-6">
                        <label class="size_text" for="buscar_secueencia">Secuencia factura: </label>
                    </div>
                    <input type="hidden" name="current_page" id="current_page" value="{{$retenciones->currentPage()}}">
                    <div class="form-group col-md-4 col-xs-6 container-4">
                          <input class="form-control buscar_secuencia size_text" type="text" id="buscar_secuencia" name="buscar_secuencia" onchange="autocompletarceros()" placeholder="Ingrese secuencia..." />
                          <button type="button" class="icon"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="form-group col-md-2">
                      <label class="size_text" for="d">Proveedor:</label>
                    </div>
                    <div class="form-group col-md-4 col-xs-6 container-4">
                          <input class="form-control nombre_proveedor size_text" type="text" id="nombre_proveedor" name="nombre_proveedor" onchange="buscar_proveedor()" placeholder="Ingrese nombre..." />
                          <button type="button" class="icon"><i class="fa fa-search"></i></button>
                    </div>
                  </div>  
              </div>
         </form>
            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('retenciones_crear')}}'" class="btn btn-danger size_text" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar Comp. de Retenciones
              </button>
            </div>
        </div>
        <div class="box-body">
          <div class="col-md-12">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr >
                        <th >Fecha</th>
                        <th >Proveedor</th>
                        <th> Descripción</th>
                        <th >Secuencia Factura</th>
                        <th >Total RFIR</th>
                        <th >Total RFIVA</th>
                        <th >Total</th>
                        <th >Acción</th>
                      </tr>
                    </thead>
                      <tbody>
                            @foreach($retenciones as $value)
                            <tr>
                              <td>{{$value->created_at}}</td>
                              <td>{{$value->id_proveedor}}</td>
                              <td>{{$value->descripcion}}</td>
                              <td>{{$value->secuencia}}</td>                        
                              <td>{{$value->rfir}}</td>
                              <td>{{$value->rfiva}}</td>
                              <td>{{$value->total}}</td>
                              <td><a href="{{route('retenciones_edit',['id'=>$value->id])}}" class="btn btn-danger">Editar</a>
                                <a class="btn btn-primary" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}">PDF comp</a>
                              </td>
                            </tr>
                            @endforeach
                      </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>
          </div>

        
        </div>
    </div>

</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){

      $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
      });

  });
  //metodo post retenciones.buscar_proveedor 
  function buscar_proveedor(){
     var proveedor= $("#nombre_proveedor").val();
     if(proveedor!=""){
          $.ajax({
            type: 'get',
            url:"{{route('retenciones.buscar_proveedor')}}",
            datatype: 'html',
            data: $("#buscador_form").serialize(),
            success: function(datahtml){
              //console.log(datahtml);

                $("#resultados").html(datahtml);
                //alert("dsada");
                $("#resultados").show();
                $("#contenedor").hide();

            },
            error: function(data){
                console.log(data);

            }
          });
     }else{
       $("#resultados").hide();
       $("#contenedor").show();
       
     }
  }
  $("#nombre_proveedor").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('compra_buscar_nombreproveedor')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                //console.log(data)
                response(data);
            }
            } );
        },
        minLength: 1,
    } );
  function secuencia_factura(){
    
  }

</script>

@endsection
