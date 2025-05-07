<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

// URL'den tarif ID'sini al
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tarif_id = $_GET['id'];

    try {
        // Tarifi ve kategorilerini veritabanından çek
        $stmt = $veritabani->prepare("SELECT t.*, STRING_AGG(k.ad, ', ') AS kategoriler_str FROM tarifler t INNER JOIN tarif_kategori_iliskisi tki ON t.id = tki.tarif_id INNER JOIN kategoriler k ON tki.kategori_id = k.id WHERE t.id = :id GROUP BY t.id");
        $stmt->bindParam(':id', $tarif_id, PDO::PARAM_INT);
        $stmt->execute();
        $tarif = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tarif) {
            echo "Tarif bulunamadı.";
            exit;
        }

        // Kullanıcının bu tarifi favorilerine ekleyip eklemediğini kontrol et
        $favoriDurumu = false;
        if (isset($_SESSION['kullanici_id'])) {
            $stmtFavoriKontrol = $veritabani->prepare("SELECT 1 FROM favoriler WHERE kullanici_id = :kullanici_id AND tarif_id = :tarif_id");
            $stmtFavoriKontrol->bindParam(':kullanici_id', $_SESSION['kullanici_id'], PDO::PARAM_INT);
            $stmtFavoriKontrol->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
            $stmtFavoriKontrol->execute();
            $favoriDurumu = $stmtFavoriKontrol->fetchColumn();
        }

        // Tarifin yorumlarını ve ortalama puanını al
        $stmtYorumlar = $veritabani->prepare("SELECT y.*, k.kullanici_adi FROM yorumlar y INNER JOIN kullanicilar k ON y.kullanici_id = k.id WHERE y.tarif_id = :tarif_id ORDER BY y.yorum_tarihi DESC");
        $stmtYorumlar->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
        $stmtYorumlar->execute();
        $yorumlar = $stmtYorumlar->fetchAll(PDO::FETCH_ASSOC);

        $stmtOrtalamaPuan = $veritabani->prepare("SELECT AVG(puan) AS ortalama_puan FROM yorumlar WHERE tarif_id = :tarif_id");
        $stmtOrtalamaPuan->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
        $stmtOrtalamaPuan->execute();
        $ortalamaPuan = $stmtOrtalamaPuan->fetch(PDO::FETCH_ASSOC)['ortalama_puan'];

    } catch (PDOException $e) {
        echo "Tarif detaylarını alırken hata oluştu: " . $e->getMessage();
        exit;
    }

} else {
    echo "Geçersiz tarif ID'si.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $tarif['baslik']; ?></title>
    <style>
        .tarif-detay-container { display: flex; gap: 20px; }
        .tarif-resim { max-width: 300px; height: auto; }
        .tarif-bilgiler { flex-grow: 1; }
        .yorum { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .favori-button { cursor: pointer; }
    </style>
</head>
<body>
    <h1><?php echo $tarif['baslik']; ?></h1>

    <div class="tarif-detay-container">
        <?php if (!empty($tarif['resim_url'])): ?>
           <img src="<?php echo $tarif['resim_url']; ?>" alt="<?php echo $tarif['baslik']; ?> resmi" class="tarif-resim" onerror="this.style.display='none'">
        <?php else: ?>
            <p>Bu tarif için resim bulunmuyor.</p>
        <?php endif; ?>

        <div class="tarif-bilgiler">
            <?php if (!empty($tarif['kategoriler_str'])): ?>
                <p><strong>Kategoriler:</strong> <?php echo $tarif['kategoriler_str']; ?></p>
            <?php endif; ?>
            <p><strong>Malzemeler:</strong><br><?php echo nl2br($tarif['malzemeler']); ?></p>
            <p><strong>Hazırlık Süresi:</strong> <?php echo $tarif['hazirlik_suresi']; ?> dakika</p>
            <p><strong>Pişirme Süresi:</strong> <?php echo $tarif['pisirme_suresi']; ?> dakika</p>
            <p><strong>Yapılış Aşamaları:</strong><br><?php echo nl2br($tarif['yapilis_asamalari']); ?></p>
            <p><strong>Ortalama Puan:</strong> <?php echo number_format($ortalamaPuan, 2); ?></p>

            <?php if (isset($_SESSION['kullanici_id'])): ?>
                <button class="favori-button" onclick="favoriIslem(<?php echo $tarif_id; ?>, <?php echo $favoriDurumu ? 'false' : 'true'; ?>, this)">
                    <?php echo $favoriDurumu ? 'Favorilerden Çıkar' : 'Favorilere Ekle'; ?>
                </button>

                <h3>Yorum Yap</h3>
                <form id="yorumForm">
                    <input type="hidden" name="tarif_id" value="<?php echo $tarif_id; ?>">
                    <textarea name="yorum_metni" rows="4" cols="50" required></textarea><br><br>
                    <label for="puan">Puan (1-5):</label>
                    <select name="puan" id="puan">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select><br><br>
                    <button type="button" onclick="yorumYap()">Yorum Gönder</button>
                </form>
            <?php else: ?>
                <p><a href="giris.php">Giriş yaparak</a> favorilere ekleyebilir ve yorum yapabilirsiniz.</p>
            <?php endif; ?>
        </div>
    </div>

    <h3>Yorumlar</h3>
    <?php if (!empty($yorumlar)): ?>
        <?php foreach ($yorumlar as $yorum): ?>
            <div class="yorum">
                <p><strong><?php echo htmlspecialchars($yorum['kullanici_adi']); ?></strong> (<?php echo $yorum['puan']; ?> puan) - <?php echo $yorum['yorum_tarihi']; ?>
                    <?php if (isset($_SESSION['kullanici_id']) && $_SESSION['kullanici_id'] == $yorum['kullanici_id']): ?>
                        <button onclick="yorumSil(<?php echo $yorum['id']; ?>)">Sil</button>
                    <?php endif; ?>
                </p>
                <p><?php echo htmlspecialchars($yorum['yorum_metni']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Bu tarif için henüz yorum yapılmamış.</p>
    <?php endif; ?>

    <br>
    <a href="tarif_listesi.php">Tarif Listesine Dön</a>

    <script>
        function favoriIslem(tarifId, ekle, button) {
            fetch('favori_islem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tarif_id=${tarifId}&ekle=${ekle}`
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'eklendi') {
                    button.textContent = 'Favorilerden Çıkar';
                    button.onclick = function() { favoriIslem(tarifId, false, this); };
                } else if (data === 'cikarildi') {
                    button.textContent = 'Favorilere Ekle';
                    button.onclick = function() { favoriIslem(tarifId, true, this); };
                } else {
                    alert(data); // Hata mesajı veya başka bir çıktı
                }
            })
            .catch(error => console.error('Favori işlemi hatası:', error));
        }

        function yorumYap() {
            const form = document.getElementById('yorumForm');
            const formData = new FormData(form);

            fetch('yorum_yap.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Yorumun başarılı olup olmadığına dair mesaj
                window.location.reload(); // Basitçe sayfayı yenileyelim.
            })
            .catch(error => console.error('Yorum yapma hatası:', error));
        }

        function yorumSil(yorumId) {
            if (confirm('Bu yorumu silmek istediğinizden emin misiniz?')) {
                fetch('yorum_sil.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `yorum_id=${yorumId}`
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Silme işleminin sonucuna dair mesaj
                    window.location.reload(); // Sayfayı yenileyerek yorum listesini güncelle
                })
                .catch(error => console.error('Yorum silme hatası:', error));
            }
        }
    </script>
</body>
</html>