<?php
include '../../database/connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $conn->prepare("DELETE FROM movie_and_series WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Film başarıyla silindi.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Silme işlemi başarısız. Film bulunamadı.'); window.location.href='index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    echo "<script>alert('Geçersiz istek.'); window.location.href='index.php';</script>";
}
?>