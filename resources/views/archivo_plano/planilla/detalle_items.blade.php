
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
            <thead>
              <tr style="color:White;background-color:#006699;font-weight:bold;">
                <th>TIPO</th>
                <th>CODIGO</th>
                <th>DESCRIPCION</th>
                <th>CANTIDAD</th>
                <th>VALOR</th>
                <th>IVA</th>
                <th>TOTAL</th>
                <th>ACCION</th>
              </tr>
            </thead>
            <tbody >
            <tr>
              <td>@if($tip){{$tip}}@endif</td>
              <td>@if($codig){{$codig}}@endif</td>
              <td>@if($descrip){{$descrip}}@endif</td>
              <td>@if($cantid){{$cantid}}@endif</td>
              <td>@if($prec){{$prec}}@endif</td>
              <td>@if($iv){{$iv}}@endif</td>
              <td>@if($total){{$total}}@endif</td>
              <td>
                <button type="button" onclick="eliminar_item('+1+')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      
</div>

<script type="text/javascript">
  
  function eliminar_item(valor)
  {
    var dato_item1 = "dato"+valor;
    var dato_item2 = 'visibilidad_item'+valor;
    document.getElementById(dato_item1).style.display='none';
    document.getElementById(dato_item2).value = 0;
  }

</script>