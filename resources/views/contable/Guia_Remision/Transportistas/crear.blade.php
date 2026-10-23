@extends('contable.Guia_Remision.Transportistas.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Transportista</h3>
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('transportistas.store')}}">
                {{ csrf_field() }}
                    <div class="form-group col-xs-6">
                        <label for="ci_ruc"  > Identificación/RUC</label>
                        <input type="number" name="ci_ruc"  class="form-control" required >
                    </div>


                    <div class="form-group col-xs-6">
                        <label for="razon_social"  >  Razón Social </label>
                        <input type="text" name="razon_social"  class="form-control" required>
                    </div>



                    <div class="form-group col-xs-6">
                        <label for="nombres">  Nombre</label>
                        <input type="text" name="nombres"  class="form-control">
                    </div>



                    <div class="form-group col-xs-6">
                        <label for="apellidos"  >  Apellidos</label>
                        <input type="text" name="apellidos"  class="form-control">
                    </div>



                    <div class="form-group col-xs-6">
                        <label for="id_empresa"  > ID Empresa</label>
                        <input type="text" name="id_empresa"  class="form-control">
                    </div>



                    <div class="form-group col-xs-6">
                        <label for="nombrecomercial">Nombre Comercial</label>
                        <input type="text" name="nombrecomercial"  class="form-control" required maxlength="25">
                    </div>



                    <div class="form-group col-xs-6">
                        <label for="ciudad"  > Ciudad</label>
                        <input type="text" name="ciudad"  class="form-control">
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="direccion"  > Dirección</label>
                        <input type="text" name="direccion"  class="form-control">
                    </div>


                    <div class="form-group col-xs-6">
                        <label for="email"> Email 1</label>
                        <input type="text" name="email"  class="form-control" required>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="email2"> Email 2</label>
                        <input type="text" name="email2"  class="form-control">
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="telefono1"  > Teléfono 1</label>
                        <input type="number" name="telefono1"  class="form-control" required>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="telefono2"  > Teléfono 2</label>
                        <input type="number" name="telefono2"  class="form-control">
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="placa"  > Placa</label>
                        <input type="text" name="placa"  class="form-control" required>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="tipo_documento"  >Tipo de Documento</label>
                        <select class="form-control validar" name="tipo_documento" id="tipo_documento" required>
                            <option>Seleccione</option>
                            <option value="{{04}}">RUC</option>
                            <option value="{{05}}">Cédula</option>
                            <option value="{{06}}">Pasaporte</option>
                            <option value="{{'08'}}">Identificación del Exterior</option>
                        </select>
                    </div>

                    <div class="form-group col-xs-6" required>
                        <label for="rise"  > RISE</label>
                        <select class="form-control validar" name="rise" id="rise" >
                            <option value="{{0}}">No</option>
                            <option value="{{1}}">Si</option>
                            </select>
                        </div>

                    <div class="form-group col-xs-6">
                        <label for="contabilidad">Contabilidad</label>
                        <select class="form-control validar" name="contabilidad" id="contabilidad" >
                            <option>Seleccione</option>
                            <option value="{{0}}">No</option>
                            <option value="{{1}}">Si</option>
                        </select>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="contribuyente_especial"  > Contribuyente Especial</label>
                        <select class="form-control validar" name="contribuyente_especial" id="contribuyente_especial" >
                            <option>Seleccione</option>
                            <option value="{{0}}">No</option>
                            <option value="{{1}}">Si</option>
                        </select>
                    </div>

                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            Agregar
                        </button>
                    </div>
              </form>
          </div>
      </div>
  </section>
@endsection
