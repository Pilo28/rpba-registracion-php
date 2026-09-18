<main class="container codigos-layout">

  <header class="codigos-header">
    <h1 class="section-titulo">Índice por Código de Acto</h1>
    <p class="section-subtitulo">
      <?= $total ?> actos registrales ordenados por código numérico.
    </p>
  </header>

  <!-- Filtro rápido -->
  <div class="codigos-filtro-wrap">
    <svg class="codigos-filtro-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="11" cy="11" r="8" />
      <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </svg>
    <label for="codigos-filtro" class="visually-hidden">
      Filtrar por código de acto o nombre del capítulo
    </label>
    <input
      id="codigos-filtro"
      type="search"
      class="codigos-filtro"
      placeholder="Filtrar por código (ej: 100) o nombre..."
      autocomplete="off"
      spellcheck="false"
    />
  </div>

  <!-- Contador de resultados -->
  <p class="codigos-status" role="status" aria-live="polite" aria-atomic="true" id="codigos-status">
    <?= $total ?> actos registrales en total
  </p>

  <!-- Tabla -->
  <div class="codigos-table-wrap">
    <table class="codigos-table" id="codigos-table">
      <caption class="visually-hidden">
        Actos registrales del Manual de Registración ordenados por código de acto numérico
      </caption>
      <thead>
        <tr>
          <th scope="col">Código</th>
          <th scope="col">Acto registral</th>
          <th scope="col" class="codigos-th--cat">Categoría</th>
          <th scope="col"><span class="visually-hidden">Ver capítulo</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($codigos as $ch): ?>
          <tr
            class="codigos-row"
            tabindex="0"
            data-href="/manual/<?= htmlspecialchars($ch['slug']) ?>"
            data-search="<?= htmlspecialchars(mb_strtolower($ch['codigoActo'] . ' ' . $ch['titulo'] . ' ' . ($categoriasPorId[$ch['categoria']] ?? ''))) ?>"
          >
            <td class="codigos-cell codigos-cell--cod">
              <span class="cod-badge"><?= htmlspecialchars($ch['codigoActo']) ?></span>
            </td>
            <td class="codigos-cell codigos-cell--titulo">
              <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" class="codigos-link"><?= htmlspecialchars($ch['titulo']) ?></a>
            </td>
            <td class="codigos-cell codigos-cell--cat">
              <?= htmlspecialchars($categoriasPorId[$ch['categoria']] ?? $ch['categoria']) ?>
            </td>
            <td class="codigos-cell codigos-cell--arrow" aria-hidden="true">›</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="codigos-empty" id="codigos-empty" hidden>
      Sin resultados. Intentá con otro código o nombre.
    </p>
  </div>

</main>

<script src="/assets/codigos.js" defer></script>
