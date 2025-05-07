<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
</head>
<body>
    <h2>Giriş Yap</h2>
    <form id="loginForm">
        <input type="email" name="email" placeholder="Eposta" required><br><br>
        <input type="password" name="password" placeholder="Şifre" required><br><br>
        <button type="submit">Giriş Yap</button>
    </form>

    <p style="color:red;" id="errorMessage"></p>

    <p>Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("login_islem.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    document.getElementById("errorMessage").textContent = data.message;
                }
            })
            .catch(error => {
                console.error("Hata:", error);
                document.getElementById("errorMessage").textContent = "Bir hata oluştu.";
            });
        });
    </script>
</body>
</html>


