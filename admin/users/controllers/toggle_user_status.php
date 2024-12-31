<?php

include '../../../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    $userQuery = $conn->prepare("SELECT is_active FROM user WHERE id = :id");
    $userQuery->execute(['id' => $userId]);
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    if($user){
        $newStatus = $user['is_active'] ? 0 : 1;

        $toggleUserStatusQuery = $conn->prepare("UPDATE user SET is_active = :is_active WHERE id = :id");
        if($toggleUserStatusQuery->execute(['is_active' => $newStatus, 'id' => $userId])){
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>