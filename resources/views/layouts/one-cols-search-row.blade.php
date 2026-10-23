<div class="row">
  @php
    $index = 0;
  @endphp
  @foreach ($items as $item)
    <div class="col-md-6">
      <div class="form-group">
          @php
            if ($item == 'Cédula' ) {$stringFormat = 'id';}
            else{
            $stringFormat =  strtolower(str_replace(' ', '', $item));}
          @endphp
          <label for="input<?=$stringFormat?>" class="col-sm-3 control-label">{{$item}}</label>
          <div class="col-sm-6">
            <input value="{{isset($oldVals) ? $oldVals[$index] : ''}}" type="text" class="form-control" name="<?=$stringFormat?>" id="input<?=$stringFormat?>" placeholder="{{$item}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
          </div>
          <button type="submit" class="btn btn-primary">
      <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
      Buscar
    </button>
      </div>
    </div>
  @php
    $index++;
  @endphp
  @endforeach
</div>