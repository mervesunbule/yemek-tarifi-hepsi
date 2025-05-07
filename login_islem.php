<?php
session_start();
header("Content-Type: application/json");
include '../db.php'; // db.php içinde $db tanımlı olmalı

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek.'
    ]);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Tüm alanları doldurun.'
    ]);
    exit;
}

$sql = "SELECT * FROM kullanicilar WHERE eposta = :email";
$stmt = $db->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['kullanici_adi'];
    $_SESSION['role'] = $user['role'];

    $redirect = ($user['role'] === 'admin') ? "admin_panel.php" : "profile.php";

    echo json_encode([
        'success' => true,
        'message' => 'Giriş başarılı.',
        'redirect' => $redirect
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Hatalı email veya şifre!'
    ]);
}