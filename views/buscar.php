<?php use App\Controllers\BuscarController; ?>
<div class="buscar-layout container">
  <header class="buscar-header">
    <h1 class="buscar-titulo">Buscador del Manual</h1>
    <p class="buscar-subtitulo">
      Buscá por tipo de acto, código de acto, requisito documental o término legal.
    </p>
  </header>

  <div class="buscar-input-wrap">
    <label for="buscar-input" class="visually-hidden">
      Término de búsqueda en el Manual de Registración
    </label>
    <form role="search" method="get" action="/buscar">
      <input
        id="buscar-input"
        name="q"
        type="search"
        class="buscar-input"
        value="<?= htmlspecialchars($query) ?>"
        placeholder="Ej: compraventa, embargo, hipoteca..."
        aria-label="Buscar en el Manual de Registración"
        autocomplete="off"
        spellcheck="false"
      />
    </form>
  </div>

  <div class="buscar-status" role="status" aria-live="polite" aria-atomic="true">
    <?php if ($estado === 'vacio'): ?>
      <p class="buscar-status__msg buscar-status__msg--hint">
        Escribí un término para buscar en el Manual de Registración.
      </p>
    <?php elseif ($estado === 'sin-resultados'): ?>
      <p class="buscar-status__msg buscar-status__msg--empty">
        Sin resultados para <strong>"<?= htmlspecialchars($query) ?>"</strong>.
        Intentá con otro término o revisá la ortografía.
      </p>
    <?php else: ?>
      <p class="buscar-status__msg">
        <strong><?= count($resultados) ?></strong>
        <?= count($resultados) === 1 ? 'resultado' : 'resultados' ?>
        para <strong>"<?= htmlspecialchars($query) ?>"</strong>
      </p>
    <?php endif; ?>
  </div>

  <?php if ($estado === 'con-resultados'): ?>
    <ul class="result-list" role="list" aria-label="Resultados de búsqueda">
      <?php foreach ($resultados as $ch): ?>
        <li>
          <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" class="result-item">
            <div class="result-item__meta">
              <?php if ($ch['codigoActo']): ?>
                <span class="result-item__codigo">Cód. <?= htmlspecialchars($ch['codigoActo']) ?></span>
              <?php endif; ?>
              <span class="result-item__categoria">
                <?= htmlspecialchars($categoriasPorId[$ch['categoria']] ?? $ch['categoria']) ?>
              </span>
            </div>
            <div class="result-item__body">
              <span class="result-item__titulo"><?= htmlspecialchars($ch['titulo']) ?></span>
              <span class="result-item__fragmento"><?= BuscarController::snippet($ch, $query) ?></span>
            </div>
            <span class="result-item__arrow" aria-hidden="true">›</span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
