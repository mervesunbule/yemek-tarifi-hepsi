<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO kullanicilar (kullanici_adi, eposta, sifre) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        echo "<p style='color:green;'>Kayıt başarılı!</p>";
        echo "<p><a href='giris.php'>Giriş Yapmak İçin Tıklayın</a></p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Hata: " . $e->getMessage() . "</p>";
        echo "<p><a href='kayit.php'>Geri Dön</a></p>";
    }
} else {
    header("Location: kayit.php");
    exit();
}
?>
