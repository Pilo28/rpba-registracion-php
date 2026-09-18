<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($seo['title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?= htmlspecialchars($seo['description']) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($seo['title']) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($seo['description']) ?>">
  <?php if ($seo['canonical'] !== null): ?>
    <link rel="canonical" href="<?= htmlspecialchars($seo['canonical']) ?>">
  <?php endif; ?>
  <?php if (!empty($seo['robots'])): ?>
    <meta name="robots" content="<?= htmlspecialchars($seo['robots']) ?>">
  <?php endif; ?>
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="stylesheet" href="/assets/site.css">
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Ir al contenido principal</a>

  <?php require __DIR__ . '/partials/header.php'; ?>

  <main id="contenido-principal">
    <?= $content ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
