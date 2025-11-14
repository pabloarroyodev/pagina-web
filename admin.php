<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administración</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <h1 class="navbar__logo">Calcular valor del peso argentino</h1>
            <button class="navbar__toggle" aria-label="Abrir menú">
                <span class="navbar__toggle-icon"></span>
            </button>
            <ul class="navbar__menu">
                <li class="navbar__item navbar__item--user">
                    <button class="user-menu-toggle" aria-label="Menú de usuario">
                        <span class="user-menu__icon"></span>
                        <span class="user-menu__label"><?php echo htmlspecialchars($_SESSION['username']); ?> (admin)</span>
                    </button>
                    <ul class="user-menu">
                        <li class="user-menu__item"><a href="logout.php" class="user-menu__link">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="container__section">
            <h2 class="section__title title--principal">Panel de administración</h2>

            <p class="section__description">
                Hola, <?php echo htmlspecialchars($_SESSION['username']); ?> (admin)
            </p>

            <?php if ($mensaje): ?>
                <p class="section__description" style="color: green;">
                    <?php echo htmlspecialchars($mensaje); ?>
                </p>
            <?php endif; ?>

            <h3 class="section__title">Crear nuevo usuario</h3>
            <form action="user_create.php" method="post" class="section__form">
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="username">Usuario</label>
                        <input type="text" id="username" name="username" class="form__input" required>
                    </div>
                </div>
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form__input">
                    </div>
                </div>
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form__input" required>
                    </div>
                </div>
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="role">Rol</label>
                        <select id="role" name="role" class="form__select">
                            <option value="user">Usuario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="form__line">
                    <button type="submit" class="form__input" style="cursor:pointer;">
                        Crear usuario
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script src="js/app.js" type="module"></script>
</body>
</html>
