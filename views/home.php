<!-- Hero -->
<section class="hero" aria-labelledby="hero-titulo">
  <div class="container hero__inner">
    <div class="hero__content">
      <h1 id="hero-titulo" class="hero__titulo">Manual de Registración</h1>
      <p class="hero__subtitulo">
        Guía práctica para inscriptores. Calificación de documentos, requisitos formales
        y control documental para todos los actos registrales.
      </p>
      <div class="hero__edicion">
        2ª edición — Actualizada enero 2024
      </div>
    </div>

    <form class="buscador" role="search" aria-label="Buscar en el manual" action="/buscar" method="get">
      <label for="buscador-input" class="buscador__label">
        Buscar en el manual
      </label>
      <div class="buscador__row">
        <input
          id="buscador-input"
          name="q"
          type="search"
          class="buscador__input"
          placeholder="Ej: compraventa, código 300, embargo..."
          autocomplete="off"
          aria-describedby="buscador-hint"
        />
        <button type="submit" class="buscador__btn">Buscar</button>
      </div>
      <p id="buscador-hint" class="buscador__hint">
        Podés buscar por nombre del acto, código de acto o documentos requeridos.
      </p>
    </form>
  </div>
</section>

<!-- Categorías -->
<section class="categorias-section" aria-labelledby="categorias-titulo">
  <div class="container">
    <h2 id="categorias-titulo" class="section-titulo">Explorar por categoría</h2>
    <p class="section-subtitulo">
      <?= $totalCapitulos ?> capítulos organizados en <?= count($categorias) ?> categorías
    </p>

    <ul class="categorias-grid" role="list">
      <?php foreach ($categorias as $cat): ?>
        <?php $meta = $categoriaMeta[$cat['id']] ?? ['icon' => '?', 'color' => '#1a3a6b']; ?>
        <li>
          <a
            href="/manual?categoria=<?= urlencode($cat['id']) ?>"
            class="cat-card"
            style="--cat-color: <?= htmlspecialchars($meta['color']) ?>"
            aria-label="<?= htmlspecialchars($cat['titulo']) ?> — <?= $porCategoria[$cat['id']] ?> capítulos"
          >
            <span class="cat-card__icon" aria-hidden="true"><?= htmlspecialchars($meta['icon']) ?></span>
            <div class="cat-card__body">
              <h3 class="cat-card__titulo"><?= htmlspecialchars($cat['titulo']) ?></h3>
              <p class="cat-card__desc"><?= htmlspecialchars($cat['descripcion']) ?></p>
              <span class="cat-card__count"><?= $porCategoria[$cat['id']] ?> capítulos</span>
            </div>
            <span class="cat-card__arrow" aria-hidden="true">›</span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- Acceso rápido -->
<section class="acceso-rapido" aria-labelledby="acceso-titulo">
  <div class="container">
    <h2 id="acceso-titulo" class="section-titulo">Capítulos más consultados</h2>
    <ul class="acceso-grid" role="list">
      <?php foreach ($destacados as $ch): ?>
        <li>
          <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" class="acceso-card">
            <span class="acceso-card__codigo">
              <?= $ch['codigoActo'] ? 'Cód. ' . htmlspecialchars($ch['codigoActo']) : '—' ?>
            </span>
            <span class="acceso-card__titulo"><?= htmlspecialchars($ch['titulo']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
