<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 120px;
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
    list-style: none;
    background-color: #FFFFFF;
    width: 40%;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>
<section class="content">
  <div class="modal-body">
    <div class="box-body">

         <div class="modal-header" style="background: #3c8dbc;">
          <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title"> PREPARACIONES </h3>
         </div>
      <form id="preparaciones_procedimientos" method="post"  action="{{route('preparaciones.mostrar_pdf')}}" >
        {{ csrf_field() }}      
        <div class="col-md-20">
            <div class="col-md-5">
          <select class="form-control" name="preparacion" id="preparacion" >
              <!--option value="">Seleccionar...</option-->
              @foreach($preparaciones as $value)
              <option value="{{$value->id}}">{{$value->nombre_preparaciones}}</option>
              @endforeach
          </select>
          </div>
        </div>  
      <div class="col-md-5"> 
        <button type="submit" class="btn btn-primary"  formtarget="_blank">Exportar</button>
      </div>
      </form>      
    </div>
  </div>        
</section>


