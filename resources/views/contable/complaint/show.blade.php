@extends('contable.complaint.base')
@section('action-content')
<div class="w-full bg-white">
    <div class="max-w-4xl my-0 mx-auto p-4">
        <h3 class="text-center"><span class="text-gray-600" style="font-size: 30px; font-weight: bold;">REPORTE COMPLAINT</span></h3>
        <div class="text-right" style="margin-right: 20px;">
            <a id="formulario" href="{{ route('formulario.complaint_pdf', ['id' => $id]) }}" target="_blank" type="button" class="btn btn-danger" style="color: #fff; background-color: #d9534f; border-color: #d9534f;">
                <i class="fa fa-file-pdf-o">&nbsp;&nbsp; PDF</i>
            </a>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mt-4">
            <div class="w-full">
                <label class="text-gray-600" for="nombre"><b>Nombre de la Institucion:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->nombre}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="medico"><b>Medico Responsable:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->medico}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="especialista"><b> Especialista Responsable de la Cuenta:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->especialista}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="procedimiento"><b> Nombre del Procedimiento: </b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->procedimiento}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="codigo"><b> código:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->codigo}}">
            </div>
            <div>
                <label class="text-gray-600" for="condicion"><b> Cantidad:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->cantidad}}">
            </div>
            <div>
                <label class="text-gray-600" for="condicion"><b> Observación:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->observacion}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="descripcion"><b> Descripcion del Dispositivo:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->descripcion}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="numero_lote"><b> Numero de Lote:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->numero_lote}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="dispositivo"><b> Dispositivo Nuevo o Reutilizado:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->dispositivo}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="fecha_vencimiento"><b> Fecha de Vencimiento:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="date" disabled value="{{$data->fecha_vencimiento}}">
            </div>
            <div class="w-full">
                <label class="text-gray-600" for="resolucion"><b> Resolucion del Procedimiento:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->resolucion}}">
            </div>
            <div>
                <label class="text-gray-600" for="condicion"><b> Condicion del paciente:</b></label>
                <input style="width: 100%; height: 40px;" class="rounded-md" type="text" disabled value="{{$data->condicion}}">
            </div>
        </div>
        <div class="w-full mt-5">
            <div class="grid place-items-center">
                @if($data->files != null)
                <embed class="rounded max-w-2xl" src="{{ asset('storage/'.$data->files) }}" quality="high" title="Fall Nature Hikes">
                @endif
            </div>
        </div>
    </div>
</div>


@endsection