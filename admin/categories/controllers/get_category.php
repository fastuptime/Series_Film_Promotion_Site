<?php
include '../../../database/connect.php';

header('Content-Type: application/json');

if(isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM category WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'category' => $category]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

?>