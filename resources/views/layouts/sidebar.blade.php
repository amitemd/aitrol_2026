<?php

use Illuminate\Support\Facades\Auth;
use Sis_medico\De_Empresa;
use Sis_medico\UsuariosControl;

$imagen = trim((string) Auth::user()->imagen_url);
$avatarPath = $imagen !== '' ? public_path('avatars/'.$imagen) : null;
$avatarUrl = $avatarPath !== null && is_file($avatarPath) ? asset('avatars/'.$imagen) : null;
$nombreUsuario = trim(implode(' ', array_filter([Auth::user()->nombre1, Auth::user()->apellido1])));
$inicialesUsuario = mb_strtoupper(mb_substr((string) Auth::user()->nombre1, 0, 1).mb_substr((string) Auth::user()->apellido1, 0, 1));
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();
$id_empresa = $empresa->id;
//dd($id_empresa);
?>
@if(session()->has('id_empresa'))
@php
$id_empresa = Session::get('id_empresa');
@endphp
@endif

<style type="text/css">
  .sidebar,
  aside.main-sidebar {
    /*background: url({{asset('/imagenes')}}/index-top-bg.png) repeat-x scroll 0 0 transparent !important;*/
  }

  .box-primary {
    background: #ecf0f5 !important;
  }

 

  :root {
    --colorPrimario: #1562ad
  }

  * {
    font-family: var(--fuentePrincipal);
  }

  .main-sidebar {
    width: 345px;
  }

  .content-wrapper,
  .right-side,
  .main-footer {
    margin-left: 345px;
  }

  @media(max-width:767px) {

    .content-wrapper,
    .right-side,
    .main-footer {
      margin-left: 0PX;
    }

    .main-sidebar {
      width: 230px;
    }
  }

  .main-header .logo {
    width: 303px;
  }

  .container-clip {
    display: flex;
    /* justify-content: space-between;  */
    gap: 10;
  }

  .input_directo {
    padding: 0 0.75rem;
    border: 1px solid #262f43;
    border-radius: 5px;
    background-color: #7886a1;
    width: 100%;
  }

  .enlaces_directos {
    padding: 5px 10px;
  }

  .enlaces_directos:hover {
    /* background-color: #1e282c; */
    color: #ffffff !important;
  }

  .fa-thumb-tack:hover {
    color: #0d6efd !important;
    cursor: pointer;
  }

  .container-acce-direc {
    max-height: 50vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  *::-webkit-scrollbar {
    -webkit-appearance: none;
  }

  *::-webkit-scrollbar:vertical {
    width: 10px;
  }

  *::-webkit-scrollbar-button:increment,
  *::-webkit-scrollbar-button {
    display: none;
  }

  *::-webkit-scrollbar:horizontal {
    height: 10px;
  }

  *::-webkit-scrollbar-thumb {
    background-color: #797979;
    border-radius: 20px;
    border: 2px solid #f1f2f3;
  }

  *::-webkit-scrollbar-track {
    border-radius: 10px;
  }
  
</style>
@php
$sidebar = $_SERVER["REQUEST_URI"];
$id_auth = Auth::user()->id;
$agenda_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('ip_creacion','like','%COMERCIAL%')->first();
$encuesta_reporte = Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('ip_creacion','like','%REPORTE_ENCUESTAS%')->first();
$permisos_1 = \Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
@endphp

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        @if($avatarUrl)
          <img src="{{ $avatarUrl }}" class="img-circle" alt="Foto de {{ $nombreUsuario }}">
        @else
          <span class="new-system-avatar new-system-avatar-sidebar" aria-hidden="true">{{ $inicialesUsuario }}</span>
        @endif
      </div>
      <div class="pull-left info">
        <p>{{ $nombreUsuario }}</p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> @lang('tsidebar.online')</a>
      </div>
    </div>

    @if($newSystemNavigation ?? false)
      <form class="sidebar-form" onsubmit="return false;">
        <div class="input-group">
          <input type="search" id="new-system-menu-search" class="form-control" placeholder="Buscar módulo u opción..." autocomplete="off">
          <span class="input-group-btn">
            <button type="button" class="btn btn-flat" aria-label="Buscar en el menú"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
    @endif

    <!-- search form (Optional) -->
    <!--form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form-->
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      @if($newSystemNavigation ?? false)
      <li class="header">PLATAFORMA LARAVEL 13</li>
      <li class="{{ request()->routeIs('new-system.dashboard') ? 'active' : '' }}">
        <a href="{{ route('new-system.dashboard') }}"><i class="fa fa-dashboard"></i> <span>Inicio</span></a>
      </li>
      <li class="{{ request()->routeIs('new-system.companies.*') ? 'active' : '' }}">
        <a href="{{ route('new-system.companies.index') }}"><i class="fa fa-building"></i> <span>Empresas</span></a>
      </li>
      @can('manage-new-system-users')
      <li class="{{ request()->routeIs('new-system.users.*') ? 'active' : '' }}">
        <a href="{{ route('new-system.users.index') }}"><i class="fa fa-users"></i> <span>Usuarios</span></a>
      </li>
      @endcan
      <li class="header">OPERACIÓN DEL SISTEMA</li>
      @endif
      <!-- Optionally, you can add icons to the links -->
      <!--<li class="active"><a href="/"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>-->


      <li class="treeview  @if(strrpos($sidebar, 'atencion')) active @endif ">
        <a href="#"><i class="fa fa-thumb-tack"></i><span>{{trans('tsidebar.acessosdirectos')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <div style="padding: 10px;">
            <input placeholder="Buscador accesos directos..." type="text" class="input_directo" onkeyup="buscadorTarget(2, 'input_directo')">
          </div>
          <div id="container_acce_direc" class="container_acce_direc" style="max-height: 50vh;overflow-y: auto;overflow-x: hidden;">
          </div>
        </ul>
      </li>

      @if((int) $rolUsuario === 1)
      <li class="@if(request()->routeIs('deployment.*')) active @endif">
        <a href="{{ route('deployment.index') }}">
          <i class="fa fa-cogs"></i>
          <span>Despliegue del sistema</span>
        </a>
      </li>
      @endif

      @if($empresa->compras != 1)

      <li class="treeview  @if(strrpos($sidebar, 'atencion')) active @endif ">
        <a href="#"><i class="fa fa-medkit"></i><span>{{trans('tsidebar.atencionAlPaciente')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">

          @if(in_array($rolUsuario, array(1, 11, 22)))
          <li class="treeview">
            <a href="#"><i class="fa fa-check-square-o"></i> <span>{{trans('tsidebar.orden_Formato_012')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip" data-route-prefixes="/orden/ingresada/formato012|/orden_012|/formato012|/formato_053|/hc4/formato012">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('orden_ingresada_formato012') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.ordenes_Ingresadas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('orden_ingresada_formato012') }}">{{trans('tsidebar.ordenes_Ingresadas')}}</a>
              </li>
            </ul>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 4, 5, 20, 11)) == true)
          <li class="treeview">
            <a href="#"><i class="fa fa-server"></i> <span>{{trans('tsidebar.control_documental')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('adelantado ') }}`, '{{trans('tsidebar.controldocumental')}}', '{{trans('tsidebar.modulopacientesadelantados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('adelantado ') }}">{{trans('tsidebar.adelantados')}}</a>
              </li>
            </ul>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1,5)))
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('turnero_sala_administracion')}}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.administraciondeturnos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('turnero_sala_administracion')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>{{trans('tsidebar.administracion_turnos')}}</a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('biopsias_paciente.index') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.ingresomasivobiopsias')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('biopsias_paciente.index') }}"><i class="fa fa-file-text"></i> <span>{{trans('tsidebar.ingresomasivobiopsias')}}</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ url('consultam ') }}`, `{{trans('tsidebar.atencionalpaciente')}}`, `{{trans('tsidebar.consultas_procedimientos')}}`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i> <span>{{trans('tsidebar.consultas_procedimientos')}}</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 4, 5, 11, 20)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ url('paciente') }}`, `{{trans('tsidebar.atencionalpaciente')}}`, `{{trans('tsidebar.pacientes')}}`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ url('paciente') }}"><i class="fa fa-fw fa-users"></i> <span>{{trans('tsidebar.pacientes')}}</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 5, 30, 11)) == true)
          <li class="treeview">
            <a href="#"><i class="fa fa-server"></i> <span>Notas de Entrega (Farmacia)</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('receta.index_generar_nota_entrega') }}`, `{{trans('tsidebar.atencionalpaciente')}}`, `Generar Nota de Entrega`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('receta.index_generar_nota_entrega') }}"><i class="fa fa-file-text"></i> <span>Generar Nota de Entrega</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('receta.index_nota_entrega') }}`, `{{trans('tsidebar.atencionalpaciente')}}`, `Subir Nota de Entrega`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('receta.index_nota_entrega') }}"><i class="fa fa-file-text"></i> <span>Subir Nota de Entrega</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('productos.comparar.index') }}`, 'Insumos', 'Aprobacion de Planilla')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('nota_entrega.planilla_index') }}"><i class="fa fa-file-text"></i> <span>Aprobacion de Planilla</span></a>
              </li>
            </ul>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('enfermeria.index') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.pacientes_del_dia')}} ({{trans('tsidebar.enfermeros')}})')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('enfermeria.index') }}"><i class="fa fa-history"></i> <span>{{trans('tsidebar.pacientes_del_dia')}} @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true) {{trans('tsidebar.enfermeros')}} @endif</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1,11,13,5, 7, 20, 9)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('historia_clinica.fullcontrol') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.pacientes_dia')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>{{trans('tsidebar.pacientes_dia')}}</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 6,7, 11,4)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('camilla.index') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.riesgo_de_Caida')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('camilla.index') }}"><i class="fa fa-hospital-o" aria-hidden="true"></i><span>{{trans('tsidebar.riesgo_de_Caida')}}</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(2, 30)) == false)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('paciente.historial_orden_lab_paciente') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.historial_examenes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('paciente.historial_orden_lab_paciente') }}"><i class="fa fa-user-md"></i><span>@if(in_array($rolUsuario, array(2)) == false) {{trans('tsidebar.tu')}} @endif {{trans('tsidebar.historial_examenes')}} </span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(2)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('paciente.historial_examenes') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.historial_ordenes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('paciente.historial_examenes') }}"><i class="fa fa-book"></i><span>{{trans('tsidebar.historial_ordenes')}} </span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(2)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('recetas_usuario') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.historial_receta')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('recetas_usuario') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i><span>{{trans('tsidebar.historial_receta')}} </span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('disponibilidad.disponibilidad_menu') }}`, '{{trans('tsidebar.atencionalpaciente')}}', '{{trans('tsidebar.disponibilidad')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('disponibilidad.disponibilidad_menu') }}"><i class="fa fa-calendar"></i><span>{{trans('tsidebar.disponibilidad')}}</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('hospital.index') }}`, '{{trans('tsidebar.Hospital')}}', '{{trans('tsidebar.Hospital')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('hospital.index') }}"><i class="fa fa-hospital-o"></i><span>{{trans('tsidebar.Hospital')}}</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(5,1)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('paciente.historial_examenes') }}`, 'Caducidad Código', 'Caducidad Código')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('caducidad_codigo.index') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Caducidad Código</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(31,1, 5, 11)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('ordenes_biopsia.orden_biopsia') }}`, 'Resultado de biopsias', 'Resultado de biopsias')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('ordenes_biopsia.orden_biopsia') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Resultado de biopsias</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(5)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('plan_cuentas.seleccion_empresa')}}`, 'Empresa', 'Modulo seleccion de empresa')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('plan_cuentas.seleccion_empresa') }}"><span>Seleccion de Empresa</span></a>
          </li>
          @endif
        </ul>
      </li>
      @endif
      @if($empresa->compras != 1)
      @if(in_array($rolUsuario, array(2, 17)) == false)
      <li class="treeview  @if(strrpos($sidebar, 'personal')) active @endif ">
        <a href="#"><i class="fa fa-user-md"></i><span>{{trans('tsidebar.personalMedico')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 20, 22)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ url('agenda') }}`, '{{trans('tsidebar.personalMedico')}}', '{{trans('tsidebar.agenda')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> <span>{{trans('tsidebar.agenda')}}</span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(33)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('agenda.agenda2') }}`, '{{trans('tsidebar.personalMedico')}}', '{{trans('tsidebar.agenda')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('agenda.agenda2') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>{{trans('tsidebar.agenda')}}</span></a>
          </li>
          @if($id_auth == '1307189140')
          <li><a href="{{ route('horario.index') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>Horario Laborable</span></a></li>
          <li><a href="{{ route('cortesia.index') }}"><i class="fa fa-fw fa fa-user"></i> <span>Cortesia</span></a></li>
          @endif
          <!--li><a href="{{ url('tecnicas') }}"><i class="glyphicon glyphicon-tasks"></i>Procedimientos</a></li-->
          @endif
          @if(in_array($rolUsuario, array(9, 1, 7)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('enfermeria.index_insumos') }}`, 'Personal médico', 'Anestesiólogo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('enfermeria.index_insumos') }}"><i class="glyphicon glyphicon-plus-sign"></i><span>Anestesiologos </span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(9, 1, 6,7,5)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('insumos_index') }}`, 'Carro de Paro', 'Carro de Paro')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('insumos_index') }}"><i class="glyphicon glyphicon-heart-empty"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Carro de Paro </span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1,5)) == true || $id_auth == '0916593445')
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('horario.index_admin') }}`, 'Personal médico', 'Horario doctores')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i><span>Horario Doctores </span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 4, 12, 22)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('produccion.produccion_estad') }}`, 'Personal médico', 'Producción de Médicos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('produccion.produccion_estad') }}"><i class="fa fa-user-md"></i><span>Producción de Médicos</span></a>
          </li>
          @endif
        </ul>
      </li>
      @endif
      @endif

      @if($empresa->compras != 1)
      <li class="treeview  @if(strrpos($sidebar, 'administracion')) active @endif ">
        <a href="#"><i class="fa fa-briefcase"></i><span>{{trans('tsidebar.administracion')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1)) == true ) <!--administracion de lab-->
          <li class="treeview">
            <a href="#"><i class="ionicons ion-ios-flask"></i> <span>{{trans('tsidebar.administracion_laboratorio')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('orden.index') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.recepcion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('orden.index') }}">{{trans('tsidebar.recepcion')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('orden.index_control') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.laboratorio')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('orden.index_control') }}">{{trans('tsidebar.laboratorio')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('nuevoResultado.realizar_new_lista') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.laboratorio_new')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('nuevoResultado.realizar_new_lista') }}">{{trans('tsidebar.laboratorio_new')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('orden.index_supervision') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.supervision')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('orden.index_supervision') }}">{{trans('tsidebar.supervision')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('examen.index') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.examenes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('examen.index') }}">{{trans('tsidebar.examenes')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('examen_costo.index') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.examenescostos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('examen_costo.index') }}">{{trans('tsidebar.examenescostos')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('protocolo.index') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.protocolos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('protocolo.index') }}">{{trans('tsidebar.protocolos')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('exa_agrupadores') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.agrupadores')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('exa_agrupadores') }}">{{trans('tsidebar.agrupadores')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('agendalabs/agenda') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.agenda')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('agendalabs/agenda') }}">{{trans('tsidebar.agenda')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('factura_agrupada.index_factura_agrupada') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.factura_agrupada')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('factura_agrupada.index_factura_agrupada') }}"> {{trans('tsidebar.factura_agrupada')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('e.labs_estadisticos') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.estadisticos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> {{trans('tsidebar.estadisticos')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('examen.listado_examenes') }}`, 'Administración Laboratorio', 'Mantenimiento de Parametros')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('examen.listado_examenes') }}"><i class="fa fa-file-archive-o"> </i> Mantenimiento de Parametros</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('documento.excel_index') }}`, 'Administración Laboratorio', 'Mantenimiento de Examenes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('documento.excel_index') }}"><i class="fa fa-file-archive-o"> </i> Mantenimiento de Examenes</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('tipo_tubo.index') }}`, 'Administración Laboratorio', 'Mantenimiento Tubos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('tipo_tubo.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Tubos</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('mantenimientoexcel.index') }}`, 'Administración Laboratorio', 'Administración de Tubos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('mantenimientoexcel.index') }}"><i class="fa fa-plus-square"> </i> Administración de Tubos</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('mantenimiento.examderivados.index') }}`, 'Administración Laboratorio', 'Mantenimiento Examenes Derivados')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('mantenimiento.examderivados.index') }}"><i class="fa fa-file"> </i> Mantenimiento Examenes Derivados</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('plantillacontrollabs.index') }}`, 'Administración Laboratorio', 'Mantenimiento Plantillas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('plantillacontrollabs.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Plantillas</a>
              </li>
            </ul>
          </li>
          @endif

          @if((!is_null($agenda_permiso) || in_array($rolUsuario, array(1, 20, 5)) == true) && in_array($rolUsuario, array(2)) == false)
          <li class="treeview  @if(strrpos($sidebar, 'insumos')) active @endif ">
            <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.comercial')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('comercial.proforma.index_proforma') }}`, '{{trans('tsidebar.comercial')}}', '{{trans('tsidebar.proformas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('comercial.proforma.index_proforma') }}">{{trans('tsidebar.proformas')}}</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('prodtarifario.index') }}`, '{{trans('tsidebar.comercial')}}', '{{trans('tsidebar.producto_tarifario')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('prodtarifario.index') }}">{{trans('tsidebar.producto_tarifario')}}</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('proforma.index_plantilla') }}`, 'Comercial', 'Plantilla de Productos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('proforma.index_plantilla') }}">Plantilla de Productos</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('productos_servicios_index')}}`, 'Inventario', 'Modulo productos servicios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('productos_servicios_index') }}"></i>Productos</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('comision.index')}}`, 'Comercial', 'Modulo Comisiones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('comision.index') }}"></i>Comisiones</a>
              </li>
            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1)) == true )<!-- Convenios Privados-->
          <li class="treeview">
            <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.convenios_privados')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">

              @php
              $opcion = '1'; // LABS PRIVADOS
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
              @endphp
              @if(!is_null($opcion_u))
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('privados.index') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloLABSprivados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('privados.index')}}"><i class="ionicons ion-ios-flask"></i> <span> {{$opcion_u->opcion->nombre}}</span></a>
              </li>
              @endif

              @php
              $opcion = '3'; // CONSULTA PACIENTES
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
              @endphp
              @if(!is_null($opcion_u))
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('pacientes.consulta') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloconsultapacientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a>
              </li>
              @endif

              @php
              $opcion = '4'; // AGENDA
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
              @endphp
              @if(!is_null($opcion_u))
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('solicitud.agenda') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloagendaprivados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('solicitud.agenda')}}"><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a>
              </li>
              @endif

            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15,6)) == true) <!-- Enfermeria -->
          <li class="treeview">
            <a href="#"> <i class="fa fa-file"> </i><span>{{trans('tsidebar.enfermeria')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-rigth"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('limpieza_equipo.index') }}`, '{{trans('tsidebar.enfermeria')}}', '{{trans('tsidebar.registro_de_Limpieza_de_Equipos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('limpieza_equipo.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i> {{trans('tsidebar.registro_de_Limpieza_de_Equipos')}}</a>
              </li>
            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 11)))<!-- Encuestas -->
          <li class="treeview">
            <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <!--li><a href="{{ route('area.index') }}">Areas</a></li>
                  <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
                  <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
                  <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li-->
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('rrhh.resultados_ok') }}`, '{{trans('tsidebar.encuestas')}}', '{{trans('tsidebar.listado_encuestas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('rrhh.resultados_ok') }}">{{trans('tsidebar.listado_encuestas')}}</a>
              </li>
              <!--li><a href="{{ route('rrhh.estadisticas') }}">Estadisticas</a></li-->
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('rrhh.encuesta_estadistica') }}`, '{{trans('tsidebar.encuestas')}}', '{{trans('tsidebar.estadisticas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('rrhh.encuesta_estadistica') }}">{{trans('tsidebar.estadisticas')}}</a>
              </li>

            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1)))<!-- Encuestas -->
          <li class="treeview">
            <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.NuevasEncuestas')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('tipo_sugerencia.new_estadisticas') }}`,  `{{trans('tsidebar.NuevasEncuestas')}}`, `{{trans('tsidebar.estadisticas')}}`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('tipo_sugerencia.new_estadisticas') }}">{{trans('tsidebar.estadisticas')}}</a>
              </li>

            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 12)) == true )<!-- Encuestas Labs -->
          <li class="treeview">
            <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas_labs')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('laboratorio.resultados_labs') }}`, '{{trans('tsidebar.listado_encuestas_labs')}}', '{{trans('tsidebar.listado_encuestas_labs')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('laboratorio.resultados_labs') }}">{{trans('tsidebar.listado_encuestas_labs')}}</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('laboratorio.estadisticalabs') }}`, '{{trans('tsidebar.listado_encuestas_labs')}}', '{{trans('tsidebar.estadisticas_labs')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('laboratorio.estadisticalabs') }}">{{trans('tsidebar.estadisticas_labs')}}</a>
              </li>

              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('nueva.encuestalabs') }}`, `{{trans('tsidebar.Index_Estadistico_Nuevo')}}`, `{{trans('tsidebar.Index_Estadistico_Nuevo')}}`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('nueva.encuestalabs') }}">{{trans('tsidebar.Index_Estadistico_Nuevo')}}</a>
              </li>

            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1,4,6,11,24,4)) == true || $id_auth=='0954346441' || !is_null($permisos_1)) <!-- Servicios Generales-->
          <li class="treeview">
            <a href="#"> <i class="fa fa-file"> </i><span>{{trans('tsidebar.servicios_Generales')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-rigth"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('limpieza_banos.index') }}`, '{{trans('tsidebar.servicios_Generales')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.registro_de_Limpieza_de_Baños')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('limpieza_banos.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>{{trans('tsidebar.registro_de_Limpieza_de_Baños')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('mantenimientos_generales.index')}}`, 'Servicios Generales', 'Modulo Registro de Limpieza y Desinfección')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('mantenimientos_generales.index')}}"><i class="fa fa-wrench" aria-hidden="true"></i> Mantenimientos</a>
              </li>
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('index_pentax_limpieza')}}`, 'Servicios Generales', 'Registro Limpieza de Pentax')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('index_pentax_limpieza')}}"><i class="fa fa-leanpub" aria-hidden="true"></i> Registro Limpieza de Pentax</a>
              </li>
            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)<!-- quirofano-->
          <li class="treeview">
            <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.pentax')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('pentax') }}`, '{{trans('tsidebar.pentax')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.control')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('pentax') }}">{{trans('tsidebar.control')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('pentaxtv') }}`, '{{trans('tsidebar.pentax')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.sala_espera')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('pentaxtv') }}">{{trans('tsidebar.sala_espera')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('pentaxtv_dr') }}`, '{{trans('tsidebar.pentax')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.ver')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('pentaxtv_dr') }}">{{trans('tsidebar.ver')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('consulta_tv') }}`, '{{trans('tsidebar.pentax')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.control_consultas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('consulta_tv') }}">{{trans('tsidebar.control_consultas')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('nrc_descuentos/aprobacion') }}`, '{{trans('tsidebar.pentax')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.Recibos_pendientes_aprobacion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('nrc_descuentos/aprobacion') }}">{{trans('tsidebar.Recibos_pendientes_aprobacion')}}</a>
              </li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.procedimientos')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('procedimientos_dr') }}`, 'Procedimientos', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.control')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('procedimientos_dr') }}">{{trans('tsidebar.control')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ url('procedimientostv_dr') }}`, 'Procedimientos', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.ver')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ url('procedimientostv_dr') }}">{{trans('tsidebar.ver')}}</a>
              </li>
            </ul>
          </li>
          @endif

          @if(in_array($rolUsuario, array(4, 5, 20)) == true )
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('orden.index') }}`, 'Administración de Laboratorio', 'Laboratorio ordenes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('orden.index') }}"><i class="ionicons ion-ios-flask"></i><span>Laboratorio - Ordenes </span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(10)) == true)
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('orden.index_control') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.laboratorio')}} {{trans('tsidebar.control')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('orden.index_control') }}"><i class="ionicons ion-ios-flask"></i><span>{{trans('tsidebar.laboratorio')}} - {{trans('tsidebar.control')}} </span></a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('nuevoResultado.realizar_new_lista') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.laboratorio_new')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('nuevoResultado.realizar_new_lista') }}">{{trans('tsidebar.laboratorio_new')}}</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('agendalabs.agenda') }}`, '{{trans('tsidebar.administracion_laboratorio')}}', '{{trans('tsidebar.agenda')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('agendalabs.agenda') }}"><i class="fa fa-calendar"></i><span>{{trans('tsidebar.agenda')}}</span></a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('examen.listado_examenes') }}`, 'Administración Laboratorio', 'Mantenimiento de Parametros')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('examen.listado_examenes') }}"><i class="fa fa-file-archive-o"> </i> Mantenimiento de Parametros</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('documento.excel_index') }}`, 'Administración Laboratorio', 'Mantenimiento de Examenes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('documento.excel_index') }}"><i class="fa fa-file-archive-o"> </i> Mantenimiento de Examenes</a>
          </li>
          @if(!is_null($permisos_1))
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('tipo_tubo.index') }}`, 'Administración de Laboratorio', 'Mantenimiento Tubos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('tipo_tubo.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Tubos</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('mantenimientoexcel.index') }}`, 'Administración de Laboratorio', 'Administración de Tubos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('mantenimientoexcel.index') }}"><i class="fa fa-plus-square"> </i> Administración de Tubos</a>
          </li>
          @endif
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('plantillacontrollabs.index') }}`, 'Administración Laboratorio', 'Mantenimiento Plantillas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('plantillacontrollabs.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Plantillas</a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(12,11, 22)) == true ) <!--Supervision-->
          <li>
            <a href="{{ route('orden.index_supervision') }}"><i class="ionicons ion-ios-flask"></i><span>Laboratorio </span></a>
          </li>
          <li>
            <a href="{{ route('examen.index') }}"><i class="ionicons ion-ios-flask"></i><span>Exámenes </span></a>
          </li>

          @endif
          @if(in_array($rolUsuario, array(33)) == true ) <!--CAMBIO PARA LABS CERTIFICACION DE EXÁMENES-->
          <li>
            <a href="{{ route('orden.index_doctor_menu') }}"><i class="ionicons ion-ios-flask"></i><span>Laboratorio </span></a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(12, 22)) == true )
          <li>
            <a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(12)) == true)
          <li>
            <a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a>
          </li>
          @endif
          @if(in_array($rolUsuario, array(1, 20)) == true )<!-- Factura agrupada LABS-->
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('factura_agrupada.index_factura_agrupada') }}`, '{{trans('tsidebar.facturas')}}', '{{trans('tsidebar.factura_agrupada_labs')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('factura_agrupada.index_factura_agrupada') }}"><i class="ionicons ion-ios-flask"></i><span>{{trans('tsidebar.factura_agrupada_labs')}}</span></a>
          </li>
          @endif

          <!-- NUEVO OPCIONES POR TIPO DE USUARIO-->
          @if(in_array($rolUsuario, array(1)) == false )<!-- OPCIONES DE CONVENIOS PRIVADOS -->
          @php
          $opcion = '1'; // LABS PRIVADOS
          $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('privados.index') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloLABSprivados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('privados.index')}}"><i class="ionicons ion-ios-flask"></i> <span>{{$opcion_u->opcion->nombre}}</span></a>
          </li>
          @endif

          @php
          $opcion = '3'; // CONSULTA PACIENTES
          $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('pacientes.consulta') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloconsultapacientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a>
          </li>
          @endif

          @php
          $opcion = '4'; // AGENDA
          $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('solicitud.agenda') }}`, '{{trans('tsidebar.convenios_privados')}}', '{{trans('tsidebar.moduloagendaprivados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('solicitud.agenda')}}"><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a>
          </li>
          @endif
          @endif

          <!-- @if(in_array($rolUsuario, array(1, 12, 5, 15,19)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('dashboard.apps') }}`, '{{trans('tsidebar.menu_apps')}}', '{{trans('tsidebar.moduloApps')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('dashboard.apps') }}"><i class="fa fa-dashboard"></i> <span>{{trans('tsidebar.menu_apps')}}</span></a>
          </li>
          @endif -->

          @if(!is_null($encuesta_reporte) || in_array($rolUsuario, array(1, 11)))
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('tiposugerencia.listado_index') }}`, '{{trans('tsidebar.encuestas')}}', '{{trans('tsidebar.listado_encuestas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('tiposugerencia.listado_index') }}"><i class="fa fa-file-text"></i> Listado de Encuestas</a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 6, 11,4)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('limpieza.salas') }}`, '{{trans('tsidebar.limpieza_y_Desinfeccion')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.limpieza_y_Desinfeccion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('limpieza.salas') }}"><i class="fa fa-file"></i><span>{{trans('tsidebar.limpieza_y_Desinfeccion')}}</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 6, 11,4, 7, 10, 24)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('control_temperatura.index_labs') }}`, 'Limpieza y Desinfeccion', 'Modulo Temperatura Labs')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('control_temperatura.index_labs') }}"><i class="fa fa-file"></i><span>Reporte temperatura Labs</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 6, 11,4, 7, 24)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('control_temperatura.index_contemp') }}`, 'Limpieza y Desinfeccion', 'Modulo Temperatura Ieced')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('control_temperatura.index_contemp') }}"><i class="fa fa-file"></i><span>Reporte temperatura Endoscopy</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15,21,20,19,16,14)) == true)
          <li class="treeview  container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('ticket_soporte_tecnico.index') }}`, '{{trans('tsidebar.Ticket')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.ticket_Soporte_Tecnico')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('ticket_soporte_tecnico.index')}}"> <i class="fa fa-ticket" aria-hidden="true"></i><span>{{trans('tsidebar.ticket_Soporte_Tecnico')}} </span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true )
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('observacion.index') }}`, '{{trans('tsidebar.observaciones')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.observaciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('observacion.index') }}"><i class="glyphicon glyphicon-copy"></i>
              <span>{{trans('tsidebar.observaciones')}} </span>
              <span class="pull-right-container">
                <small class="label pull-right bg-red" id="o_cantidad"></small>
              </span>
            </a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(1)) == true )
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('acta.entrega_index') }}`, 'Actas de Entrega', '{{trans('tsidebar.modulo')}} Actas de Entregas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('acta.entrega_index') }}"><i class="fa fa-file"></i>
              <span> Actas de Entrega </span>
              <span class="pull-right-container">
                <small class="label pull-right bg-red" id="o_cantidad"></small>
              </span>
            </a>
          </li>
          @endif



          @if(in_array($rolUsuario, array(11)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ url('procedimientostv_dr') }}`, '{{trans('tsidebar.procedimientos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.procedimientostv')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ url('procedimientostv_dr') }}"><i class="fa fa-television"></i> <span>{{trans('tsidebar.procedimientostv')}}</span></a>
          </li>
          @endif

          @if(in_array($rolUsuario, array(7, 11)) == true)
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ url('pentaxtv_dr') }}`, 'Procedimientos', 'Pentax TV')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television  "></i><span>Pentax TV</span></a>
          </li>
          @endif

        </ul>
      </li>
      @endif


      <li class="treeview  @if(strrpos($sidebar, 'contable')) active @endif ">
        <a href="#"><i class="fa fa-usd"></i><span>{{trans('tsidebar.cuentasyFinanzas')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1,20, 22)) == true)
          <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">
            <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.contable')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="treeview @if(strrpos($sidebar, 'contable/amortizacion')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('amortizacion.index')}}`, 'Contabilidad', 'Módulo de amortización de préstamos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('amortizacion.index') }}">Amortización de préstamos</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
                <a href="#"> <i class="fa fa-building"> </i>{{trans('tsidebar.empresa')}}
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('plan_cuentas.seleccion_empresa')}}`, '{{trans('tsidebar.empresa')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.seleccion_empresa')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>{{trans('tsidebar.seleccion_empresa')}}</a>
                  </li>
                </ul>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
                <a href="#"> <i class="fa fa-university"> </i>{{trans('tsidebar.contabilidad')}}
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif  container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('plan_cuentas.index')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.plan_cuentas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('plan_cuentas.index') }}"></i>{{trans('tsidebar.plan_cuentas')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('libro_mayor.index')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.libro_mayor')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('libro_mayor.index') }}"></i>{{trans('tsidebar.libro_mayor')}}</a>
                  </li>
                  <?php
                  $subpermiso = DB::table('roles_usuario')->where('id_usuario', Auth::user()->id)->first();
                  if ($subpermiso != '' && $subpermiso->tipo_usuario != '') {
                    if (in_array(21, json_decode($subpermiso->tipo_usuario)) == true) {
                  ?>
                      <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif  container-clip">
                        <button button style="background: transparent; border: none;">
                          <i onclick="anclarTarget(this, `{{ route('balance_general.index') }}`, 'Contabilidad', 'modulo de balance general')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a>
                      </li>
                      <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif  container-clip">
                        <button button style="background: transparent; border: none;">
                          <i onclick="anclarTarget(this, `{{route('estadoresultados.index')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.estado_resultado_integral')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a>
                      </li>
                      <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif  container-clip">
                        <button button style="background: transparent; border: none;">
                          <i onclick="anclarTarget(this, `{{route('balance_comprobacion.index')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.balance_comprobacion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('balance_comprobacion.index') }}"></i>{{trans('tsidebar.balance_comprobacion')}}</a>
                      </li>
                  <?php
                    }
                  }
                  ?>
                  <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif  container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('librodiario.index')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.visualizar_asientos_diarios')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('librodiario.index') }}"></i>{{trans('tsidebar.visualizar_asientos_diarios')}}</a>
                  </li>
                  {{-- <li><a href="{{ route('compras.informe') }}"></i>{{trans('tsidebar.informe_compras')}}</a>
              </li> --}}
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('flujoefectivo.index')}}`, 'Contabilidad', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.flujo_efectivo')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('flujoefectivocomparativo.index')}}`, 'Contabilidad', '{{trans('tsidebar.modulo')}}  flujo efectivo comparativo I')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('flujoefectivocomparativo.index') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}}</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <a href="{{ route('flujoefectivocomparativo.index2') }}"></i>Flujo de efectivo comparativo II</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('flujoefectivocomparativo.index2')}}`, 'Contabilidad', 'Modulo flujo efectivo comparativo II')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('ats.index') }}"></i>SRI / ATS</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
                <a href="#"> <i class="fa fa-table"> </i>Formularios SRI
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'formulario_107')) active @endif  container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('formulario_107')}}`, '{{trans('tsidebar.contabilidad')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.plan_cuentas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('formulario_107') }}"></i>Formulario 107</a>
                  </li>
                </ul>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/docelectronicos')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('docelectronicos.index')}}`, 'Contabilidad', 'Importacion de documentos SRI')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('docelectronicos.index') }}"></i>Importar Documentos</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('verificar_documento_sri')}}`, 'Compras', 'Modulo  de  compras')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('verificar_documento_sri') }}"></i>Verificar documentos SRI</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'tributacion/estadoDocumentos')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('estado_documento_sri')}}`, 'Tributacion', 'Modulo  Tributario')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('estado_documento_sri') }}"></i>Estado documentos SRI</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('librodiario.descuadrados')}}`, 'Contabilidad', '{{trans('tsidebar.modulo')}}  descuadros')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('librodiario.descuadrados') }}"></i>{{trans('tsidebar.descuadrados')}}</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'contabilidad/anulados')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('diario.index_dobleAnulacion')}}`, 'Contabilidad',  'Asientos anulados')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{route('diario.index_dobleAnulacion')}}"></i>Asientos anulados</a>
              </li>
              @if(Auth::user()->id_tipo_usuario == 1 || Auth::user()->id == "0928440866")
              <li class="treeview @if(strrpos($sidebar, 'contable/cierre/mensual')) active @endif  container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('cierre.mensual.index')}}`, '{{trans('tsidebar.contabilidad')}}', 'Cierre Contable" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('cierre.mensual.index') }}"></i>Cierre Contable</a>
              </li>
              @endif
            </ul>
          </li>
          <li class="treeview @if(strrpos($sidebar, 'acreedores')) active @endif">
            <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.acreedores')}}
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-rigth"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento')) active @endif">
                <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/rubrosa')) active @endif container-clip"">
                              <button button style=" background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('rubrosa.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.rubros_acreedores')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('rubrosa.index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.rubros_acreedores')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif  container-clip"">
                              <button button style=" background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('acreedores_index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.acreedores')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('acreedores_index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.acreedores')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif  container-clip"">
                              <button button style=" background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('saldosinicialesp.index2')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.mantenimiento')}} s{{trans('tsidebar.saldos_iniciales')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('saldosinicialesp.index2') }}"> {{trans('tsidebar.saldos_iniciales')}}</a>
                  </li>

                </ul>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos')) active @endif">
                <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="treeview">
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/anticipos')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('acreedores_cegreso_anticipo')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.anticipos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('acreedores_cegreso_anticipo') }}"><i class="fa  fa-file-text"></i>Anticipos</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/retenciones')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('retenciones_index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.retenciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('retenciones_index') }}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/egreso')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('acreedores_cegreso')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.comprobante_egreso')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('acreedores_cegreso') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso')}}</a>
                  </li>

                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/index')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('comp_egreso_masivo.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.comprobante_egreso_masivo')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('comp_egreso_masivo.index') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso_masivo')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/cuentas/egreso/varios')) active @endif container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('egresosv.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.comprobante_egreso_vario')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('egresosv.index') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.comprobante_egreso_vario')}}</a>
                  </li>
                  <li class="container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('cruce.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.cruce_valores_favor')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('cruce.index') }}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores_favor')}}</a>
                  </li>
                  <li class="treeview @if(strrpos($sidebar, 'contable/cruce_cuentas/valores')) active @endif  container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('pr.cruce_cuentas')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.cruce_cuentas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('pr.cruce_cuentas') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.cruce_cuentas')}}</a>
                  </li>
                  <li class="container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('creditoacreedores.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_credito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('creditoacreedores.index') }}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a>
                  </li>
                  <li class="container-clip">
                    <button button style="background: transparent; border: none;">
                      <i onclick="anclarTarget(this, `{{route('debitoacreedores.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.nota_debito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </button>
                    <a href="{{ route('debitoacreedores.index') }}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a>
                  </li>

              </li>
            </ul>

          </li>
        </ul>
        <ul class="treeview-menu">
          <li class="treeview @if(strrpos($sidebar, 'acreedores/informes')) active @endif">
            <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-rigth"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/cartera/pagar')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('acreedores.informenc')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.informes_nota_creadito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('acreedores.informenc') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes_nota_creadito')}}</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'acreedores/credito/informe')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('carterap.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.carteras_pagar')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('carterap.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
              </li>
              <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/informe/saldos')) active @endif container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('saldos_acreedor.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.informe_saldos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('saldos_acreedor.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('contable.reporte_tiempo')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.informe_tiempo')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('contable.reporte_tiempo') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_tiempo')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('chequesa.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.informe_cheques_girados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('chequesa.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a>
              </li>

              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('compra.informe.index_cheques_emitidos')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.cheques_emitidos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('compra.informe.index_cheques_emitidos') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.cheques_emitidos')}}</a>
              </li>

              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('informe_retenciones.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.informe_retenciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('informe_retenciones.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('contable.anticipo_proveedores')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.informe_anticipos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('contable.anticipo_proveedores') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_anticipos')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('deudasvspagos.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.deudas_vs_pagos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('deudasvspagos.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_pagos')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('deudas_pendientes.index')}}`, '{{trans('tsidebar.acredores')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.deudas_pendientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('deudas_pendientes.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('contable.reporte_edad_cartera')}}`, 'Acredores', 'modulo de Edad de cartera')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('contable.reporte_edad_cartera') }}"><i class="fa fa-file-excel-o"></i>Edad de cartera</a>
              </li>
            </ul>
          </li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.clientes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-rigth"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="treeview">
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('clientes.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.crear_cliente')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('clientes.index') }}"></i>{{trans('tsidebar.crear_cliente')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('rubros_cliente.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.rubros_clientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('rubros_cliente.index') }}"></i>{{trans('tsidebar.rubros_clientes')}}</a>
              </li>
              <li class="container-clip">
                <button button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{route('saldosinicialesclientes.index2')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.saldos_iniciales')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('saldosinicialesclientes.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a>
              </li>
          </li>
        </ul>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li class="treeview">
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('retencion.cliente')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.retenciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('retencion.cliente')}}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('chequespost.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.cheques_postfechados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('chequespost.index')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cheques_postfechados')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('cr.cruce_cuentas')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.cruce_cuentas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('cr.cruce_cuentas')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cruce_cuentas')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('comprobante_ingreso.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}{{trans('tsidebar.comprobante_ingreso')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('comprobante_ingreso.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingreso')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('comprobante_ingreso_v.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.comprobante_ingresos_varios')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('comprobante_ingreso_v.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingresos_varios')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('cruce_clientes.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.cruce_valores')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('cruce_clientes.index')}}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('nota_credito_cliente.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_credito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('nota_credito_cliente.index')}}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('nota_cliente_debito.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_debito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('nota_cliente_debito.index')}}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a>
          </li>

      </li>
    </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('clientes.deudas.pendientes')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.deudas_pendientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('clientes.deudas.pendientes')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a>
          </li>


          <!--li class="container-clip">
                                  <button button style="background: transparent; border: none;">
                                    <i onclick="anclarTarget(this, `{{route('clientes.deudas_vencidas.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.deudas_vencidas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                                  </button>
                                  <a href="{{ route('clientes.deudas_vencidas.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vencidas')}}</a>
                                </li>
                                <li class="container-clip">
                                  <button button style="background: transparent; border: none;">
                                    <i onclick="anclarTarget(this, `{{route('insumos.precioProducto.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.reporte_precios_gastritech')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                                  </button>
                                  <a href="{{ route('insumos.precioProducto.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.reporte_precios_gastritech')}}</a>
                                </li-->



          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('clientes.cartera_vencida.index')}}`, '{{trans('tsidebar.clientes')}}', 'Carteras Vencidas'" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('clientes.cartera_vencida.index')}}"><i class="fa fa-file-excel-o"></i>Cartera Vencida</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('clientes.deudas_vencidas.index')}}`, '{{trans('tsidebar.clientes')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.deudas_vencidas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('clientes.deudas_vencidas.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vencidas')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('clientes.saldo.cxc')}}`, 'Clientes', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.saldo_cuentas_por_cobrar')}} 2')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('clientes.saldo.cxc')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.saldo_cuentas_por_cobrar')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('cliente.informe.retenciones')}}`, 'Clientes', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.informe_retenciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{route('cliente.informe.retenciones')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a>
          </li>
          {{-- <li><a href="#"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
      </li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li> --}}
      <li class="container-clip">
        <button button style="background: transparent; border: none;">
          <i onclick="anclarTarget(this, `{{route('deudasvspagos.cliente')}}`, 'Clientes', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.deudas_vs_pagos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
        </button>
        <a href="{{route('deudasvspagos.cliente')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_cobros')}}</a>
      </li>
      <li class="container-clip">
        <button button style="background: transparent; border: none;">
          <i onclick="anclarTarget(this, `{{route('nota_credito_cliente.informe_notacreditoclientes')}}`, 'Clientes', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.informe_notas_credito_clientes')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
        </button>
        <a href="{{ route('nota_credito_cliente.informe_notacreditoclientes') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_notas_credito_clientes')}}</a>
      </li>
      </li>
    </ul>

    </li>
    </ul>
    </li>
    <li class="treeview @if(strrpos($sidebar, 'Banco')) active @endif">
      {{-- <a href="#"> <i class="fa fa-cubes"> </i>{{trans('tsidebar.caja_y_bancos')}} --}}
      <a href="#"> <i class="fa fa-money"> </i>{{trans('tsidebar.caja_bancos')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('caja_banco.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.crear_cajabanco')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('caja_banco.index') }}"></i>{{trans('tsidebar.crear_cajabanco')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('banco_clientes.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.listado_bancos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('banco_clientes.index') }}"></i>{{trans('tsidebar.listado_bancos')}}</a>
        </li>
        <li class="treeview @if(strrpos($sidebar, 'Banco/notadebito')) active @endif  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('notadebito.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_debito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('notadebito.index') }}"></i>{{trans('tsidebar.nota_debito')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('notacredito.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_credito')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('notacredito.index') }}"></i>{{trans('tsidebar.nota_credito')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('debitobancario.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.nota_debito_bancaria')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('debitobancario.index') }}"></i>{{trans('tsidebar.nota_debito_bancaria')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('depositobancario.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.deposito_bancario')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('depositobancario.index') }}"></i>{{trans('tsidebar.deposito_bancario')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('transferenciabancaria.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.transferencia_bancaria')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('transferenciabancaria.index') }}"></i>{{trans('tsidebar.transferencia_bancaria')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('conciliacionbancaria.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.conciliacion_bancaria')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('conciliacionbancaria.index') }}"></i>{{trans('tsidebar.conciliacion_bancaria')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('estadocuentabancos.index')}}`, '{{trans('tsidebar.caja_bancos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.estado_cuenta')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('estadocuentabancos.index') }}"></i>{{trans('tsidebar.estado_cuenta')}}</a>
        </li>
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('newconciliacionbancaria.index')}}`, 'Caja Bancos', 'Modulo de conciliación bancaria')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('newconciliacionbancaria.index') }}"></i>Nueva Conciliación Bancaria </a>
        </li>
      </ul>
    </li>
    <li class="treeview">
      <a href="#">
        <i class="fa fa-share"></i> <span>{{trans('tsidebar.activos_fijos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.mantenimientos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afGrupo.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.grupos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afGrupo.index') }}"> {{trans('tsidebar.grupos')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afTipo.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.tipos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afTipo.index') }}"> {{trans('tsidebar.tipos')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afActivo.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.activos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afActivo.index') }}"> {{trans('tsidebar.activos')}}</a>
            </li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.documentos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afDocumentoFactura.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}}   {{trans('tsidebar.facturas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afDocumentoFactura.index') }}"> {{trans('tsidebar.facturas')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afDepreciacion.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.depreciaciones')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afDepreciacion.index') }}"> {{trans('tsidebar.depreciaciones')}}</a>
            </li>

            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('documentofactura.index_preorden')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.ordenes_')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('documentofactura.index_preorden') }}"> {{trans('tsidebar.ordenes_')}}</a>
            </li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.informes')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('afInformes.index')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.saldos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('afInformes.index') }}"> {{trans('tsidebar.saldos')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('activofjo.index_listado')}}`, '{{trans('tsidebar.activos_fijos')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.listado_activos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{route('activofjo.index_listado')}}">{{trans('tsidebar.listado_activos')}}</a>
            </li>
            {{-- <li><a href="#"> {{trans('tsidebar.retencion')}}</a>
        </li> --}}
        {{-- <li><a href="#"> {{trans('tsidebar.facturas')}}</a>
    </li> --}}
    </ul>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('compras_index')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}}  {{trans('tsidebar.compras')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('compras_index') }}"></i>{{trans('tsidebar.compras')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('saldosinicialesp.index2')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.saldos_iniciales')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('saldosinicialesp.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('compras.pedidos')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.pedidos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('compras.pedidos') }}"></i>{{trans('tsidebar.pedidos')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('fact_contable_index')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.factura_contable')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('fact_contable_index') }}"></i>{{trans('tsidebar.factura_contable')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('compras.informe')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.informe_compras')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('compras.informe') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_compras')}}</a>
        </li>
        <!-- <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>-->
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('contable.compras.kardex.index')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.kardex_compra')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('contable.compras.kardex.index') }}"></i>{{trans('tsidebar.kardex_compra')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.inventario.index')}}`, '{{trans('tsidebar.compras')}}', '{{trans('tsidebar.existencias')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('insumos.inventario.index') }}">{{trans('tsidebar.existencias')}}</a>
        </li>
    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras_interno')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <!--<li class="treeview"><a href="{{ route('contable.bodega.index') }}"></i>Bodegas</a></li>-->
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('contable.compraspedidos.index')}}`, '{{trans('tsidebar.compras_interno')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.pedidos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('contable.compraspedidos.index') }}"></i>{{trans('tsidebar.pedidos')}}</a>
        </li>
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Autorizaciones</a></li>-->
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Inventario</a></li>-->
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('kardex_inventario.index')}}`, '{{trans('tsidebar.compras_interno')}}', '{{trans('tsidebar.modulo')}}{{trans('tsidebar.kardex')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('kardex_inventario.index') }}"></i>{{trans('tsidebar.kardex')}}</a>
        </li>
        <!--<li class="treeview"><a href="{{ route('fact_contable_index') }}"></i>Factura</a></li>-->
        <li class="treeview  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('pedidos_inventario.index')}}`, '{{trans('tsidebar.compras_interno')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.informe_productos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('pedidos_inventario.index') }}"></i>{{trans('tsidebar.informe_productos')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('contable.compraspedido.indexInicial')}}`, '{{trans('tsidebar.compras_interno')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.mantenimiento_iniciales')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('contable.compraspedido.indexInicial')}}">{{trans('tsidebar.mantenimiento_iniciales')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('compraspedidos.index_proceso')}}`, '{{trans('tsidebar.compras_interno')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.mantenimiento_usuarios')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('compraspedidos.index_proceso')}}">{{trans('tsidebar.mantenimiento_usuarios')}}</a>
        </li>
      </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-cubes"> </i>{{trans('tsidebar.inventario')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.kardex.index')}}`, '{{trans('tsidebar.inventario')}}', '{{trans('tsidebar.modulo')}} kardex')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('insumos.kardex.index') }}"></i>{{trans('tsidebar.kardex')}}</a>

        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.inventario.index')}}`, 'Inventario', 'Modulo inventario de insumos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('insumos.inventario.index') }}"></i>Inventario Insumos</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.inventario_serie.index')}}`, 'Inventario', 'Inventario Insumos Por Serie')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('insumos.inventario_serie.index') }}"></i>Inventario Insumos Por Serie</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('productos.saldos_iniciales')}}`, 'Inventario', 'Modulo saldos iniciales')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('productos.saldos_iniciales') }}"></i>Saldos iniciales</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('notainventario.index')}}`, 'Inventario', 'Modulo de nota ingreso inventario')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('notainventario.index') }}"></i>Nota de Ingreso Inventario</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('productos_servicios_index')}}`, 'Inventario', 'Modulo productos servicios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('productos_servicios_index') }}"></i>Productos</a>
        </li>

    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="ion-person-stalker"> </i>{{trans('tsidebar.nomina')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('config_valor.index')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.configuracion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('config_valor.index') }}"></i>{{trans('tsidebar.configuracion')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nomina.index')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.crear_empleado_rol')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nomina.index') }}"></i>{{trans('tsidebar.crear_empleado_rol')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nuevo_rol.masivo_search')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.roles_pago_empresas')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevo_rol.masivo_search') }}"></i><span style="color: red;">{{trans('tsidebar.roles_pago_empresas')}}</span></a>
        </li>
        <!--li class="container-clip">
                        <button button style="background: transparent; border: none;" >
                          <i onclick="anclarTarget(this, `{{route('buscador_rol.index')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.rol_pago_individual')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('buscador_rol.index') }}"></i>{{trans('tsidebar.rol_pago_individual')}}</a>
                    </li-->
        @if(in_array($rolUsuario, array(1,20)))
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nuevo_rol.buscador_index_sucursal')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('Rol por sucursal')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevo_rol.buscador_index_sucursal')}}"></i> {{trans('tsidebar.rol_pago_individual')}} Por Sucursal</a>
        </li>
        @endif
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('prestamos_empleado.index')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.prestamos_empleados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('prestamos_empleado.index') }}"></i>{{trans('tsidebar.prestamos_empleados')}}</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('prestamos_empleados.index_saldos')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.saldo_inicial_empleados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('prestamos_empleados.index_saldos') }}"></i>{{trans('tsidebar.saldo_inicial_empleados')}}</a>
        </li>
        <!--<li><a href="{{ route('anticipos_empleado.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>-->
        <!-- <li><a href="{{ route('nomina_anticipos_1eraquincena.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li> -->
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nominaquincena.index_quincena')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.anticipo_primera_quincena')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nominaquincena.index_quincena')}}"></i>{{trans('tsidebar.anticipo_primera_quincena')}}</a>
        </li>
        @if(in_array($rolUsuario, array(1, 20)))
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nuevo_rol.acumula_decimo_tercer')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.decimo_tercer')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevo_rol.acumula_decimo_tercer')}}"></i>{{trans('tsidebar.decimo_tercer')}}</a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 20)))
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nuevo_rol.acumula_decimo_cuarto')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.decimo_cuarto')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevo_rol.acumula_decimo_cuarto')}}"></i>{{trans('tsidebar.decimo_cuarto')}}</a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1,20)))
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('nuevo_rol.acumula_valor_vacacion')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.calculo_vacacion')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevo_rol.acumula_valor_vacacion')}}"></i>{{trans('tsidebar.calculo_vacacion')}}</a>
        </li>
        @endif
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('otros_anticipos_empleado.index')}}`, '{{trans('tsidebar.nomina')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.otros_anticipos_empleados')}} ')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('otros_anticipos_empleado.index') }}"></i>Otros Anticipos Empleados</a>
        </li>
        <!-- <li><a href="{{ route('plantillas_nomina.plantillas_prestamos') }}"></i>Plantilla Prestamos</a></li>
                      <li><a href="{{ route('plantillas_nomina.horas_extras') }}"></i>Plantilla Horas Extras</a></li> -->
        <!--li><a href="{{ route('prestamos_empleados.index_cruce') }}"></i>Prestamos vs Utilidades</a></li-->
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <!--<li><a href="{{ route('nomina_anticipos.index') }}"><i class="fa fa-file-excel-o"> </i>Anticipo Quincena</a></li>-->
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('reportes_empl.index')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.datos_empleados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('reportes_empl.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.datos_empleados')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('reportes_rol.index')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.rol_pagos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('reportes_rol.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} (TTHH)</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('reportes_rolcontable.index')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.rol_pagos')}} ({{trans('tsidebar.contabilidad')}})')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('reportes_rolcontable.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} ({{trans('tsidebar.contabilidad')}})</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('reportes_banco.index')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.archivo_banco')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('reportes_banco.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.archivo_banco')}}</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('prestamos_empleados.prestamos_saldos')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.saldo_prestamos')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('prestamos_empleados.prestamos_saldos') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.saldo_prestamos')}}</a>
          </li>
          <!--<li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('recursos_humanos.Calendario.descargar_vacaciones')}}`, '{{trans('tsidebar.informes')}}', '{{trans('tsidebar.modulo')}} {{trans('tsidebar.ReportedeVacacionesdeEmpleados')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('recursos_humanos.Calendario.descargar_vacaciones') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.ReportedeVacacionesdeEmpleados')}} </a>
          </li>-->
      </li>
    </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-wrench"> </i>{{trans('tsidebar.mantenimiento')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('area_rh.index') }}`, 'Mantenimientos', 'Mantenimientos de Área')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('area_rh.index') }}"><i class="fa fa-wrench"> </i> Área </a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('estado_civil.index') }}`, 'Mantenimientos', 'Mantenimientos Estado civil')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('estado_civil.index') }}"><i class="fa fa-wrench"> </i> Estado Civil </a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('mantenimiento.horario.index') }}`, 'Mantenimientos', 'Mantenimientos Horario')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('mantenimiento.horario.index') }}"><i class="fa fa-wrench"> </i> Horario </a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('nivel_academico.index') }}`, 'Mantenimientos', 'Mantenimientos Nivel Académico')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('nivel_academico.index') }}"><i class="fa fa-wrench"> </i>Nivel Académico</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('pagobeneficio.index') }}`, 'Mantenimientos', 'Mantenimientos Pago Benefico')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('pagobeneficio.index') }}"><i class="fa fa-wrench"> </i>Pago Benefico</a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('tipo_aporte.index') }}`, 'Mantenimientos', 'Tipo Aporte')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('tipo_aporte.index') }}"><i class="fa fa-wrench"> </i> Tipo Aporte </a>
          </li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('tipo_rol.index') }}`, 'Mantenimientos', 'Tipo Rol')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('tipo_rol.index') }}"><i class="fa fa-wrench"> </i> Tipo Rol </a>
          </li>

      </li>
    </ul>
    </li>
    </ul>

    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-line-chart"> </i>Flujos de Caja
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('flujoefectivo.index') }}`, 'Flujo Caja', 'Flujo efectivo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('flujoefectivo.index') }}"></i>Flujo Efectivo</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('flujoefectivocomparativo.index') }}`, 'Flujo Caja', 'Flujo efectivo comparativo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('flujoefectivocomparativo.index') }}"></i>Flujo Efectivo Comparativo</a>
        </li>
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('estructuraflujoefectivo.index') }}`, 'Flujo Caja', 'Estructura de flujo de efectivo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('estructuraflujoefectivo.index') }}"></i>Estructura Flujo Efectivo</a>
        </li>
    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>Ventas
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>

      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 Factura de Venta
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
              @if($empresa->inventario == 1 and $empresa->egreso_kardex == 1)
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.proformaInsumos.index')}}`, 'Proforma Ventas', 'Proforma para facturas de ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{route('insumos.proformaInsumos.index')}}"></i>Proforma de ventas</a>
            </li>
            @endif
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('venta_index')}}`, 'Ventas', 'Modulo de facturas de ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('venta_index') }}"></i>{{trans('tsidebar.factura_venta_manual')}}</a>
            </li>

            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('entregas_mercaderia.index') }}`, 'Entregas de mercadería', 'Salida de inventario, guía SRI y facturación posterior')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('entregas_mercaderia.index') }}"><i class="fa fa-truck"></i> Entregas de mercadería</a>
            </li>


            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('orden_venta')}}`, 'Ventas', 'Ordenes de Venta')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('orden_venta') }}"></i>Ordenes de Venta</a>
            </li>


            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('venta_index')}}`, 'Ventas', 'Modulo de facturas de ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('ventas.index_cierre') }}"></i>{{trans('tsidebar.factura_caja')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('ventas.index_cierre')}}`, 'Ventas', 'Modulo de facturas de caja')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('ventas.index_cierre') }}"></i>Factura de Caja RC</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('venta.index_recibo')}}`, '{{trans('tsidebar.ventas')}}', '{{trans('tsidebar.modulo')}} de recibo de cobro')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('venta.index_recibo') }}"></i>{{trans('tsidebar.factura_recibo_cobro')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('factura_convenios.index')}}`, '{{trans('tsidebar.ventas')}}', '{{trans('tsidebar.modulo')}} de convenios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('factura_convenios.index') }}"></i>{{trans('tsidebar.factura_convenios')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('venta_index2')}}`, '{{trans('tsidebar.ventas')}}', '{{trans('tsidebar.modulo')}} conglomerado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('venta_index2') }}"></i>{{trans('tsidebar.factura_conglomerada')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('ventas.omni')}}`, '{{trans('tsidebar.ventas')}}', '{{trans('tsidebar.modulo')}} hospitalizado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('ventas.omni') }}"></i>{{trans('tsidebar.factura_hospitalizados')}}</a>
            </li>
        </li>
      </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a></i>Liq de Comision</a></li>
          <li><a href=""></i>{{trans('tsidebar.liquidacion_comision')}}</a></li>
          <li><a href=""></i>{{trans('tsidebar.liquidacion_honorarios_medicos')}}</a></li>
          <li class="container-clip">
            <button button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('orden_venta')}}`, '{{trans('tsidebar.ventas')}}', 'Modulo Ordenes Venta')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('orden_venta') }}"></i>{{trans('tsidebar.ordenes_venta')}}</a>
          </li>
      </li>
    </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('venta.informe_ventas')}}`, '{{trans('tsidebar.ventas')}}', 'Modulo informe ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('venta.informe_ventas') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ventas')}}</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('venta.informe_ventas_mensual') }}`, 'Ventas', 'Informe Ventas Mensual')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('venta.informe_ventas_mensual') }}"><i class="fa fa-file-excel-o"></i>Informe Ventas Mensual</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('venta.informe_nca')}}`, 'Ventas', 'Modulo informe ventas netas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('venta.informe_nca') }}"><i class="fa fa-file-excel-o"></i>Informe de Ventas Netas</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('venta.informe_ordenes_pendientes') }}`, 'Ventas', 'Informe de Ordenes Pendientes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('venta.informe_ordenes_pendientes') }}"><i class="fa fa-file-excel-o"></i>Informe de Ordenes Pendientes</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{route('venta.informe_liquidaciones_comisiones')}}`, 'Ventas', 'Modulo informe de liquidacion comisiones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href=""><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_liquidacion_honorarios')}}</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{url('/ventas/informeNeto')}}`, 'Ventas', 'Informe de facturas netas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{url('/ventas/informeNeto')}}"><i class="fa fa-file-excel-o"></i>Informe de facturas netas</a>
          </li>
          <li><a href=""><i class="fa fa-file-excel-o"></i>Informe de Liq Honorarios</a></li>
          <li><a href="{{route('ventas.reporte_varios.ventasUtilidad')}}"><i class="fa fa-file-excel-o"></i>Informe Costo-Venta-Utilidad</a></li>
      </li>
    </ul>
    </li>
    </ul>
    </li>


    @if(in_array($rolUsuario, array(1,20)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa fa-files-o" aria-hidden="true"></i>Declaracion de Impuesto
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">

        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('compra.declaracionTributacion.index') }}`, 'Declaracion de Impuesto', `Reporte de Compras-Ventas y retencion para declaración de impuesto(Tributacion)`)" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('compra.declaracionTributacion.index') }}">Compras-Ventas y Retención</a>
        </li>

    </li>
    </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1,20)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>{{trans('tsidebar.importaciones')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('importaciones.index')}}`, 'Importación', 'Modulo de Importaciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('importaciones.index') }}">{{trans('tsidebar.importaciones')}}</a>
        </li>

        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('gastosimportacion.index')}}`, 'Importación', 'Mantenimiento Gastos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('gastosimportacion.index') }}">{{trans('tsidebar.mantenimiento_gastos')}}</a>
        </li>

        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('importaciones.index_preorden')}}`, 'Importación', 'Ordenes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('importaciones.index_preorden') }}">{{trans('tsidebar.ordenes')}}</a>
        </li>
    </li>
    </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1,20,21,7)) == true)
    <?php
    $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
    if ($deEmpresa != '') {
    ?>
      <li class="treeview">
        <a href="#"> <i class="fa fa-truck" aria-hidden="true"></i>Guia de Remisión
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('guia_remision_index') }}`, 'Guia de remisión', 'Guia de remisión')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('guia_remision_index') }}">Guia de remisión</a>
          </li>
          <li class="container-clip">
            <button style="background: transparent; border: none;">
              <i onclick="anclarTarget(this, `{{ route('guia_remision_index') }}`, 'Guia de remisión', 'Transportistas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
            </button>
            <a href="{{ route('transportistas.index') }}">Transportistas</a>
          </li>
      </li>
      </ul>
      </li>
    <?php
    }
    ?>
    @endif

    <li class="treeview">
      <a href="#"> <i class="fa fa-cogs"> </i>{{trans('tsidebar.mantenimiento')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('establecimiento.index')}}`, 'Mantenimiento', 'Establecimientos sucursales')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('establecimiento.index') }}"></i>{{trans('tsidebar.sucursales')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('punto_emision.index')}}`, 'Mantenimiento', 'Modulo Puntos de emision')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('punto_emision.index') }}"></i>{{trans('tsidebar.punto_emision')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('empleados.index')}}`, 'Mantenimiento', 'Modulo de asignar recaudacion')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('empleados.index') }}"></i>{{trans('tsidebar.asignar_recaudación')}} P-E</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('divisas.index')}}`, 'Mantenimiento', 'Modulo de divisas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('divisas.index')}}"></i>{{trans('tsidebar.divisas')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('tipo_pago.index')}}`, 'Mantenimiento', 'Modulo tipo de pagos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('tipo_pago.index') }}"></i>{{trans('tsidebar.tipo_pago')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('tipo_tarjeta.index')}}`, 'Mantenimiento', 'Modulo tipo de tarjeta')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('tipo_tarjeta.index') }}"></i>{{trans('tsidebar.tipo_tarjeta')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('bodegas.index')}}`, 'Mantenimiento', 'Modulo de bodegas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('bodegas.index') }}"></i>{{trans('tsidebar.bodegas')}}</a>
        </li>
        <!--<li><a href="#"></i>Bancos</a></li>
                      <li><a href="#"></i>Ciudad</a></li>-->
        <!--<li><a href="{{ route('caja_banco.index') }}"></i>Cajas y Bancos</a></li>-->
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('tipo_emision.index')}}`, 'Mantenimiento', 'Modulo tipo de emision')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('tipo_emision.index') }}"></i>{{trans('tsidebar.tipo_emision')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('tipo_comprobante.index')}}`, 'Mantenimiento', 'Modulo tipo de comprobante')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('tipo_comprobante.index') }}"></i>{{trans('tsidebar.tipo_comprobante')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('tipo_ambiente.index')}}`, 'Mantenimiento', 'Modulo tipo ambiente')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('tipo_ambiente.index') }}"></i>{{trans('tsidebar.tipo_ambiente')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('porcentaje_imp_renta.index')}}`, 'Mantenimiento', 'Modulo referencia de impuestos ')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('porcentaje_imp_renta.index') }}"></i>{{trans('tsidebar.retencion_impuesto_renta')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('Porcentaje.index')}}`, 'Mantenimiento', 'Modulo pago de impuestos ')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('Porcentaje.index') }}"></i>{{trans('tsidebar.pago_impuesto_renta')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('retenciones.index')}}`, 'Mantenimiento', 'Modulo de retenciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('retenciones.index') }}"></i>{{trans('tsidebar.retenciones')}}</a>
        </li>
    </li>
    </ul>
    </li>
    
    <li class="treeview">
      <a href="#"> <i class="fa fa-cog"> </i>{{trans('tsidebar.configuraciones')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <!-- <li><a href="{{ route('configuraciones.index') }}"></i>Plan de Cuenta</a></li> -->
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('productos.productos_tarifario')}}`, 'Configuraciones', 'Modulo producto tarifado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('productos.productos_tarifario') }}"></i>{{trans('tsidebar.producto_tarifario')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ctglobales.index')}}`, 'Configuraciones', 'Modulo de configuraciones globales')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('ctglobales.index')}}">{{trans('tsidebar.configuracionesglobales')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('configuraciones_pdf_index')}}`, 'Configuraciones', 'Modulo de configuraciones pdf')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('configuraciones_pdf_index') }}"></i>{{trans('tsidebar.configuracionespdf')}}</a>
        </li>
    </li>
    </ul>
    </li>


    </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1,22,26,20)) == true)
    <li class="treeview @if(strrpos($sidebar, 'financiero')) active @endif">
      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.financiero')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,22,26)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('balance_general.index')}}`, 'Financiero', 'Modulo balance general')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a>
        </li>
        <li class="@if(strrpos($sidebar, 'estado/situacion/consolidado')) active @endif container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.estadosituacion')}}`, 'Financiero', 'Modulo situacion consolidada')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.estadosituacion') }}"></i>{{trans('tsidebar.estado_situacion_consolidado')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('estadoresultados.index')}}`, 'Financiero', 'Modulo estado de resultado integral')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a>
        </li>
        <li class="@if(strrpos($sidebar, 'estado/resultados/consolidado')) active @endif container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.estadoresultados')}}`, 'Financiero', 'Modulo estado de resultado consolidado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.estadoresultados') }}"></i>{{trans('tsidebar.estado_resultado_consolidado')}}</a>
        </li>
        <li class="@if(strrpos($sidebar, 'indicador/consolidado')) active @endif container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.indicadorconsolidado')}}`, 'Financiero', 'Modulo indicador financiero consolidado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.indicadorconsolidado') }}"></i>{{trans('tsidebar.indicador_financiero_consolidado')}}</a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1,22,26,20)) == true)
        <li class="@if(strrpos($sidebar, 'indice/financiero')) active @endif container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.indicefinanciero_index')}}`, 'Financiero', 'Modulo indice finaciero')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.indicefinanciero_index') }}"></i>{{trans('tsidebar.indice_financiero')}}</a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1,22,26)) == true)
        <li class="@if(strrpos($sidebar, 'proyeccion/financiera')) active @endif container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.proyeccionfinanciera')}}`, 'Financiero', 'Modulo proyeccion financiera')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.proyeccionfinanciera') }}"></i>{{trans('tsidebar.proyeccion_financiera')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.proyeccionfinanciera2_index')}}`, 'Financiero', 'Modulo proyeccion financiera II')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.proyeccionfinanciera2_index') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} II</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('financiero.proyeccionfinanciera3')}}`, 'Financiero', 'Modulo proyeccion financiera III')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('financiero.proyeccionfinanciera3') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} III</a>
        </li>
        @endif
      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1, 11, 22)))

    <li class="treeview">
      <a href="#"><i class="fa fa-cubes"></i> <span>{{trans('tsidebar.liquidacion')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('planilla.genera_planillas')}}`, 'Liquidación', 'Modulo de ingreso plantillas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('planilla.genera_planillas') }}">{{trans('tsidebar.ingresar_Planillas')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('estaditicos_plano.index')}}`, 'Liquidación', 'Modulo de estadisticas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('estaditicos_plano.index') }}"><i class="fa fa-pie-chart"> </i> {{trans('tsidebar.estadisticos')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('formatosProductos.index')}}`, 'Liquidación', 'Modulo de formato producto')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('formatosProductos.index') }}">{{trans('tsidebar.formato_Producto')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('planilla.planillas_generadas')}}`, 'Liquidación', 'Modulo de consulta general de planillas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('planilla.planillas_generadas') }}">{{trans('tsidebar.consulta_General_de_Planilla')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('planilla.reportes')}}`, 'Liquidación', 'Modulo de generacion de reportes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('planilla.reportes') }}">{{trans('tsidebar.generacion_de_Reportes')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('planilla.genera_ap')}}`, 'Liquidación', 'Modulo para generar archivo plano IESS')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('planilla.genera_ap') }}">{{trans('tsidebar.generar_Archivo_Plano_IESS')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('genera_ap_msp.planilla')}}`, 'Liquidación', 'Modulo para generar archivo plano MSP')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('genera_ap_msp.planilla') }}">{{trans('tsidebar.generar_Archivo_Plano_MSP')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('aparchivo.resumen_planillas')}}`, 'Liquidación', 'Modulo para Consultar Resumen')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('aparchivo.resumen_planillas') }}">{{trans('tsidebar.resumen_liquidacion')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ url('administracion/procedimientos') }}`, 'Liquidación', 'Modulo mantenimientos items')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ url('administracion/procedimientos') }}">{{trans('tsidebar.mantenimiento_Items')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ url('administracion/plantillas') }}`, 'Liquidación', 'Modulo de mantenimiento plantilla')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ url('administracion/plantillas') }}">{{trans('tsidebar.mantenimiento_Plantillas')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ url('medicina_publica/index') }}`, 'Liquidación', 'Modulo de mantenimiento medicina')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ url('medicina_publica/index') }}">{{trans('tsidebar.mantenimiento_medicina_publica')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('aparchivo.total_agrupado_contable') }}`, 'Liquidación', 'Modulo de Prefactura Pública')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('aparchivo.total_agrupado_contable') }}"> Prefactura Pública </a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('nuevoArchivoPlano.index_archivo_factura') }}`, 'Liquidación', 'Modulo de Factura Mes Plano')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('nuevoArchivoPlano.index_archivo_factura') }}">{{trans('tsidebar.cargar_factura_mes_plano')}}</a>
        </li>
      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
    <li class="container-clip">
      <button style="background: transparent; border: none;">
        <i onclick="anclarTarget(this, `{{route('reporte.index_cierre')}}`, 'Cierre de Caja', 'Cierre de Caja')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
      </button>
      <a href="{{ route('reporte.index_cierre') }}"><i class="fa fa-money"></i> <span>Cierre de Caja</span></a>
    </li>

    @endif

    @if(in_array($rolUsuario, array(1,20)) == true)
    @if($id_empresa == '0993075000001')
    <li class="container-clip">
      <button style="background: transparent; border: none;">
        <i onclick="anclarTarget(this, `{{ route('c_caja.index') }}`, 'Cuentas y Finanzas', 'Cierre de Caja Laboratorio')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
      </button>
      <a href="{{ route('c_caja.index') }}"><i class="fa fa-money"></i> <span>Cierre de Caja Laboratorio</span></a>
    </li>
    @endif
    @endif

    @if(in_array($rolUsuario, array(7)) == true)
    <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">
      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.contable')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
          <a href="#"> <i class="fa fa-building"> </i>{{trans('tsidebar.empresa')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('plan_cuentas.seleccion_empresa')}}`, 'Empresa', 'modulo seleccion de empresa')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>{{trans('tsidebar.seleccion_empresa')}}</a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(7)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>Importaciones
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('importaciones.index')}}`, 'Importación', 'Modulo de Importaciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('importaciones.index') }}"></i>Importaciones</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('gastosimportacion.index')}}`, 'Importación', 'Mantenimiento Gastos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('gastosimportacion.index') }}">Mantenimiento Gastos</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('importaciones.index_preorden')}}`, 'Importación', 'Ordenes')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('importaciones.index_preorden') }}">Ordenes</a>
        </li>
      </ul>
    </li>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-truck" aria-hidden="true"></i>Guia de Remisión
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('guia_remision_index') }}`, 'Guia de remisión', 'Guia de remisión')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('guia_remision_index') }}">Guia de remisión</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('guia_remision_index') }}`, 'Guia de remisión', 'Transportistas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('transportistas.index') }}">Transportistas</a>
        </li>
    </li>
    </ul>
    </li>

    @endif

    @if(in_array($rolUsuario, array(26)) == true)
    <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">
      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.contable')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
          <a href="#"> <i class="fa fa-building"> </i>{{trans('tsidebar.empresa')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('plan_cuentas.seleccion_empresa')}}`, 'Empresa', 'modulo seleccion de empresa')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>{{trans('tsidebar.seleccion_empresa')}}</a>
            </li>
          </ul>
        <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
          <a href="#"> <i class="fa fa-university"> </i>{{trans('tsidebar.contabilidad')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('plan_cuentas.index')}}`, 'Contabilidad', 'modulo plan de cuentas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('plan_cuentas.index') }}"></i>{{trans('tsidebar.plan_cuentas')}}</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('libro_mayor.index')}}`, 'Contabilidad', 'modulo libro mayor')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('libro_mayor.index') }}"></i>{{trans('tsidebar.libro_mayor')}}</a>
            </li>
            <?php
            $subpermiso = DB::table('roles_usuario')->where('id_usuario', Auth::user()->id)->first();
            if ($subpermiso != '' && $subpermiso->tipo_usuario != '') {
              if (in_array(21, json_decode($subpermiso->tipo_usuario)) == true) {
            ?>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif container-clip">
                  <button button style="background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('estadoresultados.index')}}`, 'Contabilidad', 'modulo de balance general')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                  </button>
                  <a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif container-clip">
                  <button button style="background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('estadoresultados.index')}}`, 'Contabilidad', 'modulo de estado resultado integral')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                  </button>
                  <a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif container-clip">
                  <button button style="background: transparent; border: none;">
                    <i onclick="anclarTarget(this, `{{route('balance_comprobacion.index')}}`, 'Contabilidad', 'modulo de balance comprobacion')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                  </button>
                  <a href="{{ route('balance_comprobacion.index') }}"></i>{{trans('tsidebar.balance_comprobacion')}}</a>
                </li>
            <?php
              }
            }
            ?>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('librodiario.index')}}`, 'Contabilidad', 'Visualizacion de asientos diarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('librodiario.index') }}"></i>{{trans('tsidebar.visualizar_asientos_diarios')}}</a>
            </li>
            {{-- <li><a href="{{ route('compras.informe') }}"></i>{{trans('tsidebar.informe_compras')}}</a>
        </li> --}}
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('flujoefectivo.index')}}`, 'Contabilidad', 'modulo de flujo efectivo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>>
          <a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a>
        </li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('flujoefectivocomparativo.index')}}`, 'Contabilidad', 'modulo flujo efectivo comparativo I')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('flujoefectivocomparativo.index') }}"></i> Flujo Efectivo Comparativo </a>
        </li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif  container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('flujoefectivocomparativo.index2')}}`, 'Contabilidad', 'modulo flujo efectivo comparativo II')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('flujoefectivocomparativo.index2') }}"></i> Flujo Efectivo Comparativo II</a>
        </li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ats.index')}}`, 'Contabilidad', 'modulo SRI ATS')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('ats.index') }}"></i>SRI / ATS</a>
        </li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('librodiario.descuadrados')}}`, 'Contabilidad', 'modulo descuadros')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('librodiario.descuadrados') }}"></i>Descuadrados</a>
        </li>
      </ul>
    </li>

    @endif

    </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'recursos')) active @endif ">
      <a href="#"><i class="fa fa-users"></i><span>{{trans('tsidebar.recursosHumanos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 20)) == true)
        <li class="treeview">
          <a href="#"> <i class="fa fa-file"> </i>{{trans('tsidebar.permisos_Laborales')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('ticketpermisos.index')}}`, 'Permisos Laborales', 'Modulo solicitud de permiso')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{route('ticketpermisos.index')}}"><i class="fa fa-ticket" aria-hidden="true"></i>{{trans('tsidebar.solicitud_de_Permiso')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('ticketpermisos.index_usuario')}}`, 'Permisos Laborales', 'Modulo permisos por usuarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{route('ticketpermisos.index_usuario')}}"><i class="fa fa-ticket" aria-hidden="true"></i>{{trans('tsidebar.permisosporusuarios')}}</a>
            </li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(2, 30)) == false)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ticketpermisos.index_usuario')}}`, 'Permisos Laborales', 'Modulo permisos por usuarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('ticketpermisos.index_usuario')}}"><i class="fa fa-file"></i><span>{{trans('tsidebar.solicitud_de_Permiso')}}</span></a>
        </li>
        @php
        $archivo = null;
        $archivo = DB::table('permiso_sistemas')
        ->where('tipo', 1)
        ->where('id_usuario', Auth::user()->id)
        ->first();
        @endphp
        @if(!is_null($archivo))
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('recursos_humanos.Calendario.index_mantenimiento_vacaciones')}}`, 'Permisos Laborales', 'Modulo permisos usuarios II')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('recursos_humanos.Calendario.index_mantenimiento_vacaciones')}}"><i class="fa fa-file"></i><span> Mantenimiento Vacaciones</span></a>
        </li>
        @endif
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('recursos_humanos.Calendario.index_calendario_vacaciones')}}`, 'Permisos Laborales', 'Modulo Calendario de Vacaciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('recursos_humanos.Calendario.index_calendario_vacaciones')}}"><i class="fa fa-calendar"></i><span> Calendario Vacaciones </span></a>
        </li>
        @endif
        @if($id_auth == '0921504197')
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ticketpermisos.index')}}`, 'Permisos Laborales', 'Administración de Permisos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('ticketpermisos.index')}}"><i class="fa fa-file"></i><span>{{trans('tsidebar.admindepermisos')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(2, 30)) == false)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('historial.rol')}}`, 'Permisos Laborales', 'Modulo roles de pago')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('historial.rol') }}"><i class="fa fa-file-text"></i><span>{{trans('tsidebar.tus_roles_pago')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(2, 30)) == false)
        <li class="treeview  container-clip">

          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('trabajo_campo_index')}}`, 'Permisos Laborales', 'Trabajo de campo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>

          <a href="{{route('trabajo_campo_index')}}"><i class="fa fa-file"></i><span>Trabajo de campo</span></a>
        </li>
        @endif

      </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'reportes')) active @endif ">
      <a href="#"><i class="fa fa-pie-chart"></i><span>{{trans('tsidebar.reportes')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,4,5,11, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>{{trans('tsidebar.reportes')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('agenda.reportediario')}}`, 'Reportes', 'Modulo de agendamiento diario ')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('agenda.reportediario') }}">{{trans('tsidebar.agendamiento_Diario')}}</a>
            </li>
            <!--reporte agenda-->
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('pentax.reporteagenda')}}`, 'Reportes', 'Modulo de procediminetos endoscopios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('pentax.reporteagenda') }}">{{trans('tsidebar.procedimientos_endoscopicos')}}</a>
            </li>
            <!--reporte drH  CAMBIOS 08052018-->
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('consultam.reporteagenda')}}`, 'Reportes', 'Modulo otros procedimientos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('consultam.reporteagenda') }}">{{trans('tsidebar.otros_procedimientos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('consultam.reporteagenda2')}}`, 'Reportes', 'Modulo de preocedimientos por doctor')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('consultam.reporteagenda2') }}">{{trans('tsidebar.procedimientos_doctor')}}</a>
            </li>
            <!--reporte Hospitalizados-->
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('hospitalizados.reporte')}}`, 'Reportes', 'Modulo de hospitalizado')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('hospitalizados.reporte') }}">{{trans('tsidebar.hospitalizados')}}</a>
            </li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 7, 20, 10, 5)) == true) <!-- Reportes insumos-->
        <li class="treeview">
          <a href="#"><i class="fa fa-file"></i> <span>{{trans('tsidebar.insumos_reportes')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if($empresa->inventario == 1 and $empresa->egreso_kardex == 1)
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.proformaInsumos.index')}}`, 'Proforma Ventas', 'Proforma para facturas de ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{route('insumos.proformaInsumos.index')}}"></i>Proforma de ventas</a>
            </li>
            @endif
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.buscador_master')}}`, 'Insumos Reportes', 'Buscador Master')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.buscador_master') }}">{{trans('tsidebar.master')}}</a>
            </li>
            <!-- <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.reporte_bodega')}}`, 'Insumos Reportes', 'Productos en Bodega')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.reporte_bodega') }}">{{trans('tsidebar.productos_en_Bodega')}}</a>
            </li> 
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.reporte_caducado')}}`, 'Insumos Reportes', 'Productos Caducados')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.reporte_caducado') }}">{{trans('tsidebar.productos_Caducados')}}</a>
            </li>-->
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.buscador_usos')}}`, 'Insumos Reportes', 'Uso de Productos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.buscador_usos') }}">{{trans('tsidebar.uso_de_Productos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.buscador_usos_equipo')}}`, 'Insumos Reportes', 'Uso de Equipos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.buscador_usos_equipo') }}">{{trans('tsidebar.uso_de_Equipos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.kardex.index')}}`, 'Insumos Reportes', 'Kardex')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('insumos.kardex.index') }}">{{trans('tsidebar.kardex')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.inventario.index')}}`, 'Insumos Reportes', 'Existencias')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('insumos.inventario.index') }}">{{trans('tsidebar.existencias')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.inventario_serie.index')}}`, 'Insumos Reportes', 'Existencias Serie')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('insumos.inventario_serie.index') }}">{{trans('tsidebar.existencias_Serie')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('PedidoController.pedidos_insumos')}}`, 'Insumos Reportes', 'Pedidos por Proveedor')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('PedidoController.pedidos_insumos') }}">Pedidos por Proveedor</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('insumos.reportes.index_stock_minimo')}}`, 'Insumo Reportes', 'modulo stock minimo')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('insumos.reportes.index_stock_minimo') }}"> Stock Minimo </a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('reporte.reporteProdCaducados')}}`, 'Insumos Reportes', 'Productos Caducados')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('reporte.reporteProdCaducados') }}">Productos Caducados</a>
            </li>
            <!--li><a href="{{ route('insumos.inventario.busqueda') }}">Busqueda de Item</a></li-->
            <!-- <li><a href="{{ route('insumos.inventario.egresoprocedimiento') }}">Materiales Utilizados</a></li> -->
          </ul>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1,20, 22,14)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('venta.nuevo_estadistico')}}`, 'Reportes', 'Modulo estadisticas facturas venta')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          {{-- <a href="{{ route('venta.estadisticos') }}" ><i class="fa fa-pie-chart"> </i><span> {{trans('tsidebar.estadisticos_facturas_venta')}} </span></a> --}}
          <a href="{{ route('venta.nuevo_estadistico') }}"><i class="fa fa-pie-chart"> </i><span> {{trans('tsidebar.estadisticos_facturas_venta')}} </span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1,17,7, 20)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('clientes.deudas_vencidas.index_vendedor')}}`, 'Reportes', 'Cuentas por Cobrar Vendedor')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('clientes.deudas_vencidas.index_vendedor') }}"><i class="fa fa-file"> </i>&nbsp;<span> Cuentas por Cobrar Vendedor </span></a>
        </li>

        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ventas.indexFcVendedor')}}`, 'Reportes', 'Reporte FC por Vendedor')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('ventas.indexFcVendedor') }}"><i class="fa fa-file"> </i>&nbsp;<span> Reporte FC por Vendedor </span></a>
        </li>
        @endif


        @if(in_array($rolUsuario, array(1, 22)) == true )
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('ap_estadisticos.honorarios')}}`, 'Reportes', 'Modulo consolidado honorarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('ap_estadisticos.honorarios') }}"><i class="fa fa-list-ol"></i><span>{{trans('tsidebar.consolidado_honorarios')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 20, 22)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('facturalabs.reporte_anual')}}`, 'Reportes', 'Modulo reporte anual de laboratorio')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('facturalabs.reporte_anual') }}"><i class="fa fa-calendar"></i><span>{{trans('tsidebar.reporte_anual_laboratorio')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('estaditicos_plano.orden')}}`, 'Reportes', 'Modulo estadistico recibo cobro')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('estaditicos_plano.orden') }}"><i class="fa fa-pie-chart"></i> <span>{{trans('tsidebar.estadisticos_recibo_cobro')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('consultas.index_rfellows')}}`, 'Reportes', 'Modulo Reporte Fellows')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('consultas.index_rfellows') }}"><i class="fa fa-book"></i><span>{{trans('tsidebar.reporte_Fellows')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('reporte.certificacion.index')}}`, 'Reportes', 'Modulo reporte de laboratorio certificacion')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('reporte.certificacion.index') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.reporte_certificacion')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 7, 17)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('complaint.index')}}`, 'Reportes', 'complaint')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('complaint.index') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Reporte Complaint </span></a>
        </li>
        @endif

        @php
        $control = UsuariosControl::where('estado',1)->get();
        @endphp
        @foreach($control as $val)
        @if($val->user == $id_auth)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('index_control')}}`, 'Insumo Reportes', 'Modulo control sintomas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('index_control')}}"><i class="fa fa-user-md"></i><span>{{trans('tsidebar.control_sintomas')}}</span></a>
        </li>
        @endif
        @endforeach


      </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'insumos')) active @endif ">
      <a href="#"><i class="fa fa-list-alt"></i><span>{{trans('tsidebar.suministro')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">

        @if(in_array($rolUsuario, array(17,7,1)) == true )
        <li class="treeview container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.proformaInsumos.index')}}`, 'Proforma Ventas', 'Proforma para facturas de ventas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('insumos.proformaInsumos.index')}}"></i>Proforma de ventas</a>
        </li>
        <li class="treeview container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('importaciones.new_orden_index')}}`, 'Ordenes Importaciones', 'Ordenes de Importaciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{route('importaciones.new_orden_index')}}"></i>Ordenes de Importaciones</a>
        </li>

        <!-- <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.inventario.index')}}`, 'Insumos Reportes', 'Existencias')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('insumos.inventario.index') }}">{{trans('tsidebar.existencias')}}</a>
        </li>
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('insumos.inventario_serie.index')}}`, 'Insumos Reportes', 'Existencias Serie')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('insumos.inventario_serie.index') }}">{{trans('tsidebar.existencias_Serie')}}</a>
        </li> -->
        @endif
        @if(in_array($rolUsuario, array(1, 7, 20, 10)) == true)
        <li class="treeview">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.insumos')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('proveedor.index') }}`, 'Insumos', 'Proveedores')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('proveedor.index') }}">{{trans('tsidebar.proveedores')}}</a>
            </li>
            @if(in_array($rolUsuario, array(1, 7)) == true)
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('ordenes_compra.index') }}`, 'Insumos', 'orden Compra')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('ordenes_compra.index') }}">Orden de Compra</a>
            </li>
            @endif
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('productos.comparar.index') }}`, 'Insumos', '{{trans('tsidebar.comparativo')}}')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('productos.comparar.index') }}">{{trans('tsidebar.comparativo')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('bodega') }}`, 'Insumos', 'Bodega')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('bodega') }}">{{trans('tsidebar.bodegas')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('producto.index') }}`, 'Insumos', 'Productos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('producto.index') }}">{{trans('tsidebar.productos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('marca.index') }}`, 'Insumos', 'Marcas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('marca.index') }}">{{trans('tsidebar.marcas')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('tipo.index') }}`, 'Insumos', 'Tipos de Productos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('tipo.index') }}">{{trans('tsidebar.tipos_de_Productos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('transito.index_transito') }}`, 'Insumos', 'Productos en Transito')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('transito.index_transito') }}">{{trans('tsidebar.productos_en_Transito')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('codigo.barra') }}`, 'Insumos', 'Pedidos realizados')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('codigo.barra') }}">{{trans('tsidebar.pedidos_Realizados')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('inventario.ingresos.egresos.varios') }}`, 'Insumos', 'Ingresos-Egresos Varios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('inventario.ingresos.egresos.varios') }}">{{trans('tsidebar.ingresos_Egresos_Varios')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('equipo.index') }}`, 'Insumos', 'Equipos Medicos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('equipo.index') }}">{{trans('tsidebar.equipos_Medicos')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('plantilla.index') }}`, 'Insumos', 'Procedimientos de enfermería')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('plantilla.index') }}">{{trans('tsidebar.plantillas_Procedimientos_Enfermeria')}}</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('plantilla_procedimiento.index') }}`, 'Insumos', 'Procedimientos de control')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('plantilla_procedimiento.index') }}">{{trans('tsidebar.plantillas_Procedimientos_Control')}}</a>
            </li>
            <li class="container-clip">
              <button button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{route('librodiario.index')}}`, 'Insumos', 'Visualización de asientos diarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('librodiario.index') }}"></i>Visualizar Asientos Diario</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('insumos.cargaIncial') }}`, 'Insumos', 'carga inicial')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('insumos.cargaIncial') }}">Carga Inicial Producto</a>
            </li>
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('modulobodega.index') }}`, 'Insumos', 'Modulos Bodega')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('modulobodega.index') }}">Modulos Bodega</a>
            </li>
          </ul>
        </li>
        @endif
      </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'configuraciones')) active @endif ">
      <a href="#"><i class="fa fa-cog"></i><span>{{trans('tsidebar.configuraciones')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 4)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>{{trans('tsidebar.administracion_sistemas')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('especialidad ') }}`, 'Administracion de Sistema', 'Especialidades')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('especialidad') }}"><i class="fa fa-fw fa-briefcase"></i> <span>{{trans('tsidebar.especialidades')}}</span></a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('hospital-management.index') }}`, 'Administracion de Sistema', 'Modulo Ubicaciones')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> <span>{{trans('tsidebar.ubicaciones')}}</span></a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('procedimiento.index') }}`, 'Administracion de Sistema', 'Modulo procedimientos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> <span>{{trans('tsidebar.procedimientos')}}</span></a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('form_enviar_seguro') }}`, 'Administracion de Sistema', 'Modulo seguros')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('form_enviar_seguro') }}"><i class="fa fa-fw fa-medkit"></i> <span>{{trans('tsidebar.seguros')}}</span></a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('empresa') }}`, 'Administracion de Sistema', 'Modulo empresas')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('empresa') }}">{{trans('tsidebar.empresas')}}</a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('tecnicas') }}`, 'Administracion de Sistema', 'Modulo procedimientos completos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('tecnicas') }}">{{trans('tsidebar.proccompletos')}}</a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ route('tipo_usuario-management.index') }}`, 'Administracion de Sistema', 'Modulo tipo de usuarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ route('tipo_usuario-management.index') }}"><span>{{trans('tsidebar.tipousuario')}}</span></a>
            </li>
          </ul>


          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('cie_10_3') }}`, 'Administracion de Sistema', 'Modulo cie_10_3')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('cie_10_3') }}">Cie 10 3</a>
            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="container-clip">
              <button style="background: transparent; border: none;">
                <i onclick="anclarTarget(this, `{{ url('cie_10_4') }}`, 'Administracion de Sistema', 'Modulo Cie 10 4')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
              </button>
              <a href="{{ url('cie_10_4') }}">Cie 10 4</a>
            </li>
          </ul>
          @if(in_array($rolUsuario, array(1)) == true)
          @endif
        </li>

        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        <?php
        $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
        if ($deEmpresa != '') {
        ?>
          <!--Facturacion Electronica-->
          <li class="treeview">
            <a href="#"><i class="fa fa-link"></i> <span>Facturación Electrónica</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Generar XML')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Generar XML</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Firmar XML')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Firmar XML</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Validar XSD')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Validar XSD</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Recepción SRI')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Recepción SRI</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Autorización SRI')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Autorización SRI</span></a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'No Autorizado SRI')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>No Autorizado SRI</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('sri_electronico.enviar_sri') }}`, 'Facturación Electrónica', 'Notificación SRI')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>Notificación SRI</a>
              </li>
            </ul>
            <ul class="treeview-menu">
              <li class="container-clip">
                <button style="background: transparent; border: none;">
                  <i onclick="anclarTarget(this, `{{ route('demaestrodoc.index') }}`, 'Facturación Electrónica', 'Maestro Documentos')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
                <a href="{{ route('demaestrodoc.index') }}"><i class="fa fa-fw fa-file-o"></i>Maestro Documentos</a>
              </li>
            </ul>
            @if(in_array($rolUsuario, array(1)) == true)
            @endif
          </li>
        <?php
        }
        ?>
        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ route('user-management.index') }}`, 'Configuraciones', 'Administración de Usuarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>{{trans('tsidebar.administracion_usuarios')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 4, 5, 14, 20, 22)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{ url('manual') }}`, 'Configuraciones', 'Modulo tarifarios')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ url('manual') }}"><i class="fa fa-file-pdf-o"></i> <span>{{trans('tsidebar.tarifarios')}}</span></a>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 5, 20)) == true)
        <li class="container-clip">
          <button button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('muestrabiopsias.index')}}`, 'Reporte', 'Revision Ordenes Biopsia')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('muestrabiopsias.index') }}">
            <i class="fa fa-calendar-minus-o"></i><span> Revision Ordenes Biopsia </span>
          </a>
        </li>
        @endif

      </ul>
    </li>



    <!-- @if(in_array($rolUsuario, array(2)) == false)
        <li><a href="{{ route('prestamos_empleados.prestamos_visualizar') }}" ><i class="ion-person-stalker"></i><span>Prestamos</span></a></li>
        @endif -->

    @if(in_array($rolUsuario, array(1)) == true)
    @endif

    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>

<script type="text/javascript">
  const anclarTarget = (e, ruta, titulo = "", descripcion = "") => {

    $.ajax({
      url: `{{ route("menu.menu_usuario") }}`,
      type: "get",
      datatype: 'json',
      data: {
        'url': ruta,
        'titulo': titulo,
        'descripcion': descripcion,
      },
      success: function(data) {
        let content = document.getElementById("content_card")
        if (content != null) {
          crearCard(e, ruta, titulo, descripcion);
        }
      },
      error: function(data) {

      }
    })
  }

  const buscadorTarget = (id, className) => {
    let buscador_target = document.querySelector("." + className).value;
    // if(buscador_target.length < 3) {
    //    return ;
    // }
    $.ajax({
      url: `{{ route('menu.buscadorTarget') }}`,
      type: "get",
      dataType: "json",
      data: {
        'buscador': buscador_target,
        'tipo': id
      },
      success: function(data) {
        // console.log(data);
        if (id == 2) {
          $("#container_acce_direc").empty();
          $("#container_acce_direc").append(data.targets);
        } else {
          $("#content_card").empty();
          $("#content_card").append(data.targets);
        }

      },
      error: function(data) {

      }
    })
  }
</script>
