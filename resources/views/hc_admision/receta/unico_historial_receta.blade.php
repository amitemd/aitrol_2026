<div id="" >  
 <div class="col-md-12" style="padding-bottom: 15px;">
  @if(!is_null($hist_recetas))
  <div class="row">
        <div class="col-md-6">
          <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
          <div id="trp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #3c8dbc; ">
              @if(!is_null($hist_recetas->rp))
              <p><?php echo $hist_recetas->rp?>
              </p>
              @endif
          </div>
        </div>
        <div class="col-md-6" >
            <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
            <div id="tprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                  @if(!is_null($hist_recetas->prescripcion))
                    <p><?php echo $hist_recetas->prescripcion?></p>
                  @endif
            </div>
        </div>
  </div>
  @endif
</div>
</div>