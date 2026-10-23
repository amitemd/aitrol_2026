 <table id="example2" class="table table-bordered table-hover dataTable col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
            <thead>
              <tr role="row">
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Nombres:</th>
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Apellidos:</th> 
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Telefono:</th>
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Email:</th>
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Fecha de nacimiento:</th>
                 <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Ocupacion:</th>
                  <th style="text-align: center;" width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Accion:</th>
                 
              </tr>
            </thead>
            <tbody>
           @foreach ($paciente as $value)
                  <tr role="row" class="odd">
                <td> {{ $value->nombre1}} {{$value->nombre2}}</td>
                <td> {{ $value->apellido1}} {{$value->apellido2}}</td>
                <td> {{ $value->telefono1}} / {{$value->telefono2}}</td> 
                <td> {{ $value->email}} </td>
                <td> {{$value->fecha_nacimiento}}</td>
                <td>{{$value->ocupacion}}</td>
                <td> <a href="{{ route('hospital.questionario',['id' => $value->id])}}"  class="btn btn-warning col-md-6 col-xs-6 btn-margin" class="button-b">  Formulario</a> </td>
              @endforeach     
            </tbody>
          </table>