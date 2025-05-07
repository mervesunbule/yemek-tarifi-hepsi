<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';
// Kullanıcı girişi kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

// Formdan gelen verileri al
if (isset($_POST['id'], $_POST['baslik'], $_POST['malzemeler'], $_POST['hazirlik_suresi'], $_POST['pisirme_suresi'], $_POST['yapilis_asamalari'])) {
    
    $tarif_id = (int)$_POST['id'];
    $kullanici_id = $_SESSION['kullanici_id'];

    $baslik = trim($_POST['baslik']);
    $malzemeler = trim($_POST['malzemeler']);
    $hazirlik_suresi = trim($_POST['hazirlik_suresi']);
    $pisirme_suresi = trim($_POST['pisirme_suresi']);
    $yapilis_asamalari = trim($_POST['yapilis_asamalari']);

    // Kullanıcı gerçekten bu tarifin sahibi mi?
    $kontrol = $veritabani->prepare("SELECT id FROM tarifler WHERE id = :id AND kullanici_id = :kullanici_id");
    $kontrol->execute([
        'id' => $tarif_id,
        'kullanici_id' => $kullanici_id
    ]);

    if ($kontrol->rowCount() === 0) {
        echo "Bu tarifi güncelleyemezsiniz.";
        exit;
    }

    // Güncelleme işlemi
    $guncelle = $veritabani->prepare("
        UPDATE tarifler 
        SET baslik = :baslik, malzemeler = :malzemeler, hazirlik_suresi = :hazirlik_suresi, pisirme_suresi = :pisirme_suresi, yapilis_asamalari = :yapilis_asamalari
        WHERE id = :id AND kullanici_id = :kullanici_id
    ");
    $guncelle->execute([
        'baslik' => $baslik,
        'malzemeler' => $malzemeler,
        'hazirlik_suresi' => $hazirlik_suresi,
        'pisirme_suresi' => $pisirme_suresi,
        'yapilis_asamalari' => $yapilis_asamalari,
        'id' => $tarif_id,
        'kullanici_id' => $kullanici_id
    ]);

    // Başarıyla güncellendiyse listeye dön
    header("Location: tarif_listesi.php");
    exit;
} else {
    echo "Eksik veri gönderildi.";
    exit;
}
?>