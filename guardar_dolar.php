<?php
session_start();
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No hay sesión iniciada']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$dollarType = isset($data['dollarType']) ? trim($data['dollarType']) : '';

if ($dollarType !== 'blue' && $dollarType !== 'official') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de dólar no válido']);
    exit;
}

$pesoAmount = isset($data['pesoAmount']) ? (float) $data['pesoAmount'] : null;
$startYear = isset($data['startYear']) ? (int) $data['startYear'] : null;
$startMonth = isset($data['startMonth']) ? (int) $data['startMonth'] : null;
$endYear = isset($data['endYear']) ? (int) $data['endYear'] : null;
$endMonth = isset($data['endMonth']) ? (int) $data['endMonth'] : null;
$startUsdEquivalent = isset($data['startUsdEquivalent']) ? (float) $data['startUsdEquivalent'] : null;
$endUsdEquivalent = isset($data['endUsdEquivalent']) ? (float) $data['endUsdEquivalent'] : null;
$percentageChange = isset($data['percentageChange']) ? (float) $data['percentageChange'] : null;

if ($pesoAmount === null || $startYear === null || $startMonth === null ||
    $endYear === null || $endMonth === null || $startUsdEquivalent === null ||
    $endUsdEquivalent === null || $percentageChange === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos del cálculo']);
    exit;
}

$sql = 'INSERT INTO dollar_calculations (
            user_id, dollar_type,
            peso_amount,
            start_year, start_month,
            end_year, end_month,
            start_usd_equivalent,
            end_usd_equivalent,
            percentage_change
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    exit;
}

$stmt->bind_param(
    'isdiididdd',
    $userId,
    $dollarType,
    $pesoAmount,
    $startYear,
    $startMonth,
    $endYear,
    $endMonth,
    $startUsdEquivalent,
    $endUsdEquivalent,
    $percentageChange
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el cálculo de dólar',
        'error'   => $stmt->error
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(['success' => true, 'message' => 'Cálculo de dólar guardado correctamente']);
