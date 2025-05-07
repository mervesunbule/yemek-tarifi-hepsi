<?php
session_start();
include '../db.php'; // Veritabanı bağlantısını dahil et

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo "Tarif eklemek için giriş yapmalısınız.";
    exit;
}

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    try {
        $sql = "INSERT INTO tarifler (user_id, title, ingredients, instructions) 
                VALUES (:user_id, :title, :ingredients, :instructions)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':ingredients' => $ingredients,
            ':instructions' => $instructions
        ]);

        echo "Tarif başarıyla eklendi!";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!-- HTML Formu -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Tarif Ekle</title>
</head>
<body>
    <h2>Tarif Ekle</h2>
    <form method="POST" action="">
        <label>Tarif Başlığı:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Malzemeler:</label><br>
        <textarea name="ingredients" rows="5" cols="40" required></textarea><br><br>

        <label>Yapılış Talimatları:</label><br>
        <textarea name="instructions" rows="6" cols="40" required></textarea><br><br>

        <input type="submit" value="Tarifi Ekle">
    </form>
</body>
</html>
