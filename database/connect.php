<?php

$host = "localhost";
$port = "3306";
$dbname = "final";
$username = "root";
$password = "";


try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password, $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully";
    $GLOBALS['conn'] = $conn;
    session_start();

    $GLOBALS['checkAuth'] = function() {
        if (!isset($_SESSION['user'])) {
            echo "<script>Swal.fire('Giriş yapmalısınız!', 'Bu sayfaya erişmek için giriş yapmalısınız.', 'error').then(() => window.location.href = 'index.php');</script>";
        }
    };
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>