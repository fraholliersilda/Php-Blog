<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

session_start();

try {
    // Fetch all posts with their cover images
    $query = "SELECT posts.id, posts.title, posts.description, media.path AS cover_photo_path
              FROM posts
              LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
              ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
