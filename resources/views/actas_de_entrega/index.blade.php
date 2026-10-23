@extends('actas_de_entrega.base')
@section('action-content')

<link rel="stylesheet">
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header">
            </div>

            <!-- /.box-header -->
            <div class="box-body">
                <form method="POST" action="{{ route('acta.search_acta') }}">
                    {{ csrf_field() }}

                <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">          
                    <label for="acta_tipo" class="col-md-3 control-label">Tipo</label>
                    <div class="col-md-9">
                        <select class="form-control input-sm" name="acta_tipo" id="acta_tipo">
                        <option @if($acta_tipo=='0') selected @endif value="0" >Todos</option>
                        <option @if($acta_tipo=='1') selected @endif value="1" >Celular</option>
                        <option @if($acta_tipo=='2') selected @endif value="2" >Tarjeta de acceso</option>
                        <option @if($acta_tipo=='3') selected @endif value="3" >Computador</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="col-md-3 control-label">Desde</label>
                    <div class="col-md-9">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                        <div class="input-group-addon">
                            @php $fecha_min = DB::table('acta_de_entrega')->min('fecha'); @endphp
                          <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '{{$fecha_min}}'; buscar();"></i>
                        </div>
                      </div>
                    </div>
                  </div>
      
                  <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
                    <div class="col-md-9">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                        <div class="input-group-addon">
                            @php $fecha_max = DB::table('acta_de_entrega')->max('fecha'); @endphp
                          <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = '{{$fecha_max}}'; buscar();"></i>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="nombres" class="col-md-3 control-label">Usuario recibe</label>
                    <div class="col-md-9">
                      <div class="input-group">
                        <input value="@if($nombres_search!=''){{$nombres_search}}@endif" type="text" class="form-control input-sm" name="nombres_search" id="nombres_search" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        <div class="input-group-addon">
                          <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres_search').value = '';"></i>
                        </div>
                      </div>
                    </div>
                  </div>               

                <div class="form-group col-md-1 col-xs-4" >
                    <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                </div>
                

                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('acta.crear_acta')}}" class="btn btn-primary">Agregar acta</a>
                </div>
                </form>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="table-responsive col-md-12 col-xs-12">                
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                            <thead>
                                <tr class="well-dark">
                                    <th style="width: 1%">ID</th>
                                    <th style="width: 6%">Fecha</th>
                                    <th style="width: 8%">Empresa</th>
                                    <th style="width: 10%">Tipo</th>
                                    <th style="width: 19%">Descripción</th>
                                    <th style="width: 13%">Complementos</th>
                                    <th style="width: 13%">Usuario Entrega</th>
                                    <th style="width: 13%">Usuario Recibe</th>
                                    <th style="width: 15%">Acción</th>
                                    
                                </tr>
                            </thead>
                            <tbody id="tablee">
                                @foreach($actas as $acta)
                                @php
                                    $tipo = DB::table('tipo_acta_de_entrega')->where('id', $acta->id_tipo)->select('tipo_acta_de_entrega.nombre')->first();
                                    $usuario_ent = DB::table('users')->where('id', $acta->id_usuario_entrega)->select('users.*')->first();
                                    $usuario_rec = DB::table('users')->where('id', $acta->id_usuario_recibe)->select('users.*')->first();
                                    $nombre_empresa = DB::table('empresa')->where('id', $acta->id_empresa)->value('empresa.nombrecomercial');
                                    $nombre_acta_firmada = DB::table('acta_de_entrega')->where('id', $acta->id)->value('archivo_acta');
                                    $p_color="black"; if($nombre_acta_firmada != null) {$p_color="#3c8dbc";}

                                @endphp
                                    <tr>
                                        <td style="color: {{$p_color}}">{{ $acta->id}}</td>
                                        <td style="color: {{$p_color}}">{{ $acta->fecha}}</td>
                                        <td style="color: {{$p_color}}">{{ $nombre_empresa }}</td>
                                        <td style="color: {{$p_color}}">{{ $tipo->nombre }}</td>
                                        <td style="color: {{$p_color}}">{{ $acta->descripcion }}</td>
                                        <td style="color: {{$p_color}}">{{ $acta->info_adicional }}</td>
                                        <td style="color: {{$p_color}}">{{ $usuario_ent->nombre1 }} {{ $usuario_ent->apellido1 }} @if ($usuario_ent->apellido2 != "N/A" && $usuario_ent->apellido2 != "(N/A)") {{ $usuario_ent->apellido2 }} @endif</td>
                                        <td style="color: {{$p_color}}">{{ $usuario_rec->nombre1 }} {{ $usuario_rec->apellido1 }} @if ($usuario_rec->apellido2 != "N/A" && $usuario_rec->apellido2 != "(N/A)") {{ $usuario_rec->apellido2 }} @endif</td>
                                        <td style="text-align: center">
                                            <a href="{{route('acta.editar_acta' ,['id'=>$acta->id])}}" class="btn btn-xs btn-warning" style="margin-right: 5px;">Editar
                                                <i class="fa fa-edit"></i></a>
                                            <a href="{{ route('acta.subir_acta', ['id'=>$acta->id]) }}" class="btn btn-xs btn-primary" style="margin-right: 5px;">Subir
                                                <i class="fa fa-upload"></i></a>
                                            <a href="{{ route('acta.visualizar_pdf', ['id'=>$acta->id]) }}" target="_blank" class="btn btn-xs btn-info">PDF
                                                <i class="fa fa-eye"></i></a>

                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>    
                    </div>
                </div>
                
                <div class="col-md-5 col-xs-12">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($actas->currentPage()-1)*$actas->perPage()}}  / @if(($actas->currentPage()*$actas->perPage())<$actas->total()){{($actas->currentPage()*$actas->perPage())}} @else {{$actas->total()}} @endif de {{$actas->total()}} registros</div>
                    </table>
                </div>

                <div class="col-md-7 col-xs-12">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $actas->appends(Request::only(['id','id_empresa', 'fecha', 'id_tipo', 'descripcion', 'imei', 'numero_cel', 'codigo_tarjeta', 'modelo_computador', 'info_adicional', 'archivo_acta', 'id_usuario_entrega', 'id_usuario_recibe']))->links() }}
                    </div>
                </div>
            
            </div>
        </div>


    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    
    <script>
        
        $(function () {
            $('#fecha').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: '{{$fecha}}',
                });
            $('#fecha_hasta').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: '{{$fecha_hasta}}',
                });
            $("#fecha").on("dp.change", function (e) {
                buscar();
            });

            $("#fecha_hasta").on("dp.change", function (e) {
                buscar();
            });
        });

        function buscar() {
        var obj = document.getElementById("#boton_buscar");
        obj.click();
        }

    </script>
@endsection
