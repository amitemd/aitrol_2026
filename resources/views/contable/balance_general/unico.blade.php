<tr>
  <input type="hidden" name="id_plan{{$contador}}" value="{{$cuenta->id}}">
  <td >{{$cuenta->id}}</td>
  <td >{{$cuenta->nombre}}</td>
  <td ><input class="form-control input-sm" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" name="debe{{$contador}}"  id="debe{{$contador}}" required></td>
  <td ><input class="form-control input-sm" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" name="haber{{$contador}}" id="haber{{$contador}}" required></td>
</tr>
