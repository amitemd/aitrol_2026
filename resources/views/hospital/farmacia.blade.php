@extends('layouts.app-template-h')
@section('content')
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
      display: none;
    }
    .btn_ordenes1{
		font-size: 10px ;
		width: 100%;
		background-color: #004AC1;
		text-align: center;
		height: 100%;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 5px;
	}
  .parent{
    height: 462px;
  }
  .titulo{
 
  border-bottom:  solid 1px #004AC1 !important;
  }
  .coloresb1{
  background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
  }
  h3, h4, h5, h6{
  
  }
  h3{
    color: white;
  }
  .btn{
    color: black;
    background: 
  }
  .borde{
    border-left: 3px solid blue;
    background-color: #ECF0F1;
  }
  
</style>

<div class="content">
  <section class = "content-header">
    <div class="row">
      <div class="col-md-2">
        <h4>{{trans('farmacia.FARMACIA')}}</h4>
      </div>
    </div>
  </section>
  <div class="row">

    <!---CUADRO DE OPCIONES DE FARMACIA--->
    <div class="col-md-12">
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 col-sm-3 col-12" style="margin-top: 20px;">
            <h6 style=" color: #004AC1">{{trans('farmacia.LISTADEPRODUCTOS')}}</h6>
          </div>
          <div class="col-md-10 col-sm-10 col-12">
            <div class="col-md-12 container">
              <div class="row">
              
                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href='{{route('hospital.marcas')}}'">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.Marcas')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>

                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href='{{route('hospital.tipoproducto')}}'">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.TiposdeProductos')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>

                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href='{{route('hospital.producto')}}'">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.Producto')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>

                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1  " onclick="location.href='{{route('hospital.bodegap')}}'"  >
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;" >
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px;">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.Bodega')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>
                
              </div>
            </div>
            <div class="col-md-12 container">
              <div class="row">
            
                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href='{{route('hospital.pedidosproductos')}}'">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div onclick="location.href='"s class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label  style="font-size: 14px; color: white;">{{trans('farmacia.PedidosRealizados')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>
              
                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href=''">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.EnTransito')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>
              
                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 10px; padding-right: 10px;">
                          <div class="col-md-2 col-sm-2 col-2" style="padding-left: 3px; padding-right: 5px" >
                            <img style="color:black;" width="16px" src="{{asset('/')}}hc4/img/descargar.png">
                          </div>
                          <div class="col-md-9" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.DescargarReporte')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>

                <div class="col-md-3 col-sm-6 col-12" style="margin-bottom: 15px; height: 30px">
                  <a class="btn btn-info btn_ordenes1 "  onclick="location.href='{{route('hospital.proveedores')}}'">
                    <div class="col-12" >
                        <div class="row" style="padding-left: 15px; padding-right: 15px;">
                          <div class="col-10" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                            <label style="font-size: 14px; color: white;">{{trans('farmacia.Proveedores')}}</label>
                          </div>
                        </div>
                    </div>
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!--ETIQUETA DE BUSQUEDA DE LA TABLA-->
    <div class="col-md-12 my-3">
      <div class="card">
        <div class="card-header" >
          <form action="" method="POST" id="form_buscador" >
          {{ csrf_field() }}
            <div class="row">
              <div class="col-6 my-2">
                <h3 class="card-title">{{trans('farmacia.B&Uacute;SQUEDADEMEDICAMENTOS')}}</h3>
              </div>
              <div class="col-6 card-tools">
                <div class="input-group">
                  <div class="col-md-4 my-1">
                    <input id="codigo" type="text"  class="form-control" placeholder="codigo">
                  </div>
                  <div class="col-md-4 my-1">
                    <input id="nombre" type="text" class="form-control" placeholder="nombre">
                  </div>
                  <div class="col-md-4 ">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>{{trans('farmacia.BUSCAR')}}</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- /.card-header -->
        <div id="resultados">
        </div>
        <div  id="muestra"  class="card-body table-responsive">
          <table  class="table table-hover">
            <thead class="table-primary">
              <tr >
                <th scope="col">{{trans('farmacia.Marca')}}</th>
                <th scope="col">{{trans('farmacia.Codigo')}}</th>
                <th scope="col">{{trans('farmacia.Nombre')}}</th>
                <th scope="col">{{trans('farmacia.Descripción')}}</th>
                <th scope="col">{{trans('farmacia.Medida')}}</th>
                <th scope="col">{{trans('farmacia.StockMinimo')}}</th>
                <th scope="col">{{trans('farmacia.FormadeDespacho')}}</th>
                <th scope="col">{{trans('farmacia.RegistroSanitario')}}</th>
                <th scope="col">{{trans('farmacia.TipodeProducto')}}</th>
                <th scope="col">{{trans('farmacia.CantidaddeUsos')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($farmacia as $value)
                <tr role="row" class="odd">
                  <td>{{$value->marcas->nombre}}</td>
                  <td>{{$value->codigo}}</td>
                  <td>{{$value->nombre}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td>{{$value->medida}}</td>
                  <td>{{$value->minimo}}</td>
                  <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>  
                  <td>{{$value->registro_sanitario}}</td>
                  <td>{{$value->tipo->nombre}}</td>
                  <td>{{$value->usos}}</td>                          
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
    $("#fecha").change(function(){
      alert($("#fecha").val());
    });
    function enviar_enter(e){
      tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==13){
        buscador_paciente_fecha();
      };
    }
    function cambio_fecha(){
      alert('cambio');
    }
</script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
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
<script>
  window.addEventListener('load',function(){
    document.getElementById("codigo").addEventListener("keyup", () => {
      if((document.getElementById("codigo").value.length)>0)
        fetch(`farmacia/buscadorfarmacia?codigo=${document.getElementById("codigo").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
        $("#muestra").hide();
    })
    document.getElementById("nombre").addEventListener("keyup", () => {
      if((document.getElementById("nombre").value.length)>0)
        fetch(`farmacia/buscadorfarmacia?nombre=${document.getElementById("nombre").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
        $("#muestra").hide();
    })     
  });
</script>

@endsection