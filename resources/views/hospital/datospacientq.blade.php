@extends('hospital.base')
@section('action-content')
<style>
.card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  width: 70%;
}

.card:hover {
  box-shadow: 0 8px 16px 0;
}

.container {
  padding: 2px 16px;
}
</style>

<div class="content">
    <section class="content-header">
        <h1>
            DATOS DEL PACIENTE
            <small>Quir&oacute;fano</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Datos principales de paciente</h3>
                        <div class="box-tools pull-rigth">
                            <button type = "button" class = "btn btn-box-tool" data-widget = "collapse"><i class="fa fa-minus"></i></button>
                        <!--<div class="btn-group">
                            
                            </div>-->
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <!--IMAGEN DEL PACIENTE-->
                            <div class="col-md-4">
                                <div class="card">
                                    <img src="{{asset('/')}}hc4/img/nuevo-usuario.png" alt="Avatar" style="width:100%">
                                    <div class="container">
                                        <h4><input type="text" id = "antquirud" class = "form-control form-control-sm" placeholder = "Nombre del Paciente"></h4> 
                                        <p>Architect & Engineer</p> 
                                    </div>
                                </div>
                            </div>
                            <!--Datos del Paciente-->
                            <div class="col-md-8">
                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label for="nombre" class = "col-form-label-sm">Paciente</label>
                                        <input type="text" id = "nombre" class = "form-control form-control-sm" placeholder = "Nombre de Paciente">
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="indetificacion" class = "col-form-label-sm">Indetificaci&oacute;n</label>
                                        <input type="number" id = "indetificacion" class = "form-control form-control-sm" placeholder = "C.I:">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-3">
                                        <label for="pais" class = "col-form-label-sm">Pais</label>
                                        <input type="text" id = "pais" class = "form-control form-control-sm" placeholder = "Pais">
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="ciudad" class = "col-form-label-sm">Ciudad</label>
                                        <input type="text" id = "ciudad" class = "form-control form-control-sm" placeholder = "Ciudad">
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="direccion" class = "col-form-label-sm">Direcci&oacute;n</label>
                                        <input type="text" id = "direccion" class = "form-control form-control-sm" placeholder = "Direcci&oacute;n">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-3">
                                        <label for="fecha" class = "col-form-label-sm">Fecha de Nacimiento</label>
                                        <input type="text" id = "fecha" class = "form-control form-control-sm" placeholder = "Fecha">
                                    </div>
                                    <div class="form-group col-2">
                                        <label for="edad" class = "col-form-label-sm">Edad</label>
                                        <input type="number" id = "edad" class = "form-control form-control-sm" placeholder = "Edad">
                                    </div>
                                    <div class="form-group col-2">
                                        <label for="sexo" class = "col-form-label-sm">Sexo</label>
                                        <select class="form-control form-control-sm" id="sexo">
                                            <option>Masculino</option>
                                            <option>Femenino</option>
                                            <option>Otro</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="seguro" class = "col-form-label-sm">Seguro</label>
                                        <input type="text" id = "seguro" class = "form-control form-control-sm" placeholder = "Seguro">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-3">
                                        <label for="civil" class = "col-form-label-sm">Estado Civil</label>
                                        <input type="text" id = "civil" class = "form-control form-control-sm" placeholder = "Estado civil">
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="ocupacion" class = "col-form-label-sm">Ocupaci&oacute;n Laboral</label>
                                        <input type="text" id = "ocupacion" class = "form-control form-control-sm" placeholder = "Ocupaci&oacute;n">
                                    </div>
                                    <div class="form-group col-2">
                                        <label for="telefono" class = "col-form-label-sm">Telefono Domicilio</label>
                                        <input type="number" id = "telefono" class = "form-control form-control-sm" placeholder = "Telefono">
                                    </div>
                                    <div class="form-group col-2">
                                        <label for="celular" class = "col-form-label-sm">Telefono Celular</label>
                                        <input type="number" id = "celular" class = "form-control form-control-sm" placeholder = "Celular">
                                    </div>
                                </div>
                                <div class="form-group col-10">
                                    <label for="alergia" class = "col-form-label-sm">Alergias</label>
                                    <input type="text" id = "alergia" class = "form-control form-control-sm" placeholder = "Alergias">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-4">
                                        <label for="antpatod" class = "col-form-label-sm">ANTECEDENTE PATOLÓGICOS</label>
                                        <input type="text" id = "antpatod" class = "form-control form-control-sm">
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="antfamid" class = "col-form-label-sm">ANTECEDENTE FAMILIARES</label>
                                        <input type="text" id = "antfamid" class = "form-control form-control-sm">
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="antquirud" class = "col-form-label-sm">ANTECEDENTE QUIRURGICOS</label>
                                        <input type="text" id = "antquirud" class = "form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection