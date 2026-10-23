@extends('archivo_plano.archivo.base')
@section('action-content')
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">Consolidado Honorarios</h3>
		        </div>
		    </div>
		</div>
        <div class="box-body">
            <form method="POST" action="{{route('ap_estadisticos.honorarios')}}">
                {{ csrf_field() }}
    	  		<div class="row" >
                    <div class="form-group col-md-4 ">
                        <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                        <div class="col-md-7">
                            <input id="mes_plano"  type="text" class="form-control input-sm" name="mes_plano" value="{{$mes_plano}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group col-md-2 ">                     
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary" id="boton_buscar"><span class="glyphicon glyphicon-search" > Buscar</span></button>
                            </div>
                    </div> 
    	  		</div>
            </form>
            <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                    <thead style="background-color: #4682B4">
                        <tr >
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Seguro</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Empresa</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Honorario CRM</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">honorario HPL</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Consultas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($honorarios as $value)
                        <tr>
                            <td>{{$value['seguro']}}</td>
                            <td>{{$value['empresa']}}</td>
                            <td>{{number_format($value['acum_rob'],2,',','.')}}</td>
                            <td>{{number_format($value['acum_han'],2,',','.')}}</td>
                            <td>{{number_format($value['acum_con'],2,',','.')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
	  	</div>
	  </div>
</section>


<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
   

   

    $('#example2').DataTable({
      'language': {
        'emptyTable': '<span class="label label-primary" style="font-size:14px;">No se encontraron registros.</span>'
      },
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
    
</script>
@endsection