<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
// Veritabanı bağlantısını çağırıyoruz
//require_once 'veritabani.php';
include '../db.php';

// Formdan gelen verileri alıyoruz
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kullanıcıdan gelen bilgileri al
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Şifreyi güvenli hale getiriyoruz (hashliyoruz)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Kullanıcıyı veritabanına ekle
        $stmt = $conn->prepare("INSERT INTO kullanicilar (kullanici_adi, eposta, sifre) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);

        $stmt->execute();

        echo "Kayıt başarılı!";
        echo "<p><a href='giris.php'>Giriş Yapmak İçin Tıklayın</a></p>";
    } catch (PDOException $e) {
        echo "Kayıt sırasında hata oluştu: " . $e->getMessage();
        echo "<p><a href='kayit.php'>Geri Dön</a></p>";
    }
} else {
    // Sayfaya doğrudan erişildiyse kayıt formuna yönlendir
    header("Location: kayit.php");
    exit();
}
?>