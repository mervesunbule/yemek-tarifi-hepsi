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
    $ekle = isset($_POST['ekle']) ? filter_var($_POST['ekle'], FILTER_VALIDATE_BOOLEAN) : false;
    $kullanici_id = $_SESSION['kullanici_id'];

    if ($tarif_id > 0) {
        try {
            if ($ekle) {
                // Favorilere ekle
                $stmtEkle = $veritabani->prepare("INSERT INTO favoriler (kullanici_id, tarif_id) VALUES (:kullanici_id, :tarif_id)");
                $stmtEkle->bindParam(':kullanici_id', $kullanici_id, PDO::PARAM_INT);
                $stmtEkle->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
                if ($stmtEkle->execute()) {
                    echo "eklendi";
                } else {
                    echo "Favorilere eklenirken bir hata oluştu.";
                }
            } else {
                // Favorilerden çıkar
                $stmtCikar = $veritabani->prepare("DELETE FROM favoriler WHERE kullanici_id = :kullanici_id AND tarif_id = :tarif_id");
                $stmtCikar->bindParam(':kullanici_id', $kullanici_id, PDO::PARAM_INT);
                $stmtCikar->bindParam(':tarif_id', $tarif_id, PDO::PARAM_INT);
                if ($stmtCikar->execute()) {
                    echo "cikarildi";
                } else {
                    echo "Favorilerden çıkarılırken bir hata oluştu.";
                }
            }
        } catch (PDOException $e) {
            echo "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        echo "Geçersiz tarif ID.";
    }
} else {
    echo "Geçersiz istek.";
}
?>