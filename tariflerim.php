<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

$stmt = $veritabani->prepare("SELECT * FROM tarifler WHERE kullanici_id = :kullanici_id");
$stmt->execute([':kullanici_id' => $kullanici_id]);
$tariflerim = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tariflerim</title>
</head>
<body>
    <h1>Tariflerim</h1>

    <?php if (!empty($tariflerim)): ?>
        <ul>
            <?php foreach ($tariflerim as $tarif): ?>
                <li>
                    <?php echo htmlspecialchars($tarif['baslik']); ?>
                    - <a href="tarif_guncelle.php?id=<?php echo $tarif['id']; ?>">Güncelle</a>
                    - <a href="tarif_sil.php?id=<?php echo $tarif['id']; ?>" onclick="return confirm('Bu tarifi silmek istediğinize emin misiniz?');">Sil</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Henüz tarif eklemediniz.</p>
    <?php endif; ?>
</body>
</html>