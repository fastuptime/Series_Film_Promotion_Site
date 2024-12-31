<?php
include '../../../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    $updateCommentQuery = $conn->prepare("UPDATE comment SET status = 'deleted' WHERE id = :id");
    if($updateCommentQuery->execute(['id' => $userId])){
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>