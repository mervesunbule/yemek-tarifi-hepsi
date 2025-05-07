<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Bu sayfayı görüntülemek için giriş yapmalısınız.";
    exit;
}

$kullanici_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$stmt = $db->prepare("SELECT kullanici_adi, eposta, profil_resmi, sifre FROM kullanicilar WHERE id = :id");
$stmt->execute(['id' => $kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

// Hata veya başarı mesajı için değişken
$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $_POST['kullanici_adi'];
    $eposta = $_POST['eposta'];

    // Profil resmi işlemi
    if (isset($_FILES['profil_resmi']) && $_FILES['profil_resmi']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["profil_resmi"]["name"]);
        move_uploaded_file($_FILES["profil_resmi"]["tmp_name"], $target_file);
        $profil_resmi_yolu = basename($_FILES["profil_resmi"]["name"]);
    } else {
        $profil_resmi_yolu = $kullanici['profil_resmi']; // önceki resmi koru
    }

    // Kullanıcı adı ve eposta güncelle
    $stmt = $db->prepare("UPDATE kullanicilar SET kullanici_adi = :kullanici_adi, eposta = :eposta, profil_resmi = :profil_resmi WHERE id = :id");
    $stmt->execute([
        'kullanici_adi' => $kullanici_adi,
        'eposta' => $eposta,
        'profil_resmi' => $profil_resmi_yolu,
        'id' => $kullanici_id
    ]);

    // Şifre değiştirme işlemi
    if (!empty($_POST['eski_sifre']) && !empty($_POST['yeni_sifre'])) {
        if (password_verify($_POST['eski_sifre'], $kullanici['sifre'])) {
            $yeni_sifre_hash = password_hash($_POST['yeni_sifre'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE kullanicilar SET sifre = :sifre WHERE id = :id");
            $stmt->execute(['sifre' => $yeni_sifre_hash, 'id' => $kullanici_id]);
            $mesaj = "Profil ve şifre başarıyla güncellendi.";
        } else {
            $mesaj = "Eski şifre yanlış!";
        }
    } else {
        $mesaj = "Profil güncellendi.";
    }

    // Bilgileri tekrar çek
    $stmt = $db->prepare("SELECT kullanici_adi, eposta, profil_resmi FROM kullanicilar WHERE id = :id");
    $stmt->execute(['id' => $kullanici_id]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profili Düzenle</title>
</head>
<body>
    <h2>Profili Düzenle</h2>

    <?php if ($mesaj): ?>
        <p><strong><?= htmlspecialchars($mesaj) ?></strong></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Kullanıcı Adı:</label><br>
        <input type="text" name="kullanici_adi" value="<?= htmlspecialchars($kullanici['kullanici_adi']) ?>" required><br><br>

        <label>E-Posta:</label><br>
        <input type="email" name="eposta" value="<?= htmlspecialchars($kullanici['eposta']) ?>" required><br><br>

        <label>Profil Resmi:</label><br>
        <?php if ($kullanici['profil_resmi']): ?>
            <img src="../uploads/<?= htmlspecialchars($kullanici['profil_resmi']) ?>" width="100" alt="Profil Resmi"><br>
        <?php endif; ?>
        <input type="file" name="profil_resmi"><br><br>

        <h3>Şifre Değiştir (İsteğe Bağlı)</h3>
        <label>Eski Şifre:</label><br>
        <input type="password" name="eski_sifre"><br><br>

        <label>Yeni Şifre:</label><br>
        <input type="password" name="yeni_sifre"><br><br>

        <button type="submit">Güncelle</button>
    </form>

    <p><a href="profile.php">Geri Dön</a></p>
</body>
</html>
