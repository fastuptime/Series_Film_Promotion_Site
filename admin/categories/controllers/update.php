<?php
include '../../../database/connect.php';

header('Content-Type: application/json');

if(isset($_POST['id']) && isset($_POST['name'])) {
    try {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name));
        
        $stmt = $conn->prepare("UPDATE category SET name = ?, slug = ? WHERE id = ?");
        $result = $stmt->execute([$name, $slug, $id]);
        
        echo json_encode(['success' => $result]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

?>