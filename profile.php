<?php
session_start();
include '../db.php'; // Veritabanı bağlantısı

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo "Bu sayfayı görüntülemek için giriş yapmalısınız.";
    exit;
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$sef_lakabi = null;



try {
    // Tariflerin favori sayısını al
    $stmt = $db->prepare("SELECT COUNT(f.tarif_id) AS favori_sayisi 
                         FROM tarifler t
                         LEFT JOIN favoriler f ON t.id = f.tarif_id
                         WHERE t.kullanici_id = :kullanici_id");
    $stmt->bindParam(':kullanici_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $sonuc = $stmt->fetch(PDO::FETCH_ASSOC);

    $toplam_favori = $sonuc && isset($sonuc['favori_sayisi']) ? $sonuc['favori_sayisi'] : 0;

    // Lakap belirleme
    if ($toplam_favori >= 150) {
        $sef_lakabi = "Master Şef";
    } elseif ($toplam_favori >= 75) {
        $sef_lakabi = "Yetenekli Şef";
    } elseif ($toplam_favori >= 30) {
        $sef_lakabi = "Usta Gurme";
    } elseif ($toplam_favori >= 10) {
        $sef_lakabi = "Lezzet Kaşifi";
    } else {
        $sef_lakabi = "Acemi Aşçı";
    }

    // Şef lakabını güncelle
    $stmt = $db->prepare("UPDATE kullanicilar SET sef_lakabi = :sef_lakabi WHERE id = :kullanici_id");
    $stmt->bindParam(':sef_lakabi', $sef_lakabi);
    $stmt->bindParam(':kullanici_id', $user_id);
    $stmt->execute();



    // Lakaba göre ikon seç
//$ikon_yolu = "img/icons/default.png"; // varsayılan ikon

//switch ($sef_lakabi) {
 //   case "Acemi Aşçı":
   //     $ikon_yolu = "img/icons/beginner.png";
     //   break;
    //case "Lezzet Kaşifi":
      //  $ikon_yolu = "img/icons/explorer.png";
        //break;
    //case "Usta Gurme":
      //  $ikon_yolu = "img/icons/gourmet.png";
        //break;
    //case "Yetenekli Şef":
      //  $ikon_yolu = "img/icons/skilled-chef.png";
        //break;
    //case "MasterChef":
      //  $ikon_yolu = "img/icons/masterchef.png";
        //break;
//}


} catch (PDOException $e) {
    echo "Bir hata oluştu: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Sayfası</title>
</head>
<body>
    <h2>Hoş geldin, <?= htmlspecialchars($username) ?>!</h2>
    <p>Rolün: <?= htmlspecialchars($role) ?></p>
    <p>Şef Lakabın: <?= htmlspecialchars($sef_lakabi) ?></p>
    <!-- Profili Düzenle butonu -->
    <p>
        <a href="edit-profile.php" style="display:inline-block; padding:10px 20px; background:#28a745; color:white; border-radius:5px; text-decoration:none;">Profili Düzenle</a>
    </p>
    <p><a href="logout.php">Çıkış Yap</a></p>
</body>
</html>
