     <table class="table table-sm table-bordered">
                            <thead class="table-primary">
                                <tr> 
                                  <th scope="col">Cedula del pacienteS:</th>
                                    <th scope="col">Nombres del paciente ingresado:</th>
                                    <th scope="col">Apellidos del paciente: </th>
                                    <th scope="col">Fecha de ingreso del paciente:</th>
                                    <th scope="col">Cedula del paciente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nombres as $value)
                                <tr>
                                    <td>{{$value->id_paciente}}</td>
                                    <td>{{$value->nombre1}} {{$value->nombre2}}</td>
                                    <td>{{$value->apellido1}} {{$value ->nombre2}}</td>
                                    <td>{{$value->created_at}}</td>
                                    <td>{{$value->id_cama}}</td>
                                </tr>
                            </tbody>
                             @endforeach
                        </table>
            
