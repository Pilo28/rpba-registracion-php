<?php use App\Csrf; ?>
<div class="admin-page-header">
  <h1>Capítulos</h1>
  <a href="/admin/capitulos/nuevo" class="admin-btn admin-btn--primary">+ Nuevo capítulo</a>
</div>

<div class="admin-filtro-wrap">
  <input
    type="search"
    id="capitulos-filtro"
    class="admin-filtro"
    placeholder="Buscar por título, código, categoría o slug..."
    autocomplete="off"
  >
  <p class="admin-filtro-status" id="capitulos-filtro-status" role="status" aria-live="polite">
    <?= count($capitulos) ?> capítulos en total
  </p>
</div>

<table class="admin-table" id="capitulos-table">
  <thead>
    <tr>
      <th>Código</th>
      <th>Título</th>
      <th>Categoría</th>
      <th>Slug</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($capitulos as $ch): ?>
      <?php $categoriaTitulo = $categoriasPorId[$ch['categoria']] ?? $ch['categoria']; ?>
      <tr data-search="<?= htmlspecialchars(mb_strtolower(($ch['codigoActo'] ?? '') . ' ' . $ch['titulo'] . ' ' . $categoriaTitulo . ' ' . $ch['slug'])) ?>">
        <td><?= $ch['codigoActo'] ? htmlspecialchars($ch['codigoActo']) : '—' ?></td>
        <td><?= htmlspecialchars($ch['titulo']) ?></td>
        <td><?= htmlspecialchars($categoriaTitulo) ?></td>
        <td><code><?= htmlspecialchars($ch['slug']) ?></code></td>
        <td class="admin-table__actions">
          <a href="/manual/<?= htmlspecialchars($ch['slug']) ?>" target="_blank" rel="noopener">Ver</a>
          <a href="/admin/capitulos/<?= $ch['id'] ?>/editar">Editar</a>
          <form method="post" action="/admin/capitulos/<?= $ch['id'] ?>/eliminar"
                onsubmit="return confirm('¿Borrar «<?= htmlspecialchars($ch['titulo'], ENT_QUOTES) ?>»?');">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <button type="submit" class="admin-btn--danger">Borrar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="admin-filtro-empty" id="capitulos-filtro-empty" hidden>Sin resultados.</p>

<script src="/assets/admin-filter.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    initAdminFilter({
      input: 'capitulos-filtro',
      table: 'capitulos-table',
      status: 'capitulos-filtro-status',
      empty: 'capitulos-filtro-empty',
      label: 'capítulo',
      labelPlural: 'capítulos',
    });
  });
</script>
