<?php
session_start();

// Veritabanı bağlantısını ekle
include '../db.php';

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Şifreyi güvenli hale getir
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Aynı e-posta ile daha önce kayıt yapılmış mı kontrol et
        // "eposta" alanını kullanın
        $check = $conn->prepare("SELECT id FROM kullanicilar WHERE eposta = :email");
        $check->execute(['email' => $email]);
        if ($check->rowCount() > 0) {
            echo "Bu e-posta zaten kayıtlı!";
        } else {
            // Yeni kullanıcıyı ekle
            // "kullanici_adi", "eposta", "sifre" ve "admin_mi" alanlarını kullanın
            $sql = "INSERT INTO kullanicilar (kullanici_adi, eposta, sifre, admin_mi) VALUES (:username, :email, :password, 0)"; // Varsayılan olarak admin_mi = 0 (kullanıcı)
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
            ]);

            echo "✅ Kayıt başarılı! Giriş yapmak için <a href='login.php'>buraya tıklayın</a>.";
        }
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
</head>
<body>
    <h2>Kayıt Formu</h2>
    <form method="POST" action="">
        <label>Kullanıcı Adı:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Şifre:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Kayıt Ol">
    </form>

    <br>
    <a href="login.php">Zaten hesabınız var mı? Giriş yap</a>
</body>
</html>

