@extends('contable.usuario_caja.base')
@section('action-content')

<style type="text/css">

  .container-4{
    overflow: hidden;
    vertical-align: middle;
    white-space: nowrap;
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


  .container-4 input#buscar_codigo::-webkit-input-placeholder {
    color: #65737e;
  }
        
  .container-4 input#buscar_codigo:-moz-placeholder { /* Firefox 18- */
    color: #65737e;  
  }
        
  .container-4 input#buscar_codigo::-moz-placeholder {  /* Firefox 19+ */
    color: #65737e;  
  }
        
  .container-4 input#buscar_codigo:-ms-input-placeholder {  
    color: #65737e;  
  }


  .container-4 input{
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
     
  .cabecera{
      background-color: #3c8dbc;
      border-radius: 8px;
    
  }
      
  .color_texto{
      color: #fff;
  }   

</style>

    <section class="content">
      <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h8 class="box-title size_text">Listado Usuarios Punto Emision</h8>
            </div>
            <div class="col-md-2">
              <button type="button" onclick="location.href='#'" class="btn btn-danger size_text" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar Usuario Punto Emision
              </button>
            </div>
        </div>
       
      </div>
    </section>
  
  <script type="text/javascript">
    $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
    })
  </script> 
@endsection