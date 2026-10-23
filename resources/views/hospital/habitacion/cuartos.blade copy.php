@extends('layouts.app-template-h')
@section('content')

<style>
  .caja {
    margin-bottom: 10px;
    border-radius: 10px;
    color: white;
  }

  .cuarto {
    margin-bottom: 10px;
  }
</style>

<div class="contetn" id="areachange">
  <section class="card-header">
    <h4>{{trans('hospitalizacion.GESTIONDECUARTOS')}}</h4>
  </section>
  <div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" id="content">

      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">&nbsp;</div>
        <div class="col-md-12">
          <h3><b>{{trans('hospitalizacion.Pacientesenespera')}}</b></h3>
        </div>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              &nbsp;
            </div>
            <div class="col-md-6" style="text-align: right;">
              <a class="btn btn-primary" href="{{route('hospital.gcuartos_habitacion')}}">{{trans('hospitalizacion.PacientesenHabitación')}} </a> 
              &nbsp;
            </div>
          </div>
        </div>
        <div class="col-md-12" style="padding-top: 10px;">
          <div class="table table-responsive">
            <table id="example2" class="table table-striped table-hover-animation">
              <thead>
                <tr>
                  <th>{{trans('hospitalizacion.Fecha')}}</th>
                  <th>{{trans('hospitalizacion.Paciente')}}</th>
                  <th>{{trans('hospitalizacion.Causa')}}</th>
                  <th>{{trans('hospitalizacion.Doctor')}}</th>
                  <th>{{trans('hospitalizacion.Sala')}}</th>
                  <th>{{trans('hospitalizacion.Observacion')}}</th>
                  <th>{{trans('hospitalizacion.Estado')}}</th>
                  <th>{{trans('hospitalizacion.Accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enespera as $y)
                @php $e= \Sis_medico\Ho_Hospitalizacion::where('id_traspaso',$y->id)->first(); @endphp
                <tr>
                  <td>{{$y->fecha}}</td>
                  <td>{{$y->paciente->apellido2}} {{$y->paciente->apellido1}} {{$y->paciente->nombre1}}</td>
                  <td>{{$y->causa}}</td>
                  <td>@if(isset($y->doctor)) {{$y->doctor->apellido1}} {{$y->doctor->nombre1}} @endif</td>
                  <td>@if(isset($y->sala)){{$y->sala->nombre_sala}}@endif</td>
                  <td>{{substr($y->observaciones,0,60)}}...</td>
                  <td>@if($y->estado==1) <span class="badge badge-light-danger"> Pendiente</span> @elseif($y->estado==2) {{trans('hospitalizacion.ENHABITACION')}} @endif</td>
                  <td> @if(is_null($e)) @if($y->estado==1) <button class="btn btn-primary btn-sm bsz" type="button" onclick="promise('{{$y->id}}',this)"> <i class="fa fa-plus"></i> &nbsp; </button> @endif @endif </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-md-12" id="anadir">
        </div>
        <br>
        <div class="col-md-12">
          <h3 class="">{{trans('hospitalizacion.SeleccionarSala')}}</h3>
        </div>  
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header with-border">
              <h3 class="card-title">{{trans('hospitalizacion.ESTADODELCUARTO')}}</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="card-body">

              <div class="form-group">
                <div class="caja col-md-6 col-sm-6 col-12" style="background: #00C851">{{trans('hospitalizacion.LIBRES')}}</div>
                <div class="caja col-md-6 col-sm-6 col-12" style="background: #ffbb33">{{trans('hospitalizacion.PREPARACION')}}</div>
                <div class="caja col-md-6 col-sm-6 col-12" style="background: #CC0000">{{trans('hospitalizacion.OCUPADAS')}}</div>
                <div class="caja col-md-6 col-sm-6 col-12" style="background: #90a4ae">{{trans('hospitalizacion.NODISPONIBLE')}}</div>
              </div>
              <div class="form-group">
                <h5>{{trans('hospitalizacion.TIPODECUARTO')}}</h5>
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-6">
                    <div class="row">
                      <div class="col-md-5 col-5">
                        <img src="{{asset('/')}}hc4/img/cama_simple_bloqueada.png" alt="">
                      </div>
                      <p>{{trans('hospitalizacion.SIMPLE')}}</p>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-6 col-6">
                    <div class="row">
                      <div class="col-md-6 col-6">
                        <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" alt="">
                      </div>
                      <p>{{trans('hospitalizacion.DOBLE')}}</p>
                    </div>
                  </div>

                </div>
              </div>
              <div class="form-grupo">
                <div class="row cuarto">
                  <div class="col-md-6 col-5">
                    <div class="row">
                      <div class="col-md-5 col-6">
                        <img src="{{asset('/')}}hc4/img/suite.png" alt="">
                      </div>
                      <p>{{trans('hospitalizacion.SUITE')}}</p>
                    </div>
                  </div>

                  <div class="col-md-6 col-6">
                    <div class="row">
                      <div class="col-md-6 col-6">
                        <img src="{{asset('/')}}hc4/img/triple_bloqueada.png" alt="">
                      </div>
                      <p>{{trans('hospitalizacion.TRIPLE')}}</p>
                    </div>
                  </div>

                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6" style="margin-bottom: 10px;">
                    <div class="row">
                      <div class="col-md-5 col-3">
                        <img src="{{asset('/')}}hc4/img/ejecutiva_bloqueada.png" alt="">
                      </div>
                      <p>{{trans('hospitalizacion.EJECUTIVA')}}</p>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <!-- ./box-body -->
          </div>
          <!-- /.box -->

          <!-- <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">PRECIO DE HABITACI&Oacute;N</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          /.box-header 
          <div class="box-body">
            <div class="row">
              <div class="col-lg-6 col-xs-6">
                small box 
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3>$ 80</h3>
                    <p>Simple</p>
                  </div>
                  <div class="icon">
                    <img src="{{asset('/')}}hc4/img/cama_simple_bloqueada.png" alt="">
                  </div>
                </div>
              </div>
               ./col 
              <div class="col-lg-6 col-xs-6">
                small box
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3>$ 120</h3>

                    <p>Doble</p>
                  </div>
                  <div class="icon">
                    <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" alt="">
                  </div>
                </div>
              </div>
          
              <div class="col-lg-6 col-xs-6">
        
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3>$ 140</h3>

                    <p>Triple</p>
                  </div>
                  <div class="icon">
                    <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" alt="">
                  </div>
                </div>
              </div>
           
              <div class="col-lg-6 col-xs-6">
              
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3>$ 170</h3>

                    <p>Suite</p>
                  </div>
                  <div class="icon">
                    <img src="{{asset('/')}}hc4/img/Ejecutiva_Bloqueada.png" alt=""> 
                  </div>
                </div>
              </div>
         
              <div class="col-lg-6 col-xs-6">
            
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3>$ 170</h3>

                    <p>Ejecutiva</p>
                  </div>
                  <div class="icon">
                    <img src="{{asset('/')}}hc4/img/Ejecutiva_Bloqueada.png" alt=""> 
                  </div>
                </div>
              </div>
            </div>
          </div>
       
        </div> -->
          <!-- /.box -->

        </div>

        <div class="col-md-8">
          <a href="{{route('hospital_admin.gestionh')}}" class="btn btn-primary"> &nbsp; <i class="fa fa-procedures"></i> Gestion de Habitaciones </a>
          <button type="button" class="btn btn-primary" onclick="freehabitation()"> <i class="fa fa-door-open"></i> </button>
          <div class="card card-danger">
            <div class="card-header with-border">
              <h3 class="card-title">{{TRANS('hospitalizacion.TIPOSDEHABITACION')}}</h3>
              <div class="card-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="card-body">
              <div class="col-md-12 col-sm-12 col-12">
                <div class="row">
                  <div class="col-md-4 col-sm-5 col-12">

                    <select name="tipoh" id="tipoh" class="form-control " onchange="muestra(this.value);" data-show-subtext="true" data-live-search="true">
                      <option value="">{{trans('hospitalizacion.TODOS')}}</option>
                      <option value="1">{{trans('hospitalizacion.SIMPLE')}}</option>
                      <option value="2">{{trans('hospitalizacion.DOBLE')}}</option>
                      <option value="3">{{trans('hospitalizacion.TRIPLE')}}</option>
                      <option value="4">{{trans('hospitalizacion.EJECUTIVA')}}</option>
                      <option value="5">{{trans('hospitalizacion.SUITE')}}</option>
                    </select>

                  </div>
                  <div class="col-md-4 col-sm-5 col-12">
                    <select name="pisomaster" id="pisomaster" onchange=" pisos(this.value);" class="form-control">
                      <option value="">{{trans('hospitalizacion.TODOS')}}</option>
                      <option value="1" id="piso1">{{trans('hospitalizacion.PISO1')}}</option>
                      <option value="2" id="piso2">{{trans('hospitalizacion.PISO2')}}</option>
                      <option value="3" id="piso3">{{trans('hospitalizacion.PISO3')}}</option>
                    </select>
                  </div>
                </div>
              </div>
              <br>
              <div class="content">
                <div class="row">
                  @foreach($pisos as $piso)
                  <!-- la habitacion tiene el area
                   
                   -->
                   
                  <div class="col-md-12 col-12 col-sm-12" id="pisosa{{$piso->id}}">
                    PISO {{$piso->id}}
                    <div class="row" style="margin-top: 10px;">
                      @foreach($piso->habitacion as $value)
                      @if(($value->estado)!=4)

                        <div class="my-2" id="camax{{($value->id_tipo)}}" style="@if(($value->estado!=4)) border: 1px solid #5DADE2; @else border: 1px solid 
                            #9a9a9a; @endif text-align: center; margin-left: 15px;padding: 30px;" title=" CUARTO N°: {{$value->codigo}}">
                          <p>{{$value->codigo}}</p>  
                          @foreach(($value->cama) as $val)
                            @php $e= \Sis_medico\Ho_Hospitalizacion::where('id_cama',$val->id)->where('estado','1')->first();  @endphp
                            @if(isset($val)) 
                            @foreach(($val->transaccion) as $mues)
                            
                            @php $imagen = $mues->cama->habitacion->tipo->descripcion.$mues->estado_cama.'.'.'png';  @endphp
                            <a @if(!is_null($e)) href="{{route('hospitalizacion.show',['id'=>$val->id,'ids'=>$e->id])}}" @else href="javascript:loadUrl({{$val->id}})"  @endif title=" CAMA N° : {{$val->codigo}}">
                              <img onmouseover="bigImg(this)" onmouseout="normalImg(this)" alt="camas" src="{{asset('/hc4/img'.'/'.$imagen)}}">
                            </a>
                          @endforeach
                          @endif
                          @endforeach
                        </div>
                      @endif
                      @endforeach
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            <!-- ./box-body -->
          </div>
          <!-- /.box -->
        </div>

      </div>
    </div>

  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  var table = $('#example2').DataTable({
    'paging': false,
    'dom': 'lBrtip',
    'lengthChange': true,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': true,
    'sInfoEmpty': true,
    'sInfoFiltered': true,
    "buttons": [{
        extend: 'copyHtml5',
        className: 'btn btn-icon btn-outline-primary btn-sm',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        className: 'btn btn-icon btn-outline-primary btn-sm',
        title: 'HOSPITAL ',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6]
        }
      },
      {
        extend: 'csvHtml5',
        className: 'btn btn-icon btn-outline-primary btn-sm',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        className: 'btn btn-icon btn-outline-primary btn-sm',
        pageSize: 'LEGAL',
        footer: true,
        title: 'HOSPITAL ',
        customize: function(doc) {
          doc.styles.title = {
            color: 'black',
            fontSize: '17',
            alignment: 'center'
          }
        },
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6]
        }
      }
    ],
  });

  function muestra(valor) {
    if (valor == 1) {
      $("[id*=camax1]").show('slow');
      $("[id*=camax2]").hide('slow');
      $("[id*=camax3]").hide('slow');
      $("[id*=camax4]").hide('slow');
      $("[id*=camax5]").hide('slow');
    } else if (valor == 2) {
      $("[id*=camax1]").hide('slow');
      $("[id*=camax2]").show('slow');
      $("[id*=camax3]").hide('slow');
      $("[id*=camax4]").hide('slow');
      $("[id*=camax5]").hide('slow');
    } else if (valor == 3) {
      $("[id*=camax1]").hide('slow');
      $("[id*=camax2]").hide('slow');
      $("[id*=camax3]").hide('slow');
      $("[id*=camax4]").show('slow');
      $("[id*=camax5]").hide('slow');
    } else if (valor == 4) {
      $("[id*=camax1]").hide('slow');
      $("[id*=camax2]").hide('slow');
      $("[id*=camax3]").show('slow');
      $("[id*=camax4]").hide('slow');
      $("[id*=camax5]").hide('slow');
    } else if (valor == 5) {
      $("[id*=camax1]").hide('slow');
      $("[id*=camax2]").hide('slow');
      $("[id*=camax3]").hide('slow');
      $("[id*=camax4]").hide('slow');
      $("[id*=camax5]").show('slow');
    } else if (valor == 0) {
      $("[id*=camax1]").show('slow');
      $("[id*=camax2]").show('slow');
      $("[id*=camax3]").show('slow');
      $("[id*=camax4]").show('slow');
      $("[id*=camax5]").show('slow');
    }
  }
</script>
<script type="text/javascript">
  function pisos(valor) {
    if (valor == 1) {
      $('#pisosa1').show('show');
      $('#pisosa2').hide('slow');
      $('#pisosa3').hide('slow');

    } else if (valor == 2) {
      $('#pisosa2').show('show');
      $('#pisosa1').hide('slow');
      $('#pisosa3').hide('slow');

    } else if (valor == 3) {
      $('#pisosa3').show();
      $('#pisosa1').hide('slow');
      $('#pisosa2').hide('slow');

    } else if (valor == 0) {
      $('#pisosa1').show('slow');
      $('#pisosa2').show('slow');
      $('#pisosa3').show('slow');
    }
  }
</script>
<script type="text/javascript">
  var pacientes_memory = [];

  function bigImg(x) {
    /*   x.style.height = "40px";
      x.style.width = "40px"; */
  }

  function normalImg(x) {
    /* x.style.height = "auto";
    x.style.width = "auto"; */
  }

  function promise(d, e) {
    $.ajax({
      url: "{{route('cuartos.get_paciente')}}", // upload url
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      data: {
        'id': d,
        'status': '1'
      },
      success: function(data) {
        console.log(data);
        if ((pacientes_memory.length) > 0) {
          console.log("error");
          Swal.fire('Mensaje:', 'Ya se encuentra seleccionado un paciente', 'error');
        } else {
          /*$('#anadir').append('<div class="col-md-12"> <div class="row"> <div class="col-md-12"> <div class="row"> <div> <b> Paciente Seleccionado:  </b> </div> &nbsp; <button type="button" onclick="reset(this)" class="btn btn-danger btn-sm"> <i class="fa fa-remove"> </i> </button> </div> </div> <div class="col-md-12"> <span class="badge bg-secondary">' + data.paciente + '</span> </div> <div class="col-md-12"> <b> Fecha y Hora: </b> </div> <div class="col-md-12"> <span class="badge bg-secondary">' + data.fecha + '</span>  </div> </div> </div>');*/
          $('#anadir').html(data);
          pacientes_memory.push({
            id: d
          });
          $(e).attr('disabled', 'disabled');

          console.log(pacientes_memory);
        }


      },
      error: function(xhr, status, error) {
        alert('Error, contactase con el programador');
      }
    });
  }

  function freehabitation() {
    Swal.fire({
      title: 'Esta seguro?',
      text: "Se liberará todas las camas, y se pasaran a preparacion",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        location.href = "{{route('hospitalizacion.freehabitation')}}";
      }
    })
    
  }

  function loadUrl(id) {
    if ((pacientes_memory.length) > 0) {
      $.ajax({
        url: "{{route('cuartos.get_paciente')}}", // upload url
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          'id': id,
          'status': '2'
        },
        success: function(data) {
          console.log(data);
          if (data.state == 'success') {
            var dataurl = pacientes_memory[0].id;
            pacientes_memory = [];
            location.href = "{{url('hospital/cuarto/paciente/')}}/" + id + "/" + dataurl;

          } else {
            Swal.fire('Mensaje', 'Existe el paciente asignado', 'error');
          }
        },
        error: function(xhr, status, error) {
          alert('Error, contactase con el programador');
        }
      });

    } else {
      Swal.fire('Mensaje', 'Seleccione paciente primero', 'error');
    }
  }

  function getModal(id) {
    $.ajax({
      type: "get",
      url: "{{route('cuartos.modal_paciente')}}",
      data: {
        'id': id,
      },
      datatype: "html",
      success: function(datahtml, data) {
        console.log(data);
        $("#content").html(datahtml);
        $("#calendarModal").modal("show");
      },
      error: function() {
        alert('error al cargar');
      }
    });

  }

  function reset(e) {
    $(e).parent().parent().parent().remove();
    $('.bsz').attr('disabled', false);
    pacientes_memory = [];
  }
</script>

<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
@endsection