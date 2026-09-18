<div class="manual-layout">
  <!-- Sidebar -->
  <aside class="sidebar" aria-label="Categorías del manual">
    <div class="sidebar__inner">
      <p class="sidebar__titulo">Categorías</p>
      <nav>
        <ul class="sidebar__nav">
          <li>
            <a
              href="/manual"
              class="sidebar__link <?= $categoriaActiva === null ? 'sidebar__link--active' : '' ?>"
            >
              Todos los capítulos
              <span class="sidebar__count"><?= count($todos) ?></span>
            </a>
          </li>
          <?php foreach ($categorias as $cat): ?>
            <li>
              <a
                href="/manual?categoria=<?= urlencode($cat['id']) ?>"
                class="sidebar__link <?= $categoriaActiva === $cat['id'] ? 'sidebar__link--active' : '' ?>"
              >
                <?= htmlspecialchars($cat['titulo']) ?>
                <span class="sidebar__count"><?= count($capitulosPorCategoria[$cat['id']]) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Contenido principal -->
  <main id="contenido-manual" class="manual-content">
    <header class="manual-content__header">
      <h1 class="manual-content__titulo">
        <?= $categoriaActivaInfo ? htmlspecialchars($categoriaActivaInfo['titulo']) : 'Índice del Manual' ?>
      </h1>
      <?php if ($categoriaActivaInfo): ?>
        <p class="manual-content__desc"><?= htmlspecialchars($categoriaActivaInfo['descripcion']) ?></p>
      <?php else: ?>
        <p class="manual-content__desc">
          <?= count($todos) ?> capítulos organizados por categoría.
          Manual de Registración — 2ª edición, enero 2024.
        </p>
      <?php endif; ?>
    </header>

    <?php if ($categoriaActiva !== null): ?>
      <!-- Vista filtrada por categoría -->
      <section aria-label="Capítulos de la categoría">
        <ul class="chapter-list" role="list">
          <?php foreach ($capitulosFiltrados as $ch): ?>
            <li>
              <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" class="chapter-item">
                <?php if ($ch['codigoActo']): ?>
                  <span class="chapter-item__codigo">Cód. <?= htmlspecialchars($ch['codigoActo']) ?></span>
                <?php endif; ?>
                <div class="chapter-item__body">
                  <span class="chapter-item__titulo"><?= htmlspecialchars($ch['titulo']) ?></span>
                  <span class="chapter-item__meta">
                    Pág. <?= (int) $ch['pagina'] ?>
                    <?php if (count($ch['clasificacionIngreso'])): ?>
                      · <?= htmlspecialchars(implode(' / ', $ch['clasificacionIngreso'])) ?>
                    <?php endif; ?>
                  </span>
                </div>
                <span class="chapter-item__arrow" aria-hidden="true">›</span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php else: ?>
      <!-- Vista de todos agrupados por categoría -->
      <?php foreach ($categorias as $cat): ?>
        <?php $capitulos = $capitulosPorCategoria[$cat['id']]; ?>
        <?php if (count($capitulos) > 0): ?>
          <section id="cat-<?= htmlspecialchars($cat['id']) ?>" class="cat-section" aria-labelledby="cat-h2-<?= htmlspecialchars($cat['id']) ?>">
            <h2 id="cat-h2-<?= htmlspecialchars($cat['id']) ?>" class="cat-section__titulo">
              <?= htmlspecialchars($cat['titulo']) ?>
              <span class="cat-section__count"><?= count($capitulos) ?></span>
            </h2>
            <p class="cat-section__desc"><?= htmlspecialchars($cat['descripcion']) ?></p>
            <ul class="chapter-list" role="list">
              <?php foreach ($capitulos as $ch): ?>
                <li>
                  <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" class="chapter-item">
                    <?php if ($ch['codigoActo']): ?>
                      <span class="chapter-item__codigo">Cód. <?= htmlspecialchars($ch['codigoActo']) ?></span>
                    <?php else: ?>
                      <span class="chapter-item__codigo chapter-item__codigo--none">—</span>
                    <?php endif; ?>
                    <div class="chapter-item__body">
                      <span class="chapter-item__titulo"><?= htmlspecialchars($ch['titulo']) ?></span>
                      <span class="chapter-item__meta">
                        Pág. <?= (int) $ch['pagina'] ?>
                        <?php if (count($ch['clasificacionIngreso'])): ?>
                          · <?= htmlspecialchars(implode(' / ', $ch['clasificacionIngreso'])) ?>
                        <?php endif; ?>
                      </span>
                    </div>
                    <span class="chapter-item__arrow" aria-hidden="true">›</span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>
</div>
