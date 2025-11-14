<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'user';

if ($username === '' || $password === '') {
    header('Location: admin.php?msg=' . urlencode('Usuario y contraseña son obligatorios.'));
    exit;
}

if ($role !== 'admin' && $role !== 'user') {
    $role = 'user';
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sql = 'INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,?)';
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die('Error en la consulta: ' . $mysqli->error);
}

$stmt->bind_param('ssss', $username, $email, $passwordHash, $role);

if ($stmt->execute()) {
    $msg = 'Usuario creado correctamente.';
} else {
    $msg = 'Error al crear usuario: ' . $stmt->error;
}
$stmt->close();

header('Location: admin.php?msg=' . urlencode($msg));
exit;
