
<?php include 'connect.php'; ?>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['content']) && isset($_POST['movie_and_series_id'])) {
            $content = $_POST['content'];
            $movie_and_series_id = $_POST['movie_and_series_id'];

            $query = $conn->prepare("INSERT INTO comment (movie_and_series_id, user_id, content, status) VALUES (:movie_and_series_id, :user_id, :content, :status)");
            $query->execute(['movie_and_series_id' => $movie_and_series_id, 'user_id' => $_SESSION['user']['id'], 'content' => $content, 'status' => 'pending']);

            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing movie_and_series_id or content']);
            exit;
        }
    }
?>