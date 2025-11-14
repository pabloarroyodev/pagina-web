<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Completa usuario y contraseña.'));
    exit;
}

$sql = 'SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1';
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die('Error en la consulta: ' . $mysqli->error);
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password_hash'])) {
    header('Location: login.php?error=' . urlencode('Usuario o contraseña incorrectos.'));
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

if ($user['role'] === 'admin') {
    header('Location: admin.php');
} else {
    header('Location: index.html');
}
exit;
