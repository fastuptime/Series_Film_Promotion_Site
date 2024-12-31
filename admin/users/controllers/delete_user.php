<?php
include '../../../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    try {
        $conn->beginTransaction();

        $deleteCommentsQuery = $conn->prepare("DELETE FROM comment WHERE user_id = :user_id");
        $deleteCommentsQuery->execute(['user_id' => $userId]);

        $deleteUserQuery = $conn->prepare("DELETE FROM user WHERE id = :id");
        $deleteUserQuery->execute(['id' => $userId]);

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>