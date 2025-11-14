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

$amount1 = isset($data['amount1']) ? (float) $data['amount1'] : null;
$year1 = isset($data['year1']) ? (int) $data['year1'] : null;
$month1 = isset($data['month1']) ? (int) $data['month1'] : null;
$amount2 = isset($data['amount2']) ? (float) $data['amount2'] : null;
$year2 = isset($data['year2']) ? (int) $data['year2'] : null;
$month2 = isset($data['month2']) ? (int) $data['month2'] : null;
$adjustedAmount = isset($data['adjustedAmount']) ? (float) $data['adjustedAmount'] : null;
$percentageDifference = isset($data['percentageDifference']) ? (float) $data['percentageDifference'] : null;

if ($amount1 === null || $amount2 === null || $year1 === null || $year2 === null ||
    $month1 === null || $month2 === null || $adjustedAmount === null ||
    $percentageDifference === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos del cálculo']);
    exit;
}

$sql = 'INSERT INTO price_comparison_calculations (
            user_id,
            amount1, year1, month1,
            amount2, year2, month2,
            adjusted_amount,
            percentage_difference
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    exit;
}

$stmt->bind_param(
    'ididididd',
    $userId,
    $amount1,
    $year1,
    $month1,
    $amount2,
    $year2,
    $month2,
    $adjustedAmount,
    $percentageDifference
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el cálculo de comparación',
        'error'   => $stmt->error
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(['success' => true, 'message' => 'Cálculo de comparación guardado correctamente']);
