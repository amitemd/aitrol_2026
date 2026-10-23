@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
  .control_width{
    width: 90%;
    height: 60%;
  }
  .has-cc span img{
            width:2.775rem;
  }
  .has-cc .form-control-cc {
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;
            margin-right: 1px;

  }
  .has-cc .form-control-cc2{
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;
            margin-right: 1px;
  }
  .cvc_help{
            cursor: pointer;
  }
  .cabecera{
      background-color: #3c8dbc;
      border-radius: 8px;
  }
  .color_label{
        color: #ffffff;
  }
   /* Style the tab */
/* Style the tab */
  .tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #ddd;
  }

  /* Create an active/current tablink class */
  .tab button.active {
    background-color: #ccc;
  }

  /* Style the tab content */
  .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }
  .tabcontent2 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

</style>
<script type="text/javascript">  

$(function () {    
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});    
</script>
<div class="content">


    <div class="box">
         <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">

            <div class="col-md-6">
              <div class="row">
                  <div class="col-md-12" style="text-align: center;">
                    <label class="control-label">LISTA DE ACCIONES</label>
                  </div>
                  <div class="col-md-4">
                      <a href="{{route('acreedores_ccreate')}}" class="btn btn-primary size_text color_label">Agregar Comprobante Egresos Varios</a>
                  </div>
              </div>
            </div>

         </div>

        <div class="box-body">
          <div class="col-md-12">
                  <div class="col-md-12" id="compra">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr>
                                <th style="width: 5%;">Nùmero Factura</th>
                                <th style="width: 20%;">Proveedor</th>
                                <th style="width: 20%;">Fecha Emisión</th>
                                <th style="width: 10%">Total Factura</th>
                                <th style="width: 5%;">Pendiente</th>
                                <th style="width: 10%">Fecha</th>
                                <th style="width: 20%;">Acción</th>
                              </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>Número de factura</td>
                                    <td></td>
                               </tr>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
   $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });

  });

</script>

@endsection