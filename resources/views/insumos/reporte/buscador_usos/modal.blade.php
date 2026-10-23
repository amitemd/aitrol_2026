<div class="row">
    <div class="col-md-12">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        <div class="col-md-12">
            <h3>Producto: {{$producto->nombre}}</h3>
        </div>
        <div class="table-responsive col-md-12">
            <table class="table table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th>Serie</th>
                        <th>Paciente</th>
                        <th>Fecha Procedimiento</th>
                        <th>Procedimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $value)
                        @php
                          $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                          $mas = true;
                          $texto = "";
                          foreach($adicionales as $value2)
                            {
                              if($mas == true){
                               $texto = $texto.$value2->procedimiento->nombre  ;
                               $mas = false;
                              }
                              else{
                                $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                              }
                            }
                        @endphp
                        <tr>
                            <td>{{$value->serie}}</td>
                            <td>{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}}</td>
                            <td>{{$value->fecha_atencion}}</td>
                            <td>@if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
