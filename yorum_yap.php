<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if (!isset($_SESSION['kullanici_id'])) {
    echo "Giriş yapmanız gerekmektedir.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tarif_id = isset($_POST['tarif_id']) ? intval($_POST['tarif_id']) : 0;
    $yorum_metni = isset($_POST['yorum_metni']) ? trim($_POST['yorum_metni']) : '';
    $puan = isset($_POST['puan']) ? intval($_POST['puan']) : 0;
    $kullanici_id = $_SESSION['kullanici_id'];

    if ($tarif_id > 0 && !empty($yorum_metni) && $puan >= 1 && $puan <= 5) {
        try {
            $stmtYorumEkle = $veritabani->prepare("INSERT INTO yorumlar (kullanici_id, tarif_id, yorum_metni, puan) VALUES (:kullanici_id, :tarif_id, :yorum_metni, :puan)");
            $stmtYorumEkle->bindParam(':kullanici_id', $kullanici_id, PDO::PARAM_INT);
            $stmtYorumEkle->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
            $stmtYorumEkle->bindParam(':yorum_metni', $yorum_metni);
            $stmtYorumEkle->bindParam(':puan', $puan, PDO::PARAM_INT);
            if ($stmtYorumEkle->execute()) {
                echo "Yorumunuz başarıyla gönderildi.";
            } else {
                echo "Yorum gönderilirken bir hata oluştu.";
            }
        } catch (PDOException $e) {
            echo "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        echo "Geçersiz yorum bilgileri.";
    }
} else {
    echo "Geçersiz istek.";
}
?>