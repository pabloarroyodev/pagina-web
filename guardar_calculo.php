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
$type = isset($data['type']) ? trim($data['type']) : '';

if ($type !== 'inflation') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de cálculo no soportado']);
    exit;
}

$startAmount = isset($data['startAmount']) ? (float) $data['startAmount'] : null;
$startYear = isset($data['startYear']) ? (int) $data['startYear'] : null;
$startMonth = isset($data['startMonth']) ? (int) $data['startMonth'] : null;
$endYear = isset($data['endYear']) ? (int) $data['endYear'] : null;
$endMonth = isset($data['endMonth']) ? (int) $data['endMonth'] : null;
$endAmount = isset($data['endAmount']) ? (float) $data['endAmount'] : null;
$accumulatedInflation = isset($data['accumulatedInflation']) ? (float) $data['accumulatedInflation'] : null;
$averageMonthlyInflation = isset($data['averageMonthlyInflation']) ? (float) $data['averageMonthlyInflation'] : null;
$averageYearlyInflation = isset($data['averageYearlyInflation']) ? (float) $data['averageYearlyInflation'] : null;

if ($startAmount === null || $startYear === null || $startMonth === null ||
    $endYear === null || $endMonth === null || $endAmount === null ||
    $accumulatedInflation === null || $averageMonthlyInflation === null ||
    $averageYearlyInflation === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos del cálculo']);
    exit;
}

$sql = 'INSERT INTO calculations (
            user_id, type, input_amount,
            start_year, start_month, end_year, end_month,
            result_amount, accumulated_inflation,
            avg_monthly_inflation, avg_yearly_inflation
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    exit;
}

$stmt->bind_param(
    'isdiiiidddd',
    $userId,                 // i: user_id
    $type,                   // s: type
    $startAmount,            // d: input_amount
    $startYear,              // i: start_year
    $startMonth,             // i: start_month
    $endYear,                // i: end_year
    $endMonth,               // i: end_month
    $endAmount,              // d: result_amount
    $accumulatedInflation,   // d: accumulated_inflation
    $averageMonthlyInflation,// d: avg_monthly_inflation
    $averageYearlyInflation  // d: avg_yearly_inflation
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el cálculo',
        'error'   => $stmt->error
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(['success' => true, 'message' => 'Cálculo guardado correctamente']);
