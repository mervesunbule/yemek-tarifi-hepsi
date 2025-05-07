<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

// Kullanıcı girişi kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $tarif_id = (int)$_POST['id'];
    $kullanici_id = $_SESSION['kullanici_id'];

    // Kullanıcı gerçekten bu tarifin sahibi mi?
    $kontrol = $veritabani->prepare("SELECT id FROM tarifler WHERE id = :id AND kullanici_id = :kullanici_id");
    $kontrol->execute([
        'id' => $tarif_id,
        'kullanici_id' => $kullanici_id
    ]);

    if ($kontrol->rowCount() === 0) {
        echo "Bu tarifi silemezsiniz.";
        exit;
    }

    // Silme işlemi
    $sil = $veritabani->prepare("DELETE FROM tarifler WHERE id = :id AND kullanici_id = :kullanici_id");
    $sil->execute([
        'id' => $tarif_id,
        'kullanici_id' => $kullanici_id
    ]);

    // Başarıyla silindi
    header("Location: tarif_listesi.php");
    exit;
} else {
    echo "Geçersiz istek.";
    exit;
}
?>