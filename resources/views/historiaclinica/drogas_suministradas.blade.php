<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="col-md-12"><h4>GASES:</h4></div>
     <div class="table-responsive col-md-6">
      <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
        <thead>
          <tr>
            <th >N.</th>  
            <th >Nombre</th>
            <th >Presentación</th>
            <th >Dosis</th>
            <th >Unidad</th>
            <th >Utilizado</th>
          </tr>
        <tbody >
          @php $contador=1; @endphp
          @foreach ($gasesadministrados as $value)
          
            <tr >
              <td >{{$contador}}</td>
              <td >{{ $value->dnombre}}</td>
              <td >{{ $value->dpresentacion}}</td>
              <td >{{ $value->dosis}}</td>
              <td >{{ $value->unidad }}</td>
              <td >.</td>
            </tr>
          @php $contador=$contador+1; @endphp  
            
          @endforeach

        </tbody>
      </table>
    </div>
    <div class="col-md-12"><h4>DROGAS A ADMINISTRAR:</h4></div>
    <div class="table-responsive col-md-6">
      <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
        <thead>
          <tr>
            <th >N.</th>  
            <th >Nombre</th>
            <th >Presentación</th>
            <th >Dosis</th>
            <th >Unidad</th>
            <th >Utilizado</th>
          </tr>
        </thead>
        <tbody >
          @php $contador=1; @endphp
          @foreach ($drogasadministradas as $value)
          @if($value->id==$contador)
            <tr >
              <td >{{$contador}}</td>
              <td >{{ $value->dnombre}}</td>
              <td >{{ $value->dpresentacion}}</td>
              <td >{{ $value->dosis}}</td>
              <td >{{ $value->unidad }}</td>
              <td >.</td>
            </tr>
          @php $contador=$contador+2; @endphp  
          @endif  
          @endforeach

        </tbody>
      </table>
    </div>
    <div class="table-responsive col-md-6">
      <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
        <thead>
          <tr>
            <th >N.</th>  
            <th >Nombre</th>
            <th >Presentación</th>
            <th >Dosis</th>
            <th >Unidad</th>
            <th >Utilizado</th>
          </tr>
        </thead>
        <tbody >
          @php $contador=2; @endphp
          @foreach ($drogasadministradas as $value)
          @if($value->id==$contador)
            <tr >
              <td >{{$contador}}</td>
              <td >{{ $value->dnombre}}</td>
              <td >{{ $value->dpresentacion}}</td>
              <td >{{ $value->dosis}}</td>
              <td >{{ $value->unidad }}</td>
              <td >.</td>
            </tr>
          @php $contador=$contador+2; @endphp  
          @endif  
          @endforeach

        </tbody>
      </table>
    </div>
  </div>
  
</div>