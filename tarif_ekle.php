<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';
// Kullanıcı giriş yapmış mı kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

// Belirli sabit kategoriler
$kategoriler = [
    1 => 'Tatlılar',
    2 => 'Ana Yemekler',
    3 => 'Çorbalar',
    4 => 'Atıştırmalıklar',
    5 => 'İçecekler'
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Ekle</title>
    <style>
        #resim-onizleme {
            max-width: 200px;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Yeni Tarif Ekle</h1>

    <form action="tarif_ekle_islem.php" method="post" enctype="multipart/form-data">
        <label>Kategori:</label><br>
        <select name="kategori_id[]" multiple size="5" required>
            <?php foreach ($kategoriler as $id => $ad): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($ad) ?></option>
            <?php endforeach; ?>
        </select><br>
        <small>Birden fazla kategori seçmek için Ctrl (Windows) veya Command (Mac) tuşunu kullanabilirsiniz.</small><br><br>

        <label>Başlık:</label><br>
        <input type="text" name="baslik" required><br><br>

        <label>Malzemeler:</label><br>
        <textarea name="malzemeler" rows="5" required></textarea><br><br>

        <label>Hazırlık Süresi (dakika):</label><br>
        <input type="number" name="hazirlik_suresi" min="0" required><br><br>

        <label>Pişirme Süresi (dakika):</label><br>
        <input type="number" name="pisirme_suresi" min="0" required><br><br>

        <label>Yapılış Aşamaları:</label><br>
        <textarea name="yapilis_asamalari" rows="8" required></textarea><br><br>

        <label>Tarif Resmi (isteğe bağlı):</label><br>
        <input type="file" name="tarif_resim" accept="image/*" onchange="resimOnizle(this)"><br><br>

        <img id="resim-onizleme" src="#" alt="Seçilen Resim Önizlemesi" style="display: none;">

        <button type="submit">Tarifi Ekle</button>
    </form>

    <script>
        function resimOnizle(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('resim-onizleme').src = e.target.result;
                    document.getElementById('resim-onizleme').style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>