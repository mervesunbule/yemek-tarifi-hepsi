_<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($veritabani)) {
        $kullanici_id = $_SESSION['kullanici_id'];
        $kategori_ids = $_POST['kategori_id'] ?? [];
        $baslik = trim($_POST['baslik'] ?? '');
        $malzemeler = trim($_POST['malzemeler'] ?? '');
        $hazirlik_suresi = isset($_POST['hazirlik_suresi']) ? intval($_POST['hazirlik_suresi']) : 0;
        $pisirme_suresi = isset($_POST['pisirme_suresi']) ? intval($_POST['pisirme_suresi']) : 0;
        $yapilis_asamalari = trim($_POST['yapilis_asamalari'] ?? '');
        $resim_url = '';

        // Gerekli alan kontrolü
        if (empty($kategori_ids) || empty($baslik) || empty($malzemeler) || empty($yapilis_asamalari)) {
            echo "Lütfen tüm gerekli alanları doldurun ve en az bir kategori seçin.";
            echo "<p><a href='tarif_ekle.php'>Geri Dön</a></p>";
            exit;
        }

        // Resim yükleme işlemi
        if (isset($_FILES['tarif_resim']) && $_FILES['tarif_resim']['error'] == 0) {
            $hedef_klasor = "uploads/";
            if (!file_exists($hedef_klasor)) {
                mkdir($hedef_klasor, 0777, true);
            }

            $dosya_adi = basename($_FILES["tarif_resim"]["name"]);
            $hedef_yol = $hedef_klasor . uniqid() . "_" . $dosya_adi;

            if (move_uploaded_file($_FILES["tarif_resim"]["tmp_name"], $hedef_yol)) {
                $resim_url = $hedef_yol;
            }
        }

        try {
            // Tarif ekle
            $stmtTarif = $veritabani->prepare("INSERT INTO tarifler (kullanici_id, baslik, malzemeler, hazirlik_suresi, pisirme_suresi, yapilis_asamalari, resim_url, eklenme_tarihi) 
            VALUES (:kullanici_id, :baslik, :malzemeler, :hazirlik_suresi, :pisirme_suresi, :yapilis_asamalari, :resim_url, NOW()) RETURNING id");

            $stmtTarif->execute([
                ':kullanici_id' => $kullanici_id,
                ':baslik' => $baslik,
                ':malzemeler' => $malzemeler,
                ':hazirlik_suresi' => $hazirlik_suresi,
                ':pisirme_suresi' => $pisirme_suresi,
                ':yapilis_asamalari' => $yapilis_asamalari,
                ':resim_url' => $resim_url
            ]);

            $tarif_id = $stmtTarif->fetchColumn(); // Eklenen tarifin ID'si

            // Tarif-kategori ilişkisi kaydı
            $stmtIliski = $veritabani->prepare("INSERT INTO tarif_kategori_iliskisi (tarif_id, kategori_id) VALUES (:tarif_id, :kategori_id)");
            foreach ($kategori_ids as $kategori_id) {
                $stmtIliski->execute([
                    ':tarif_id' => $tarif_id,
                    ':kategori_id' => $kategori_id
                ]);
            }

            echo "Tarif başarıyla kaydedildi!";
            echo "<p><a href='anasayfa.php'>Ana Sayfa'ya Dön</a></p>";

        } catch (PDOException $e) {
            echo "Tarif kaydetme sırasında hata oluştu: " . $e->getMessage();
            echo "<p><a href='tarif_ekle.php'>Geri Dön</a></p>";
        }

    } else {
        echo "Veritabanı bağlantısı kurulamadı.";
    }
} else {
    header("Location: tarif_ekle.php");
    exit;
}
?>