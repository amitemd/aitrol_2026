<tr>
  <input type="hidden" name="id_plan{{$contador}}" value="{{$cuenta->id}}">
  <td >{{$cuenta->id}}</td>
  <td >{{$cuenta->nombre}}<input type="hidden" name="nombre{{$contador}}" value="{{$cuenta->nombre}}"></td>
  <td ><input class="form-control input-sm debe" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="debe{{$contador}}"  id="debe{{$contador}}" required></td>
  <td ><input class="form-control input-sm" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="haber{{$contador}}" id="haber{{$contador}}" required></td>
  <td>
      <button type="button" class="btn btn-danger delete" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
          <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
      </button>
  </td>
</tr>
