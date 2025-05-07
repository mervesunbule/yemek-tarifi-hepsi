<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if (!isset($_SESSION['kullanici_id'])) {
    echo "Giriş yapmanız gerekmektedir.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yorum_id = isset($_POST['yorum_id']) ? intval($_POST['yorum_id']) : 0;
    $kullanici_id = $_SESSION['kullanici_id'];

    if ($yorum_id > 0) {
        try {
            $stmtKontrol = $veritabani->prepare("SELECT 1 FROM yorumlar WHERE id = :yorum_id AND kullanici_id = :kullanici_id");
            $stmtKontrol->bindParam(':yorum_id', $yorum_id, PDO::PARAM_INT);
            $stmtKontrol->bindParam(':kullanici_id', $kullanici_id, PDO::PARAM_INT);
            $stmtKontrol->execute();

            if ($stmtKontrol->rowCount() > 0) {
                $stmtSil = $veritabani->prepare("DELETE FROM yorumlar WHERE id = :yorum_id");
                $stmtSil->bindParam(':yorum_id', $yorum_id, PDO::PARAM_INT);
                if ($stmtSil->execute()) {
                    echo "Yorum başarıyla silindi.";
                } else {
                    echo "Yorum silinirken bir hata oluştu.";
                }
            } else {
                echo "Bu yorumu silme yetkiniz yok.";
            }
        } catch (PDOException $e) {
            echo "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        echo "Geçersiz yorum ID.";
    }
} else {
    echo "Geçersiz istek.";
}
?>