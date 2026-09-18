<?php

use App\Csrf;

$ch = $capitulo ?? [];
$clasificacionActual = $ch['clasificacionIngreso'] ?? [];
$certCatastral = array_key_exists('requiereCertCatastral', $ch) ? $ch['requiereCertCatastral'] : null;
$action = $isEdit ? '/admin/capitulos/' . $ch['id'] : '/admin/capitulos';
?>

<h1><?= $isEdit ? 'Editar capítulo' : 'Nuevo capítulo' ?></h1>

<?php foreach ($errors as $error): ?>
  <p class="admin-error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post" class="admin-form" action="<?= htmlspecialchars($action) ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

  <label for="titulo">Título</label>
  <input id="titulo" name="titulo" type="text" required value="<?= htmlspecialchars($ch['titulo'] ?? '') ?>">

  <label for="slug">Slug (para la URL /manual/...)</label>
  <input id="slug" name="slug" type="text" pattern="[a-z0-9-]+" required value="<?= htmlspecialchars($ch['slug'] ?? '') ?>">

  <div class="admin-form__row">
    <div>
      <label for="codigo_acto">Código de acto (vacío si no aplica)</label>
      <input id="codigo_acto" name="codigo_acto" type="text" value="<?= htmlspecialchars($ch['codigoActo'] ?? '') ?>">
    </div>
    <div>
      <label for="pagina">Página del manual</label>
      <input id="pagina" name="pagina" type="number" min="1" required value="<?= htmlspecialchars((string) ($ch['pagina'] ?? '')) ?>">
    </div>
  </div>

  <label for="categoria">Categoría</label>
  <select id="categoria" name="categoria" required>
    <option value="">— Elegir —</option>
    <?php foreach ($categorias as $cat): ?>
      <option value="<?= htmlspecialchars($cat['id']) ?>" <?= ($ch['categoria'] ?? '') === $cat['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['titulo']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <fieldset class="admin-fieldset">
    <legend>Clasificación de ingreso</legend>
    <?php foreach ($clasificacionesDisponibles as $c): ?>
      <label class="admin-checkbox">
        <input type="checkbox" name="clasificacion[]" value="<?= htmlspecialchars($c) ?>"
               <?= in_array($c, $clasificacionActual, true) ? 'checked' : '' ?>>
        <?= htmlspecialchars($c) ?>
      </label>
    <?php endforeach; ?>
  </fieldset>

  <label for="art2_ley17801">Art. 2 Ley 17.801</label>
  <input id="art2_ley17801" name="art2_ley17801" type="text" required value="<?= htmlspecialchars($ch['art2Ley17801'] ?? '') ?>">

  <label for="rubros_folio_real">Rubro/s del folio real (uno por línea)</label>
  <textarea id="rubros_folio_real" name="rubros_folio_real" rows="3"><?= htmlspecialchars(implode("\n", $ch['rubrosFolioReal'] ?? [])) ?></textarea>

  <div class="admin-form__row">
    <label class="admin-checkbox">
      <input type="checkbox" name="requiere_cert_dominio" <?= !empty($ch['requiereCertDominio']) ? 'checked' : '' ?>>
      Requiere certificado de dominio
    </label>
    <label class="admin-checkbox">
      <input type="checkbox" name="requiere_insc_provisional" <?= !empty($ch['requiereInscProvisional']) ? 'checked' : '' ?>>
      Requiere inscripción provisional
    </label>
  </div>

  <label for="requiere_cert_catastral">Certificado catastral</label>
  <select id="requiere_cert_catastral" name="requiere_cert_catastral">
    <option value="si" <?= $certCatastral === true ? 'selected' : '' ?>>Sí</option>
    <option value="no" <?= $certCatastral === false ? 'selected' : '' ?>>No</option>
    <option value="nd" <?= $certCatastral === null ? 'selected' : '' ?>>Según corresponda</option>
  </select>

  <label for="descripcion">Descripción</label>
  <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($ch['descripcion'] ?? '') ?></textarea>

  <label for="documentos">Control documental (un documento por línea)</label>
  <textarea id="documentos" name="documentos" rows="6" required><?= htmlspecialchars(implode("\n", $ch['documentos'] ?? [])) ?></textarea>

  <label for="modelo_asiento">Modelo de asiento</label>
  <textarea id="modelo_asiento" name="modelo_asiento" rows="3" required><?= htmlspecialchars($ch['modeloAsiento'] ?? '') ?></textarea>

  <label for="observaciones">Observaciones (opcional)</label>
  <textarea id="observaciones" name="observaciones" rows="3"><?= htmlspecialchars($ch['observaciones'] ?? '') ?></textarea>

  <div class="admin-form__actions">
    <button type="submit" class="admin-btn admin-btn--primary">Guardar</button>
    <a href="/admin/capitulos" class="admin-btn">Cancelar</a>
  </div>
</form>
