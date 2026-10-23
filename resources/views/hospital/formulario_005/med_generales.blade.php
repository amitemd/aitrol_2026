<div class="card">
    <div class="card-header bg bg-primary">
        <div class="row">
            <div class="col-md-6">
                <label class="colorbasic sradio" > 3 </label> 
            </div>
            <div class="col-md-6">
                <label class="colorbasic" > {{trans('hospitalizacion.MedidasGenerales')}} </label>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row" style="padding-top: 10px;">
            <div class="col-md-6">
                <label> {{trans('hospitalizacion.FechadeNacimiento:')}} </label>
                <input type="text" name="fecha" class="form-control input-sm ">
            </div>
            <div class="col-md-6">
                <label>{{trans('hospitalizacion.Medico:')}} </label>
                <input type="text" name="operacion" class="form-control input-sm ">
            </div>
            <div class="col-md-6">
                <label>{{trans('hospitalizacion.Descripcion:')}} </label>
                <input type="text" name="cie" class="form-control input-sm ">
            </div>
           
        </div>
    </div>

</div>