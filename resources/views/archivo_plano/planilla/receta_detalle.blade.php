
          <div class="table-responsive col-md-12" >
            <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
              <thead>
                <tr role="row">
                 
                  <th width="10">Medicina</th>
                 
                </tr>
              </thead>
              <tbody>
              @foreach($detalles as $detalle)
                
                  <tr>
                      
                      <td>{{$detalle->nombre}} <b>Cantidad: </b>{{$detalle->cantidad}}</td>
                   
                      
                  </tr>
              @endforeach
              </tbody>
            </table>
          </div>
       