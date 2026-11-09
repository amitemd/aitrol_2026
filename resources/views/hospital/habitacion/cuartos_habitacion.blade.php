@extends('layouts.app-template-h')
@section('content')

<style>
  .room-patient-list {
    --room-navy: #07375f;
    --room-blue: #0b6fac;
    --room-cyan: #22b8cf;
    --room-soft: #edf8fb;
    --room-border: #d2e7ef;
    --room-text: #17324d;
    --room-muted: #647b8e;
    color: var(--room-text);
  }

  .room-patient-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 16px;
    padding: 22px 24px;
    border: 1px solid var(--room-border);
    border-radius: 12px;
    background: radial-gradient(circle at 12% 0, rgba(34, 184, 207, .2), transparent 34%), linear-gradient(125deg, #fff, #eef9fc);
    box-shadow: 0 12px 30px rgba(7, 55, 95, .07);
  }

  .room-patient-hero h3 { margin: 0; color: var(--room-navy); font-size: 24px; font-weight: 900; }
  .room-patient-hero p { margin: 6px 0 0; color: var(--room-muted); font-weight: 600; }
  .room-patient-hero .hero-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    margin-right: 10px;
    border-radius: 10px;
    background: var(--room-soft);
    color: var(--room-blue);
    vertical-align: middle;
  }

  .room-patient-card .card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .room-patient-card .card-title { margin: 0; color: var(--room-navy); font-size: 17px; font-weight: 900; }
  .room-patient-count {
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--room-soft);
    color: var(--room-blue);
    font-size: 12px;
    font-weight: 800;
  }
  .room-patient-card .table-responsive { overflow-x: auto; }
  .room-patient-card .table tbody tr { transition: background-color .15s ease; }
  .room-patient-card .table tbody tr:hover { background: #f1f9fc; }
  .room-patient-card .btn-primary { white-space: nowrap; }

  @media (max-width: 767px) {
    .room-patient-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
    .room-patient-hero .btn { width: 100%; }
  }
</style>

<div class="room-patient-list" id="areachange">
  <section class="room-patient-hero">
    <div>
      <h3><span class="hero-icon"><i class="fa fa-user-injured"></i></span> Pacientes en habitación</h3>
      <p>Consulta los pacientes hospitalizados y accede a su gestión clínica desde un solo lugar.</p>
    </div>
    <a href="{{route('hospital.gcuartos')}}" class="btn btn-outline-primary">
      <i class="fa fa-arrow-left"></i> Volver a gestión de cuartos
    </a>
  </section>
  <div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" id="content">

      </div>
    </div>
  </div>
  <div class="card room-patient-card">
    <div class="card-header">
      <h4 class="card-title"><i class="fa fa-procedures"></i> {{trans('hospitalizacion.PacientesenHabitación')}}</h4>
      <span class="room-patient-count">{{$enhabitacion->count()}} registros</span>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="table table-responsive">
            <table id="example2" class="table table-striped table-hover-animation">
              <thead>
                <tr>
                  <th>{{trans('hospitalizacion.Fecha')}}</th>
                  <th>{{trans('hospitalizacion.Paciente')}}</th>
                  <th>{{trans('hospitalizacion.Causa')}}</th>
                  <th>{{trans('hospitalizacion.Doctor')}}</th>
                  <th>{{trans('hospitalizacion.SalaHabitacion')}}</th>
                  <th>{{trans('hospitalizacion.Observacion')}}</th>
                  <th>{{trans('hospitalizacion.Estado')}}</th>
                  <th>{{trans('hospitalizacion.Accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enhabitacion as $y)
                @php $e= \Sis_medico\Ho_Hospitalizacion::where('id_traspaso',$y->id)->first(); @endphp
                @if(!is_null($e))
                <tr>
                  <td>{{$y->fecha}}</td>
                  <td>{{$y->paciente->apellido2}} {{$y->paciente->apellido1}} {{$y->paciente->nombre1}}</td>
                  <td>{{$y->causa}}</td>
                  <td>@if(isset($y->doctor)) {{$y->doctor->apellido1}} {{$y->doctor->nombre1}} @endif</td>
                  <td>@if(isset($y->sala)){{$y->sala->nombre_sala}}@endif</td>
                  <td>{{$y->observaciones}}</td>
                  <td>@if($y->estado==1) <span class="badge badge-light-danger"> {{trans('hospitalizacion.Pendiente')}}</span> @elseif($y->estado==2) {{trans('hospitalizacion.ENHABITACION')}} @endif</td>
                  <!--td> @if(is_null($e)) @if($y->estado==1) <button class="btn btn-primary btn-sm bsz" type="button" onclick="promise('{{$y->id}}',this)"> <i class="fa fa-plus"></i> &nbsp; </button> @endif @endif </td-->
                  <td><a class="btn btn-primary" href="{{route('hospitalizacion.show',['id'=>$e->id_cama,'id_d'=>$e->id])}}"> {{trans('hospitalizacion.Gestion')}}</a></td>
                </tr>
                @endif
                @endforeach
              </tbody>
            </table>
          </div>
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
        text: "{{trans('hospitalizacion.Copiar')}}",
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
          Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.PacienteYaSeleccionado')}}", 'error');
        } else {
          $('#anadir').append('<div class="col-md-12"> <div class="row"> <div class="col-md-12"> <div class="row"> <div> <b> {{trans('hospitalizacion.PacienteSeleccionado:')}}  </b> </div> &nbsp; <button type="button" onclick="reset(this)" class="btn btn-danger btn-sm"> <i class="fa fa-remove"> </i> </button> </div> </div> <div class="col-md-12"> <span class="badge bg-secondary">' + data.paciente + '</span> </div> <div class="col-md-12"> <b> {{trans('hospitalizacion.FechayHora:')}} </b> </div> <div class="col-md-12"> <span class="badge bg-secondary">' + data.fecha + '</span>  </div> </div> </div>');
          pacientes_memory.push({
            id: d
          });
          $(e).attr('disabled', 'disabled');

          console.log(pacientes_memory);
        }


      },
      error: function(xhr, status, error) {
        alert("{{trans('hospitalizacion.ErrorContactarSoporte')}}");
      }
    });
  }

  function freehabitation() {
    Swal.fire({
      title: "{{trans('hospitalizacion.ConfirmarAccion')}}",
      text: "{{trans('hospitalizacion.LiberarCamasTexto')}}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: "{{trans('hospitalizacion.Si')}}"
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
            Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.PacienteAsignado')}}", 'error');
          }
        },
        error: function(xhr, status, error) {
          alert("{{trans('hospitalizacion.ErrorContactarSoporte')}}");
        }
      });

    } else {
      Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.SeleccionePacientePrimero')}}", 'error');
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
        alert("{{trans('hospitalizacion.ErrorCargar')}}");
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
