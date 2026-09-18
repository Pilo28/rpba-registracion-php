<?php use App\Csrf; ?>
<div class="admin-page-header">
  <h1>Categorías</h1>
  <a href="/admin/categorias/nueva" class="admin-btn admin-btn--primary">+ Nueva categoría</a>
</div>

<?php if (!empty($_SESSION['admin_flash_error'])): ?>
  <p class="admin-error"><?= htmlspecialchars($_SESSION['admin_flash_error']) ?></p>
  <?php unset($_SESSION['admin_flash_error']); ?>
<?php endif; ?>

<div class="admin-filtro-wrap">
  <input
    type="search"
    id="categorias-filtro"
    class="admin-filtro"
    placeholder="Buscar por título, id o descripción..."
    autocomplete="off"
  >
  <p class="admin-filtro-status" id="categorias-filtro-status" role="status" aria-live="polite">
    <?= count($categorias) ?> categorías en total
  </p>
</div>

<table class="admin-table" id="categorias-table">
  <thead>
    <tr>
      <th>Orden</th>
      <th>Id</th>
      <th>Título</th>
      <th>Descripción</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($categorias as $cat): ?>
      <tr data-search="<?= htmlspecialchars(mb_strtolower($cat['id'] . ' ' . $cat['titulo'] . ' ' . $cat['descripcion'])) ?>">
        <td><?= (int) $cat['orden'] ?></td>
        <td><code><?= htmlspecialchars($cat['id']) ?></code></td>
        <td><?= htmlspecialchars($cat['titulo']) ?></td>
        <td class="admin-table__desc"><?= htmlspecialchars($cat['descripcion']) ?></td>
        <td class="admin-table__actions">
          <a href="/admin/categorias/<?= urlencode($cat['id']) ?>/editar">Editar</a>
          <form method="post" action="/admin/categorias/<?= urlencode($cat['id']) ?>/eliminar"
                onsubmit="return confirm('¿Borrar la categoría «<?= htmlspecialchars($cat['titulo'], ENT_QUOTES) ?>»?');">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <button type="submit" class="admin-btn--danger">Borrar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="admin-filtro-empty" id="categorias-filtro-empty" hidden>Sin resultados.</p>

<script src="/assets/admin-filter.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    initAdminFilter({
      input: 'categorias-filtro',
      table: 'categorias-table',
      status: 'categorias-filtro-status',
      empty: 'categorias-filtro-empty',
      label: 'categoría',
      labelPlural: 'categorías',
    });
  });
</script>
