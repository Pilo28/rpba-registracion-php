<?php use App\Csrf; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ingresar | Panel de administración</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="stylesheet" href="/assets/admin.css">
</head>
<body class="admin-login-body">
  <main class="admin-login">
    <form class="admin-login__form" method="post" action="/admin/login">
      <h1>Panel de administración</h1>
      <p class="admin-login__subtitle">Manual de Registración</p>

      <?php if ($error): ?>
        <p class="admin-error" role="alert"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

      <label for="user">Usuario</label>
      <input id="user" name="user" type="text" autocomplete="username" required autofocus>

      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>

      <button type="submit">Ingresar</button>
    </form>
  </main>
</body>
</html>
