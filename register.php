<?php include 'database/connect.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email OR username = :username");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    function hash_password($password) {
        return hash('sha256', $password);
    }

    if (!$user) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("INSERT INTO user (username, email, password, ip_address) VALUES (:username, :email, :password, :ip_address)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $hashedPassword = hash_password($password);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $_SESSION['user'] = $user;
    
        echo "<html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>Swal.fire('Başarılı!', 'Başarıyla kayıt oldunuz.', 'success').then(() => window.location.href = 'index.php');</script></body></html>";
    } else {
        echo "<html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>Swal.fire('Hata!', 'Bu e-posta adresi veya kullanıcı adı zaten kullanımda.', 'error').then(() => window.location.href = 'index.php');</script></body></html>";
    }
}
?>