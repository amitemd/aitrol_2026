<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
</style>
<div class="modal-body">
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
    <div class="row" >
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th width="10">Sel.</th>
              <th width="10">Fecha</th>
              <th width="10">Convenio</th>
              <th width="10">Especialidad</th>
              <th width="10">Doctor</th>
              <th width="10">Cant.</th>
              <th width="10">Codigo</th>
              <th width="20">Valor</th> 
            </tr>
          </thead>
          <tbody>
          @foreach($ic as $value)
          <tr>
            @foreach($value as $espe)
            @php
            //dd($ic);
            $codigo = '99203';
            $id_empresa = $espe->empresa_agenda;
            $id_seguro = $espe->seguro_historia;
            if($espe->id_empresa != null){
              $id_empresa =$espe->id_empresa;
            }
            if($espe->id_seguro){
              $id_seguro =$espe->id_seguro;
            }
            $convenio =  Sis_medico\Convenio::where('id_seguro',$id_seguro)->where('id_empresa', $id_empresa)->first();            
            //dd($convenio);
            $proc = Sis_medico\ApProcedimiento::where('ap_procedimiento.codigo', $codigo)->join('ap_procedimiento_nivel as apn','apn.id_procedimiento','ap_procedimiento.id')->where('apn.cod_conv', $convenio->id_nivel)->select('ap_procedimiento.descripcion', 'apn.uvr1','apn.prc1')->first();
            //dd($proc);
            $valor = round(($proc->uvr1*$proc->prc1),2);
            @endphp
            <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_cardio('{{$espe->hcid}}','{{$a_plano->id}}','{{$espe->id_seguro}}','{{$espe->id_empresa}}','{{$espe->id_agenda}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
            <td>{{substr($espe->fechaini,0,10)}}</td>
            <td>{{$espe->nombre_seguro}} / @if($espe->id_empresa != null) {{$espe->nombre_corto}}@else {{$espe->empresa_agenda}}@endif</td>
            <td>{{$espe->nombre_espe}}</td>
            <td>Dr. {{$espe->nombre1}} {{$espe->apellido1}}</td>
            <td>1</td>
            <td>{{$codigo}}</td>
            <td>{{$valor}}</td>
            @endforeach
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $('#example2_labs').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

</script>  

