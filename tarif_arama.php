<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';
$sonuclar = [];

if (isset($_GET['arama'])) {
    $arama = trim($_GET['arama']);
    if (!empty($arama)) {
        $stmt = $veritabani->prepare("
            SELECT t.id, t.baslik 
            FROM tarifler t
            LEFT JOIN tarif_kategori_iliskisi tk ON t.id = tk.tarif_id
            LEFT JOIN kategoriler k ON tk.kategori_id = k.id
            WHERE t.baslik ILIKE :arama OR t.malzemeler ILIKE :arama OR k.ad ILIKE :arama
            GROUP BY t.id
        ");
        $stmt->execute([':arama' => "%$arama%"]);
        $sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Arama</title>
</head>
<body>
    <h1>Tarif Arama</h1>
    <form method="GET" action="tarif_arama.php">
        <input type="text" name="arama" placeholder="Tarif Ara..." required>
        <button type="submit">Ara</button>
    </form>

    <?php if (!empty($sonuclar)): ?>
        <h2>Sonuçlar:</h2>
        <ul>
            <?php foreach ($sonuclar as $tarif): ?>
                <li><a href="tarif_detay.php?id=<?php echo $tarif['id']; ?>"><?php echo htmlspecialchars($tarif['baslik']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (isset($_GET['arama'])): ?>
        <p>Sonuç bulunamadı.</p>
    <?php endif; ?>
</body>
</html>