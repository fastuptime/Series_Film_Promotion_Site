
<?php include 'connect.php'; ?>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'false') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['contentId']) && isset($input['status'])) {
            $contentId = $input['contentId'];
            $status = $input['status'];

            $query = $conn->prepare("INSERT INTO watchlist (user_id, movie_and_series_id, status) VALUES (:user_id, :movie_and_series_id, :status)");
            $query->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $contentId, 'status' => $status]);

            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing contentId or status']);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'true') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['contentId'])) {
            $contentId = $input['contentId'];

            $query = $conn->prepare("DELETE FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
            $query->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $contentId]);

            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing contentId']);
            exit;
        }
    }

    $query = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id");
    $query->execute(['user_id' => $_SESSION['user']['id']]);
    $watchlist = $query->fetchAll(PDO::FETCH_ASSOC);
?>