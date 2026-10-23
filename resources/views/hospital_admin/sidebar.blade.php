<?php 
  $imagen= Auth::user()->imagen_url;
  if($imagen==' '){
    $imagen='avatar.jpg';
  }
  $rolUsuario = Auth::user()->id_tipo_usuario;
  $id_auth = Auth::user()->id;      
?>
<style>
  .main-sidebar { background-color: #004AC1 !important }
</style>
<ul class="navbar-nav main-sidebar sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
    <div class="sidebar-brand-icon">
      <i class="fas fa-hospital"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Sistema hospitalario</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="#">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Administración</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Interface
  </div>

  <!-- Nav Item - Imagenes -->
  <li class="nav-item">
    <a class="nav-link" href="#">
      <i class="fas fa-image"></i>
      <span>Im&aacute;genes</span></a>
  </li>

  <!-- Nav Item - Tables -->
  <li class="nav-item">
    <a class="nav-link" href="#">
      <i class="fas fa-microscope"></i>
      <span>Laboratorio</span></a>
  </li>

  <!-- Nav Item - Utilities Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="{ route('hospital_admin.farmacia') }}" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
      <i class="fas fa-prescription-bottle-alt"></i>
      <span>Farmacia</span>
    </a>
    <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Menú Farmacia:</h6>
        <a class="collapse-item" href="{{route('hospital_admin.marcas')}}">Marcas</a>
        <a class="collapse-item" href="{{route('hospital_admin.tipoprodu')}}">Tipos de Productos</a>
        <a class="collapse-item" href="{{route('hospital_admin.producto')}}">Producto</a>
        <a class="collapse-item" href="{{route('hospital_admin.bodega')}}">Bodega</a>
        <a class="collapse-item" href="{{route('hospital_admin.pedido')}}">Pedidos Realizados</a>
        <a class="collapse-item" href="{{route('hospital_admin.transito')}}">En Transito</a>
        <a class="collapse-item" href="#">Descargar Reportes</a>
        <a class="collapse-item" href="{{route('hospital_admin.proveedores')}}">Proveedores</a>
      </div>
    </div>
  </li>

  <!-- Nav Item - Imagenes -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('hospital_admin.gestionqui') }}">
      <i class="fas fa-fw fa-chart-area"></i>
      <span>Quir&oacute;fano</span></a>
  </li>

  <!-- Nav Item - Imagenes -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('hospital_admin.gestionh') }}">
      <i class="fas fa-bed"></i>
      <span>Habitación</span></a>
  </li>

  <!-- Nav Item - Servicios Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
      <i class="fas fa-concierge-bell"></i>
      <span>Servicios</span>
    </a>
    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Creación de platos:</h6>
        <a class="collapse-item" href="{{route('hospital_admin.listamenu')}}">Menú</a>
        <a class="collapse-item" href="{{route('hospital_admin.crearmenu')}}">Crear Menú</a>
        <!-- <a class="collapse-item" href="#">Register</a>
        <a class="collapse-item" href="#">Forgot Password</a>
        <div class="collapse-divider"></div>
        <h6 class="collapse-header">Other Pages:</h6>
        <a class="collapse-item" href="#">404 Page</a>
        <a class="collapse-item" href="#">Blank Page</a> -->
      </div>
    </div>
  </li>

  <!-- Nav Item - Imagenes -->
  <li class="nav-item">
    <a class="nav-link" href="#">
      <i class="fas fa-file-medical-alt"></i>
      <span>Emergencia</span></a>
  </li>

    <!-- Nav Item - Imagenes -->
    <li class="nav-item">
    <a class="nav-link" href="{{ route('hospital_admin.gestion_c') }}">
      <i class="fas fa-bed"></i>
      <span>Gestión de Camillas</span></a>
  </li>

  <!-- Divider -->
  <!-- <hr class="sidebar-divider d-none d-md-block"> -->

  <!-- Heading -->
  <!-- <div class="sidebar-heading">
    Addons
  </div> -->

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
