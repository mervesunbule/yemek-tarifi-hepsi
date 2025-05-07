<?php
session_start();
//require_once 'veritabani.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $baslik = trim($_POST['baslik']);
    $aciklama = trim($_POST['aciklama']);
    $malzemeler = trim($_POST['malzemeler']);
    $yapilis_yolu = trim($_POST['yapilis_yolu']);

    try {
        $stmt = $conn->prepare("UPDATE tarifler 
                                SET baslik = :baslik, aciklama = :aciklama, malzemeler = :malzemeler, yapilis_yolu = :yapilis_yolu
                                WHERE id = :id");

        $stmt->bindParam(':baslik', $baslik);
        $stmt->bindParam(':aciklama', $aciklama);
        $stmt->bindParam(':malzemeler', $malzemeler);
        $stmt->bindParam(':yapilis_yolu', $yapilis_yolu);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        header("Location: tarif_listele.php");
        exit();
    } catch (PDOException $e) {
        echo "Tarif güncellenirken hata oluştu: " . $e->getMessage();
        echo "<br><a href='tarif_listele.php'>Geri Dön</a>";
    }
} else {
    header("Location: tarif_listele.php");
    exit();
}
?>