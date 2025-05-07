<?php
session_start();
include '../db.php'; // Veritabanı bağlantısını içeren dosyayı dahil edin

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Bu sayfayı görüntülemek için yetkiniz yok.";
    exit;
}

try {
    $sql = "SELECT id, kullanici_adi, eposta, admin_mi FROM kullanicilar";
    $stmt = $db->prepare($sql); // $conn yerine $db kullanıldı
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Veri çekilirken bir hata oluştu: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
</head>
<body>

<h2>Admin Paneli - Kullanıcı Listesi</h2>

<p>Hoş geldin, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Çıkış Yap</a></p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Kullanıcı Adı</th>
        <th>Email</th>
        <th>Admin mi?</th>
        <th>İşlemler</th>
    </tr>

    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['kullanici_adi']) ?></td>
        <td><?= htmlspecialchars($user['eposta']) ?></td>
        <td><?= htmlspecialchars($user['admin_mi'] == 1 ? 'Evet' : 'Hayır') ?></td>
        <td>
            <a href="delete_user.php?id=<?= $user['id'] ?>">Sil</a> |
            <a href="edit_user.php?id=<?= $user['id'] ?>">Düzenle</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
