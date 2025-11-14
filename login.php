<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: index.html');
        exit;
    }
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
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
            <ul class="navbar__menu"></ul>
        </nav>
    </header>

    <main class="container">
        <section class="container__section">
            <h2 class="section__title title--principal">Iniciar sesión</h2>

            <?php if ($error): ?>
                <p class="section__description" style="color: red;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form action="login_process.php" method="post" class="section__form">
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="username">Usuario</label>
                        <input type="text" id="username" name="username" class="form__input" required>
                    </div>
                </div>
                <div class="form__line">
                    <div class="form__group">
                        <label class="form__addon" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form__input" required>
                    </div>
                </div>
                <div class="form__line">
                    <button type="submit" class="form__input" style="cursor:pointer;">
                        Entrar
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script src="js/app.js" type="module"></script>
</body>
</html>
