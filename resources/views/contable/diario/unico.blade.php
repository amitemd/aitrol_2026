<tr class="de">
  <input type="hidden" name="id_plan{{$contador}}" value="{{$cuenta->id}}">
  <td class="s" >{{$cuenta->pempresa->plan}}</td>
  <td class="s" >{{$cuenta->pempresa->nombre}}<input type="hidden" name="nombre{{$contador}}" value="{{$cuenta->pempresa->nombre}}"></td>
  <td class="s" ><input class="form-control input-sm debe" type="text" style="width: 80%;" onchange="decimal('debe{{$contador}}'); changed('s');" onkeypress="return isNumberKey(event)" name="debe{{$contador}}"  id="debe{{$contador}}" value="0.00" required></td>
  <td class="s" ><input class="form-control input-sm haber" type="text" style="width: 80%;" onchange="decimal('haber{{$contador}}'); changed('s');" onkeypress="return isNumberKey(event)" name="haber{{$contador}}" id="haber{{$contador}}" value="0.00" required></td>
  <td><button type="button" class="btn btn-danger s" onclick="$(this).parent().parent().find('.s').remove(); changed();">Eliminar</button></td>
</tr>
