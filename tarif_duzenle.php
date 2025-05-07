<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

// Kullanıcı girişi kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

// Tarif ID kontrolü
if (!isset($_GET['id'])) {
    header("Location: tarif_listesi.php");
    exit;
}

$tarif_id = (int)$_GET['id'];
$kullanici_id = $_SESSION['kullanici_id'];

// Tarif bilgilerini çek
$sorgu = $veritabani->prepare("SELECT * FROM tarifler WHERE id = :id AND kullanici_id = :kullanici_id");
$sorgu->execute([
    'id' => $tarif_id,
    'kullanici_id' => $kullanici_id
]);
$tarif = $sorgu->fetch(PDO::FETCH_ASSOC);

// Eğer tarif bulunamazsa
if (!$tarif) {
    echo "Bu tarifi düzenleyemezsiniz.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Düzenle</title>
</head>
<body>
    <h1>Tarif Düzenle</h1>

    <form action="tarif_guncelle_islem.php" method="post">
        <input type="hidden" name="id" value="<?php echo $tarif['id']; ?>">

        <label>Başlık:</label><br>
        <input type="text" name="baslik" value="<?php echo htmlspecialchars($tarif['baslik']); ?>" required><br><br>

        <label>Malzemeler:</label><br>
        <textarea name="malzemeler" required><?php echo htmlspecialchars($tarif['malzemeler']); ?></textarea><br><br>

        <label>Hazırlık Süresi:</label><br>
        <input type="text" name="hazirlik_suresi" value="<?php echo htmlspecialchars($tarif['hazirlik_suresi']); ?>" required><br><br>

        <label>Pişirme Süresi:</label><br>
        <input type="text" name="pisirme_suresi" value="<?php echo htmlspecialchars($tarif['pisirme_suresi']); ?>" required><br><br>

        <label>Yapılış Aşamaları:</label><br>
        <textarea name="yapilis_asamalari" required><?php echo htmlspecialchars($tarif['yapilis_asamalari']); ?></textarea><br><br>

        <button type="submit">Güncelle</button>
    </form>

    <br>
    <a href="tarif_listesi.php">Geri Dön</a>
</body>
</html>