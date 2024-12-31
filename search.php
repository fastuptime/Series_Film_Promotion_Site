<?php include 'database/connect.php'; ?>
<?php

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    $searchQuery = $conn->prepare("SELECT * FROM movie_and_series WHERE name LIKE :search");
    $searchQuery->execute(['search' => "%$search%"]);
    $searchResults = $searchQuery->fetchAll(PDO::FETCH_ASSOC);
    $searchQueryCategory = $conn->prepare("SELECT * FROM movie_and_series WHERE category_id IN (SELECT
    id FROM category WHERE name LIKE :search)");
    $searchQueryCategory->execute(['search' => "%$search%"]);
    $searchResultsCategory = $searchQueryCategory->fetchAll(PDO::FETCH_ASSOC);
    $searchResults = array_merge($searchResults, $searchResultsCategory);
    
    echo json_encode($searchResults);
    exit;
}
