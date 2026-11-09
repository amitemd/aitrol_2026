<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte por fecha</title>

    <!-- Bootstrap (opcional: o usa tu CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Contenedor con scroll para la tabla */
        .table-container {
            max-height: 420px; /* ajusta altura */
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: .25rem;
        }

        /* Encabezado sticky para la tabla */
        .table-container thead th {
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 2;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body class="bg-light">
<div>
    
    <center><h3 class="mb-5">Reporte de Historias clinicas descargadas</h3></center>
    <form id="form_busqueda" class="form-inline mb-3">
        <div class="form-group mr-5">
            <label for="from" class="mr-2">Fecha desde</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ old('from', $from ?? '') }}">
        </div>
        <div class="form-group mr-5">
            <label for="to" class="mr-2">Fecha hasta</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ old('to', $to ?? '') }}">
        </div>
        <button type="button" id="btnBuscar" class="btn btn-primary">Buscar</button>
    </form>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card">
        <div class="card-body p-2">
            <div class="table-container">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Descripción</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->nombre1 }} {{ $item->apellido1 }} {{ $item->apellido2 }}</td>
                                <td>{{ $item->descripcion ?? '—' }}</td>
                                <td>{{ $item->dato_ant2 ?? '—' }}</td>
                                <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay registros para el rango seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>

<!-- Optional JS (Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
    $("#btnBuscar").on("click", function() {
      $.ajax({
          type: "GET",
          url: "{{ route('agregar.cargar_log_hc') }}",
          data: {
              from: $("#from").val(),
              to: $("#to").val()
          },
          success: function(data) {
              $("#area_trabajo").html(data);
          },
          error: function() {
              alert("Error al cargar datos");
          }
      });
    });
</script>
</body>
</html>
