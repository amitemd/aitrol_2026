<!-- split buttons box -->
@extends('disponibilidad.base')
@section('action-content')

<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
    
    }
</style>
 <section class="content">

  <div class="box">
    <div class="box-header">  
      <h4><b>UNIDADES</b></h4>
      
    </div>
   
   <div class="box-body">
 
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <table id="example2" class="table table-bordered table-hover dataTable" >
            <tbody>
             @foreach($hospital as $hospital)
               
                <div class="col-md-4" style="padding: 5px;">
                    <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                        <a href="{{ route('disponibilidad.sala_opciones',['id' => $hospital->id])}}" class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center">{{$hospital->nombre_hospital}}
                        </a>
                    </div>
                
                </div>
            </div>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

  <script type="text/javascript">



  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  



 </script> 
@endsection

