@include('hc4.partials.module_header_styles')
<div class="box-header with-border hc4-standard-module-header">
    <div class="hc4-standard-module-header__title">
        <img class="hc4-standard-module-header__icon" src="{{$moduleIcon}}" alt="">
        <span>{{$moduleTitle}}</span>
    </div>
    @if(!is_null($paciente))
        <div class="hc4-standard-module-header__patient" style="display:flex !important; visibility:visible !important; opacity:1 !important;">
            PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
            {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
        </div>
    @endif
    @if(!empty($actionLabel) && !empty($actionOnClick))
        <div class="hc4-standard-module-header__actions" style="display:flex !important; visibility:visible !important; opacity:1 !important;">
            <button type="button" class="hc4-standard-module-header__action" style="display:inline-flex !important; visibility:visible !important; opacity:1 !important;" onclick="{{$actionOnClick}}">
                <img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
                <span>{{$actionLabel}}</span>
            </button>
        </div>
    @endif
</div>
