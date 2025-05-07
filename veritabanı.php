<?php
$host = 'localhost';    // Sunucu
$port = '5433';         // PostgreSQL portu
$dbname = 'yemek_tarifi'; // Veritabanı adı
$user = 'postgres';     // Kullanıcı adı
$password = '12345678'; // Şifre

try {
    $veritabani = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $veritabani->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    exit;
}
?>