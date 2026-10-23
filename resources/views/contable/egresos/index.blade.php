@extends('contable.egresos.base')
@section('action-content')

<div class="content">

    <div class="box">
         <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Nota de Debito Acreedores</h3>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('creditoacreedores.index')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar Nota Debito Acreedores
              </button>
            </div>
        </div>
        <div class="box-body">
          <div class="col-md-12">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Fecha</th>
                      <th >Proveedor</th>
                      <th> Descripción</th>
                      <th >Secuencia Factura</th>
                      <th >Tipo RFIR</th>
                      <th >Tipo RFIVA</th>
                      <th >Total</th>
                      <th >Acción</th>
                    </tr>
                  </thead>
                    <tbody>
                         <!--  
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                        
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><a href="" class="btn btn-danger">Editar</a></td>
                          </tr>
                          -->
                    </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando registros   de  registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
               
                </div>
              </div>
            </div>
           </div>
          </div>
        
        </div>
    </div>

</div>


@endsection
