<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title) ?> | Panel de administración</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="stylesheet" href="/assets/admin.css">
</head>
<body>
  <div class="admin-shell">
    <header class="admin-header">
      <a href="/admin" class="admin-header__logo">Panel — Manual de Registración</a>
      <nav class="admin-nav">
        <a href="/admin/capitulos">Capítulos</a>
        <a href="/admin/categorias">Categorías</a>
        <a href="/" target="_blank" rel="noopener">Ver sitio ↗</a>
        <form method="post" action="/admin/logout" class="admin-nav__logout">
          <button type="submit">Salir</button>
        </form>
      </nav>
    </header>

    <main class="admin-main">
      <?= $content ?>
    </main>
  </div>
</body>
</html>
