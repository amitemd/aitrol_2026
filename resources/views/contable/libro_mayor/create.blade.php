@extends('contable.retenciones.base')
@section('action-content')
<style type="text/css">
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
</style>

<section class="content"> 
    <form class="form-vertical" method="post">
     <div class="col-12 col-xs-12">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="buscar" class = "col-form-label-sm">Buscar</label>
                <input type="text" id = "buscar" name="buscar" class = "form-control form-control-sm" disabled>
            </div>
            <div class="form-group col-md-4">
                <label style="padding-left: 0px">Estado</label>
                <div style="background-color: green; margin-top: 20px;" class="form-control col-md-1"></div>           
            </div>
            <div class="form-group col-md-4">
                <label for="id">ID:</label>            
            </div>
            <div class="form-group col-md-4">
                <label for="numero_factura">Número</label>
                <input type="text" id="numero_factura" name="numero_factura">
            </div>
            <div class="form-group col-md-4">
                <label for="asiento">Asiento</label>
                <input type="text" id="asiento" name="asiento">
            </div>
            <div class="form-group col-md-4">
                <label for="fecha_hoy">Fecha: </label>
                <input type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
            </div>
            <div class="form-group col-md-4">
                <label for="tipo">Tipo: </label>
                <select name="tipo" id="tipo" disabled="disabled">
                  <option value="0">CLI-RT</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="proyecto">Proyecto: </label>
                <select name="proyecto" id="proyecto" disabled="disabled">
                    <option value="0">0000</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="caja">CAJA:</label>
                <select name="caja" id="caja" disabled="disabled">
                    <option value="0"></option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Concepto:</label>
            </div>
            <div class="form-group col-6">
                <input type="text" name="concepto" id="concepto">
            </div>
            <div class="form-group col-md-4">&nbsp;</div>
            <div class="form-group col-md-1">
                <label>Cliente</label>
            </div>
            <div class="form-group col-md-3">
                <select name="cliente" id="cliente">
                    <option value="0">32131</option>
                </select>
            </div>
            <div class="form-group col-md-5">
                <input type="text" name="mostrar_cliente" disabled>
            </div>
            <div class="form-group col-md-3">
                <label for="autorizacion"></label>
                <input type="text" name="autorizacion" id="autorizacion">
            </div>
        </div>
     </div>
     <div class="col-12 col-xs-12">
        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                
                                <table id="example2" role="grid" aria-describedby="example2_info">
                                    <thead style="background-color: #FFF3E3">
                                    <tr style="position: relative;">
                                        <th style="width: 5%; text-align: center;">Número RF.</th>
                                        <th style="width: 23%; text-align: center;">Número Fact.</th>
                                        <th style="width: 5%; text-align: center;">Fecha</th>
                                        <th style="width: 5%; text-align: center;">Divisa</th>
                                        <th style="width: 5%; text-align: center;">Base Fuente</th>
                                        <th style="width: 8%; text-align: center;">Tipo RFIR</th>
                                        <th style="width: 3%; text-align: center;">Total RFIR</th>
                                        <th style="width: 5%; text-align: center;">Base de .I.V.A</th>
                                        <th style="width: 8%; text-align: center;">Tipo RFIVA</th>
                                        <th style="width: 1%; text-align: center;">Total RFIVA</th>
                                    </tr>
                                    </thead>
                                    <tbody id="crear">
                                    @php $cont=0; @endphp
                                    @foreach (range(1, 20) as $i)
                                        <tr>
                                            <!-- GEORGE AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                        </tr>
                                        @php $cont = $cont +1; @endphp
                                    @endforeach
                                        
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
        </div>
     </div>
    </form>
</section>



@endsection