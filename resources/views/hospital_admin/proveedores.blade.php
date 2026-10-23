@extends('hospital_admin.base')
@section('action-content')
<style>
  .box{
    border-color: #FDFEFE; border-radius: 30px;
  }
</style>
<div class="modal fade" id="modals"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="modalsd"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="modalst" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px;">
  <div class="col-md-12" style=" margin-top: 5px; padding: 8px; border-radius: 30px;background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">
    <form method="POST" id="form_buscador" action="">
      {{ csrf_field() }}
      <div class="row">
        <div class="col-md-12 col-sm-12 col-12">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-5">
                <div class="row">
                  <div class="col-md-2">
                    <img src="{{asset('/')}}hc4/img/guarantee.png" style="width: 40px; text-align:right;">
                  </div>
                  <div class="col-md-4">
                    <h1 style="font-size: 25px; color: white;">PROVEEDORES</h1>
                  </div>
                </div>
              </div>
                <div class="col-md-2" style="margin-top: 12px">
                <a type="button" href="{{ route('hospital_admin.modalprovedor')}}" class="btn btn-primary coloresb" data-toggle="modal" data-target="#modals" >
                  <img src="{{asset('/')}}hc4/img/add.png" style="text-align:left; height: 20px;"><b>&nbsp;&nbsp;</b>AGREGAR PROOVEDOR
                </a>
              </div>
                <div class="col-md-2" style="margin-top: 12px">
                <a type="button" href="{{ route('hospital_admin.modalprovedord')}}"  class="btn btn-primary coloresb" data-toggle="modal" data-target="#modalsd" >
                  <img src="{{asset('/')}}hc4/img/add.png" style="text-align:left; height: 20px;"><b>&nbsp;&nbsp;</b>TIPO DE PROOVEDORES
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<section class="content">
  <div class="box">
    <div class="box-header with-border" style="font-family: Montserrat;color: white; margin-top: 5px; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 5px">
      <h3 class="box-title">LISTA DE PROVEEDORES</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="box-body">
      <div class="table-responsive col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
          <thead>
            <tr role="row">
              <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Logo</th>
              <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >RUC</th>
              <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Raz&oacute;n social</th>
              <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre Comercial</th>
              <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Email</th>
              <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo Proveedor</th>
              <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Accion</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($proovedor as $value)
              <tr role="row" class="odd">
                <td>   <img src="{{asset('/logo').'/'.$value  ->logo}}" style="width:80px;height:80px;"  alt="Logo Image" > </td>
                <td> {{ $value->ruc}}</td>
                <td> {{ $value->razonsocial}}</td>
                <td> {{ $value->nombrecomercial}}</td>
                <td> {{ $value->email}}</td>
                <td> @if(($value->id_tipoproveedor)==1) Takeda Mexico @elseif (($value->id_tipoproveedor)==2) Roche @elseif (($value->id_tipoproveedor)==3) ICN Farmacéutica @elseif (($value->id_tipoproveedor)==4) farmacia  @endif </td>  
                <td> <a  href="{{ route('hospital_admin.modaleditarpr', ['id' => $value->id]) }}" data-toggle="modalst" data-target="#modalst" class="btn btn-warning col-md-6 col-xs-6 btn-margin">Editar</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection