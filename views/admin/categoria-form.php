<?php use App\Csrf; ?>

<h1><?= $isEdit ? 'Editar categoría' : 'Nueva categoría' ?></h1>

<?php foreach ($errors as $error): ?>
  <p class="admin-error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post" class="admin-form" action="<?= $isEdit ? '/admin/categorias/' . urlencode($formActionId) : '/admin/categorias' ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

  <label for="id">Id (slug interno, ej: "hipoteca")</label>
  <input id="id" name="id" type="text" pattern="[a-z0-9-]+" required
         value="<?= htmlspecialchars($categoria['id'] ?? '') ?>">
  <?php if ($isEdit): ?>
    <p class="admin-hint">Si lo cambiás, los capítulos que ya estaban en esta categoría se reasignan automáticamente al nuevo id.</p>
  <?php endif; ?>

  <label for="titulo">Título</label>
  <input id="titulo" name="titulo" type="text" required value="<?= htmlspecialchars($categoria['titulo'] ?? '') ?>">

  <label for="descripcion">Descripción</label>
  <textarea id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>

  <label for="orden">Orden</label>
  <input id="orden" name="orden" type="number" min="0" value="<?= htmlspecialchars((string) ($categoria['orden'] ?? '')) ?>">
  <p class="admin-hint">
    <?php
      $ocupados = array_values(array_filter($ordenesUsados, fn ($c) => $c['id'] !== ($formActionId ?? null)));
      if ($ocupados !== []):
    ?>
      Ya en uso:
      <?php foreach ($ocupados as $i => $c): ?>
        <strong><?= (int) $c['orden'] ?></strong> (<?= htmlspecialchars($c['titulo']) ?>)<?= $i < count($ocupados) - 1 ? ', ' : '' ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </p>

  <div class="admin-form__actions">
    <button type="submit" class="admin-btn admin-btn--primary">Guardar</button>
    <a href="/admin/categorias" class="admin-btn">Cancelar</a>
  </div>
</form>
