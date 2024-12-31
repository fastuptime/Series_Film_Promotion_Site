<?php
include '../../../database/connect.php';

header('Content-Type: application/json');

if(isset($_POST['name'])) {
    try {
        $name = trim($_POST['name']);
        
        if(empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Kategori adı boş olamaz']);
            exit;
        }
        
        $turkce = array("ç", "Ç", "ğ", "Ğ", "ı", "İ", "ö", "Ö", "ş", "Ş", "ü", "Ü");
        $normal = array("c", "c", "g", "g", "i", "i", "o", "o", "s", "s", "u", "u");
        $slug = str_replace($turkce, $normal, $name);
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $slug));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $check = $conn->prepare("SELECT id FROM category WHERE slug = ?");
        $check->execute([$slug]);
        if($check->rowCount() > 0) {
            $slug .= '-' . time();
        }
        
        $stmt = $conn->prepare("INSERT INTO category (name, slug) VALUES (?, ?)");
        $result = $stmt->execute([$name, $slug]);
        
        if($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Kategori başarıyla eklendi',
                'category' => [
                    'id' => $conn->lastInsertId(),
                    'name' => $name,
                    'slug' => $slug
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kategori eklenemedi']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek']);
}