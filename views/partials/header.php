<header class="site-header">
  <div class="container site-header__inner">
    <a href="/" class="site-header__logo" aria-label="Registración — Inicio">
      <span class="site-header__logo-nombre">Registración</span>
    </a>

    <!-- Navegación desktop -->
    <nav class="site-nav-wrapper" aria-label="Navegación principal">
      <ul class="site-nav">
        <li><a href="/manual" class="site-nav__link <?= nav_active('/manual') ?>">Manual</a></li>
        <li><a href="/codigos" class="site-nav__link <?= nav_active('/codigos') ?>">Códigos</a></li>
        <li><a href="/buscar" class="site-nav__link <?= nav_active('/buscar') ?>">Buscar</a></li>
        <li><a href="/asistente" class="site-nav__link site-nav__link--accent <?= nav_active('/asistente') ?>">Asistente IA</a></li>
      </ul>
    </nav>

    <!-- Botón hamburguesa (solo móvil) -->
    <button
      class="site-nav__hamburger"
      id="nav-hamburger"
      aria-label="Menú de navegación"
      aria-controls="mobile-nav"
      aria-expanded="false"
      type="button"
    >
      <span class="site-nav__hamburger-bar"></span>
      <span class="site-nav__hamburger-bar"></span>
      <span class="site-nav__hamburger-bar"></span>
    </button>
  </div>

  <!-- Navegación móvil desplegable -->
  <nav id="mobile-nav" aria-label="Navegación principal" class="site-nav--mobile" hidden>
    <ul class="site-nav--mobile__list">
      <li><a href="/manual" class="site-nav--mobile__link <?= nav_active('/manual') ?>">Manual</a></li>
      <li><a href="/codigos" class="site-nav--mobile__link <?= nav_active('/codigos') ?>">Códigos</a></li>
      <li><a href="/buscar" class="site-nav--mobile__link <?= nav_active('/buscar') ?>">Buscar</a></li>
      <li><a href="/asistente" class="site-nav--mobile__link site-nav--mobile__link--accent <?= nav_active('/asistente') ?>">Asistente IA</a></li>
    </ul>
  </nav>
</header>

<script src="/assets/nav.js" defer></script>
