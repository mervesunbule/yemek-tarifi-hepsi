<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tüm Tarifler</title>
</head>
<body>
    <h1>Tüm Tarifler</h1>

    <a href="tarif_ekle.php">+ Yeni Tarif Ekle</a><br><br>

    <?php
    $sorgu = $veritabani->query("SELECT * FROM tarifler ORDER BY olusturma_tarihi DESC");
    foreach ($sorgu as $tarif) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
        echo "<h2>" . htmlspecialchars($tarif['baslik']) . "</h2>";
        if ($tarif['resim']) {
            echo "<img src='" . htmlspecialchars($tarif['resim']) . "' width='200'><br><br>";
        }
        echo "<p><b>Hazırlık Süresi:</b> " . htmlspecialchars($tarif['hazirlik_suresi']) . "</p>";
        echo "<p><b>Pişirme Süresi:</b> " . htmlspecialchars($tarif['pisirme_suresi']) . "</p>";
        echo "<a href='tarif_detay.php?id=" . $tarif['id'] . "'>Tarifi Görüntüle</a>";
        echo "</div>";
    }
    ?>
</body>
</html>