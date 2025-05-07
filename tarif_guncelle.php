<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Geçersiz istek.";
    exit;
}

$tarif_id = intval($_GET['id']);
$kullanici_id = $_SESSION['kullanici_id'];

$stmt = $veritabani->prepare("SELECT * FROM tarifler WHERE id = :id AND kullanici_id = :kullanici_id");
$stmt->execute([':id' => $tarif_id, ':kullanici_id' => $kullanici_id]);
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    echo "Tarif bulunamadı.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Güncelle</title>
</head>
<body>
    <h1>Tarifi Güncelle</h1>
    <form action="tarif_guncelle_islem.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $tarif['id']; ?>">

        <label>Başlık:</label><br>
        <input type="text" name="baslik" value="<?php echo htmlspecialchars($tarif['baslik']); ?>" required><br><br>

        <label>Malzemeler:</label><br>
        <textarea name="malzemeler" required><?php echo htmlspecialchars($tarif['malzemeler']); ?></textarea><br><br>

        <label>Hazırlık Süresi (dakika):</label><br>
        <input type="number" name="hazirlik_suresi" value="<?php echo htmlspecialchars($tarif['hazirlik_suresi']); ?>" min="0" required><br><br>

        <label>Pişirme Süresi (dakika):</label><br>
        <input type="number" name="pisirme_suresi" value="<?php echo htmlspecialchars($tarif['pisirme_suresi']); ?>" min="0" required><br><br>

        <label>Yapılış Aşamaları:</label><br>
        <textarea name="yapilis_asamalari" required><?php echo htmlspecialchars($tarif['yapilis_asamalari']); ?></textarea><br><br>

        <button type="submit">Güncelle</button>
    </form>
</body>
</html>