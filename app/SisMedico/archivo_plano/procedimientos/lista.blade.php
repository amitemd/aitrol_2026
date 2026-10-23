@extends('archivo_plano.procedimientos.baselista')
<div style="display:none">
{{ $cab1= Request::get('cabecera') }}
{{ $des1= Request::get('descripcion') }}
</div>

<!-- Main content -->
<section class="content">
  <div class="col-md-12">
    <div class="col-md-4">
      <span id="Label6" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingreso de Procedimientos</span>
    </div>
    
  </div>
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-6">
            <!--<h3 class="box-title">Lista Plantillas </h3>-->
          </div>
      </div>
    </div> 
    <!-- /.box-header -->
    <div class="box-body">
        
        <!--AQUI VA EL BUSCADOR-->
        <form class="form-horizontal" action= "" method="GET">
          <input type="hidden" name="cabecera" value="{{ $cab1 }}">
          <div class="row">
            <div class="col-md-4">
              <label for="fecha" class="col-md-2 control-label">Fecha:&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
              <input type="date" name="fecha" id="fecha">
            </div>
            <br>
            <div class="col-md-8">
                <label for="descripcion" class="col-md-4 control-label">Procedimiento:</label>
                <select  name="descripcion"  style="width: 420px">
                @foreach($lista as $fila)
                <option value="{{ $fila->codigo }}">{{ $fila->descripcion }}</option>
                @endforeach
                </select>&nbsp;
                <button class="btn btn-info" type="submit">Buscar</button>
                <a class="btn btn-success" onclick="pasader('{{ $cab1 }}, {{ $des1 }}')" >Usar</a>
            </div>
          </div>
          <br>
          
          
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Tipo</th>
                  <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Código</th>
                  <th width="60%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripción</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($procedimientos as $procedimiento)
                  <tr role="row" class="odd">
                    <td class="sorting_1">{{ $procedimiento->tipo }}</td>
                    <td> {{ $procedimiento->codigo }}</td>
                    <td> {{ $procedimiento->descripcion }}</td>
                  </tr>
              @endforeach
              </tbody>
              <tfoot>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
   
<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
<script>


function closeIframe() {
   var iframe = document.getElementById('dito');
   iframe.parentNode.removeChild(iframe);
}

function pasader(i, j){
  var fec = document.getElementById('fecha').value;
  if (fec=='') {
    alert('Fecha es Requerida');
    //swal("Error!","Fecha es Requerida");
    return false;
  }else{
    var valInput = i;
    var i= i+','+fec;
    $("input[name='lulu']",parent.document.body).val(valInput);
    window.parent.CloseModal(window.frameElement, i, j, fec);
  }
}
</script>