<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    function hash_password($password) {
        return hash('sha256', $password);
    }

    if ($user) {
        if ($user['password'] === hash_password($password)) {
            if ($user['is_active'] == 0) {
                echo "<script>Swal.fire('Hata!', 'Hesabınız aktif değil.', 'error');</script>";
                exit;
            }

            $_SESSION['user'] = $user;

            $stmt = $conn->prepare("UPDATE user SET last_login = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
            
            echo "<script>Swal.fire('Giriş başarılı!', 'Başarıyla giriş yaptınız.', 'success').then(() => window.location.href = 'index.php');</script>";
        } else {
            echo "<script>Swal.fire('Hata!', 'E-posta veya şifre hatalı.', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('Hata!', 'E-posta veya şifre hatalı.', 'error');</script>";
    }
}

?>