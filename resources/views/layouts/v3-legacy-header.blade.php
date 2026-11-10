<header class="main-header v3-legacy-header" role="banner">
  <nav class="navbar navbar-static-top" aria-label="Barra superior">
    <button type="button" class="v3-legacy-menu-toggle" aria-label="Mostrar u ocultar menú" aria-expanded="true">
      <i class="fas fa-bars" aria-hidden="true"></i>
    </button>
    <div class="v3-legacy-breadcrumb" aria-label="Ruta de navegación">
      <a class="v3-legacy-home" href="{{ url('v3/admin') }}">
        <i class="fas fa-home" aria-hidden="true"></i>
        <span>Inicio</span>
      </a>
      @if(!empty($headerModule))
        <i class="fas fa-chevron-right v3-legacy-breadcrumb__separator" aria-hidden="true"></i>
        <span class="v3-legacy-breadcrumb__current">
          <i class="{{ $headerModuleIcon ?? 'fas fa-th-large' }}" aria-hidden="true"></i>
          {{ $headerModule }}
        </span>
      @endif
    </div>
    <div class="v3-legacy-user">
      <button type="button" class="v3-legacy-menu-toggle" id="v3LegacyThemeToggle" aria-label="Cambiar a modo oscuro" title="Cambiar a modo oscuro">
        <i class="fas fa-moon" aria-hidden="true"></i>
      </button>
      <span class="v3-legacy-user-name">
        <i class="fas fa-user-circle" aria-hidden="true"></i>
        {{ trim((string) config('data.usuario')->nombre1.' '.config('data.usuario')->apellido1) }}
      </span>
      <a href="{{ route('logout') }}" class="v3-legacy-logout"
         onclick="event.preventDefault();document.getElementById('v3-legacy-logout-form').submit();">
        <i class="fas fa-sign-out-alt" aria-hidden="true"></i><span>Salir</span>
      </a>
      <form id="v3-legacy-logout-form" action="{{ route('logout') }}" method="POST" hidden>{{ csrf_field() }}</form>
    </div>
  </nav>
</header>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var button=document.getElementById('v3LegacyThemeToggle');if(!button)return;
  function render(){var dark=document.body.classList.contains('dark-mode');button.setAttribute('aria-label',dark?'Cambiar a modo claro':'Cambiar a modo oscuro');button.setAttribute('title',button.getAttribute('aria-label'));button.innerHTML='<i class="fas fa-'+(dark?'sun':'moon')+'" aria-hidden="true"></i>';}
  button.addEventListener('click',function(){document.body.classList.toggle('dark-mode');try{localStorage.setItem('aitrol-v3-theme',document.body.classList.contains('dark-mode')?'dark':'light')}catch(e){}render();});render();
});
</script>
