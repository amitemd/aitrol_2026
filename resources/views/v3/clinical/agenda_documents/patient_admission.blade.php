<!doctype html>
<html><head><meta charset="utf-8"><style>
@page{margin:0;size:A4 portrait}html,body{margin:0;padding:0;width:595.28pt;height:841.89pt;font-family:DejaVu Sans,Arial,sans-serif;color:#111}.page{position:relative;width:595.28pt;height:841.89pt;overflow:hidden}.template{position:absolute;inset:0;width:595.28pt;height:841.89pt}.value{position:absolute;z-index:2;font-size:8.2pt;font-weight:700;line-height:10pt;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.small{font-size:7.2pt}.declaration{font-size:7.4pt;font-weight:600}.mark{position:absolute;z-index:2;font-family:DejaVu Sans,sans-serif;font-size:11pt;font-weight:900;line-height:11pt}.bottom{font-size:7pt;text-align:center}
.surname1{left:30pt;top:119pt;width:111pt}.surname2{left:149pt;top:119pt;width:111pt}.names{left:268pt;top:119pt;width:226pt}.history{left:506pt;top:119pt;width:58pt}
.age{left:30pt;top:150pt;width:25pt}.sex{left:65pt;top:150pt;width:73pt}.address{left:149pt;top:150pt;width:344pt}.date{left:506pt;top:150pt;width:58pt}
.phones{left:30pt;top:181pt;width:225pt}.email{left:268pt;top:181pt;width:296pt}.declarant-name{left:362pt;top:232pt;width:164pt;text-align:center}.declarant-id{left:77pt;top:247pt;width:92pt;text-align:center}
.private-mark{left:266pt;top:552pt}.insurance-mark{left:266pt;top:574pt}.insurance-name{left:289pt;top:566pt;width:126pt}
</style></head><body><div class="page">
<img class="template" src="{{$templateData}}">
<div class="value surname1">{{mb_strtoupper($patient->apellido1,'UTF-8')}}</div>
<div class="value surname2">{{mb_strtoupper($patient->apellido2,'UTF-8')}}</div>
<div class="value names">{{mb_strtoupper(trim($patient->nombre1.' '.$patient->nombre2),'UTF-8')}}</div>
<div class="value history">{{$historyNumber}}</div>
<div class="value age">{{$age===null?'N/D':$age}}</div>
<div class="value sex small">{{$patientSex}}</div>
<div class="value address small">{{mb_strtoupper($patient->direccion ?: 'NO REGISTRADA','UTF-8')}}</div>
<div class="value date small">{{$attentionDate}}</div>
<div class="value phones">{{$patientPhones ?: 'NO REGISTRADO'}}</div>
<div class="value email">{{$patientEmail ?: 'NO REGISTRADO'}}</div>
@if($isPrivatePatient)<div class="mark private-mark">X</div>@endif
@if($isPrivateInsurance)<div class="mark insurance-mark">X</div><div class="value small insurance-name">{{mb_strtoupper($insurance->nombre,'UTF-8')}}</div>@endif
</div></body></html>
