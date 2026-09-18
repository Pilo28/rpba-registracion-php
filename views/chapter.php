<?php if ($chapter !== null): $ch = $chapter; ?>
  <article class="chapter-page">
    <div class="container">

      <!-- Breadcrumb -->
      <nav class="breadcrumb" aria-label="Ruta de navegación">
        <ol>
          <li><a href="/">Inicio</a></li>
          <li aria-hidden="true" class="breadcrumb__sep">›</li>
          <li><a href="/manual">Manual</a></li>
          <li aria-hidden="true" class="breadcrumb__sep">›</li>
          <li aria-current="page"><?= htmlspecialchars($ch['titulo']) ?></li>
        </ol>
      </nav>

      <!-- Header del capítulo -->
      <header class="chapter-header">
        <div class="chapter-header__top">
          <?php if ($ch['codigoActo']): ?>
            <span class="badge-codigo" aria-label="Código de acto <?= htmlspecialchars($ch['codigoActo']) ?>">
              Código <?= htmlspecialchars($ch['codigoActo']) ?>
            </span>
          <?php endif; ?>
          <span class="badge-pagina">Pág. <?= (int) $ch['pagina'] ?></span>
          <span class="badge-cat" aria-label="Categoría: <?= htmlspecialchars($catInfo['titulo'] ?? '') ?>">
            <a href="/manual?categoria=<?= urlencode($ch['categoria']) ?>">
              <?= htmlspecialchars($catInfo['titulo'] ?? $ch['categoria']) ?>
            </a>
          </span>
        </div>
        <h1 class="chapter-header__titulo"><?= htmlspecialchars($ch['titulo']) ?></h1>
      </header>

      <!-- Ficha técnica -->
      <section class="ficha" aria-labelledby="ficha-titulo">
        <h2 id="ficha-titulo" class="visually-hidden">Datos técnicos del acto</h2>
        <dl class="ficha__grid">
          <div class="ficha__item">
            <dt>Clasificación de ingreso</dt>
            <dd>
              <?php foreach ($ch['clasificacionIngreso'] as $c): ?>
                <span class="tag tag--ingreso"><?= htmlspecialchars($c) ?></span>
              <?php endforeach; ?>
            </dd>
          </div>
          <div class="ficha__item">
            <dt>Art. 2 Ley 17.801</dt>
            <dd><?= htmlspecialchars($ch['art2Ley17801']) ?></dd>
          </div>
          <div class="ficha__item">
            <dt>Rubro/s del folio real</dt>
            <dd><?= htmlspecialchars(implode(', ', $ch['rubrosFolioReal'])) ?></dd>
          </div>
          <div class="ficha__item">
            <dt>Certificado de dominio</dt>
            <dd>
              <span class="tag <?= $ch['requiereCertDominio'] ? 'tag--si' : 'tag--no' ?>">
                <?= $ch['requiereCertDominio'] ? 'Sí' : 'No' ?>
              </span>
            </dd>
          </div>
          <div class="ficha__item">
            <dt>Inscripción provisional</dt>
            <dd>
              <span class="tag <?= $ch['requiereInscProvisional'] ? 'tag--si' : 'tag--no' ?>">
                <?= $ch['requiereInscProvisional'] ? 'Sí' : 'No' ?>
              </span>
            </dd>
          </div>
          <div class="ficha__item">
            <dt>Certificado catastral</dt>
            <dd>
              <?php if ($ch['requiereCertCatastral'] === null): ?>
                <span class="tag tag--nd">Según corresponda</span>
              <?php else: ?>
                <span class="tag <?= $ch['requiereCertCatastral'] ? 'tag--si' : 'tag--no' ?>">
                  <?= $ch['requiereCertCatastral'] ? 'Sí' : 'No' ?>
                </span>
              <?php endif; ?>
            </dd>
          </div>
        </dl>
      </section>

      <div class="chapter-body">
        <!-- Descripción -->
        <section class="chapter-section" aria-labelledby="desc-titulo">
          <h2 id="desc-titulo" class="chapter-section__titulo">Descripción</h2>
          <p class="chapter-section__texto"><?= htmlspecialchars($ch['descripcion']) ?></p>
        </section>

        <!-- Control documental -->
        <section class="chapter-section" aria-labelledby="docs-titulo">
          <h2 id="docs-titulo" class="chapter-section__titulo">Control Documental</h2>
          <ul class="doc-list" role="list">
            <?php foreach ($ch['documentos'] as $doc): ?>
              <li class="doc-list__item">
                <span class="doc-list__icon" aria-hidden="true">•</span>
                <?= htmlspecialchars($doc) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>

        <!-- Modelo de asiento -->
        <section class="chapter-section" aria-labelledby="asiento-titulo">
          <h2 id="asiento-titulo" class="chapter-section__titulo">Modelo de asiento</h2>
          <blockquote class="asiento">
            <p><?= htmlspecialchars($ch['modeloAsiento']) ?></p>
          </blockquote>
        </section>

        <!-- Observaciones -->
        <?php if ($ch['observaciones']): ?>
          <section class="chapter-section chapter-section--obs" aria-labelledby="obs-titulo">
            <h2 id="obs-titulo" class="chapter-section__titulo">Observaciones</h2>
            <p class="chapter-section__texto"><?= htmlspecialchars($ch['observaciones']) ?></p>
          </section>
        <?php endif; ?>
      </div>

      <!-- Navegación anterior / siguiente -->
      <nav class="chapter-nav" aria-label="Navegar entre capítulos">
        <div class="chapter-nav__inner">
          <?php if ($prevNext['prev']): ?>
            <a href="/manual/<?= htmlspecialchars($prevNext['prev']['slug']) ?>" class="chapter-nav__link chapter-nav__link--prev" rel="prev">
              <span class="chapter-nav__dir" aria-hidden="true">‹ Anterior</span>
              <span class="chapter-nav__nombre"><?= htmlspecialchars($prevNext['prev']['titulo']) ?></span>
            </a>
          <?php else: ?>
            <div></div>
          <?php endif; ?>

          <?php if ($prevNext['next']): ?>
            <a href="/manual/<?= htmlspecialchars($prevNext['next']['slug']) ?>" class="chapter-nav__link chapter-nav__link--next" rel="next">
              <span class="chapter-nav__dir" aria-hidden="true">Siguiente ›</span>
              <span class="chapter-nav__nombre"><?= htmlspecialchars($prevNext['next']['titulo']) ?></span>
            </a>
          <?php else: ?>
            <div></div>
          <?php endif; ?>
        </div>
      </nav>

    </div>
  </article>
<?php else: ?>
  <div class="container" style="padding-block: 4rem; text-align: center;">
    <h1>Capítulo no encontrado</h1>
    <p>El capítulo <strong><?= htmlspecialchars($slug) ?></strong> no existe en el manual.</p>
    <a href="/manual" style="color: var(--color-primary); font-weight: 600;">
      Ver índice del manual
    </a>
  </div>
<?php endif; ?>
