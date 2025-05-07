<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

// Kullanıcı giriş yapmış mı kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

try {
    // Kullanıcının favori tariflerini veritabanından çek
    $stmtFavoriler = $veritabani->prepare("SELECT t.* FROM favoriler f INNER JOIN tarifler t ON f.tarif_id = t.id WHERE f.kullanici_id = :kullanici_id ORDER BY f.eklenme_tarihi DESC");
    $stmtFavoriler->bindParam(':kullanici_id', $kullanici_id, PDO::PARAM_INT);
    $stmtFavoriler->execute();
    $favoriTarifler = $stmtFavoriler->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Favori tarifleri alırken hata oluştu: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favori Tariflerim</title>
    <style>
        .tarif-listesi { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .tarif-karti { border: 1px solid #ccc; padding: 15px; }
        .tarif-baslik { font-size: 1.2em; margin-bottom: 5px; }
        .tarif-resim { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <h1>Favori Tariflerim</h1>

    <?php if (!empty($favoriTarifler)): ?>
        <div class="tarif-listesi">
            <?php foreach ($favoriTarifler as $tarif): ?>
                <div class="tarif-karti">
                    <h2 class="tarif-baslik"><a href="tarif_detay.php?id=<?php echo $tarif['id']; ?>"><?php echo htmlspecialchars($tarif['baslik']); ?></a></h2>
                    <?php if (!empty($tarif['resim_url'])): ?>
                        <img src="<?php echo $tarif['resim_url']; ?>" alt="<?php echo htmlspecialchars($tarif['baslik']); ?>" class="tarif-resim" onerror="this.style.display='none'">
                    <?php else: ?>
                        <p>Bu tarif için resim bulunmuyor.</p>
                    <?php endif; ?>
                    <p><strong>Hazırlık Süresi:</strong> <?php echo $tarif['hazirlik_suresi']; ?> dakika</p>
                    <p><strong>Pişirme Süresi:</strong> <?php echo $tarif['pisirme_suresi']; ?> dakika</p>
                    </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Henüz favorilerinize hiçbir tarif eklemediniz.</p>
    <?php endif; ?>

    <br>
    <a href="anasayfa.php">Anasayfaya Dön</a>
</body>
</html>