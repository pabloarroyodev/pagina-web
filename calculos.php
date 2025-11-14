<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuario';

// Cálculos de inflación
$inflationCalculations = [];
$sqlInflation = 'SELECT id, input_amount, start_year, start_month, end_year, end_month,
                        result_amount, accumulated_inflation, avg_monthly_inflation,
                        avg_yearly_inflation, created_at
                 FROM calculations
                 WHERE user_id = ?
                 ORDER BY created_at DESC';

if ($stmt = $mysqli->prepare($sqlInflation)) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $inflationCalculations = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Cálculos de comparación de precios
$priceComparisons = [];
$sqlComparison = 'SELECT id, amount1, year1, month1, amount2, year2, month2,
                         adjusted_amount, percentage_difference, created_at
                  FROM price_comparison_calculations
                  WHERE user_id = ?
                  ORDER BY created_at DESC';

if ($stmt = $mysqli->prepare($sqlComparison)) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $priceComparisons = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Cálculos de dólar (blue y oficial)
$dollarCalculations = [];
$sqlDollar = 'SELECT id, dollar_type, peso_amount, start_year, start_month, end_year, end_month,
                     start_usd_equivalent, end_usd_equivalent, percentage_change, created_at
              FROM dollar_calculations
              WHERE user_id = ?
              ORDER BY created_at DESC';

if ($stmt = $mysqli->prepare($sqlDollar)) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $dollarCalculations = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis cálculos guardados</title>
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
                <li class="navbar__item"><a href="index.html" class="navbar__link">Inicio</a></li>
                <li class="navbar__item navbar__item--user">
                    <button class="user-menu-toggle" aria-label="Menú de usuario">
                        <span class="user-menu__icon"></span>
                        <span class="user-menu__label"><?php echo h($username); ?></span>
                    </button>
                    <ul class="user-menu">
                        <li class="user-menu__item"><a href="calculos.php" class="user-menu__link">Cálculos</a></li>
                        <li class="user-menu__item"><a href="logout.php" class="user-menu__link">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="container__section">
            <h2 class="section__title title--principal">Mis cálculos guardados</h2>
            <p class="section__description">
                Aquí podés ver una "instantánea" de los cálculos que guardaste usando las distintas herramientas.
            </p>
        </section>

        <?php if (empty($inflationCalculations) && empty($priceComparisons) && empty($dollarCalculations)): ?>
            <section class="container__section">
                <p class="section__description">Todavía no guardaste ningún cálculo.</p>
            </section>
        <?php endif; ?>

        <?php if (!empty($inflationCalculations)): ?>
            <section class="container__section">
                <h3 class="section__title">Cálculos de inflación</h3>
                <?php foreach ($inflationCalculations as $calc): ?>
                    <article class="section__description" style="border: 1px solid #bdc3c7; border-radius: 6px; padding: 1rem; margin-bottom: 1rem; background-color: #fff;">
                        <p><strong>Fecha del cálculo:</strong> <?php echo h($calc['created_at']); ?></p>
                        <p><strong>Monto inicial:</strong> $<?php echo h(number_format($calc['input_amount'], 2, ',', '.')); ?></p>
                        <p><strong>Desde:</strong> <?php echo h($calc['start_month']); ?>/<?php echo h($calc['start_year']); ?>
                           <strong>hasta</strong> <?php echo h($calc['end_month']); ?>/<?php echo h($calc['end_year']); ?></p>
                        <p><strong>Monto final equivalente:</strong> $<?php echo h(number_format($calc['result_amount'], 2, ',', '.')); ?></p>
                        <p><strong>Inflación acumulada:</strong> <?php echo h(number_format($calc['accumulated_inflation'], 2, ',', '.')); ?>%</p>
                        <p><strong>Promedio mensual:</strong> <?php echo h(number_format($calc['avg_monthly_inflation'], 2, ',', '.')); ?>%</p>
                        <p><strong>Promedio anual:</strong> <?php echo h(number_format($calc['avg_yearly_inflation'], 2, ',', '.')); ?>%</p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($priceComparisons)): ?>
            <section class="container__section">
                <h3 class="section__title">Comparaciones de precios</h3>
                <?php foreach ($priceComparisons as $comp): ?>
                    <article class="section__description" style="border: 1px solid #bdc3c7; border-radius: 6px; padding: 1rem; margin-bottom: 1rem; background-color: #fff;">
                        <p><strong>Fecha del cálculo:</strong> <?php echo h($comp['created_at']); ?></p>
                        <p><strong>Primer valor:</strong> $<?php echo h(number_format($comp['amount1'], 2, ',', '.')); ?>
                           (<?php echo h($comp['month1']); ?>/<?php echo h($comp['year1']); ?>)</p>
                        <p><strong>Segundo valor:</strong> $<?php echo h(number_format($comp['amount2'], 2, ',', '.')); ?>
                           (<?php echo h($comp['month2']); ?>/<?php echo h($comp['year2']); ?>)</p>
                        <p><strong>Primer valor ajustado por inflación:</strong> $<?php echo h(number_format($comp['adjusted_amount'], 2, ',', '.')); ?></p>
                        <p><strong>Diferencia del segundo valor frente al ajustado:</strong>
                            <?php echo h(number_format($comp['percentage_difference'], 2, ',', '.')); ?>%
                        </p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($dollarCalculations)): ?>
            <section class="container__section">
                <h3 class="section__title">Cálculos con dólar</h3>
                <?php foreach ($dollarCalculations as $d): ?>
                    <article class="section__description" style="border: 1px solid #bdc3c7; border-radius: 6px; padding: 1rem; margin-bottom: 1rem; background-color: #fff;">
                        <p><strong>Fecha del cálculo:</strong> <?php echo h($d['created_at']); ?></p>
                        <p><strong>Tipo de dólar:</strong>
                            <?php echo $d['dollar_type'] === 'official' ? 'Oficial' : 'Blue'; ?>
                        </p>
                        <p><strong>Monto en pesos:</strong> $<?php echo h(number_format($d['peso_amount'], 2, ',', '.')); ?></p>
                        <p><strong>Desde:</strong> <?php echo h($d['start_month']); ?>/<?php echo h($d['start_year']); ?>
                           <strong>hasta</strong> <?php echo h($d['end_month']); ?>/<?php echo h($d['end_year']); ?></p>
                        <p><strong>Equivalencia en USD (inicio):</strong>
                            $<?php echo h(number_format($d['start_usd_equivalent'], 2, ',', '.')); ?></p>
                        <p><strong>Equivalencia en USD (final):</strong>
                            $<?php echo h(number_format($d['end_usd_equivalent'], 2, ',', '.')); ?></p>
                        <p><strong>Variación porcentual:</strong>
                            <?php echo h(number_format($d['percentage_change'], 2, ',', '.')); ?>%
                        </p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <script src="js/app.js" type="module"></script>
</body>
</html>
