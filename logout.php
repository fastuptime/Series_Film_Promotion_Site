<?php include 'database/connect.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_destroy();

    echo "<script>window.location.href = 'index.php';</script>";
}
?>