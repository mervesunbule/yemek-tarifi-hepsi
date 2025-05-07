<?php
$hash = '$2y$10$tUxTBplyC5xrnJ0rUhIlm.ZLCFrZFx5Zg.usYOnLNdCkWp9qQY0Ou';
$password = '12345678';

if (password_verify($password, $hash)) {
    echo "Eşleşiyor!";
} else {
    echo "Eşleşmiyor.";
}
?>
