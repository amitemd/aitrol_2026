<table id="example2" class="table table-bordered table-hover dataTable col-md-12 col-sm-12 col-12" role="grid" aria-describedby="example2_info" style="margin-right: 1400px;">
        <thead>
          <tr role="row">
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Logo</th>
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre Comercial</th>
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Raz&oacute;n social</th>
            <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Ruc</th>
            <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Email</th>
            <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo Proveedor</th>
            
        </thead>
        <tbody>
        	 @foreach ($proovedor as $value)
                  <tr role="row" class="odd">
                  <td>   <img src="{{asset('/logo').'/'.$value  ->logo}}" style="width:80px;height:80px;"  alt="Logo Image" > </td>
                  <td> {{ $value->nombrecomercial}}</td>
                  <td> {{ $value->razonsocial}}</td>
                  <td> {{ $value->ruc}}</td>
                  <td> {{ $value->email}}</td>
                  <td> @if(($value->id_tipoproveedor)==1) Takeda Mexico @elseif (($value->id_tipoproveedor)==2) Roche @elseif (($value->id_tipoproveedor)==3) ICN Farmacéutica @elseif (($value->id_tipoproveedor)==4) farmacia  @endif </td>  
                </tr>
           @endforeach     
            </tbody>
</table>