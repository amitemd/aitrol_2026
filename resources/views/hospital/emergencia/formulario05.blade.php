@extends('hospital.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}>
<?php 
date_default_timezone_set('America/Guayaquil');
$fecha_actual=date("Y-m-d H:i:s");
 ?>
 <style>
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
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
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }
</style>
 <section class="content-header">
    <div class="row">
      <div class="col-md-10 col-sm-10">
        <label>
          FORMULARIO 005
        </label>
      </div>
      <div class="col-2">
        <button type="button" onclick ="location.href='{{route('hospital.emergencia')}}'" class="btn btn-danger btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i>  Regresar</button>
      </div>
    </div>
  </section>
<div class="content">
  
  <div class="row">
      <!--APELLIDO Y NOMBRE-->
      <div class="col-md-12">
        <div class="box box-primary">
           @if ($errors->any())
          @foreach($errors->all() as $error) 
            <div class="alert alert-danger" role="alert">
              {{$error}}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endforeach
        @endif
          <div class="box-header with-border">
            <h3 class="box-title">Registro Formulario 005:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <form>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm" >Apellidos Nombres:</label>
                      @foreach($users as $value)
                      <input type="text" readonly="readonly" class="form-control" value="{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}} ">
                    </div>
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm">Fecha de nacimiento:</label>
                      <input type="text" readonly="readonly" class="form-control" value="{{$value->fecha_nacimiento}}"  >
                    </div>
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm">Sexo(M/F):</label>
                      <input type="text" readonly="readonly" class="form-control" id="" name="" value="@if(($value->sexo)==1) MASCULINO @elseif(($value->sexo)==2) FEMENINO @endif">
                    </div>
                     <div class="form-group col-md-3">
                      <label class="col-form-label-sm">Medico:</label>
                      <input type="text"  class="form-control" id="nombrerecibe">
                    </div>
                     @endforeach
                  </div> 
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--EVOLUCION-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">1.Evoluci&oacute;n:</h3>
            <form action="{{route('hospital.formuarioevolucion')}}" method="POST" id="formulario">
               {{ csrf_field() }}
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
           <div class="col-md-12">
            @if(Session('success'))
            <div class="alert alert-success">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <form>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm">No:</label>
                      @foreach($users as $value)
                        <input readonly="readonly" type="text" class="form-control" value="{{$value->id}}" id="no_evolucion" name="no_evolucion">
                      @endforeach
                       <input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
                    </div>
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm" >Fecha:</label>
                      <input readonly="readonly" type="text" value="<?=$fecha_actual ?>" class="form-control" id="fecha_evolucion" name="fecha_evolucion">
                    </div>
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm">Medico:</label>
                      <input type="text" class="form-control" readonly="readonly" id="nombreenvia" name="medico">
                    </div>
                    <div class="form-group col-md-3">
                      <label class="col-form-label-sm">Codigo:</label>
                      <input type="text" class="form-control" id="codigo" name="codigo">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label-sm">Nota de Evolución</label>
                     <textarea class="form-control" id="nota_de_evolucion" name="nota_de_evolucion" rows="3"></textarea>
                  </div>

                   <div class="form-row">
                    <div class="form-group col-md-12">
                      <label class="col-form-label-sm">Examen Fisico:</label>
                      <textarea class="form-control" id="examen_fisico" name="examen_fisico" rows="3"></textarea>
                    </div>
                  </div>
                  <div class="box-footer">
                    <div class="row">
                        <button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
                        </button>
                        <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.resultado',$id_paciente)}}'"><i class="fas fa-history"></i> Resultados
                        </button>
                  </div>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--DIAGNOSTICO-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">2.Diagnostico:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="col-md-12">
            @if(Session('successo'))
            <div class="alert alert-success">
            {{session('successo')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div  class="table-responsive col-md-12">
                <form action="{{route('hospital.diagnostico005')}}" method="POST" id="formulario">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
                  <table  id="tabla" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" >
                    <thead>
                      <tr>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Operacion</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cie</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Tipo</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medico</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Acci&oacute;n</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="fila-fija">
                        <td><input  name="fecha_diagnostico[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>
                        <td><input required  name="operacion[]" placeholder="Operaci&oacute;n"/></td>
                        <td><input required  id="buscarnombre"  name="cie[]" placeholder="Cie"/></td>
                        <td><select required  name="tipo[]" placeholder="Tipo">
                          <option></option>
                          <option value="presuntivo">PRESUNTIVO:</option>
                          <option value="definitivo">DEFINITIVO:</option>
                        </select></td>
                        <td><input id="medico_llenado" readonly="readonly"  name="medico_urgente[]" placeholder="Medico"/></td>
                        <td style="text-align:center;" class="eliminar"><buttom type="button" class="btn btn-danger remove" ><i class="fas fa-minus"></i> Eliminar</buttom></td>        
                      </tr>
                    </tbody>
                    </table>
                    <div class="btn-der">
                      <buttom type="submit"   class="btn btn-primary"><i class="far fa-save"></i> Guardar</buttom>
                      <button id="adicional" name="adicional" type="button" class="btn btn-success addRow"><i class="fas fa-plus"></i> Mas</button>
                        <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.diagnostico005',$id_paciente)}}'"><i class="fas fa-history"></i> Resultados
                        </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--MEDIDAS GENERALES-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">3.Medidas Generales:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="col-md-12">
            @if(Session('exito'))
            <div class="alert alert-success">
            {{session('exito')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div  class="table-responsive col-md-12">
                <form action="{{route('hospital.medidas_generales')}}" method="POST" id="formulario">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
                  <table  id="agregar" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" >
                    <thead>
                      <tr>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medico</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Descripci&oacute;n</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Acci&oacute;n</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="fila-fija">
                        <td style="text-align:center;"><input  name="fecha_generales[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>
                        <td style="text-align:center;"><input required id="medico_general1" readonly="readonly"  name="medico_general[]" placeholder="Medico"/></td>
                        <td style="text-align:center;"><input required  name="descripcion_general[]" placeholder="Descripci&oacute;n"/></td>
                        <td style="text-align:center;" class="quitar"><button type="button" class="btn btn-danger" ><i class="fas fa-minus"></i> Eliminar</button></td>        
                      </tr>
                    </tbody>
                    </table>
                    <div class="btn-der">
                      <button type="submit" name=""  class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
                      <button id="adicional" name="adicional" type="button" class="btn btn-success agregar_td"><i class="fas fa-plus"></i> Mas</button>
                       <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.resultado_generales',$id_paciente)}}'"><i class="fas fa-book"></i> Resultados
                        </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

       <!--TRATAMIENTO-->
     <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">4.Tratamiento:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="col-md-12">
            @if(Session('listo'))
            <div class="alert alert-success">
            {{session('listo')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div  class="table-responsive col-md-12">
                <form action="{{route('hospital.tratamiento')}}" method="POST" id="formulario">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
                  <table  id="tratamiento" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" >
                    <thead>
                      <tr>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medico</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Descripci&oacute;n</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Acci&oacute;n</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="fila-fija">
                        <td style="text-align:center;"><input  name="fechatratamiento[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>
                        <td style="text-align:center;"><input  id="medico_tratamiento1" readonly="readonly"  name="medico_tratamiento[]" placeholder="Medico"/></td>
                        <td style="text-align:center;"><input required  name="descripcion_tratamiento[]" placeholder="Descripci&oacute;n"/></td>
                        <td style="text-align:center;" class="esconder"><button type="button" class="btn btn-danger" ><i class="fas fa-minus"></i> Eliminar</button></td>        
                      </tr>
                    </tbody>
                    </table>
                    <div class="btn-der">
                      <button type="submit"  class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
                      <button id="adicional" name="adicional" type="button" class="btn btn-success td"><i class="fas fa-plus"></i> Mas</button>
                       <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.mostrar_resultadotratamiento',$id_paciente)}}'"><i class="fas fa-book"></i> Resultados
                        </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!--PLAN-->
     <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">5.Plan:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="col-md-12">
            @if(Session('validado'))
            <div class="alert alert-success">
            {{session('validado')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div  class="table-responsive col-md-12">
                <form action="{{route('hospital.plan')}}" method="POST" id="formulario">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
                  <table  id="plan" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" >
                    <thead>
                      <tr>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medico</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Prescripci&oacute;n</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Acci&oacute;n</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="fila-fija">
                        <td style="text-align:center;"><input  name="fechaplan[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>
                        <td style="text-align:center;"><input id="medico_plan1" readonly="readonly"  name="medico_plan[]" placeholder="Medico"/></td>
                        <td style="text-align:center;"><input required  name="descripcion_plan[]" placeholder="Prescripci&oacute;n"/></td>
                        <td style="text-align:center;" class="ver"><button type="button" class="btn btn-danger"><i class="fas fa-minus"></i> Eliminar</button></td>        
                      </tr>
                    </tbody>
                    </table>
                    <div class="btn-der">
                      <button type="submit"  class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
                      <button id="adicional" name="adicional" type="button" class="btn btn-success td_mas"><i class="fas fa-plus"></i> Mas</button>
                       <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.resultado_plan',$id_paciente)}}'"><i class="fas fa-book"></i> Resultados
                        </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--MEDICAMENTOS-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">6.Medicamentos:</h3>
            <form action="{{route('hospital.medicamentos')}}" method="POST" id="formulario">
              {{ csrf_field() }}
              <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="col-md-12">
            @if(Session('ok'))
            <div class="alert alert-success">
            {{session('ok')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label>Medicamento:</label>
                      <input type="text" class="form-control" required name="medicamento" id="medicamento">
                    </div>
                    <div class="form-group col-md-4">
                      <label >Posologia:</label>
                      <input type="text" class="form-control" required name="posologia" id="posologia">
                    </div>
                    <div class="form-group col-md-4">
                      <label >Indicaciones Medicinas:</label>
                      <input type="text" class="form-control" required name="indicaciones_medi" id="indicaciones_medi">
                    </div>
                  </div>
                    <div class="form-row">
                    <div class="form-group col-md-3">
                      <label>Cantidad:</label>
                      <input type="text" class="form-control" required name="cantidad" id="cantidad">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Nombre:</label>
                      <input type="text" class="form-control" required name="nombre_medicina" id="nombre_medicina">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Presentaci&oacute;n:</label>
                      <input type="text" class="form-control" required name="presentacion_medicamento" id="presentacion_medicamento">
                    </div>
                     <div class="form-group col-md-3">
                      <label >Concentraci&oacute;n:</label>
                      <input type="text" class="form-control" required name="concentracion_medicamento" id="concentracion_medicamento">
                    </div>
                  </div>
                   <div class="form-row">
                    <div class="form-group col-md-2">
                      <label>Dosis:</label>
                      <input type="text" class="form-control" required name="dosis_medicamento" id="dosis_medicamento">
                    </div>
                    <div class="form-group col-md-2">
                      <label >Unidad:</label>
                      <input type="text" class="form-control" required name="unidad_medicamento" id="unidad_medicamento">
                    </div>
                    <div class="form-group col-md-2">
                      <label >Via:</label>
                      <input type="text" class="form-control" required  name="via_medicamento" id="via_medicamento">
                    </div>
                     <div class="form-group col-md-3">
                      <label >Frecuencia:</label>
                      <input type="text" class="form-control" required name="frecuencia_medicamento" id="frecuencia_medicamento">
                    </div>
                    <div class="form-group col-md-3">
                      <label >Duraci&oacute;n:</label>
                      <input type="text" class="form-control" required name="duracion_medicamento" id="duracion_medicamento">
                    </div>
                  </div>
                  </div>
                   <div class="box-footer">
                    <div class="row">
                       <button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
                        </button>
                        <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.medicamentos_resultado',$id_paciente)}}'"><i class="fas fa-book"></i> Resultados
                        </button>
                  </div>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--SOLICITUD DE EXAMENES-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">7.Solicitud de Examenes:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <form>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--TRASPASOS DE SALAS-->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">8.Traspasos de Salas:</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="col-md-12">
            @if(Session('dato'))
            <div class="alert alert-success">
            {{session('dato')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div  class="table-responsive col-md-12">
                <form action="{{route('hospital.salas')}}" method="POST" id="formulario">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_paciente" id="id_paciente" value="{{$id_paciente}}">
                  <table  id="salas" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" >
                    <thead>
                      <tr>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Fecha</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Area</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medicina</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Descripci&oacute;n</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Medico</th>
                        <th width="16.66%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Acci&oacute;n</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="fila-fija">
                        <td><input  name="fecha_salas[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>
                        <td><input required   name="area_salas[]" placeholder="Area"/></td>
                        <td><input  required  name="medicina_salas[]" placeholder="Medicinas"/></td>
                        <td><input required name="descripcion_salas[]" placeholder="Descripci&oacute;n"/></td>
                        <td><input  name="medico_salas[]" id="medico_salas1" readonly="readonly" placeholder="Medico"/></td>
                        <td style="text-align:center;" class="salas_eliminar"><button type="button" class="btn btn-danger remove"><i class="fas fa-minus"></i> Eliminar</button></td>        
                      </tr>
                    </tbody>
                    </table>
                    <div class="btn-der">
                      <button type="submit"  value="Guardar" class="btn btn-primary"><i class="far fa-save"></i> Guradar</button>
                      <button id="adicional" name="adicional" type="button" class="btn btn-success agregar"><i class="fas fa-plus"></i> Mas</button>
                       <button type="button" class="btn btn-info" 
                          onclick ="location.href='{{route('hospital.salas_resultado',$id_paciente)}}'"><i class="fas fa-book"></i> Resultados
                        </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
  $('.addRow').on('click',function(){
    addRow();
  });
  function addRow()
  {
    var tr='<tr>'+'<td><input required readonly="readonly" name="fecha_diagnostico[]" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>'+'<td><input required name="operacion[]" placeholder="Operaci&oacute;n"/></td>'+'<td><input required  name="cie[]" placeholder="Cie"/></td>'+'<td><select required name="tipo[]" placeholder="Tipo"><option><option>PRESUNTIVO</option><option>DEFINITIVO</option></select></td>'+ '<td><input id="medico_llenado2" readonly="readonly" required name="medico_urgente[]" placeholder="Medico"/></td>'+'<td class="eliminar" style="text-align:center;"><buttom  type="button" class="btn btn-danger "><i class="fas fa-minus"></i> Eliminar</</td>'
      '</tr>'+
    $('#tabla').append(tr);
  };
  $(document).on("click",".eliminar",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });

  $('.agregar_td').on('click',function(){
    agregar_td();
  });
  function agregar_td()
  {
    var tr='<tr>'+'<td style="text-align:center;"><input required name="fecha_generales[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>'+'<td style="text-align:center;"><input  name="medico_general[]" id="medico_general2" readonly="readonly" placeholder="Medico"/></td>'+'<td style="text-align:center;"><input  name="descripcion_general[]" placeholder="Descripci&oacute;n"/></td>'+'<td style="text-align:center;" class="quitar"><button type="button" class="btn btn-danger"><i class="fas fa-minus"></i> Eliminar</button></td>'  
      '</tr>'+
    $('#agregar').append(tr);
  };
  $(document).on("click",".quitar",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });

$('.td').on('click',function(){
    td();
  });
  function td()
  {
    var tr='<tr>'+'<td style="text-align:center;"><input required name="fechatratamiento[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>'+'<td style="text-align:center;"><input  name="medico_tratamiento[]" id="medico_tratamiento2" readonly="readonly" placeholder="Medico"/></td>'+'<td style="text-align:center;"><input  name="descripcion_tratamiento[]" placeholder="Descripci&oacute;n"/></td>'+'<td style="text-align:center;" class="quitar"><button type="button" class="btn btn-danger"><i class="fas fa-minus"></i> Eliminar</button></td>'  
      '</tr>'+
    $('#tratamiento').append(tr);
  };
  $(document).on("click",".esconder",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });

  $('.td_mas').on('click',function(){
    td_mas();
  });
  function td_mas()
  {
    var tr='<tr>'+'<td style="text-align:center;"><input required name="fechaplan[]" readonly="readonly" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>'+'<td style="text-align:center;"><input id="medico_plan2" readonly="readonly"  name="medico_plan[]" placeholder="Medico"/></td>'+'<td style="text-align:center;"><input  name="descripcion_salas[]" placeholder="Descripci&oacute;n"/></td>'+'<td style="text-align:center;" class="ver"><button type="button" class="btn btn-danger"><i class="fas fa-minus"></i> Eliminar</button></td>'  
      '</tr>'+
    $('#plan').append(tr);
  };
  $(document).on("click",".ver",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });

   $('.agregar').on('click',function(){
    agregar();
  });
  function agregar()
  {
      var tr='<tr>'+'<td><input required readonly="readonly" name="fecha_salas[]" value="<?php echo date("Y-m-d");?>" placeholder="Fecha"/></td>'+'<td><input required name="area_salas[]" placeholder="Area"/></td>'+'<td><input required name="medicina_salas[]" placeholder="Medicina"/></td>'+'<td><input required name="descripcion_salas[]" placeholder="Descripci&oacute;n"/></td>'+ '<td><input required name="medico_salas[]" id="medico_salas2" readonly="readonly" placeholder="Medico"/></td>'+'<td class="salas_eliminar" style="text-align:center;"><button  type="button" class="btn btn-danger "><i class="fas fa-minus"></i> Eliminar</button></td>'
      '</tr>'+
    $('#salas').append(tr);
  };
  $(document).on("click",".salas_eliminar",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });
 $(document).ready(function () {
        $("#nombrerecibe").keyup(function () {
            var value = $(this).val();
            $("#nombreenvia").val(value);
            $("#medico_llenado").val(value);
            $("#medico_llenado2").val(value);
            $("#medico_general1").val(value);
            $("#medico_general2").val(value);
            $("#medico_tratamiento1").val(value);
            $("#medico_tratamiento2").val(value);
            $("#medico_plan1").val(value);
            $("#medico_plan2").val(value);
            $("#medico_salas1").val(value);
            $("#medico_salas2").val(value);
        });
});

 $("#buscarnombre").autocomplete({
  source: function( request, response ){
    $.ajax({
      method:'GET',
      url: "{{route('hospital.autocompletarcie')}}",
      dataType: "json",
      data: { term: request.term },
      success: function( data ) {
      response(data);
      }
    });
  },
  minLength: 2,
  change: function( event, ui ){
  }
});
</script>
@endsection