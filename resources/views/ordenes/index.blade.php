@extends('ordenes.base')
@section('action-content')
<style>
    th,
    td {
        text-align: center;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12 col-lg-12">
                <div class="col-md-10 col-lg-9">
                    <h4 style="text-align: left;">Ordenes de Procedimiento</h4>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" id="form" action="{{route('consulta_ordenes.index')}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-3">
                            <label for="tipo" class="col-md-7 texto">Tipo Procedimiento</label>
                            <div class="col-md-5">
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="">Seleccione</option>
                                    <option @if($tip=='0' ) selected @endif value="0">Endoscopico</option>
                                    <option @if($tip=='1' ) selected @endif value="1">Funcional</option>
                                    <option @if($tip=='2' ) selected @endif value="2">Imagenes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha" class="col-md-5 texto">Fecha Desde</label>
                            <div class="col-md-7">
                                <input style="text-align: center;line-height:10px;" type="date" name="desde" id="desde" class="form-control" value="{{$desde}}">
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="fecha" class="col-md-5 texto">Fecha Hasta</label>
                            <div class="col-md-7">
                                <input style="text-align: center;line-height:10px;" type="date" name="hasta" id="hasta" class="form-control" value="{{$hasta}}">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <button type="submit" id="buscar" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Fecha Orden</th>
                                    <th style="width: 20%;">Cedula</th>
                                    <th style="width: 20%;">Paciente</th>
                                    <th style="width: 20%;">Seguro</th>
                                    <th style="width: 20%;">Doctor</th>
                                    <th style="width: 20%;">Tipo Procedimiento</th>
                                    <th style="width: 20%;">Grupo Procedimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($consultaGeneral as $value)
                            @php
                            //dd($value);
                            $nombreProc = [];
                            $tipoProcedimiento = DB::table('orden_tipo as ot')
                            ->join('orden_procedimiento as op', 'ot.id', 'op.id_orden_tipo')
                            ->where('ot.id_orden', $value->id)
                            ->get();

                            //$tipoProcedimiento = Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$value->id)->get();
                            $evolucion = Sis_medico\Hc_Evolucion::find($value->id_evolucion);
                            //dd($evolucion->historiaclinica->seguro->nombre);
                            //dd($tipoProcedimiento);
                            foreach($tipoProcedimiento as $val){
                            $nombreProce=Sis_medico\Procedimiento::where('id',$val->id_procedimiento)->get();
                            array_push($nombreProc,$nombreProce);
                            }
                            $paciente = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
                            $doctor = Sis_medico\User::where('id',$value->id_doctor)->first();
                            @endphp

                            <tr>
                                <td>{{$value->fecha_orden}}</td>
                                <td>{{$value->id_paciente}}</td>
                                <td>@if(empty($paciente)) @else{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}@endif</td>
                                <td>@if(!is_null($evolucion)) {{$evolucion->historiaclinica->seguro->nombre}} @else {{$paciente->seguro->nombre}} @endif</td>
                                <td>@if(empty($doctor)) @else{{$doctor->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}}@endif</td>
                                <td>@if(($value->tipo_procedimiento)==0) Endoscopico @elseif(($value->tipo_procedimiento)==1) Funcional @elseif(($value->tipo_procedimiento)==2) Imagenes @endif</td>
                                <td>@if(empty($nombreProc)) @else @foreach($nombreProc as $key=>$value) @if($key>0) + @endif @foreach($value as $val) {{$val->nombre}} @endforeach @endforeach @endif</td>
                            </tr>
                            @endforeach
                            </tbody>

                        </table>
                        <?php /*
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($consultaGeneral->currentPage() - 1) * $consultaGeneral->perPage())}} / {{count($consultaGeneral) + (($consultaGeneral->currentPage() - 1) * $consultaGeneral->perPage())}} de {{$consultaGeneral->total()}} registros
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{$consultaGeneral->appends(['tipo'=>$tip,'desde'=>$desde,'hasta'=>$hasta])->links() }}
                                </div>
                            </div>
                        </div>
                        */ ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $( document ).ready(function() {
            
        $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
        "scrollY": 300,
        "scrollX": true,
        'scrollCollapse': true,
        responsive:'true',
        dom: 'Bfrtilp',
        buttons:[
            {
              extend:    'excelHtml5',
              text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
              titleAttr: "{{trans('winsumos_reportes.exportar_excel')}}",
              className: 'btn btn-success'
            },
            {
              extend:    'pdfHtml5',
              text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
              titleAttr: "{{trans('winsumos_reportes.exportar_pdf')}}",
              className: 'btn btn-danger'
            },
            {
              extend:    'print',
              text:      '<i class="fa fa-print"></i> ',
              titleAttr: "{{trans('winsumos_reportes.imprimir')}}",
              className: 'btn btn-info'
            },
          ]
      });

    });
</script>
@endsection