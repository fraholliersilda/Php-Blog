<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ATIS/actions/db.php';

// session_start();

$posts = []; 

try {
    $query = "SELECT posts.id, posts.title, posts.description, 
                     media.path AS cover_photo_path, users.username,media.user_id
              FROM posts
              LEFT JOIN media ON posts.id = media.post_id AND media.photo_type = 'cover'
              LEFT JOIN users ON media.user_id = users.id
              ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

