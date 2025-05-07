<?php
session_start();
require_once 'veritabani.php';

// Eğer kullanıcı giriş yapmadıysa, girişe yönlendir
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

// Seçilen kategori ID'sini al
$secilenKategoriId = isset($_GET['kategori_id']) ? $_GET['kategori_id'] : '';

// Arama kelimesini al
$aramaKelimesi = isset($_GET['arama']) ? trim($_GET['arama']) : '';

// Kategorileri statik olarak tanımla
$statikKategoriler = [
    'tatlilar' => 'Tatlılar',
    'ana_yemekler' => 'Ana Yemekler',
    'corbalar' => 'Çorbalar',
    'atistirmaliklar' => 'Atıştırmalıklar',
    'icecekler' => 'İçecekler'
];

// Tarifleri veritabanından çek
$sql = "SELECT t.id, t.baslik, t.resim_url, t.kullanici_id,
               (SELECT STRING_AGG(k.ad, ', ')
                FROM tarif_kategori_iliskisi tki
                INNER JOIN kategoriler k ON tki.kategori_id = k.id
                WHERE tki.tarif_id = t.id) AS kategoriler_str
        FROM tarifler t
        WHERE 1=1";

if (!empty($secilenKategoriId) && isset($statikKategoriler[$secilenKategoriId])) {
    $sql .= " AND t.id IN (
                SELECT tki.tarif_id 
                FROM tarif_kategori_iliskisi tki
                INNER JOIN kategoriler k ON tki.kategori_id = k.id
                WHERE k.ad = :kategori_adi
            )";
}

if (!empty($aramaKelimesi)) {
    $sql .= " AND t.baslik ILIKE :arama"; // PostgreSQL için ILIKE
}

$sql .= " ORDER BY t.eklenme_tarihi DESC";

try {
    $stmtTarif = $veritabani->prepare($sql);
    
    if (!empty($secilenKategoriId) && isset($statikKategoriler[$secilenKategoriId])) {
        $stmtTarif->bindParam(':kategori_adi', $statikKategoriler[$secilenKategoriId]);
    }
    
    if (!empty($aramaKelimesi)) {
        $aramaKelimesi = "%$aramaKelimesi%";
        $stmtTarif->bindParam(':arama', $aramaKelimesi);
    }
    
    $stmtTarif->execute();
    $tarifler = $stmtTarif->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Tarifleri listelerken hata oluştu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Listesi</title>
    <style>
        .tarif-listesi-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .tarif-listesi-item img {
            width: 80px;
            height: auto;
        }
        .tarif-yonetim {
            margin-left: auto;
        }
        form.inline {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>Tarif Listesi</h1>

    <div>
        <form method="get">
            <label for="kategori_id">Kategoriler:</label>
            <select name="kategori_id" id="kategori_id" onchange="this.form.submit()">
                <option value="">Tüm Kategoriler</option>
                <?php foreach ($statikKategoriler as $key => $kategoriAdi): ?>
                    <option value="<?php echo $key; ?>" <?php if ($secilenKategoriId === $key) echo 'selected'; ?>>
                        <?php echo $kategoriAdi; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <form method="get" style="margin-top:10px;">
            <input type="text" name="arama" placeholder="Tarif Ara" value="<?php echo htmlspecialchars(isset($_GET['arama']) ? $_GET['arama'] : ''); ?>">
            <button type="submit">Ara</button>
        </form>
    </div>

    <?php if (empty($tarifler)): ?>
        <p>Henüz tarif bulunamadı.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tarifler as $tarif): ?>
                <li class="tarif-listesi-item">
                    <a href="tarif_detay.php?id=<?php echo $tarif['id']; ?>">
                        <?php echo htmlspecialchars($tarif['baslik']); ?>
                    </a>
                    <?php if (!empty($tarif['resim_url'])): ?>
                        <img src="<?php echo htmlspecialchars($tarif['resim_url']); ?>" alt="<?php echo htmlspecialchars($tarif['baslik']); ?> resmi">
                    <?php endif; ?>
                    <?php if (!empty($tarif['kategoriler_str'])): ?>
                        <span>(Kategoriler: <?php echo htmlspecialchars($tarif['kategoriler_str']); ?>)</span>
                    <?php endif; ?>

                    <?php if ($tarif['kullanici_id'] == $kullanici_id): ?>
                        <div class="tarif-yonetim">
                            <a href="tarif_duzenle.php?id=<?php echo $tarif['id']; ?>">🛠️ Düzenle</a>
                            <form action="tarif_sil.php" method="post" class="inline" onsubmit="return confirm('Bu tarifi silmek istediğinizden emin misiniz?');">
                                <input type="hidden" name="id" value="<?php echo $tarif['id']; ?>">
                                <button type="submit">❌ Sil</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <br>
    <a href="anasayfa.php">Anasayfaya Dön</a>
</body>
</html>