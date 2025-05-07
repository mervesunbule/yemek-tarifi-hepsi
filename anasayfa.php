<?php
session_start();
require_once 'veritabani.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['kullanici_id'])) {
    header('Location: giris.php');
    exit();
}

// Kullanıcı adı
$kullanici_adi = $_SESSION['kullanici_adi'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Anasayfa</title>
</head>
<body>

<h1>Hoş geldin, <?php echo htmlspecialchars($kullanici_adi); ?>!</h1>

<ul>
    <li><a href="tarif_ekle.php">➕ Tarif Ekle</a></li>
    <li><a href="tarif_listesi.php">📋 Tarifleri Görüntüle</a></li>
    <li><a href="favoriler.php">❤️ Favorilerim</a></li>
    <li><a href="cikis.php">🚪 Çıkış Yap</a></li>
</ul>

</body>
</html>