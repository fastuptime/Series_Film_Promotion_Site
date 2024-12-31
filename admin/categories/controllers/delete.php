<?php
include '../../../database/connect.php';

header('Content-Type: application/json');

if(isset($_POST['id'])) {
    try {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        echo json_encode(['success' => $result]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

?>