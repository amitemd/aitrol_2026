@extends('archivo_plano.archivo.base')
@section('action-content')
@php
$tipo_usuario = Auth::user()->id_tipo_usuario;
@endphp

<section class="content">
    <div class="box-body dobra">
        <form method="POST" id="reporte_master"action="{{ route('nuevoarchivoplano.reporte_valores_por_empresa')}}" >
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-4 ">
                    <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                    <div class="col-md-7">
                        <input id="mes_plano" value="@if(isset($mes_plano)) {{$mes_plano}} @endif" type="text" class="form-control input-sm" name="mes_plano" autocomplete="off">
                    </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                        <div class="col-md-7">

                            <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                                @foreach($empresas as $value)
                                    
                                    <option  value="{{$value->id}}"  @if($empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                                    
                                @endforeach
                            </select>
                        </div>
                    </div>
                <div class="form-group col-md-4 ">
                        <label for="seguro" class="col-md-4 control-label">Seguro:</label>
                        <div class="col-md-7">
                            <select id="seguro" name="seguro" class="form-control input-sm" >
                                @foreach($seguros_publicos as $value)
                                    
                                    <option  @if($seguro == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                            
                                @endforeach
                            </select>
                        </div>
                    </div>
                <div class="form-group col-md-2 col-xs-2" style="text-align: right;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
                </div>
            </div>

        </form>
        <div class="form-group col-md-12">
            <div class="table-responsive col-md-12">
                <table id="log_auditoria" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="log_auditoria_info" style="width: 100%">
                    <thead>
                        <tr class='well-dark'>
                            <th width="10%" style="text-align: center;"> Tipo</th>
                            <th width="10%" style="text-align: center;"> Codigo</th>
                            <th width="10%" style="text-align: center;"> Insumo</th>
                            <th width="20%" style="text-align: center;"> Descripcion </th>
                            <th width="40%" style="text-align: center;"> Valor + 10% </th>
                            <th width="30%" style="text-align: center;"> Valor Unitario </th>
                            <th width="30%" style="text-align: center;"> 10% </th>
                            <th width="30%" style="text-align: center;"> IVA 12% </th>
                            <th width="30%" style="text-align: center;"> Ip Creacion </th>


                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($detalles as $value)
                        <tr style="text-align: center;">
                            <td>{{$value->tipo}}</td>
                            <td>{{$value->codigo}}</td>
                            <td>{{$value->descripcion}}</td>
                            <td>{{$value->valor}}</td>
                            <td>{{$value->valor_unitario}}</td>
                            <td>{{$value->ip_creacion}}</td>

                        </tr>
                        @endforeach


                    </tbody>

                </table>
            </div>

        </div>

</section>

<script type="text/javascript">
    $('#log_auditoria').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });
</script>    

@endsection