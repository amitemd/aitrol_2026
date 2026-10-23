@extends('contable.cruce_valores_cliente.base')
@section('action-content')
<!-- Ventana modal editar -->
<style>
  *{
    font-family: var(--fuentePrincipal);
  }
</style>
<div class="modal fade" id="modal_devoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro de Cruce de Valores</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">CRUCE DE VALORES A FAVOR</h3>
            </div>

            <div class="col-md-1 text-right">
                <button onclick="location.href='{{route('cruce_clientes.create')}}'" class="btn btn-success" >
                  <i class="fa fa-file"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR CRUCE DE VALORES</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('cruce_clientes.search') }}" >
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">Detalle</label>
                </div>
                <div class="form-group col-md-2 col-xs-2 container-4" style="padding-left: 1px;">
                      <input class="form-control" type="text" id="detalle" name="detalle"  placeholder="Ingrese Detalle de Cruce..."  />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">Cliente: </label>
                </div>
                <div class="form-group col-md-3 col-xs-2 container-4" style="padding-left: 15px;">
                    <select style="width: 100%;" name="id_cliente" id="id_cliente" class="form-control select2">
                      <option value="">Seleccione...</option>
                      @foreach($cliente as $value)
                       <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>


                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="id_asiento_cabecera">Asiento</label>
                </div>
                <div class="form-group col-md-2 col-xs-2 container-4" style="padding-left: 1px;">
                      <input class="form-control" type="text" id="id_asiento_cabecera" name="id_asiento_cabecera"  placeholder="Ingrese Asiento..."  />
                </div>

                <br><br><br>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">Fecha: </label>
                    
                </div>
                <div class="form-group col-md-2 col-xs-2 container-2" style="padding-left: 15px;">
                    <input class="form-control" name="fecha_pago" id="fecha_pago" type="date" > 
                </div>
                <div class="col-xs-1">
                  <button type="submit" id="buscarEmpleado" class="btn btn-primary" style="border-radius: 20px;padding: 5px 24px;">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE CRUCE DE VALORES</label>
            </div>
        </div> 
        <div class="box-body dobra">
          <div class="form-group col-md-12">
            <div class="form-row">
                <div id="resultados">
                </div>  
                <div id="contenedor">
                  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                        

                        <table id="example2" class="table table-hover table-striped">
                          <thead class="well-dark">
                          <tr>
                            <th>Secuencia</th>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Cliente</th>
                            <th>Observacion</th>
                            <th>Creado por</th>
                            <th>Estado</th>
                            <th>Acción</th>
                          </tr>
                          </thead>
                          <tbody>
                            @foreach ($anticipo as $value)
                            <tr>
                              <td>@if(($value->secuencia)!=null) {{$value->secuencia}} @endif</td>
                              <td>@if(($value->fecha_pago)!=null) {{$value->fecha_pago}} @endif</td>
                              <td>@if(!is_null($value->id_asiento_cabecera)) {{$value->id_asiento_cabecera}}  @endif</td>
                              <td>@if(!is_null($value->cliente)) {{$value->cliente->nombre}} @endif</td>
                              <td>@if(!is_null($value->detalle)) {{$value->detalle}} @endif</td>
                              <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                              <td>@if($value->estado==1) ACTIVO @else INACTIVO @endif </div> </div> </div> </td>
                              <td>
                              @if(($value->estado)==1)
                              <button class="btn btn-danger" onclick="anular(this, {{$value->id}});"><i class="fa fa-trash"></i></button> 
                              @endif
                              <a class="btn btn-success btn-gray" href="{{route('cruce_clientes.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                              </td>
                            </tr>
                             
                            @endforeach
                          </tbody>
                          <tfoot>
                            <!-- <tr>
                              <th>Rendering engine</th>
                              <th>Browser</th>
                              <th>Platform(s)</th>
                              <th>Engine version</th>
                              <th>CSS grade</th>
                            </tr> -->
                          </tfoot>
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} / {{count($anticipo) + (($anticipo->currentPage() - 1) * $anticipo->perPage())}} de {{$anticipo->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                {{ $anticipo->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha_pago']))->links() }}
                              </div>
                            </div>
                          </div>   
                        </div>
                      </div>

                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
  </section>
  </div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    $('.select2').select2({
            tags: false
        });
    $(document).ready(function(){
      $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': true,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : true,
      'sInfoEmpty':  true,
      'sInfoFiltered': true,
      'language': {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

    });
    $('#modal_devoluciones').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');

  });
  function anular(e, id){
    Swal.fire({
        title: '¿Desea Anular esta comprobante?',
        text: "No puedes revertir esta acccion!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate="";
        test(id,id);
      }
    })
  }
  async function test(e, id) {
    try {
      const { value: text } = await Swal.fire({
          input: 'textarea',
          inputPlaceholder: 'Ingrese motivo de anulación...',
          inputAttributes: {
            'aria-label': 'Ingrese motivo de anulación'
          },
          showCancelButton: true
      })

      if (text) {
        $.ajax({ 
          type: 'get',
          url:"{{ url('contable/anticipo/anular_anticipo_cliente/')}}/"+id,
          datatype: 'json',
          data: {'concepto':text},
          success: function(data){
            console.log(data);
            // let asiento = 
            if(data.status == 'success') {
              let ref = `<a style="color:red; font-weight:bold;" target="_blank" href="{{url('contable/contabilidad/libro/edit/${data.id_asiento}')}}">ASIENTO : ${data.id_asiento}</a>`
              alertas(data.status, data.status.toUpperCase(), `${data.msj} <br> <b>${ref}</b>`)
              e.style.display = 'none'
            }else{
              alertas(data.status, data.status.toUpperCase(), `${data.msj}`)
            }
          },
          error: function(data){
            console.log(data);
          }
        }); 

      }
                  
    } catch(err) {
      console.log(err);
    }
  }
  
</script>
@endsection


